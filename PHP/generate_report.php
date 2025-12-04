<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$report_type = $_POST['report_type'] ?? '';
$report_category = $_POST['report_category'] ?? '';
$student_id = $_POST['student_id'] ?? '';
$course_id = $_POST['course_id'] ?? '';
$department = $_POST['department'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

try {
    switch ($report_category) {
        case 'student':
            $data = generateStudentReport($pdo, $report_type, $student_id);
            break;
        case 'course':
            $data = generateCourseReport($pdo, $report_type, $course_id);
            break;
        case 'department':
            $data = generateDepartmentReport($pdo, $report_type, $department);
            break;
        case 'system':
            $data = generateSystemReport($pdo, $report_type, $start_date, $end_date);
            break;
        default:
            throw new Exception('Invalid report category');
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function generateStudentReport($pdo, $type, $student_id) {
    switch ($type) {
        case 'transcript':
            $stmt = $pdo->prepare("
                SELECT s.Matric_No, s.first_name, s.last_name, s.Department, s.Level,
                       c.course_code, c.course_title, c.course_unit,
                       e.ca_score, e.test_score, e.exam_score, e.total_score, e.grade, e.grade_point
                FROM studenttbl s
                LEFT JOIN evaluationtbl e ON s.student_id = e.student_id
                LEFT JOIN coursetbl c ON e.course_id = c.course_id
                WHERE s.student_id = ?
                ORDER BY c.course_code
            ");
            $stmt->execute([$student_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        case 'grade_report':
            $stmt = $pdo->prepare("
                SELECT c.course_code, c.course_title, e.total_score, e.grade, e.grade_point
                FROM evaluationtbl e
                JOIN coursetbl c ON e.course_id = c.course_id
                WHERE e.student_id = ? AND e.grade IS NOT NULL
                ORDER BY c.course_code
            ");
            $stmt->execute([$student_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        case 'course_history':
            $stmt = $pdo->prepare("
                SELECT c.course_code, c.course_title, cr.registration_date, cr.approval_status
                FROM course_regtbl cr
                JOIN coursetbl c ON cr.course_id = c.course_id
                WHERE cr.student_id = ?
                ORDER BY cr.registration_date DESC
            ");
            $stmt->execute([$student_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

function generateCourseReport($pdo, $type, $course_id) {
    switch ($type) {
        case 'enrollment':
            $stmt = $pdo->prepare("
                SELECT s.Matric_No, s.first_name, s.last_name, s.email, s.Department, s.Level,
                       cr.registration_date, cr.approval_status
                FROM course_regtbl cr
                JOIN studenttbl s ON cr.student_id = s.student_id
                WHERE cr.course_id = ?
                ORDER BY cr.registration_date DESC
            ");
            $stmt->execute([$course_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        case 'grade_distribution':
            $stmt = $pdo->prepare("
                SELECT e.grade, COUNT(*) as count, AVG(e.total_score) as avg_score
                FROM evaluationtbl e
                WHERE e.course_id = ? AND e.grade IS NOT NULL
                GROUP BY e.grade
                ORDER BY e.grade
            ");
            $stmt->execute([$course_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        case 'performance':
            $stmt = $pdo->prepare("
                SELECT s.Matric_No, s.first_name, s.last_name,
                       e.ca_score, e.test_score, e.exam_score, e.total_score, e.grade
                FROM evaluationtbl e
                JOIN studenttbl s ON e.student_id = s.student_id
                WHERE e.course_id = ?
                ORDER BY e.total_score DESC
            ");
            $stmt->execute([$course_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

function generateDepartmentReport($pdo, $type, $department) {
    switch ($type) {
        case 'enrollment_summary':
            $stmt = $pdo->prepare("
                SELECT Level, COUNT(*) as student_count
                FROM studenttbl
                WHERE Department = ?
                GROUP BY Level
                ORDER BY Level
            ");
            $stmt->execute([$department]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        case 'performance_overview':
            $stmt = $pdo->prepare("
                SELECT AVG(e.total_score) as avg_score, COUNT(*) as total_grades,
                       SUM(CASE WHEN e.grade IN ('A', 'B') THEN 1 ELSE 0 END) as good_grades
                FROM evaluationtbl e
                JOIN coursetbl c ON e.course_id = c.course_id
                WHERE c.department = ? AND e.grade IS NOT NULL
            ");
            $stmt->execute([$department]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        case 'grade_statistics':
            $stmt = $pdo->prepare("
                SELECT e.grade, COUNT(*) as count
                FROM evaluationtbl e
                JOIN coursetbl c ON e.course_id = c.course_id
                WHERE c.department = ? AND e.grade IS NOT NULL
                GROUP BY e.grade
                ORDER BY e.grade
            ");
            $stmt->execute([$department]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

function generateSystemReport($pdo, $type, $start_date, $end_date) {
    $date_filter = '';
    $params = [];
    
    if ($start_date && $end_date) {
        $date_filter = " WHERE DATE(created_at) BETWEEN ? AND ?";
        $params = [$start_date, $end_date];
    }
    
    switch ($type) {
        case 'user_summary':
            $stmt = $pdo->prepare("
                SELECT 'Students' as user_type, COUNT(*) as count FROM studenttbl
                UNION ALL
                SELECT 'Lecturers' as user_type, COUNT(*) as count FROM lecturertbl
                UNION ALL
                SELECT 'Admins' as user_type, COUNT(*) as count FROM admintbl
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        case 'course_summary':
            $stmt = $pdo->prepare("
                SELECT department, COUNT(*) as course_count, SUM(course_unit) as total_units
                FROM coursetbl
                GROUP BY department
                ORDER BY department
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        case 'grade_summary':
            $stmt = $pdo->prepare("
                SELECT grade, COUNT(*) as count, AVG(total_score) as avg_score
                FROM evaluationtbl
                WHERE grade IS NOT NULL
                GROUP BY grade
                ORDER BY grade
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        case 'activity_log':
            $stmt = $pdo->prepare("
                SELECT activity_type, description, timestamp
                FROM studentrecentactivitytbl
                ORDER BY timestamp DESC
                LIMIT 100
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>