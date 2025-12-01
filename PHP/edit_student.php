<?php
session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit();
}

try {
    // Get form data
    $student_id = trim($_POST['student_id'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_num = trim($_POST['phone_num'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $academic_year = trim($_POST['academic_year'] ?? '');
    $gender = trim($_POST['gender'] ?? '');

    // Validation
    $errors = [];

    if (empty($student_id)) {
        $errors[] = 'Student ID is required';
    }

    if (empty($first_name)) {
        $errors[] = 'First name is required';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $first_name)) {
        $errors[] = 'First name must contain only letters and spaces';
    }

    if (empty($last_name)) {
        $errors[] = 'Last name is required';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $last_name)) {
        $errors[] = 'Last name must contain only letters and spaces';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    if (empty($phone_num)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^\+?[0-9\s\-\(\)]+$/', $phone_num)) {
        $errors[] = 'Invalid phone number format';
    }

    if (empty($department)) {
        $errors[] = 'Department is required';
    }

    if (empty($level)) {
        $errors[] = 'Level is required';
    }

    if (empty($academic_year)) {
        $errors[] = 'Academic year is required';
    }

    if (empty($gender)) {
        $errors[] = 'Gender is required';
    }

    // Check if email already exists for another student
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM studenttbl WHERE email = ? AND student_id != ?");
        $stmt->execute([$email, $student_id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Email already exists for another student';
        }
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    // Update student
    $stmt = $pdo->prepare("UPDATE studenttbl SET first_name = ?, last_name = ?, email = ?, Phone_Num = ?, Department = ?, Level = ?, academic_year = ?, Gender = ? WHERE student_id = ?");
    $stmt->execute([$first_name, $last_name, $email, $phone_num, $department, $level, $academic_year, $gender, $student_id]);

    // Log the activity
    $admin_id = $_SESSION['admin_id'];
    $description = "Updated student: $first_name $last_name ($student_id)";
    $log_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
    $log_stmt->execute(['student_updated', $description, $admin_id, 'admin']);

    echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]]);
}
?>
