<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'errors' => ['Invalid request method']]);
    exit;
}

try {
    $student_id = $_POST['student_id'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $detailed_reason = $_POST['detailed_reason'] ?? '';
    
    if (empty($student_id) || empty($reason) || empty($detailed_reason)) {
        echo json_encode(['success' => false, 'errors' => ['All fields are required']]);
        exit;
    }
    
    // Check if student exists
    $checkStmt = $pdo->prepare("SELECT student_id FROM studenttbl WHERE student_id = ?");
    $checkStmt->execute([$student_id]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'errors' => ['Student not found']]);
        exit;
    }
    
    // For demonstration, we'll delete the student record (decline registration)
    // In a real system, you might move to a declined_registrations table or update status
    $deleteStmt = $pdo->prepare("DELETE FROM studenttbl WHERE student_id = ?");
    $deleteStmt->execute([$student_id]);
    
    echo json_encode(['success' => true, 'message' => 'Registration declined successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]]);
}
?>