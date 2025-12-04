<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$student_id = $_SESSION['student_id'];

try {
    // Get published grades only
    $stmt = $pdo->prepare("
        SELECT 
            e.ca_score,
            e.test_score,
            e.exam_score,
            e.total_score,
            e.grade,
            e.grade_point,
            e.quality_points,
            e.academic_year,
            e.semester,
            c.course_code,
            c.course_title,
            c.course_unit,
            CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name
        FROM evaluationtbl e
        JOIN coursetbl c ON e.course_id = c.course_id
        JOIN lecturertbl l ON e.lecturer_id = l.LecturerID
        JOIN grade_submissions gs ON e.course_id = gs.course_id AND e.lecturer_id = gs.lecturer_id
        WHERE e.student_id = ? AND gs.published = 1
        ORDER BY e.academic_year DESC, e.semester DESC, c.course_code
    ");
    $stmt->execute([$student_id]);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'grades' => $grades]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>