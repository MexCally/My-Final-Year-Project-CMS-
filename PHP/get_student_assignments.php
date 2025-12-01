<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$student_id = $_SESSION['student_id'];

try {
    // Get assignments for courses the student is registered for
    $stmt = $pdo->prepare("
        SELECT 
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
            CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name,
            s.sub_id,
            s.submitted_at
        FROM assignmenttbl a
        JOIN coursetbl c ON a.course_id = c.course_id
        LEFT JOIN lecturertbl l ON a.lecturer_id = l.LecturerID
        LEFT JOIN ass_subtbl s ON a.assignment_id = s.assignment_id AND s.student_id = ?
        WHERE a.is_active = 1 
        AND a.course_id IN (
            SELECT course_id FROM course_regtbl WHERE student_id = ?
        )
        ORDER BY a.due_date ASC
    ");
    
    $stmt->execute([$student_id, $student_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['assignments' => $assignments]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>