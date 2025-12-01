<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

$student_id = $_SESSION['student_id'];

try {
    // Get approved courses for this student
    $stmt = $pdo->prepare("SELECT 
        c.course_id,
        c.course_code,
        c.course_title,
        c.course_unit,
        c.department,
        c.level,
        c.semester,
        CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name,
        cr.academic_year,
        cr.semester as reg_semester,
        cr.date_approved
    FROM course_regtbl cr
    JOIN coursetbl c ON cr.course_id = c.course_id
    LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
    WHERE cr.student_id = ? AND cr.approval_status = 'approved'
    ORDER BY c.course_code");
    
    $stmt->execute([$student_id]);
    $approved_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'courses' => $approved_courses
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>