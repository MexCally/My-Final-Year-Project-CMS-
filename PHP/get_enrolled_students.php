<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$courseId = $_GET['course_id'] ?? null;

if (!$courseId) {
    echo json_encode(['error' => 'Course ID required']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            s.student_id,
            s.Matric_No,
            s.first_name,
            s.last_name,
            s.email,
            s.Phone_Num,
            s.Department,
            s.Level,
            cr.date_registered
        FROM studenttbl s
        JOIN course_regtbl cr ON s.student_id = cr.student_id
        WHERE cr.course_id = ? AND cr.approval_status IN ('Approved', 'Registered')
        ORDER BY s.Matric_No
    ");
    $stmt->execute([$courseId]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'students' => $students]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}