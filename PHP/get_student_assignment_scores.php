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
        echo json_encode(['success' => false, 'message' => 'You are not authorized to view this data']);
        exit();
    }
    
    // Fetch assignment scores for the student in this course
    $stmt = $pdo->prepare("
        SELECT 
            a.assignment_id,
            a.title,
            a.max_score,
            sub.score_received,
            sub.submitted_at
        FROM assignmenttbl a
        LEFT JOIN ass_subtbl sub ON a.assignment_id = sub.assignment_id AND sub.student_id = ?
        WHERE a.course_id = ? AND a.lecturer_id = ?
        ORDER BY a.created_at DESC
    ");
    
    $stmt->execute([$student_id, $course_id, $lecturer_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'assignments' => $assignments
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_student_assignment_scores.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>