<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$type = $_GET['type'] ?? '';

try {
    switch ($type) {
        case 'students':
            $stmt = $pdo->prepare("SELECT student_id, Matric_No, first_name, last_name FROM studenttbl ORDER BY first_name, last_name");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'courses':
            $stmt = $pdo->prepare("SELECT course_id, course_code, course_title FROM coursetbl ORDER BY course_code");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        default:
            throw new Exception('Invalid data type');
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>