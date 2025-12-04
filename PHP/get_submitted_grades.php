<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            gs.id,
            gs.course_id,
            gs.lecturer_id,
            gs.submitted_at,
            gs.approved_at,
            gs.approved_by,
            gs.published,
            c.course_code,
            c.course_title,
            CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name,
            COUNT(e.student_id) as student_count
        FROM grade_submissions gs
        JOIN coursetbl c ON gs.course_id = c.course_id
        JOIN lecturertbl l ON gs.lecturer_id = l.LecturerID
        LEFT JOIN evaluationtbl e ON gs.course_id = e.course_id AND gs.lecturer_id = e.lecturer_id
        GROUP BY gs.id, gs.course_id, gs.lecturer_id, gs.submitted_at, gs.approved_at, gs.approved_by, gs.published, c.course_code, c.course_title, l.First_name, l.Last_Name
        ORDER BY gs.submitted_at DESC
    ");
    
    $stmt->execute();
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($submissions);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>