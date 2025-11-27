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

try {
    // Get assignments for this lecturer
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
        c.course_title,
        COUNT(s.submission_id) as submission_count,
        (SELECT COUNT(DISTINCT e.student_id) FROM enrollmenttbl e WHERE e.course_id = a.course_id) as total_students
    FROM assignmenttbl a
    JOIN coursetbl c ON a.course_id = c.course_id
    LEFT JOIN submissiontbl s ON a.assignment_id = s.assignment_id
    WHERE a.lecturer_id = ?
    GROUP BY a.assignment_id
    ORDER BY a.due_date DESC");
    
    $stmt->execute([$lecturer_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['assignments' => $assignments]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>