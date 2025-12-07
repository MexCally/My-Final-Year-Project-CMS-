<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$studentId = $_SESSION['student_id'];

try {
    $stmt = $pdo->prepare("SELECT decline_status, decline_reason, approval_reason FROM course_regtbl WHERE student_id = ? AND approval_status = 'Declined' ORDER BY date_approved DESC LIMIT 1");
    $stmt->execute([$studentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && $result['decline_reason']) {
        echo json_encode([
            'success' => true,
            'has_decline' => true,
            'status' => $result['decline_status'],
            'reason' => $result['decline_reason'],
            'details' => $result['approval_reason']
        ]);
    } else {
        echo json_encode(['success' => true, 'has_decline' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
