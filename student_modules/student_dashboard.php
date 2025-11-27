<?php
session_start();
require_once '../config/db.php';

// Redirect to login if student is not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch logged-in student details
$stmt = $pdo->prepare("SELECT student_id, Matric_No, first_name, last_name, email, Department, Level 
                       FROM studenttbl 
                       WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    // If student record is missing, force re-login
    session_unset();
    session_destroy();
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_name       = $student['first_name'] . ' ' . $student['last_name'];
$student_matric     = $student['Matric_No'];
$student_department = $student['Department'] ?? 'Unknown Department';
$student_level      = $student['Level'] ?? '';

// Initials for avatar
$firstInitial = mb_substr($student['first_name'], 0, 1, 'UTF-8');
$lastInitial  = mb_substr($student['last_name'], 0, 1, 'UTF-8');
$student_initials = strtoupper($firstInitial . $lastInitial);

// Fetch enrolled courses for this student
$courseStmt = $pdo->prepare("
    SELECT 
        c.course_code,
        c.course_title,
        c.course_unit,
        c.department,
        c.level,
        c.semester,
        CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name
    FROM enrollmenttbl e
    JOIN coursetbl c ON e.course_id = c.course_id
    LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
    WHERE e.student_id = ?
    ORDER BY c.course_code
");
$courseStmt->execute([$student_id]);
$enrolled_courses = $courseStmt->fetchAll();
$registered_courses_count = is_array($enrolled_courses) ? count($enrolled_courses) : 0;

// Fetch results for this student (uses resulttbl structure)
$resultStmt = $pdo->prepare("
    SELECT 
        r.course_id,
        r.ca_score,
        r.test_score,
        r.exam_score,
        r.total_score,
        r.grade_letter,
        r.academic_year,
        r.semester,
        c.course_code,
        c.course_title,
        c.course_unit
    FROM resulttbl r
    JOIN coursetbl c ON r.course_id = c.course_id
    WHERE r.student_id = ?
    ORDER BY c.course_code
");
$resultStmt->execute([$student_id]);
$results = $resultStmt->fetchAll();

// Get current academic year for this student (latest from results)
$academicYearStmt = $pdo->prepare("
    SELECT academic_year 
    FROM resulttbl 
    WHERE student_id = ? AND academic_year IS NOT NULL AND academic_year != '' 
    ORDER BY created_at DESC 
    LIMIT 1
");
$academicYearStmt->execute([$student_id]);
$academicYearRow = $academicYearStmt->fetch();
$current_academic_year = $academicYearRow['academic_year'] ?? null;

// Load recent activities for this student
$activityStmt = $pdo->prepare("
    SELECT activity_type, activity_description, timestamp 
    FROM studentrecentactivitytbl 
    WHERE student_id = ? 
    ORDER BY timestamp DESC 
    LIMIT 10
");
$activityStmt->execute([$student_id]);
$recent_activities = $activityStmt->fetchAll();

// GPA calculation based on score → points (0–5) and letter mapping
// Excel logic provided:
// =IF(C4<40,"0",IF(C4<45,"1",IF(C4<50,"2",IF(C4<60,"3",IF(C4<70,"4","5")))))
// =IF(C4<40,"F",IF(C4<45,"E",IF(C4<50,"D",IF(C4<60,"C",IF(C4<70,"B","A")))))
$letter_points_map = [
    'A' => 5.0,
    'B' => 4.0,
    'C' => 3.0,
    'D' => 2.0,
    'E' => 1.0,
    'F' => 0.0,
];

$total_points = 0.0;
$total_credits = 0;

if (is_array($results)) {
    foreach ($results as $row) {
        // Prefer numeric total_score; fall back to stored letter_grade if needed
        $raw_score = $row['total_score'] ?? null;
        $raw_letter = $row['grade_letter'] ?? '';

        $credits = (int)($row['course_unit'] ?? 0);
        if ($credits <= 0) {
            $credits = 3; // fallback credit hours
        }

        // Determine points either from numeric score or letter grade (Excel logic)
        $points = null;

        if ($raw_score !== null && $raw_score !== '' && is_numeric($raw_score)) {
            // Treat as numeric score (0–100)
            $score = (float)$raw_score;
            if ($score < 40) {
                $points = 0.0;
            } elseif ($score < 45) {
                $points = 1.0;
            } elseif ($score < 50) {
                $points = 2.0;
            } elseif ($score < 60) {
                $points = 3.0;
            } elseif ($score < 70) {
                $points = 4.0;
            } else {
                $points = 5.0;
            }
        } else {
            // Treat as letter grade (A–F)
            $letter = strtoupper(trim($raw_letter));
            if (isset($letter_points_map[$letter])) {
                $points = $letter_points_map[$letter];
            }
        }

        if ($points === null) {
            continue; // skip unrecognized grades
        }

        $total_credits += $credits;
        $total_points  += $credits * $points;
    }
}

$current_gpa = $total_credits > 0 ? number_format($total_points / $total_credits, 2) : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/student_dashboard.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                EduManage Pro
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($student_name); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../student_modules/student_profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../student_modules/student_logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="user-profile">
                        <div class="profile-avatar"><?php echo htmlspecialchars($student_initials); ?></div>
                        <div>
                            <h6 class="mb-1"><?php echo htmlspecialchars($student_name); ?></h6>
                            <small class="text-muted">Matric No: <?php echo htmlspecialchars($student_matric); ?></small>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($student_department); ?></small>
                        </div>
                    </div>
                    
                    <nav class="nav flex-column px-3">
                        <a class="nav-link active" href="#dashboard" data-section="dashboard">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#courses" data-section="courses">
                            <i class="fas fa-book"></i>My Courses
                        </a>
                        <a class="nav-link" href="#results" data-section="results">
                            <i class="fas fa-chart-line"></i>Results
                        </a>
                        <a class="nav-link" href="#transcript" data-section="transcript">
                            <i class="fas fa-file-alt"></i>Transcript
                        </a>
                        <a class="nav-link" href="#progress" data-section="progress">
                            <i class="fas fa-tasks"></i>Academic Progress
                        </a>
                        <a class="nav-link" href="course_selection.php">
                            <i class="fas fa-plus-circle"></i>Course Selection
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    
                    <!-- Dashboard Section -->
                    <div id="dashboard" class="content-section">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Dashboard Overview</h2>
                            <span class="text-muted">
                                <?php if (!empty($current_academic_year)): ?>
                                    Academic Year <?php echo htmlspecialchars($current_academic_year); ?>
                                <?php else: ?>
                                    Academic Year not set
                                <?php endif; ?>
                            </span>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <h3><?php echo (int)$registered_courses_count; ?></h3>
                                    <p class="mb-0">Registered Courses</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, var(--success-color), #58d68d);">
                                    <h3><?php echo htmlspecialchars($current_gpa); ?></h3>
                                    <p class="mb-0">Current GPA</p>
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, var(--warning-color), #f7dc6f);">
                                    <h3>85%</h3>
                                    <p class="mb-0">Attendance Rate</p>
                                </div>
                            </div> -->
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, var(--accent-color), #ec7063);">
                                    <h3>4</h3>
                                    <p class="mb-0">Pending Assignments</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Activity</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($recent_activities)): ?>
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($recent_activities as $activity): ?>
                                                    <div class="list-group-item border-0 px-0">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-1">
                                                                    <?php echo htmlspecialchars($activity['activity_type']); ?>
                                                                </h6>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars($activity['activity_description']); ?>
                                                                </small>
                                                            </div>
                                                            <small class="text-muted">
                                                                <?php
                                                                    $ts = $activity['timestamp'] ?? '';
                                                                    echo $ts ? htmlspecialchars(date('M j, Y H:i', strtotime($ts))) : '';
                                                                ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-3">
                                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                                <p>No recent activity yet</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Upcoming Deadlines</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <small class="fw-bold">Algorithm Analysis Project</small>
                                                <small class="text-danger">2 days</small>
                                            </div>
                                            <div class="progress progress-custom mt-1">
                                                <div class="progress-bar bg-danger" style="width: 80%"></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <small class="fw-bold">Software Engineering Essay</small>
                                                <small class="text-warning">5 days</small>
                                            </div>
                                            <div class="progress progress-custom mt-1">
                                                <div class="progress-bar bg-warning" style="width: 60%"></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <small class="fw-bold">Network Security Lab</small>
                                                <small class="text-success">10 days</small>
                                            </div>
                                            <div class="progress progress-custom mt-1">
                                                <div class="progress-bar bg-success" style="width: 30%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- My Courses Section -->
                    <div id="courses" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">My Registered Courses</h2>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Spring 2024
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Spring 2024</a></li>
                                    <li><a class="dropdown-item" href="#">Fall 2023</a></li>
                                    <li><a class="dropdown-item" href="#">Summer 2023</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="row">
                            <?php if (!empty($enrolled_courses)): ?>
                                <?php foreach ($enrolled_courses as $course): ?>
                                    <div class="col-lg-6 mb-4">
                                        <div class="card dashboard-card course-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <h5 class="card-title text-primary">
                                                            <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?>
                                                        </h5>
                                                        <p class="text-muted mb-1">
                                                            <?php echo htmlspecialchars($course['lecturer_name'] ?? 'Lecturer TBD'); ?>
                                                        </p>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars($course['department'] . ' • Level ' . $course['level']); ?>
                                                        </small>
                                                    </div>
                                                    <span class="badge grade-badge bg-secondary">
                                                        <?php echo (int)($course['course_unit'] ?? 0); ?> Units
                                                    </span>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-primary btn-custom btn-sm" type="button">View Materials</button>
                                                    <button class="btn btn-outline-primary btn-sm" type="button">Assignments</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        You are not currently enrolled in any courses.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Results Section -->
                    <div id="results" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Academic Results</h2>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    All Semesters
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Spring 2024</a></li>
                                    <li><a class="dropdown-item" href="#">Fall 2023</a></li>
                                    <li><a class="dropdown-item" href="#">Summer 2023</a></li>
                                    <li><a class="dropdown-item" href="#">Spring 2023</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="card dashboard-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Semester Results</h5>
                            </div>
                            <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-custom">
                                                <thead>
                                                    <tr>
                                                        <th>Course Code</th>
                                                        <th>Course Title</th>
                                                        <th>Credit Hours</th>
                                                        <th>CA Score</th>
                                                        <th>Test Score</th>
                                                        <th>Exam Score</th>
                                                        <th>Total</th>
                                                        <th>Final Grade</th>
                                                        <th>GPA Points</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($results)): ?>
                                                        <?php foreach ($results as $row): ?>
                                                            <?php
                                                                $ca_score   = $row['ca_score']   ?? null;
                                                                $test_score = $row['test_score'] ?? null;
                                                                $exam_score = $row['exam_score'] ?? null;
                                                                $total_score = $row['total_score'] ?? null;
                                                                $stored_letter = $row['grade_letter'] ?? '';

                                                                $display_letter = '';
                                                                $points = null;

                                                                $credits = (int)($row['course_unit'] ?? 0);
                                                                if ($credits <= 0) {
                                                                    $credits = 3;
                                                                }

                                                                if ($total_score !== null && $total_score !== '' && is_numeric($total_score)) {
                                                                    $score = (float)$total_score;
                                                                    if ($score < 40) {
                                                                        $points = 0.0;
                                                                        $display_letter = 'F';
                                                                    } elseif ($score < 45) {
                                                                        $points = 1.0;
                                                                        $display_letter = 'E';
                                                                    } elseif ($score < 50) {
                                                                        $points = 2.0;
                                                                        $display_letter = 'D';
                                                                    } elseif ($score < 60) {
                                                                        $points = 3.0;
                                                                        $display_letter = 'C';
                                                                    } elseif ($score < 70) {
                                                                        $points = 4.0;
                                                                        $display_letter = 'B';
                                                                    } else {
                                                                        $points = 5.0;
                                                                        $display_letter = 'A';
                                                                    }
                                                                } else {
                                                                    $letter = strtoupper(trim($stored_letter));
                                                                    $display_letter = $letter;
                                                                    if (isset($letter_points_map[$letter])) {
                                                                        $points = $letter_points_map[$letter];
                                                                    }
                                                                }

                                                                $gpa_points = $points !== null ? number_format($points, 1) : '-';
                                                            ?>
                                                            <tr>
                                                                <td class="fw-bold"><?php echo htmlspecialchars($row['course_code']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['course_title'] ?? ''); ?></td>
                                                                <td><?php echo (int)$credits; ?></td>
                                                                <td><span class="badge bg-<?php echo is_numeric($ca_score) ? 'success' : 'secondary'; ?>">
                                                                    <?php echo is_numeric($ca_score) ? htmlspecialchars($ca_score) : '-'; ?>
                                                                </span></td>
                                                                <td><span class="badge bg-<?php echo is_numeric($test_score) ? 'success' : 'secondary'; ?>">
                                                                    <?php echo is_numeric($test_score) ? htmlspecialchars($test_score) : '-'; ?>
                                                                </span></td>
                                                                <td><span class="badge bg-<?php echo is_numeric($exam_score) ? 'success' : 'secondary'; ?>">
                                                                    <?php echo is_numeric($exam_score) ? htmlspecialchars($exam_score) : '-'; ?>
                                                                </span></td>
                                                                <td><span class="badge bg-<?php echo is_numeric($total_score) ? 'primary' : 'secondary'; ?>">
                                                                    <?php echo is_numeric($total_score) ? htmlspecialchars($total_score) : '-'; ?>
                                                                </span></td>
                                                                <td>
                                                                    <span class="badge bg-success fs-6">
                                                                        <?php echo htmlspecialchars($display_letter); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($gpa_points); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="8" class="text-center text-muted">
                                                                No results available yet.
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="alert alert-info alert-custom">
                                            <h6><i class="fas fa-info-circle me-2"></i>Semester Summary</h6>
                                            <p class="mb-1"><strong>Total Credit Hours:</strong> 12</p>
                                            <p class="mb-1"><strong>Semester GPA:</strong> 3.5</p>
                                            <p class="mb-0"><strong>Cumulative GPA:</strong> 3.7</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-success alert-custom">
                                            <h6><i class="fas fa-trophy me-2"></i>Academic Standing</h6>
                                            <p class="mb-0">Dean's List - Excellent Performance</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transcript Section -->
                    <div id="transcript" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Academic Transcript</h2>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-custom">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </button>
                                <button class="btn btn-outline-primary btn-custom">
                                    <i class="fas fa-envelope me-2"></i>Request Official
                                </button>
                            </div>
                        </div>

                        <div class="card dashboard-card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="mb-1">Official Academic Transcript</h5>
                                        <p class="mb-0">Computer Science Program</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <p class="mb-1"><strong>Student ID:</strong> STU2024001</p>
                                        <p class="mb-0"><strong>Generated:</strong> March 10, 2024</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Year 1 -->
                                <h6 class="text-primary border-bottom pb-2 mb-3">Academic Year 2022-2023</h6>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Fall 2022</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <tr>
                                                        <td>CS101 - Introduction to Programming</td>
                                                        <td>3</td>
                                                        <td><span class="badge bg-success">A</span></td>
                                                        <td>4.0</td>
                                                    </tr>
                                                    <tr>
                                                        <td>MATH201 - Calculus I</td>
                                                        <td>4</td>
                                                        <td><span class="badge bg-success">B+</span></td>
                                                        <td>3.3</td>
                                                    </tr>
                                                    <tr>
                                                        <td>ENG101 - English Composition</td>
                                                        <td>3</td>
                                                        <td><span class="badge bg-success">A-</span></td>
                                                        <td>3.7</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p class="small text-muted">Semester GPA: 3.67 | Credit Hours: 10</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Spring 2023</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <tr>
                                                        <td>CS102 - Object-Oriented Programming</td>
                                                        <td>3</td>
                                                        <td><span class="badge bg-success">A</span></td>
                                                        <td>4.0</td>
                                                    </tr>
                                                    <tr>
                                                        <td>MATH202 - Calculus II</td>
                                                        <td>4</td>
                                                        <td><span class="badge bg-success">B</span></td>
                                                        <td>3.0</td>
                                                    </tr>
                                                    <tr>
                                                        <td>PHYS101 - Physics I</td>
                                                        <td>4</td>
                                                        <td><span class="badge bg-success">B+</span></td>
                                                        <td>3.3</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p class="small text-muted">Semester GPA: 3.45 | Credit Hours: 11</p>
                                    </div>
                                </div>

                                <!-- Year 2 -->
                                <h6 class="text-primary border-bottom pb-2 mb-3">Academic Year 2023-2024</h6>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Fall 2023</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <tr>
                                                        <td>CS201 - Computer Architecture</td>
                                                        <td>3</td>
                                                        <td><span class="badge bg-success">A-</span></td>
                                                        <td>3.7</td>
                                                    </tr>
                                                    <tr>
                                                        <td>CS205 - Database Systems</td>
                                                        <td>3</td>
                                                        <td><span class="badge bg-success">B+</span></td>
                                                        <td>3.3</td>
                                                    </tr>
                                                    <tr>
                                                        <td>STAT301 - Statistics</td>
                                                        <td>3</td>
                                                        <td><span class="badge bg-success">A</span></td>
                                                        <td>4.0</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p class="small text-muted">Semester GPA: 3.67 | Credit Hours: 9</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Spring 2024 (Current)</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <tr>
                                                        <td>CS301 - Data Structures & Algorithms</td>
                                                        <td>3</td>
                                                        <td><span class="badge bg-secondary">In Progress</span></td>
                                                        <td>-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>CS350 - Software Engineering</td>
                                                        <td>3</td>
                                                        <td><span class="badge bg-secondary">In Progress</span></td>
                                                        <td>-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>CS410 - Web Development</td>
                                                        <td>3</td>
                                                        <td><span class="badge bg-secondary">In Progress</span></td>
                                                        <td>-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p class="small text-muted">Credit Hours: 9</p>
                                    </div>
                                </div>

                                <!-- Summary -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="alert alert-primary alert-custom">
                                            <h6><i class="fas fa-graduation-cap me-2"></i>Academic Summary</h6>
                                            <p class="mb-1"><strong>Total Credit Hours Completed:</strong> 30</p>
                                            <p class="mb-1"><strong>Credit Hours in Progress:</strong> 9</p>
                                            <p class="mb-0"><strong>Cumulative GPA:</strong> 3.70</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-success alert-custom">
                                            <h6><i class="fas fa-award me-2"></i>Academic Honors</h6>
                                            <p class="mb-1">• Dean's List - Fall 2022, Spring 2024</p>
                                            <p class="mb-0">• Academic Excellence Award - 2023</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Progress Section -->
                    <div id="progress" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Academic Progress Monitor</h2>
                            <span class="badge bg-success fs-6">On Track for Graduation</span>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Degree Progress</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="fw-bold">Overall Degree Completion</span>
                                                <span class="fw-bold">39/120 Credits (32.5%)</span>
                                            </div>
                                            <div class="progress progress-custom" style="height: 15px;">
                                                <div class="progress-bar bg-primary" style="width: 32.5%"></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary">Core Requirements</h6>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Computer Science Core</small>
                                                        <small>18/45 Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-success" style="width: 40%"></div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Mathematics</small>
                                                        <small>8/12 Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-warning" style="width: 67%"></div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Science Requirements</small>
                                                        <small>4/8 Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-info" style="width: 50%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-primary">General Education</h6>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>English & Communication</small>
                                                        <small>6/9 Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-success" style="width: 67%"></div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Liberal Arts</small>
                                                        <small>3/15 Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-danger" style="width: 20%"></div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Electives</small>
                                                        <small>0/31 Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-secondary" style="width: 0%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-target me-2"></i>Graduation Timeline</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h3 class="text-primary">Spring 2026</h3>
                                            <p class="text-muted">Expected Graduation</p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">Semesters Remaining</small>
                                            <h4 class="text-success">4</h4>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">Credits Needed</small>
                                            <h4 class="text-warning">81</h4>
                                        </div>
                                        <div class="alert alert-info alert-custom">
                                            <small><i class="fas fa-lightbulb me-1"></i>
                                            Maintain 15+ credits per semester to graduate on time.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card dashboard-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Action Items & Recommendations</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-danger">Urgent Actions</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-circle text-danger me-2" style="font-size: 0.5rem;"></i>
                                                Register for Liberal Arts courses next semester
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-circle text-warning me-2" style="font-size: 0.5rem;"></i>
                                                Complete remaining Math requirements
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-circle text-info me-2" style="font-size: 0.5rem;"></i>
                                                Schedule advisor meeting for course planning
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success">Recommendations</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Consider summer courses to lighten future load
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Explore internship opportunities
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Join computer science student organizations
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Selection Section -->
                    <div id="course-selection" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Course Selection</h2>
                            <div class="d-flex gap-2">
                                <span class="badge bg-info fs-6">Registration Opens: April 1, 2024</span>
                                <button class="btn btn-success btn-custom">Submit Registration</button>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Available Courses - Fall 2024</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <select class="form-select">
                                                    <option>All Departments</option>
                                                    <option>Computer Science</option>
                                                    <option>Mathematics</option>
                                                    <option>Liberal Arts</option>
                                                    <option>Science</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-select">
                                                    <option>All Levels</option>
                                                    <option>100-200 (Introductory)</option>
                                                    <option>300-400 (Intermediate)</option>
                                                    <option>500+ (Advanced)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" placeholder="Search courses...">
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Select</th>
                                                        <th>Course</th>
                                                        <th>Title</th>
                                                        <th>Credits</th>
                                                        <th>Schedule</th>
                                                        <th>Instructor</th>
                                                        <th>Seats</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="cs401">
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">CS401</td>
                                                        <td>Advanced Algorithms</td>
                                                        <td>3</td>
                                                        <td>MWF 10:00-11:00</td>
                                                        <td>Dr. Johnson</td>
                                                        <td><span class="badge bg-success">15/30</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="cs420">
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">CS420</td>
                                                        <td>Machine Learning</td>
                                                        <td>3</td>
                                                        <td>TTh 2:00-3:30</td>
                                                        <td>Prof. Chen</td>
                                                        <td><span class="badge bg-warning">25/25</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="cs450" checked>
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">CS450</td>
                                                        <td>Computer Networks</td>
                                                        <td>3</td>
                                                        <td>MW 1:00-2:30</td>
                                                        <td>Dr. Rodriguez</td>
                                                        <td><span class="badge bg-success">12/25</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="math301">
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">MATH301</td>
                                                        <td>Discrete Mathematics</td>
                                                        <td>3</td>
                                                        <td>MWF 9:00-10:00</td>
                                                        <td>Prof. Williams</td>
                                                        <td><span class="badge bg-success">18/30</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="eng201" checked>
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">ENG201</td>
                                                        <td>Technical Writing</td>
                                                        <td>3</td>
                                                        <td>TTh 11:00-12:30</td>
                                                        <td>Ms. Davis</td>
                                                        <td><span class="badge bg-success">20/25</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Selected Courses</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                                <div>
                                                    <strong>CS450</strong>
                                                    <br>
                                                    <small>Computer Networks</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-primary">3 Credits</span>
                                                    <br>
                                                    <button class="btn btn-sm btn-outline-danger mt-1">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                                <div>
                                                    <strong>ENG201</strong>
                                                    <br>
                                                    <small>Technical Writing</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-primary">3 Credits</span>
                                                    <br>
                                                    <button class="btn btn-sm btn-outline-danger mt-1">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Credits:</strong>
                                            <strong>6</strong>
                                        </div>
                                        <div class="alert alert-info alert-custom mt-3">
                                            <small><i class="fas fa-info-circle me-1"></i>
                                            Minimum 12 credits required for full-time status.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Schedule Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Time</th>
                                                        <th>Mon</th>
                                                        <th>Wed</th>
                                                        <th>Tue</th>
                                                        <th>Thu</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-bold">11:00</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="bg-primary text-white text-center small">ENG201</td>
                                                        <td class="bg-primary text-white text-center small">ENG201</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">1:00</td>
                                                        <td class="bg-success text-white text-center small">CS450</td>
                                                        <td class="bg-success text-white text-center small">CS450</td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/student_dashboard.js"></script>
</body>
</html>