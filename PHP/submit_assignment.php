<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$student_id = $_SESSION['student_id'];
$assignment_id = $_POST['assignment_id'] ?? '';

if (empty($assignment_id) || !isset($_FILES['assignment_file'])) {
    echo json_encode(['success' => false, 'message' => 'Assignment ID and file are required']);
    exit;
}

try {
    // Check if already submitted
    $check_stmt = $pdo->prepare("SELECT sub_id FROM ass_subtbl WHERE assignment_id = ? AND student_id = ?");
    $check_stmt->execute([$assignment_id, $student_id]);
    
    if ($check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Assignment already submitted']);
        exit;
    }
    
    // Handle file upload
    $file = $_FILES['assignment_file'];
    $upload_dir = '../uploads/assignments/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . $student_id . '_' . $assignment_id . '.' . $file_extension;
    $file_path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Save submission record
        $stmt = $pdo->prepare("INSERT INTO ass_subtbl (assignment_id, student_id, file_path) VALUES (?, ?, ?)");
        $stmt->execute([$assignment_id, $student_id, 'uploads/assignments/' . $filename]);
        
        // Log activity
        $activity_stmt = $pdo->prepare("INSERT INTO studentrecentactivitytbl (student_id, activity_type, activity_description) VALUES (?, ?, ?)");
        $activity_stmt->execute([$student_id, 'Assignment Submission', 'Submitted assignment for Assignment ID: ' . $assignment_id]);
        
        echo json_encode(['success' => true, 'message' => 'Assignment submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>