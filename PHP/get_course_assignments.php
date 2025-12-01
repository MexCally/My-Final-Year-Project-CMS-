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

if (empty($course_id)) {
    echo json_encode(['success' => false, 'message' => 'Course ID required']);
    exit;
}

try {
    // Verify student is enrolled in this course
    $enrollStmt = $pdo->prepare("SELECT COUNT(*) FROM course_regtbl WHERE student_id = ? AND course_id = ?");
    $enrollStmt->execute([$student_id, $course_id]);
    
    if ($enrollStmt->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => 'Not enrolled in this course']);
        exit;
    }
    
    // Get assignments with submission status
    $assignmentsStmt = $pdo->prepare("
        SELECT 
            a.assignment_id,
            a.title,
            a.description,
            a.due_date,
            a.max_score,
            a.created_at,
            c.course_code,
            c.course_title,
            CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name,
            s.sub_id,
            s.submitted_at,
            s.file_path AS submission_file
        FROM assignmenttbl a
        JOIN coursetbl c ON a.course_id = c.course_id
        LEFT JOIN lecturertbl l ON a.lecturer_id = l.LecturerID
        LEFT JOIN ass_subtbl s ON a.assignment_id = s.assignment_id AND s.student_id = ?
        WHERE a.course_id = ?
        ORDER BY a.due_date ASC
    ");
    $assignmentsStmt->execute([$student_id, $course_id]);
    $assignments = $assignmentsStmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'assignments' => $assignments
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>