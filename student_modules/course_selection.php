<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student info
$stmt = $pdo->prepare("SELECT Department, Level FROM studenttbl WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Check existing registration for current semester
$currentSemester = 'Fall 2024';
$currentYear = '2024/2025';

$existingRegStmt = $pdo->prepare("SELECT approval_status FROM course_regtbl WHERE student_id = ? AND semester = ? AND academic_year = ? LIMIT 1");
$existingRegStmt->execute([$student_id, $currentSemester, $currentYear]);
$registrationStatus = $existingRegStmt->fetchColumn();
$existingRegistration = $registrationStatus !== false;

// Handle course registration submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_registration'])) {
    $selected_courses = $_POST['courses'] ?? [];
    
    if (count($selected_courses) < 5) {
        $error = "You must select at least 5 courses for registration.";
    } elseif ($existingRegistration) {
        $error = "You have already submitted a registration for this semester.";
    } else {
        $success_count = 0;
        foreach ($selected_courses as $course_id) {
            try {
                // Check if already registered for this specific course
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM course_regtbl WHERE student_id = ? AND course_id = ? AND semester = ? AND academic_year = ?");
                $checkStmt->execute([$student_id, $course_id, $currentSemester, $currentYear]);
                
                if ($checkStmt->fetchColumn() == 0) {
                    $regStmt = $pdo->prepare("INSERT IGNORE INTO course_regtbl (student_id, course_id, academic_year, semester, date_registered) VALUES (?, ?, ?, ?, NOW())");
                    if ($regStmt->execute([$student_id, $course_id, $currentYear, $currentSemester])) {
                        $success_count++;
                    }
                }
            } catch (PDOException $e) {
                // Skip duplicate entries silently
                continue;
            }
        }
        
        if ($success_count > 0) {
            $success = "Successfully registered for $success_count course(s)!";
            $existingRegistration = true;
        } else {
            $error = "Failed to register courses. Please try again.";
        }
    }
}

// Fetch available courses
$courseStmt = $pdo->prepare("
    SELECT 
        c.course_id,
        c.course_code,
        c.course_title,
        c.course_unit,
        c.department,
        c.level,
        c.semester,
        CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name,
        COUNT(e.student_id) as enrolled_count
    FROM coursetbl c
    LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
    LEFT JOIN enrollmenttbl e ON c.course_id = e.course_id
    GROUP BY c.course_id
    ORDER BY c.department, c.course_code
");
$courseStmt->execute();
$courses = $courseStmt->fetchAll();

// Fetch already registered courses for this student
$enrolledStmt = $pdo->prepare("SELECT course_id FROM course_regtbl WHERE student_id = ? AND semester = ? AND academic_year = ?");
$enrolledStmt->execute([$student_id, $currentSemester, $currentYear]);
$enrolled_courses = array_column($enrolledStmt->fetchAll(), 'course_id');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Selection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(to right, #e2e2e2, #c9d6ff);
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            padding: 20px 0;
        }
        .container {
            max-width: 1200px;
        }
        .course-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .course-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            /* background: linear-gradient(135deg, #1d976c 0%, #93f9b9 100%); */
            color: white;
            padding: 20px;
            text-align: center;
        }
        .course-item {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .course-item:hover {
            border-color: #1d976c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(29, 151, 108, 0.2);
        }
        .course-item.enrolled {
            background: #d4edda;
            border-color: #28a745;
        }
        .course-item.selected {
            background: #e3f2fd;
            border-color: #2196f3;
        }
        .btn-register {
            background: linear-gradient(135deg, #1d976c 0%, #93f9b9 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(29, 151, 108, 0.4);
        }
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="course-card">
            <div class="course-header">
                <h1><i class="bi bi-book-half me-2"></i>Course Selection & Registration</h1>
                <p class="mb-0">Select courses for the upcoming semester</p>
            </div>
            
            <div class="p-4">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($existingRegistration): ?>
                    <?php if ($registrationStatus === 'pending'): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-clock me-2"></i>
                            Your course registration is pending admin approval. You will be able to download your form once approved.
                        </div>
                    <?php elseif ($registrationStatus === 'approved'): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            Your course registration has been approved!
                            <a href="download_form_selector.php" class="btn btn-sm btn-success ms-2">
                                <i class="bi bi-download me-1"></i>Download Form
                            </a>
                        </div>
                    <?php elseif ($registrationStatus === 'declined'): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle me-2"></i>
                            Your course registration was declined. Please contact the admin for more information.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="filter-section">
                    <div class="row">
                        <div class="col-md-3">
                            <select class="form-select" id="departmentFilter">
                                <option value="">All Departments</option>
                                <?php
                                $departments = array_unique(array_column($courses, 'department'));
                                foreach ($departments as $dept): ?>
                                    <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="levelFilter">
                                <option value="">All Levels</option>
                                <option value="ND 1">ND 1</option>
                                <option value="ND 2">ND 2</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search courses, lecturers, departments...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">Clear</button>
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <div class="row">
                        <div class="col-lg-8">
                            <h4 class="mb-3">Available Courses</h4>
                            <div id="coursesList">
                                <?php foreach ($courses as $course): ?>
                                    <?php 
                                    $isEnrolled = in_array($course['course_id'], $enrolled_courses);
                                    $enrolledCount = $course['enrolled_count'] ?? 0;
                                    ?>
                                    <div class="course-item <?php echo $isEnrolled ? 'enrolled' : ''; ?>" 
                                         data-department="<?php echo htmlspecialchars($course['department']); ?>"
                                         data-level="<?php echo htmlspecialchars($course['level']); ?>"
                                         data-search="<?php echo htmlspecialchars(strtolower($course['course_code'] . ' ' . $course['course_title'] . ' ' . ($course['lecturer_name'] ?? '') . ' ' . $course['department'] . ' ' . $course['semester'])); ?>">
                                        <div class="row align-items-center">
                                            <div class="col-md-1">
                                                <?php if (!$isEnrolled): ?>
                                                    <input type="checkbox" name="courses[]" value="<?php echo $course['course_id']; ?>" 
                                                           class="form-check-input course-checkbox">
                                                <?php else: ?>
                                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-11">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($course['course_code']); ?></h6>
                                                        <p class="mb-1 fw-bold"><?php echo htmlspecialchars($course['course_title']); ?></p>
                                                        <small class="text-muted">
                                                            <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($course['lecturer_name'] ?? 'TBD'); ?> |
                                                            <i class="bi bi-building me-1"></i><?php echo htmlspecialchars($course['department']); ?> |
                                                            <i class="bi bi-layers me-1"></i>Level <?php echo htmlspecialchars($course['level']); ?>
                                                        </small>
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <span class="badge bg-primary mb-2"><?php echo $course['course_unit']; ?> Units</span><br>
                                                        <span class="badge bg-info"><?php echo $enrolledCount; ?> Enrolled</span>
                                                        <?php if ($isEnrolled): ?>
                                                            <br><span class="badge bg-success mt-1">Registered</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="sticky-top" style="top: 20px;">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="bi bi-cart me-2"></i>Selected Courses</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="selectedCourses">
                                            <p class="text-muted text-center">No courses selected</p>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Units:</strong>
                                            <strong id="totalUnits">0</strong>
                                        </div>
                                        <?php if (!$existingRegistration): ?>
                                            <button type="submit" name="submit_registration" class="btn btn-register w-100 mt-3" id="submitBtn" disabled>
                                                <i class="bi bi-send me-2"></i>Submit Registration (Min 5 courses)
                                            </button>
                                        <?php else: ?>
                                            <div class="alert alert-secondary text-center">
                                                <i class="bi bi-clock me-2"></i>Registration Submitted
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mt-3 text-center">
                                    <a href="student_dashboard.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const courses = <?php echo json_encode($courses); ?>;
        
        function updateSelectedCourses() {
            const checkboxes = document.querySelectorAll('.course-checkbox:checked');
            const selectedDiv = document.getElementById('selectedCourses');
            const totalUnitsSpan = document.getElementById('totalUnits');
            const submitBtn = document.getElementById('submitBtn');
            
            if (checkboxes.length === 0) {
                selectedDiv.innerHTML = '<p class="text-muted text-center">No courses selected</p>';
                totalUnitsSpan.textContent = '0';
                if (submitBtn) submitBtn.disabled = true;
                return;
            }
            
            let html = '';
            let totalUnits = 0;
            
            checkboxes.forEach(checkbox => {
                const course = courses.find(c => c.course_id == checkbox.value);
                if (course) {
                    totalUnits += parseInt(course.course_unit);
                    html += `
                        <div class="mb-2 p-2 bg-light rounded">
                            <small class="fw-bold">${course.course_code}</small><br>
                            <small>${course.course_title}</small>
                            <span class="badge bg-primary float-end">${course.course_unit} units</span>
                        </div>
                    `;
                }
            });
            
            selectedDiv.innerHTML = html;
            totalUnitsSpan.textContent = totalUnits;
            
            // Enable/disable submit button based on minimum 5 courses
            if (submitBtn) {
                submitBtn.disabled = checkboxes.length < 5;
                submitBtn.innerHTML = checkboxes.length < 5 ? 
                    '<i class="bi bi-send me-2"></i>Submit Registration (Min 5 courses)' :
                    '<i class="bi bi-send me-2"></i>Submit Registration (' + checkboxes.length + ' courses)';
            }
        }
        
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('course-checkbox')) {
                updateSelectedCourses();
                e.target.closest('.course-item').classList.toggle('selected', e.target.checked);
            }
        });
        
        // Filters
        function filterCourses() {
            const dept = document.getElementById('departmentFilter').value;
            const level = document.getElementById('levelFilter').value;
            const search = document.getElementById('searchFilter').value.toLowerCase();
            
            document.querySelectorAll('.course-item').forEach(item => {
                const itemDept = item.dataset.department;
                const itemLevel = item.dataset.level;
                const itemSearch = item.dataset.search;
                
                const deptMatch = !dept || itemDept === dept;
                const levelMatch = !level || itemLevel === level;
                const searchMatch = !search || itemSearch.includes(search);
                
                item.style.display = deptMatch && levelMatch && searchMatch ? 'block' : 'none';
            });
        }
        
        document.getElementById('departmentFilter').addEventListener('change', filterCourses);
        document.getElementById('levelFilter').addEventListener('change', filterCourses);
        document.getElementById('searchFilter').addEventListener('input', filterCourses);
        
        function clearFilters() {
            document.getElementById('departmentFilter').value = '';
            document.getElementById('levelFilter').value = '';
            document.getElementById('searchFilter').value = '';
            filterCourses();
        }
    </script>
</body>
</html>