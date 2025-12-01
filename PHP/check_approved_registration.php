<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

$student_id = $_SESSION['student_id'];

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM course_regtbl WHERE student_id = ? AND approval_status = 'approved'");
    $stmt->execute([$student_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'has_approved' => $result['count'] > 0,
        'count' => $result['count']
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>