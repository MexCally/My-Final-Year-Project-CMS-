<?php
session_start();
require_once '../config/db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(401);
    die('Unauthorized');
}

if (!isset($_GET['file_path']) || !isset($_GET['sub_id'])) {
    http_response_code(400);
    die('Missing parameters');
}

$file_path = $_GET['file_path'];
$sub_id = $_GET['sub_id'];
$lecturer_id = $_SESSION['lecturer_id'];

try {
    // Verify that this submission belongs to an assignment created by this lecturer
    $stmt = $pdo->prepare("SELECT s.file_path, s.student_id, a.title, st.student_name 
                          FROM ass_subtbl s 
                          JOIN assignmenttbl a ON s.assignment_id = a.assignment_id 
                          JOIN studenttbl st ON s.student_id = st.student_id
                          WHERE s.sub_id = ? AND a.lecturer_id = ?");
    $stmt->execute([$sub_id, $lecturer_id]);
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$submission) {
        http_response_code(403);
        die('Access denied');
    }
    
    // Construct full file path
    $full_path = '../' . $submission['file_path'];
    
    // Check if file exists
    if (!file_exists($full_path)) {
        http_response_code(404);
        die('File not found');
    }
    
    // Get file info
    $file_name = basename($full_path);
    $file_size = filesize($full_path);
    $file_type = mime_content_type($full_path);
    
    // Set headers for download
    header('Content-Type: ' . $file_type);
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Content-Length: ' . $file_size);
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    
    // Output file
    readfile($full_path);
    exit();
    
} catch (PDOException $e) {
    http_response_code(500);
    die('Database error');
}
?>