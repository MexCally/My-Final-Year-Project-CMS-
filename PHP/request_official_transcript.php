<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$student_id = $_SESSION['student_id'];
$input = json_decode(file_get_contents('php://input'), true);

$method = $input['method'] ?? '';
$student_name = $input['student_name'] ?? '';
$matric_no = $input['matric_no'] ?? '';

if (empty($method)) {
    echo json_encode(['success' => false, 'message' => 'Request method required']);
    exit;
}

try {
    // Generate tracking number
    $tracking_number = 'TR' . date('Ymd') . str_pad($student_id, 4, '0', STR_PAD_LEFT) . rand(100, 999);
    
    // Determine recipient details based on method
    $recipient_details = '';
    switch($method) {
        case 'email':
            $recipient_details = 'Email delivery to registered address';
            break;
        case 'mail':
            $recipient_details = 'Postal mail to registered address';
            break;
        case 'pickup':
            $recipient_details = 'In-person pickup at registrar office';
            break;
    }
    
    // Insert transcript request
    $stmt = $pdo->prepare("
        INSERT INTO transcript_requests (
            student_matric_no, 
            student_name, 
            recipient_type, 
            recipient_details, 
            request_date, 
            status,
            tracking_number
        ) VALUES (?, ?, ?, ?, NOW(), 'Pending', ?)
    ");
    
    $stmt->execute([
        $matric_no,
        $student_name,
        $method,
        $recipient_details,
        $tracking_number
    ]);
    
    // Log activity
    $activityStmt = $pdo->prepare("
        INSERT INTO studentrecentactivitytbl (
            student_id, 
            activity_type, 
            activity_description, 
            timestamp
        ) VALUES (?, ?, ?, NOW())
    ");
    
    $activityStmt->execute([
        $student_id,
        'Transcript Request',
        "Official transcript requested via {$method}. Tracking: {$tracking_number}"
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Transcript request submitted successfully',
        'reference_id' => $tracking_number
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>