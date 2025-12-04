<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$lecturer_id = $_SESSION['lecturer_id'];
$input = json_decode(file_get_contents('php://input'), true);
$course_id = $input['course_id'] ?? '';

if (empty($course_id)) {
    echo json_encode(['success' => false, 'message' => 'Course ID is required']);
    exit;
}

try {
    // Verify lecturer owns this course
    $stmt = $pdo->prepare("SELECT course_code, course_title FROM coursetbl WHERE course_id = ? AND lecturer_id = ?");
    $stmt->execute([$course_id, $lecturer_id]);
    $course = $stmt->fetch();
    
    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Access denied to this course']);
        exit;
    }
    
    $pdo->beginTransaction();
    
    // Check if grades exist for this course
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as grade_count FROM evaluationtbl 
        WHERE course_id = ? AND lecturer_id = ?
    ");
    $stmt->execute([$course_id, $lecturer_id]);
    $result = $stmt->fetch();
    
    if ($result['grade_count'] == 0) {
        echo json_encode(['success' => false, 'message' => 'No grades found for this course']);
        exit;
    }
    
    // Log the submission to grade_submissions table
    $stmt = $pdo->prepare("
        INSERT INTO grade_submissions (course_id, lecturer_id, academic_year, semester)
        VALUES (?, ?, YEAR(NOW()), 
                CASE WHEN MONTH(NOW()) BETWEEN 1 AND 6 THEN 'Second' ELSE 'First' END)
        ON DUPLICATE KEY UPDATE submitted_at = NOW()
    ");
    $stmt->execute([$course_id, $lecturer_id]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Grades for {$course['course_code']} have been successfully submitted to records office"
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Failed to submit grades: ' . $e->getMessage()]);
}
?>