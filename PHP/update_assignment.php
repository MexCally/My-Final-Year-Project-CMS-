<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturer_id = $_SESSION['lecturer_id'];
    $assignment_id = intval($_POST['assignment_id']);
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $max_score = intval($_POST['max_score']);
    $due_date = $_POST['due_date'];
    $academic_year = htmlspecialchars(trim($_POST['academic_year']));
    $semester = htmlspecialchars(trim($_POST['semester']));
    
    // Validation
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Assignment title is required.";
    }
    
    if (empty($description)) {
        $errors[] = "Assignment description is required.";
    }
    
    if ($max_score <= 0) {
        $errors[] = "Maximum score must be greater than 0.";
    }
    
    if (empty($due_date)) {
        $errors[] = "Due date is required.";
    }
    
    // Verify assignment belongs to this lecturer
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT assignment_id FROM assignmenttbl WHERE assignment_id = ? AND lecturer_id = ?");
            $stmt->execute([$assignment_id, $lecturer_id]);
            
            if (!$stmt->fetch()) {
                $errors[] = "You do not have permission to edit this assignment.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // Update assignment if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE assignmenttbl SET title = ?, description = ?, max_score = ?, due_date = ?, academic_year = ?, semester = ? WHERE assignment_id = ? AND lecturer_id = ?");
            
            $stmt->execute([
                $title,
                $description,
                $max_score,
                $due_date,
                $academic_year,
                $semester,
                $assignment_id,
                $lecturer_id
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Assignment updated successfully!'
            ]);
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update assignment: ' . $e->getMessage()
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