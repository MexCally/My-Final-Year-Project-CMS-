<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$student_id = $_SESSION['student_id'];
$assignment_id = $_POST['assignment_id'] ?? '';
$comments = $_POST['comments'] ?? '';

if (empty($assignment_id)) {
    echo json_encode(['success' => false, 'message' => 'Assignment ID required']);
    exit;
}

if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File upload required']);
    exit;
}

try {
    // Verify assignment exists and student is enrolled
    $verifyStmt = $pdo->prepare("
        SELECT a.assignment_id, a.title, a.due_date, c.course_code
        FROM assignmenttbl a
        JOIN coursetbl c ON a.course_id = c.course_id
        WHERE a.assignment_id = ? 
        AND c.course_id IN (
            SELECT course_id FROM course_regtbl WHERE student_id = ?
        )
    ");
    $verifyStmt->execute([$assignment_id, $student_id]);
    $assignment = $verifyStmt->fetch();
    
    if (!$assignment) {
        echo json_encode(['success' => false, 'message' => 'Assignment not found or not enrolled']);
        exit;
    }
    
    // Check if already submitted
    $existingStmt = $pdo->prepare("SELECT sub_id FROM ass_subtbl WHERE assignment_id = ? AND student_id = ?");
    $existingStmt->execute([$assignment_id, $student_id]);
    
    if ($existingStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Assignment already submitted']);
        exit;
    }
    
    // Check if past due date
    if (strtotime($assignment['due_date']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Assignment is past due date']);
        exit;
    }
    
    // Validate file
    $file = $_FILES['submission_file'];
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF, DOC, DOCX, TXT allowed']);
        exit;
    }
    
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'File too large. Maximum 10MB allowed']);
        exit;
    }
    
    // Create upload directory if not exists
    $uploadDir = '../uploads/assignments/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
            exit;
        }
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'assignment_' . $assignment_id . '_student_' . $student_id . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save file']);
        exit;
    }
    
    // Insert submission record
    $insertStmt = $pdo->prepare("
        INSERT INTO ass_subtbl (assignment_id, student_id, file_path, comments, submitted_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $insertStmt->execute([$assignment_id, $student_id, $filePath, $comments]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Assignment submitted successfully',
        'submission_id' => $pdo->lastInsertId()
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>