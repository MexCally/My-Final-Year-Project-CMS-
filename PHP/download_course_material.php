<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$student_id = $_SESSION['student_id'];
$material_id = $_GET['material_id'] ?? '';

if (empty($material_id)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Material ID required']);
    exit;
}

try {
    // Verify student is enrolled in the course that contains this material
    $enrollStmt = $pdo->prepare("
        SELECT COUNT(*) FROM course_regtbl cr
        JOIN course_materialtbl cm ON cr.course_id = cm.course_id
        WHERE cr.student_id = ? AND cm.material_id = ?
    ");
    $enrollStmt->execute([$student_id, $material_id]);

    if ($enrollStmt->fetchColumn() == 0) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['success' => false, 'message' => 'Not authorized to download this material']);
        exit;
    }

    // Get material details
    $materialStmt = $pdo->prepare("
        SELECT m.title, m.file_path_url, m.file_type
        FROM course_materialtbl m
        WHERE m.material_id = ?
    ");
    $materialStmt->execute([$material_id]);
    $material = $materialStmt->fetch();

    if (!$material) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['success' => false, 'message' => 'Material not found']);
        exit;
    }

    $file_path = $material['file_path_url'];

    // Check if file exists
    if (!file_exists($file_path)) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['success' => false, 'message' => 'File not found on server']);
        exit;
    }

    // Get file info
    $file_name = basename($file_path);
    $file_size = filesize($file_path);

    // Set appropriate headers for download
    header('Content-Type: ' . mime_content_type($file_path));
    header('Content-Disposition: attachment; filename="' . $material['title'] . '"');
    header('Content-Length: ' . $file_size);
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    // Clear output buffer
    if (ob_get_level()) {
        ob_clean();
    }

    // Read and output file
    readfile($file_path);
    exit;

} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
