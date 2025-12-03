<?php
session_start();
// Prevent caching so users can't view dashboard via browser back after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
require_once '../config/db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    header('Location: ../authentications/lecturer_login.html');
    exit();
}

$lecturer_id = (int)($_SESSION['lecturer_id'] ?? 0);
$lecturer_name = $_SESSION['lecturer_name'] ?? '';

// Get dashboard statistics
try {
    // Total courses assigned to this lecturer
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM coursetbl WHERE lecturer_id = ?");
    $stmt->execute([$lecturer_id]);
    $total_courses = $stmt->fetch()['count'];
    
    // Total unique students enrolled in ALL of lecturer's courses
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT cr.student_id) as count FROM course_regtbl cr 
                          JOIN coursetbl c ON cr.course_id = c.course_id 
                          WHERE c.lecturer_id = ? AND cr.approval_status = 'Approved'");
    $stmt->execute([$lecturer_id]);
    $total_students = $stmt->fetch()['count'];
    
    // Pending grades (results without grades for lecturer's courses)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM resulttbl r 
                          JOIN coursetbl c ON r.course_id = c.course_id 
                          WHERE c.lecturer_id = ? AND (r.grade IS NULL OR r.grade = '')");
    $stmt->execute([$lecturer_id]);
    $pending_grades = $stmt->fetch()['count'];
    
    // Assignments due (adjust based on your assignment table structure)
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM assignmenttbl a 
                              JOIN coursetbl c ON a.course_id = c.course_id 
                              WHERE c.lecturer_id = ? AND a.due_date >= CURDATE()");
        $stmt->execute([$lecturer_id]);
        $assignments_due = $stmt->fetch()['count'];
    } catch (PDOException $e2) {
        $assignments_due = 0; // Table might not exist
    }
    
} catch (PDOException $e) {
    // Set default values if queries fail
    $total_courses = 0;
    $total_students = 0;
    $pending_grades = 0;
    $assignments_due = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/lecturer_dashboard.css" rel="stylesheet">
</head>
<body>    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                Lecturer Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($lecturer_name); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="lecturer_profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <!-- <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li> -->
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="lecturer_logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#dashboard" data-section="dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#courses" data-section="courses">
                                <i class="fas fa-book me-2"></i>
                                My Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#grading" data-section="grading">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Student Grading
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#assignments" data-section="assignments">
                                <i class="fas fa-tasks me-2"></i>
                                Assignments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#materials" data-section="materials">
                                <i class="fas fa-file-upload me-2"></i>
                                Course Materials
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#records" data-section="records">
                                <i class="fas fa-chart-line me-2"></i>
                                Academic Records
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Dashboard Section -->
                <div id="dashboard" class="content-section">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Dashboard Overview</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-calendar me-1"></i>
                                    This Semester
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Courses
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <a href="#courses" class="text-decoration-none">
                                                    <?php echo (int)$total_courses . ' ' . ((int)$total_courses === 1 ? 'Course' : 'Courses'); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Students
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_students; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Pending Grades
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_grades; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Assignments Due
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $assignments_due; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                                </div>
                                <div class="card-body">
                                    <div id="recentActivitiesContainer">
                                        <div class="text-center py-4">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2">Loading recent activities...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
                                            <i class="fas fa-plus me-2"></i>Create Assignment
                                        </button>
                                        <button class="btn btn-success" type="button">
                                            <i class="fas fa-upload me-2"></i>Upload Materials
                                        </button>
                                        <button class="btn btn-info" type="button">
                                            <i class="fas fa-chart-bar me-2"></i>View Reports
                                        </button>
                                        <button class="btn btn-warning" type="button">
                                            <i class="fas fa-edit me-2"></i>Grade Students
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses Section -->
                <div id="courses" class="content-section" style="display: none;">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">My Courses</h1>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Course
                        </button>
                    </div>

                    <?php
                    // Fetch courses assigned to this lecturer
                    try {
                        $stmt = $pdo->prepare("SELECT 
                            c.course_id,
                            c.course_code,
                            c.course_title,
                            c.course_description,
                            c.course_unit,
                            c.department,
                            c.level,
                            c.semester,
                            COUNT(DISTINCT cr.student_id) as enrolled_students
                        FROM coursetbl c
                        LEFT JOIN course_regtbl cr ON c.course_id = cr.course_id AND cr.approval_status = 'Approved'
                        WHERE c.lecturer_id = ?
                        GROUP BY c.course_id
                        ORDER BY c.course_code");
                        $stmt->execute([$lecturer_id]);
                        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $courses = [];
                    }
                    ?>

                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Assigned Courses</h6>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="courseSearch" placeholder="Search courses...">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($courses)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No courses assigned yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="coursesTable">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>S/N</th>
                                                <th>Course Code</th>
                                                <th>Course Title</th>
                                                <th>Department</th>
                                                <th>Level</th>
                                                <th>Units</th>
                                                <th>Semester</th>
                                                <th>Enrolled Students</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($courses as $index => $course): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($course['course_code']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($course['course_title']); ?></td>
                                                <td><?php echo htmlspecialchars($course['department']); ?></td>
                                                <td><?php echo htmlspecialchars($course['level']); ?></td>
                                                <td><?php echo htmlspecialchars($course['course_unit']); ?></td>
                                                <td><?php echo htmlspecialchars($course['semester'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $course['enrolled_students']; ?> students</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary me-1 view-details-btn" 
                                                            title="View Details" 
                                                            data-course-id="<?php echo $course['course_id']; ?>"
                                                            data-course-code="<?php echo htmlspecialchars($course['course_code']); ?>"
                                                            data-course-title="<?php echo htmlspecialchars($course['course_title']); ?>"
                                                            data-course-description="<?php echo htmlspecialchars($course['course_description']); ?>"
                                                            data-course-unit="<?php echo htmlspecialchars($course['course_unit']); ?>"
                                                            data-department="<?php echo htmlspecialchars($course['department']); ?>"
                                                            data-level="<?php echo htmlspecialchars($course['level']); ?>"
                                                            data-semester="<?php echo htmlspecialchars($course['semester'] ?? 'N/A'); ?>"
                                                            data-enrolled-students="<?php echo $course['enrolled_students']; ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#courseDetailsModal">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success me-1 manage-course-btn" 
                                                            title="Manage Course"
                                                            data-course-id="<?php echo $course['course_id']; ?>"
                                                            data-course-code="<?php echo htmlspecialchars($course['course_code']); ?>"
                                                            data-course-title="<?php echo htmlspecialchars($course['course_title']); ?>"
                                                            data-course-description="<?php echo htmlspecialchars($course['course_description']); ?>"
                                                            data-course-unit="<?php echo htmlspecialchars($course['course_unit']); ?>"
                                                            data-department="<?php echo htmlspecialchars($course['department']); ?>"
                                                            data-level="<?php echo htmlspecialchars($course['level']); ?>"
                                                            data-semester="<?php echo htmlspecialchars($course['semester'] ?? ''); ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#manageCourseModal">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info view-students-btn" 
                                                            title="View Students"
                                                            data-course-id="<?php echo $course['course_id']; ?>"
                                                            data-course-code="<?php echo htmlspecialchars($course['course_code']); ?>"
                                                            data-course-title="<?php echo htmlspecialchars($course['course_title']); ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#viewStudentsModal">
                                                        <i class="fas fa-users"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Grading Section -->
                <div id="grading" class="content-section" style="display: none;">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Student Grading</h1>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary course-filter-btn active" data-course-id="all">All Courses</button>
                            <?php foreach ($courses as $course): ?>
                                <button type="button" class="btn btn-outline-secondary course-filter-btn" data-course-id="<?php echo $course['course_id']; ?>">
                                    <?php echo htmlspecialchars($course['course_code']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php
                    // Fetch students and their grades for lecturer's courses
                    try {
                        $stmt = $pdo->prepare("
                            SELECT DISTINCT
                                s.student_id,
                                s.Matric_No as matric_number,
                                s.first_name,
                                s.last_name,
                                c.course_id,
                                c.course_code,
                                c.course_title,
                                c.course_unit,
                                e.ca_score,
                                e.test_score,
                                e.exam_score,
                                e.total_score,
                                e.grade,
                                e.grade_point,
                                e.quality_points,
                                e.eval_id
                            FROM studenttbl s
                            JOIN course_regtbl cr ON s.student_id = cr.student_id
                            JOIN coursetbl c ON cr.course_id = c.course_id
                            LEFT JOIN evaluationtbl e ON s.student_id = e.student_id AND c.course_id = e.course_id
                            WHERE c.lecturer_id = ? AND cr.approval_status = 'Approved'
                            ORDER BY c.course_code, s.Matric_No
                        ");
                        $stmt->execute([$lecturer_id]);
                        $student_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        // Group by course
                        $courses_with_students = [];
                        foreach ($student_grades as $record) {
                            $course_id = $record['course_id'];
                            if (!isset($courses_with_students[$course_id])) {
                                $courses_with_students[$course_id] = [
                                    'course_code' => $record['course_code'],
                                    'course_title' => $record['course_title'],
                                    'students' => []
                                ];
                            }
                            $courses_with_students[$course_id]['students'][] = $record;
                        }
                    } catch (PDOException $e) {
                        $courses_with_students = [];
                        $student_grades = [];
                    }
                    
                    // Function to calculate grade letter based on total score (Excel formula)
                    function calculateGradeLetter($total_score) {
                        if ($total_score < 40) return 'F';
                        if ($total_score < 45) return 'E';
                        if ($total_score < 50) return 'D';
                        if ($total_score < 60) return 'C';
                        if ($total_score < 70) return 'B';
                        return 'A';
                    }
                    
                    // Function to calculate grade point based on total score (Excel formula)
                    function calculateGradePoint($total_score) {
                        if ($total_score < 40) return 0;
                        if ($total_score < 45) return 1;
                        if ($total_score < 50) return 2;
                        if ($total_score < 60) return 3;
                        if ($total_score < 70) return 4;
                        return 5;
                    }
                    
                    // Function to get grade display info
                    function getGradeDisplayInfo($grade_letter) {
                        switch ($grade_letter) {
                            case 'A': return ['class' => 'bg-success'];
                            case 'B': return ['class' => 'bg-warning'];
                            case 'C': return ['class' => 'bg-info'];
                            case 'D': return ['class' => 'bg-secondary'];
                            case 'E': return ['class' => 'bg-danger'];
                            case 'F': return ['class' => 'bg-danger'];
                            default: return ['class' => 'bg-secondary'];
                        }
                    }
                    ?>

                    <?php if (empty($courses_with_students)): ?>
                        <div class="card shadow">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Students to Grade</h5>
                                <p class="text-muted">No students are enrolled in your courses yet.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($courses_with_students as $course_id => $course_data): ?>
                            <div class="card shadow mb-4 course-grading-card" data-course-id="<?php echo $course_id; ?>">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($course_data['course_code'] . ' - ' . $course_data['course_title']); ?></h5>
                                    <span class="badge bg-primary"><?php echo count($course_data['students']); ?> Students</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Matric Number</th>
                                                    <th>Student Name</th>
                                                    <th>CA Score (30)</th>
                                                    <th>Test Score (20)</th>
                                                    <th>Exam Score (50)</th>
                                                    <th>Total Score</th>
                                                    <th>Grade</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($course_data['students'] as $student): ?>
                                                    <?php
                                                    $ca_score = $student['ca_score'] ?? 0;
                                                    $test_score = $student['test_score'] ?? 0;
                                                    $exam_score = $student['exam_score'] ?? 0;
                                                    $total_score = $student['total_score'] ?? 0;
                                                    $grade_letter = $student['grade'] ?? calculateGradeLetter($total_score);
                                                    $grade_info = getGradeDisplayInfo($grade_letter);
                                                    ?>
                                                    <tr data-student-id="<?php echo $student['student_id']; ?>" data-course-id="<?php echo $course_id; ?>">
                                                        <td><strong><?php echo htmlspecialchars($student['matric_number']); ?></strong></td>
                                                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm score-input" 
                                                                   data-score-type="ca" 
                                                                   value="<?php echo $ca_score; ?>" 
                                                                   min="0" max="30">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm score-input" 
                                                                   data-score-type="test" 
                                                                   value="<?php echo $test_score; ?>" 
                                                                   min="0" max="20">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm score-input" 
                                                                   data-score-type="exam" 
                                                                   value="<?php echo $exam_score; ?>" 
                                                                   min="0" max="50">
                                                        </td>
                                                        <td><strong class="total-score"><?php echo number_format($total_score, 1); ?></strong></td>
                                                        <td><span class="badge grade-badge <?php echo $grade_info['class']; ?>"><?php echo $grade_letter; ?></span></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary save-grade-btn" 
                                                                    data-student-id="<?php echo $student['student_id']; ?>" 
                                                                    data-course-id="<?php echo $course_id; ?>" 
                                                                    data-eval-id="<?php echo $student['eval_id'] ?? ''; ?>" 
                                                                    data-credit-units="<?php echo $student['course_unit']; ?>">
                                                                <i class="fas fa-save"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-success me-2 save-all-grades-btn" data-course-id="<?php echo $course_id; ?>">
                                            <i class="fas fa-save me-2"></i>Save All Grades
                                        </button>
                                        <button class="btn btn-info me-2 export-grades-btn" data-course-id="<?php echo $course_id; ?>">
                                            <i class="fas fa-download me-2"></i>Export Grades
                                        </button>
                                        <button class="btn btn-warning submit-grades-btn" data-course-id="<?php echo $course_id; ?>">
                                            <i class="fas fa-paper-plane me-2"></i>Submit to Records
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Assignments Section -->
                <div id="assignments" class="content-section" style="display: none;">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Assignments</h1>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
                            <i class="fas fa-plus me-2"></i>Create Assignment
                        </button>
                    </div>

                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">My Assignments</h6>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="assignmentSearch" placeholder="Search assignments...">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                            // Fetch assignments for this lecturer
                            try {
                                $stmt = $pdo->prepare("SELECT 
                                    a.assignment_id,
                                    a.title,
                                    a.description,
                                    a.max_score,
                                    a.due_date,
                                    a.academic_year,
                                    a.semester,
                                    a.is_active,
                                    a.created_at,
                                    c.course_code,
                                    c.course_title
                                FROM assignmenttbl a
                                JOIN coursetbl c ON a.course_id = c.course_id
                                WHERE a.lecturer_id = ?
                                ORDER BY a.due_date DESC");
                                
                                $stmt->execute([$lecturer_id]);
                                $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                $assignments = [];
                                // Debug: uncomment next line to see error
                                // echo "Error: " . $e->getMessage();
                            }
                            ?>
                            
                            <div id="assignmentsContainer">
                                <?php if (empty($assignments)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No assignments created yet.</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
                                            <i class="fas fa-plus me-2"></i>Create Your First Assignment
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($assignments as $assignment): ?>
                                            <?php
                                            $dueDate = new DateTime($assignment['due_date']);
                                            $now = new DateTime();
                                            $isOverdue = $dueDate < $now;
                                            ?>
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="card h-100 <?php echo $isOverdue ? 'border-danger' : 'border-primary'; ?>">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($assignment['course_code']); ?></h6>
                                                        <span class="badge <?php echo $assignment['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                            <?php echo $assignment['is_active'] ? 'Active' : 'Inactive'; ?>
                                                        </span>
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($assignment['title']); ?></h5>
                                                        <p class="card-text text-muted small">
                                                            <?php echo htmlspecialchars(substr($assignment['description'], 0, 100)) . (strlen($assignment['description']) > 100 ? '...' : ''); ?>
                                                        </p>
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                Due: <?php echo $dueDate->format('M j, Y g:i A'); ?>
                                                                <?php if ($isOverdue): ?>
                                                                    <span class="text-danger ms-1">(Overdue)</span>
                                                                <?php endif; ?>
                                                            </small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                <i class="fas fa-star me-1"></i>
                                                                Max Score: <?php echo $assignment['max_score']; ?> points
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="btn-group w-100" role="group">
                                                            <button class="btn btn-sm btn-outline-primary view-assignment-btn" title="View Details"
                                                                    data-bs-toggle="modal" data-bs-target="#assignmentDetailsModal"
                                                                    data-assignment-id="<?php echo $assignment['assignment_id']; ?>"
                                                                    data-title="<?php echo htmlspecialchars($assignment['title']); ?>"
                                                                    data-description="<?php echo htmlspecialchars($assignment['description']); ?>"
                                                                    data-course-code="<?php echo htmlspecialchars($assignment['course_code']); ?>"
                                                                    data-course-title="<?php echo htmlspecialchars($assignment['course_title']); ?>"
                                                                    data-max-score="<?php echo $assignment['max_score']; ?>"
                                                                    data-due-date="<?php echo $assignment['due_date']; ?>"
                                                                    data-academic-year="<?php echo htmlspecialchars($assignment['academic_year']); ?>"
                                                                    data-semester="<?php echo htmlspecialchars($assignment['semester']); ?>"
                                                                    data-is-active="<?php echo $assignment['is_active']; ?>"
                                                                    data-created-at="<?php echo $assignment['created_at']; ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-success view-submissions-btn" title="View Submissions"
                                                                    data-assignment-id="<?php echo $assignment['assignment_id']; ?>"
                                                                    data-title="<?php echo htmlspecialchars($assignment['title']); ?>"
                                                                    data-course="<?php echo htmlspecialchars($assignment['course_code']); ?>"
                                                                    data-bs-toggle="modal" data-bs-target="#viewSubmissionsModal">
                                                                <i class="fas fa-file-alt"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-warning edit-assignment-btn" title="Edit"
                                                                    data-bs-toggle="modal" data-bs-target="#editAssignmentModal"
                                                                    data-assignment-id="<?php echo $assignment['assignment_id']; ?>"
                                                                    data-title="<?php echo htmlspecialchars($assignment['title']); ?>"
                                                                    data-description="<?php echo htmlspecialchars($assignment['description']); ?>"
                                                                    data-max-score="<?php echo $assignment['max_score']; ?>"
                                                                    data-due-date="<?php echo date('Y-m-d\TH:i', strtotime($assignment['due_date'])); ?>"
                                                                    data-academic-year="<?php echo htmlspecialchars($assignment['academic_year']); ?>"
                                                                    data-semester="<?php echo htmlspecialchars($assignment['semester']); ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger delete-assignment-btn" title="Delete"
                                                                    data-assignment-id="<?php echo $assignment['assignment_id']; ?>"
                                                                    data-title="<?php echo htmlspecialchars($assignment['title']); ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Materials Section -->
                <div id="materials" class="content-section" style="display: none;">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Course Materials</h1>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMaterialModal">
                            <i class="fas fa-upload me-2"></i>Upload Material
                        </button>
                    </div>

                    <div id="materialsContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading materials...</p>
                        </div>
                    </div>
                </div>

                <!-- Records Section -->
                <div id="records" class="content-section" style="display: none;">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Academic Records</h1>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary active" id="currentSemesterBtn">Current Semester</button>
                            <button type="button" class="btn btn-outline-secondary" id="previousSemesterBtn">Previous Semester</button>
                            <button type="button" class="btn btn-outline-secondary" id="allTimeBtn">All Time</button>
                        </div>
                    </div>

                    <?php
                    // Fetch grade distribution data
                    try {
                        $stmt = $pdo->prepare("
                            SELECT 
                                e.grade,
                                COUNT(*) as count,
                                AVG(e.total_score) as avg_score
                            FROM evaluationtbl e
                            JOIN coursetbl c ON e.course_id = c.course_id
                            WHERE c.lecturer_id = ? AND e.grade IS NOT NULL
                            GROUP BY e.grade
                            ORDER BY e.grade
                        ");
                        $stmt->execute([$lecturer_id]);
                        $grade_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        $total_graded = array_sum(array_column($grade_distribution, 'count'));
                        $grade_percentages = [];
                        foreach ($grade_distribution as $grade) {
                            $grade_percentages[$grade['grade']] = $total_graded > 0 ? round(($grade['count'] / $total_graded) * 100, 1) : 0;
                        }
                    } catch (PDOException $e) {
                        $grade_distribution = [];
                        $grade_percentages = [];
                    }
                    ?>

                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card shadow">
                                <div class="card-header">
                                    <h5 class="mb-0">Student Performance Overview</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="performanceChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow">
                                <div class="card-header">
                                    <h5 class="mb-0">Grade Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    $grades = ['A' => 'success', 'B' => 'warning', 'C' => 'info', 'D' => 'secondary', 'E' => 'danger', 'F' => 'danger'];
                                    foreach ($grades as $grade => $color): 
                                        $percentage = $grade_percentages[$grade] ?? 0;
                                    ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span><?php echo $grade; ?> Grade</span>
                                            <span><?php echo $percentage; ?>%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-<?php echo $color; ?>" style="width: <?php echo $percentage; ?>%"></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header">
                            <h5 class="mb-0">Student Academic Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Matric Number</th>
                                            <th>Student Name</th>
                                            <th>Course</th>
                                            <th>Current Grade</th>
                                            <th>Total Score</th>
                                            <th>Progress</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($student_grades)): ?>
                                        <?php foreach ($student_grades as $record): 
                                            if ($record['grade']):
                                                $grade_info = getGradeDisplayInfo($record['grade']);
                                                $progress = min(100, ($record['total_score'] / 100) * 100);
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($record['matric_number']); ?></td>
                                            <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($record['course_code']); ?></td>
                                            <td><span class="badge <?php echo $grade_info['class']; ?>"><?php echo $record['grade']; ?></span></td>
                                            <td><?php echo number_format($record['total_score'], 1); ?>%</td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar <?php echo str_replace('bg-', 'bg-', $grade_info['class']); ?>" style="width: <?php echo $progress; ?>%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary view-student-details" 
                                                        data-student-id="<?php echo $record['student_id']; ?>"
                                                        data-course-id="<?php echo $record['course_id']; ?>">View Details</button>
                                            </td>
                                        </tr>
                                        <?php endif; endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">No student records available</p>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Create Assignment Modal -->
    <div class="modal fade" id="createAssignmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createAssignmentForm">
                        <div class="mb-3">
                            <label for="assignmentTitle" class="form-label">Assignment Title</label>
                            <input type="text" class="form-control" id="assignmentTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="assignmentCourse" class="form-label">Course</label>
                            <select class="form-select" id="assignmentCourse" name="course_id" required>
                                <option value="">Select Course</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['course_id']; ?>">
                                        <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="assignmentDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="assignmentDescription" name="description" rows="4" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="assignmentDueDate" class="form-label">Due Date</label>
                                <input type="datetime-local" class="form-control" id="assignmentDueDate" name="due_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="assignmentPoints" class="form-label">Maximum Score</label>
                                <input type="number" class="form-control" id="assignmentPoints" name="max_score" min="1" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="academicYear" class="form-label">Academic Year</label>
                                <input type="text" class="form-control" id="academicYear" name="academic_year" placeholder="e.g., 2023/2024" required>
                            </div>
                            <div class="col-md-6">
                                <label for="assignmentSemester" class="form-label">Semester</label>
                                <select class="form-select" id="assignmentSemester" name="semester" required>
                                    <option value="">Select Semester</option>
                                    <option value="First">First Semester</option>
                                    <option value="Second">Second Semester</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="createAssignmentBtn">Create Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Material Modal -->
    <div class="modal fade" id="uploadMaterialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Course Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadMaterialForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="materialCourse" class="form-label">Course</label>
                            <select class="form-select" id="materialCourse" name="course_id" required>
                                <option value="">Select Course</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['course_id']; ?>">
                                        <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="materialTitle" class="form-label">Material Title</label>
                            <input type="text" class="form-control" id="materialTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="materialType" class="form-label">Material Type</label>
                            <select class="form-select" id="materialType" name="file_type" required>
                                <option value="">Select Type</option>
                                <option value="lecture">Lecture Notes</option>
                                <option value="slides">Slides</option>
                                <option value="reading">Reading Material</option>
                                <option value="video">Video</option>
                                <option value="code">Code Examples</option>
                                <option value="assignment">Assignment</option>
                                <option value="reference">Reference Material</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="materialFile" class="form-label">File</label>
                            <input type="file" class="form-control" id="materialFile" name="material_file" required>
                            <div class="form-text">Maximum file size: 50MB</div>
                        </div>
                        <div class="mb-3">
                            <label for="materialDescription" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="materialDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isPublished" name="is_published" checked>
                            <label class="form-check-label" for="isPublished">
                                Publish immediately (students can access)
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="uploadMaterialBtn">Upload Material</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Details Modal -->
    <div class="modal fade" id="courseDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseDetailsTitle">Course Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Course Code:</label>
                                <p id="modalCourseCode" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Course Title:</label>
                                <p id="modalCourseTitle" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Department:</label>
                                <p id="modalDepartment" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Level:</label>
                                <p id="modalLevel" class="mb-0"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Units:</label>
                                <p id="modalUnits" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Semester:</label>
                                <p id="modalSemester" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Enrolled Students:</label>
                                <p id="modalEnrolledStudents" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Course Description:</label>
                        <p id="modalCourseDescription" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="editCourseBtn">Edit Course</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Course Modal -->
    <div class="modal fade" id="manageCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageCourseTitle">Course Management</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-tasks fa-3x text-primary mb-3"></i>
                                    <h5>Create Assignment</h5>
                                    <p class="text-muted">Create new assignments for this course</p>
                                    <button class="btn btn-primary" id="createAssignmentForCourse">Create Assignment</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-upload fa-3x text-success mb-3"></i>
                                    <h5>Upload Materials</h5>
                                    <p class="text-muted">Upload course materials and resources</p>
                                    <button class="btn btn-success" id="uploadMaterialForCourse">Upload Materials</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                                    <h5>View Analytics</h5>
                                    <p class="text-muted">View course performance and statistics</p>
                                    <button class="btn btn-info" id="viewCourseAnalytics">View Analytics</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-check fa-3x text-warning mb-3"></i>
                                    <h5>Grade Students</h5>
                                    <p class="text-muted">Grade assignments and exams</p>
                                    <button class="btn btn-warning" id="gradeStudentsForCourse">Grade Students</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Students Modal -->
    <div class="modal fade" id="viewStudentsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentsTitle">Enrolled Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="studentSearch" placeholder="Search students...">
                    </div>
                    <div id="studentsTableContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading students...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="exportStudentsBtn">Export List</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Details Modal -->
    <div class="modal fade" id="assignmentDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignmentDetailsTitle">Assignment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Assignment Title:</label>
                                <p id="modalAssignmentTitle" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Course:</label>
                                <p id="modalAssignmentCourse" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Maximum Score:</label>
                                <p id="modalAssignmentMaxScore" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Due Date:</label>
                                <p id="modalAssignmentDueDate" class="mb-0"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Academic Year:</label>
                                <p id="modalAssignmentYear" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Semester:</label>
                                <p id="modalAssignmentSemester" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <p id="modalAssignmentStatus" class="mb-0"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created:</label>
                                <p id="modalAssignmentCreated" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description:</label>
                        <p id="modalAssignmentDescription" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" id="editAssignmentBtn">Edit Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Assignment Modal -->
    <div class="modal fade" id="editAssignmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editAssignmentForm">
                        <input type="hidden" id="editAssignmentId" name="assignment_id">
                        <div class="mb-3">
                            <label for="editAssignmentTitle" class="form-label">Assignment Title</label>
                            <input type="text" class="form-control" id="editAssignmentTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAssignmentDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editAssignmentDescription" name="description" rows="4" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="editAssignmentDueDate" class="form-label">Due Date</label>
                                <input type="datetime-local" class="form-control" id="editAssignmentDueDate" name="due_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editAssignmentPoints" class="form-label">Maximum Score</label>
                                <input type="number" class="form-control" id="editAssignmentPoints" name="max_score" min="1" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="editAcademicYear" class="form-label">Academic Year</label>
                                <input type="text" class="form-control" id="editAcademicYear" name="academic_year" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editAssignmentSemester" class="form-label">Semester</label>
                                <select class="form-select" id="editAssignmentSemester" name="semester" required>
                                    <option value="First">First Semester</option>
                                    <option value="Second">Second Semester</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="updateAssignmentBtn">Update Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Submissions Modal -->
    <div class="modal fade" id="viewSubmissionsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewSubmissionsTitle">Assignment Submissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Assignment:</strong> <span id="submissionAssignmentTitle"></span></p>
                                <p><strong>Course:</strong> <span id="submissionCourse"></span></p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p><strong>Total Submissions:</strong> <span id="totalSubmissions" class="badge bg-primary">0</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="submissionSearch" placeholder="Search submissions by student name or matric number...">
                    </div>
                    <div id="submissionsTableContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading submissions...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="exportSubmissionsBtn">Export List</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/lecturer_dashboard.js"></script>
    <script>
    // Client-side session verification: calls server endpoint and redirects if session invalid
    (function(){
        fetch('../PHP/check_session.php', { credentials: 'include' })
            .then(function(res){ return res.json(); })
            .then(function(data){
                if (!data || !data.ok) {
                    var role = data && data.user_role ? data.user_role : '';
                    if (role === 'admin') window.location.href = '../authentications/admin_login.html';
                    else if (role === 'lecturer') window.location.href = '../authentications/lecturer_login.html';
                    else window.location.href = '../authentications/student_login.html';
                }
            }).catch(function(){
                // On network error, force redirect to login
                window.location.href = '../authentications/lecturer_login.html';
            });
    })();
    </script>
    <script>
        // Initialize performance chart
        let performanceChart;
        function initPerformanceChart() {
            const ctx = document.getElementById('performanceChart');
            if (ctx) {
                const gradeData = <?php echo json_encode($grade_distribution); ?>;
                const labels = gradeData.map(item => item.grade + ' Grade');
                const counts = gradeData.map(item => parseInt(item.count));
                const avgScores = gradeData.map(item => parseFloat(item.avg_score));
                
                performanceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Students',
                            data: counts,
                            backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#6c757d', '#dc3545', '#dc3545'],
                            yAxisID: 'y'
                        }, {
                            label: 'Average Score',
                            data: avgScores,
                            type: 'line',
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { type: 'linear', display: true, position: 'left' },
                            y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } }
                        }
                    }
                });
            }
        }
        
        // Initialize chart when records section is shown
        document.addEventListener('click', function(e) {
            if (e.target.dataset.section === 'records' && !performanceChart) {
                setTimeout(initPerformanceChart, 100);
            }
        });

        // Export grades functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.export-grades-btn')) {
                const courseId = e.target.closest('.export-grades-btn').dataset.courseId;
                const rows = document.querySelectorAll(`[data-course-id="${courseId}"] tbody tr`);
                
                let csvContent = "Matric Number,Student Name,CA Score,Test Score,Exam Score,Total Score,Grade\n";
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const matricNumber = cells[0].textContent.trim();
                    const studentName = cells[1].textContent.trim();
                    const caScore = cells[2].querySelector('input').value || '0';
                    const testScore = cells[3].querySelector('input').value || '0';
                    const examScore = cells[4].querySelector('input').value || '0';
                    const totalScore = cells[5].textContent.trim();
                    const grade = cells[6].textContent.trim();
                    
                    csvContent += `"${matricNumber}","${studentName}",${caScore},${testScore},${examScore},${totalScore},"${grade}"\n`;
                });
                
                const blob = new Blob([csvContent], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `grades_course_${courseId}.csv`;
                a.click();
                window.URL.revokeObjectURL(url);
            }
            
            // Submit grades functionality
            if (e.target.closest('.submit-grades-btn')) {
                const courseId = e.target.closest('.submit-grades-btn').dataset.courseId;
                
                if (confirm('Are you sure you want to submit these grades to the records office? This action cannot be undone.')) {
                    fetch('../handlers/submit_grades.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ course_id: courseId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Grades submitted successfully!');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(() => alert('Network error occurred'));
                }
            }
            
            // Semester filter buttons
            if (e.target.id === 'currentSemesterBtn' || e.target.id === 'previousSemesterBtn' || e.target.id === 'allTimeBtn') {
                document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
                e.target.classList.add('active');
            }
        });
    </script>
</body>
</html>
