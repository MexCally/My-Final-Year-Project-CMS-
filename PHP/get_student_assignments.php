<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$student_id = $_SESSION['student_id'];
$course_id = $_GET['course_id'] ?? '';

try {
    $stmt = $pdo->prepare("
        SELECT a.*, c.course_code, c.course_title,
               s.sub_id, s.submitted_at, s.score_received
        FROM assignmenttbl a
        JOIN coursetbl c ON a.course_id = c.course_id
        JOIN course_regtbl cr ON c.course_id = cr.course_id
        LEFT JOIN ass_subtbl s ON a.assignment_id = s.assignment_id AND s.student_id = ?
        WHERE cr.student_id = ? AND cr.approval_status IN ('Approved', 'Registered')
        AND a.is_active = 1 AND c.course_id = ?
        ORDER BY a.due_date ASC
    ");
    $stmt->execute([$student_id, $student_id, $course_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'assignments' => $assignments]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>