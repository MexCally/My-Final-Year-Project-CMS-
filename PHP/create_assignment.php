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
    
    // Get and sanitize input data
    $course_id = intval($_POST['course_id']);
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
    
    if (empty($academic_year)) {
        $errors[] = "Academic year is required.";
    }
    
    if (empty($semester)) {
        $errors[] = "Semester is required.";
    }
    
    // Verify that the course belongs to this lecturer
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT course_id FROM coursetbl WHERE course_id = ? AND lecturer_id = ?");
            $stmt->execute([$course_id, $lecturer_id]);
            
            if (!$stmt->fetch()) {
                $errors[] = "You do not have permission to create assignments for this course.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // Create assignment if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO assignmenttbl (course_id, lecturer_id, title, description, max_score, due_date, academic_year, semester, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");
            
            $stmt->execute([
                $course_id,
                $lecturer_id,
                $title,
                $description,
                $max_score,
                $due_date,
                $academic_year,
                $semester
            ]);
            
            $assignment_id = $pdo->lastInsertId();
            
            // Log the activity
            $activity_stmt = $pdo->prepare("INSERT INTO lecturerrecentactivitytbl (LecturerID, activity_type, activity_description) VALUES (?, ?, ?)");
            $activity_description = "Created assignment: " . $title;
            $activity_stmt->execute([$lecturer_id, 'assignment_created', $activity_description]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Assignment created successfully!',
                'assignment_id' => $assignment_id
            ]);
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create assignment: ' . $e->getMessage()
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