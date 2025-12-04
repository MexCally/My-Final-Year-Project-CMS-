<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id']) && !isset($_SESSION['lecturer_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($_GET['material_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Material ID is required']);
    exit();
}

$material_id = $_GET['material_id'];

try {
    $stmt = $pdo->prepare("SELECT file_path_url, title FROM course_materialtbl WHERE material_id = ?");
    $stmt->execute([$material_id]);
    $material = $stmt->fetch();
    
    if (!$material) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Material not found']);
        exit();
    }
    
    $filename = basename($material['file_path_url']);
    
    // Try multiple file locations
    $possible_paths = [
        __DIR__ . '/../uploads/materials/' . $filename,
        '../uploads/materials/' . $filename,
        'uploads/materials/' . $filename,
        __DIR__ . '/uploads/materials/' . $filename
    ];
    
    $file_path = null;
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            $file_path = $path;
            break;
        }
    }
    
    if (!$file_path) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'File not found: ' . $filename]);
        exit();
    }
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($file_path));
    
    readfile($file_path);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>