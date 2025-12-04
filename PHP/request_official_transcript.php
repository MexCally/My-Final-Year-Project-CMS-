<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$student_id = $_SESSION['student_id'];
$purpose = $_POST['purpose'] ?? '';
$delivery_address = $_POST['delivery_address'] ?? '';

if (empty($purpose) || empty($delivery_address)) {
    echo json_encode(['success' => false, 'message' => 'Purpose and delivery address are required']);
    exit;
}

try {
    // Get student info
    $stmt = $pdo->prepare("SELECT Matric_No, first_name, last_name FROM studenttbl WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }
    
    $student_name = $student['first_name'] . ' ' . $student['last_name'];
    $tracking_number = 'TR' . date('Ymd') . rand(1000, 9999);
    
    $stmt = $pdo->prepare("
        INSERT INTO transcript_requests (student_matric_no, student_name, recipient_type, recipient_details, tracking_number)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$student['Matric_No'], $student_name, $purpose, $delivery_address, $tracking_number]);
    
    echo json_encode(['success' => true, 'message' => 'Official transcript request submitted successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>