<?php
session_start();
require_once '../config/db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];
$student_id = $_GET['student_id'] ?? '';
$course_id = $_GET['course_id'] ?? '';

if (empty($student_id) || empty($course_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

try {
    // Verify that the lecturer teaches this course
    $stmt = $pdo->prepare("SELECT course_id FROM coursetbl WHERE course_id = ? AND lecturer_id = ?");
    $stmt->execute([$course_id, $lecturer_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You are not authorized to view this student\'s details']);
        exit();
    }
    
    // Fetch student academic details
    $stmt = $pdo->prepare("
        SELECT 
            s.student_id,
            s.Matric_No as matric_number,
            s.first_name,
            s.last_name,
            s.Department as department,
            s.Level as level,
            c.course_code,
            c.course_title,
            e.ca_score,
            e.test_score,
            e.exam_score,
            e.total_score,
            e.grade,
            e.grade_point
        FROM studenttbl s
        JOIN course_regtbl cr ON s.student_id = cr.student_id
        JOIN coursetbl c ON cr.course_id = c.course_id
        LEFT JOIN evaluationtbl e ON s.student_id = e.student_id AND c.course_id = e.course_id
        WHERE s.student_id = ? AND c.course_id = ? AND cr.approval_status IN ('Approved', 'Registered')
    ");
    
    $stmt->execute([$student_id, $course_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found or not enrolled in this course']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'student' => $student
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_student_academic_details.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>