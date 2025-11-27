<?php
session_start();
require_once '../config/db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturer_id = $_SESSION['lecturer_id'];
    $material_id = intval($_POST['material_id']);
    
    if (empty($material_id)) {
        echo json_encode(['success' => false, 'message' => 'Material ID is required.']);
        exit();
    }
    
    try {
        // First, verify that this material belongs to the lecturer
        $stmt = $pdo->prepare("SELECT cm.file_path_url, cm.title FROM course_materialtbl cm WHERE cm.material_id = ? AND cm.uploaded_by_lecturer_id = ?");
        $stmt->execute([$material_id, $lecturer_id]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$material) {
            echo json_encode(['success' => false, 'message' => 'Material not found or you do not have permission to delete it.']);
            exit();
        }
        
        // Delete the file from filesystem
        $file_path = '../' . $material['file_path_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete the record from database
        $stmt = $pdo->prepare("DELETE FROM course_materialtbl WHERE material_id = ? AND uploaded_by_lecturer_id = ?");
        $stmt->execute([$material_id, $lecturer_id]);
        
        // Log the activity
        $activity_stmt = $pdo->prepare("INSERT INTO lecturerrecentactivitytbl (LecturerID, activity_type, activity_description) VALUES (?, ?, ?)");
        $activity_description = "Deleted material: " . $material['title'];
        $activity_stmt->execute([$lecturer_id, 'material_deleted', $activity_description]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Material deleted successfully!'
        ]);
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete material: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
?>