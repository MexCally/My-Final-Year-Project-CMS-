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
    
    // Debug: Log the lecturer_id
    error_log("Upload material - Lecturer ID from session: " . $lecturer_id);
    
    // Get form data
    $course_id = intval($_POST['course_id']);
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $file_type = htmlspecialchars(trim($_POST['file_type']));
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    // Debug: Log form data
    error_log("Upload material - Course ID: $course_id, Title: $title, File Type: $file_type");
    
    // Validation
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Material title is required.";
    }
    
    if (empty($file_type)) {
        $errors[] = "File type is required.";
    }
    
    if (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Please select a file to upload.";
    }
    
    // Verify that the course belongs to this lecturer
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT course_id FROM coursetbl WHERE course_id = ? AND lecturer_id = ?");
            $stmt->execute([$course_id, $lecturer_id]);
            
            if (!$stmt->fetch()) {
                $errors[] = "You do not have permission to upload materials for this course.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // Handle file upload
    $file_path_url = '';
    if (empty($errors) && isset($_FILES['material_file'])) {
        $file = $_FILES['material_file'];
        $upload_dir = '../uploads/materials/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        // Check file size (limit to 50MB)
        if ($file['size'] > 50 * 1024 * 1024) {
            $errors[] = "File size must be less than 50MB.";
        }
        
        // Move uploaded file
        if (empty($errors) && move_uploaded_file($file['tmp_name'], $file_path)) {
            $file_path_url = 'uploads/materials/' . $filename;
        } else {
            $errors[] = "Failed to upload file.";
        }
    }
    
    // Insert material record if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO course_materialtbl (course_id, title, description, file_path_url, file_type, is_published, uploaded_by_lecturer_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            
            // Debug: Log the insert parameters
            error_log("Inserting material with lecturer_id: $lecturer_id");
            
            $stmt->execute([
                $course_id,
                $title,
                $description,
                $file_path_url,
                $file_type,
                $is_published,
                $lecturer_id
            ]);
            
            $material_id = $pdo->lastInsertId();
            
            // Log the activity
            $activity_stmt = $pdo->prepare("INSERT INTO lecturerrecentactivitytbl (LecturerID, activity_type, activity_description) VALUES (?, ?, ?)");
            $activity_description = "Uploaded material: " . $title;
            $activity_stmt->execute([$lecturer_id, 'material_uploaded', $activity_description]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Material uploaded successfully!',
                'material_id' => $material_id
            ]);
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to upload material: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Validation errors occurred.',
            'errors' => $errors
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
?>