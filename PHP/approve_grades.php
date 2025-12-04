<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$submission_id = $_POST['submission_id'] ?? '';

if (empty($submission_id)) {
    echo json_encode(['success' => false, 'message' => 'Submission ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Update grade_submissions table
    $stmt = $pdo->prepare("
        UPDATE grade_submissions 
        SET approved_at = NOW(), approved_by = ?, published = 1, published_at = NOW(), published_by = ?
        WHERE id = ?
    ");
    $stmt->execute([$admin_id, $admin_id, $submission_id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Grade submission not found');
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Grades approved and published successfully'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>