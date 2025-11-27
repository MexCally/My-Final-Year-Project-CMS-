<?php
session_start();
require_once '../config/db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone_num = trim($_POST['phone_num']);
    $department = trim($_POST['department']);
    $gender = $_POST['gender'];
    $password = $_POST['password'];

    // Server-side validation
    $errors = [];

    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }

    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }

    if (empty($phone_num) || !preg_match('/^[0-9]{10,15}$/', $phone_num)) {
        $errors[] = "Valid phone number (10-15 digits) is required.";
    }

    if (empty($department)) {
        $errors[] = "Department is required.";
    }

    if (empty($gender) || !in_array($gender, ['Male', 'Female'])) {
        $errors[] = "Valid gender is required.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check if email already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT LecturerID FROM lecturertbl WHERE Email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Email already exists.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO lecturertbl (AdminID, First_name, Last_Name, Email, Phone_Num, Password, Department, Gender, created_at) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$first_name, $last_name, $email, $phone_num, $hashed_password, $department, $gender]);

            // Redirect to login page with success message
            $_SESSION['success'] = "Lecturer account created successfully. Please login.";
            header("Location: ../authentications/lecturer_login.html");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }

    // If there are errors, redirect back with errors
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: lecturer_register.html");
        exit();
    }
} else {
    // If not POST, redirect to register form
    header("Location: lecturer_register.html");
    exit();
}
?>
