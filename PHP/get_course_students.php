<?php
session_start();
require_once '../config/db.php';

// Check if admin or lecturer is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['lecturer_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$course_code = $_GET['course_code'] ?? null;

if (!$course_code) {
    http_response_code(400);
    echo json_encode(['error' => 'Course code is required.']);
    exit();
}

try {
    // Get course ID from course code
    $stmt = $pdo->prepare("SELECT course_id FROM coursetbl WHERE course_code = ?");
    $stmt->execute([$course_code]);
    $course = $stmt->fetch();
    
    if (!$course) {
        http_response_code(404);
        echo json_encode(['error' => 'Course not found.']);
        exit();
    }
    
    $course_id = $course['course_id'];
    
    // Get students enrolled in this course via course registration
    $stmt = $pdo->prepare("SELECT 
        s.student_id,
        s.Matric_No,
        s.first_name,
        s.last_name,
        s.email,
        s.Phone_Num,
        s.Department,
        s.Level,
        cr.date_registered as enrollment_date,
        'Active' as status
    FROM studenttbl s
    JOIN course_regtbl cr ON s.student_id = cr.student_id
    WHERE cr.course_id = ? AND cr.approval_status = 'Registered'
    ORDER BY s.first_name, s.last_name");
    
    $stmt->execute([$course_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['students' => $students]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>