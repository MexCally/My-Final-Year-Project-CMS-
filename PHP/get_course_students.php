<?php
session_start();
require_once '../config/db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];
$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Course ID is required.']);
    exit();
}

try {
    // Verify that this course belongs to the lecturer
    $stmt = $pdo->prepare("SELECT course_id FROM coursetbl WHERE course_id = ? AND lecturer_id = ?");
    $stmt->execute([$course_id, $lecturer_id]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'You do not have access to this course.']);
        exit();
    }
    
    // Get students enrolled in this course
    $stmt = $pdo->prepare("SELECT 
        s.student_id,
        s.Matric_No,
        s.first_name,
        s.last_name,
        s.email,
        s.Phone_Num,
        s.Department,
        s.Level,
        e.enrollment_date
    FROM studenttbl s
    JOIN enrollmenttbl e ON s.student_id = e.student_id
    WHERE e.course_id = ?
    ORDER BY s.first_name, s.last_name");
    
    $stmt->execute([$course_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['students' => $students]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>