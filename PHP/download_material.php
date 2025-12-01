<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    http_response_code(403);
    echo 'Unauthorized access.';
    exit();
}

if (!isset($_GET['material_id'])) {
    http_response_code(400);
    echo 'Material ID is required.';
    exit();
}

$student_id = $_SESSION['student_id'];
$material_id = $_GET['material_id'];

try {
    // Verify student has access to this material
    $stmt = $pdo->prepare("
        SELECT cm.file_path_url, cm.title
        FROM course_materialtbl cm
        JOIN coursetbl c ON cm.course_id = c.course_id
        WHERE cm.material_id = ? 
        AND cm.is_published = 1
        AND cm.course_id IN (
            SELECT course_id FROM course_regtbl WHERE student_id = ?
        )
    ");
    
    $stmt->execute([$material_id, $student_id]);
    $material = $stmt->fetch();
    
    if (!$material) {
        http_response_code(404);
        echo 'Material not found or access denied.';
        exit();
    }
    
    $file_path = '../' . $material['file_path_url'];
    
    if (!file_exists($file_path)) {
        http_response_code(404);
        echo 'File not found on server.';
        exit();
    }
    
    // Set headers for file download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Content-Length: ' . filesize($file_path));
    
    // Output file
    readfile($file_path);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Database error occurred.';
}
?>