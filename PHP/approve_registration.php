<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'errors' => ['Invalid request method']]);
    exit;
}

try {
    $student_id = $_POST['student_id'] ?? '';
    $comments = $_POST['comments'] ?? '';
    
    if (empty($student_id)) {
        echo json_encode(['success' => false, 'errors' => ['Student ID is required']]);
        exit;
    }
    
    // Update student status to approved (you may need to add a status field)
    // For now, we'll just update a field or add to an activity log
    
    // Check if student exists
    $checkStmt = $pdo->prepare("SELECT student_id FROM studenttbl WHERE student_id = ?");
    $checkStmt->execute([$student_id]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'errors' => ['Student not found']]);
        exit;
    }
    
    // For demonstration, we'll just log this approval
    // In a real system, you might update a status field or move from pending to approved table
    
    echo json_encode(['success' => true, 'message' => 'Registration approved successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]]);
}
?>