<?php
session_start();
require_once '../config/db.php';

// Sanitize input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect + sanitize
    $first_name = sanitize_input($_POST['first_name']);
    $last_name  = sanitize_input($_POST['last_name']);
    $email      = sanitize_input($_POST['email']);
    $phone_num = sanitize_input($_POST['phone_num']);
    $password   = $_POST['password'];

    // Validation
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
        $errors[] = "Valid phone number (10–15 digits) is required.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check email uniqueness
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT admin_id FROM admintbl WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $errors[] = "Email already exists.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Insert if no errors
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO admintbl (first_name, last_name, email, phone_num, password, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([$first_name, $last_name, $email, $phone_num, $hashed_password]);

            // Create session
            $_SESSION['admin_id'] = $pdo->lastInsertId();
            $_SESSION['admin_name'] = "$first_name $last_name";
            $_SESSION['admin_email'] = $email;

            // Redirect to correct dashboard
            header("Location: ../admin_modules/admin_dashboard.php");
            exit();

        } catch (PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }

    // Redirect back with errors
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../admin_register.html");
        exit();
    }

} else {
    // If accessed without POST
    header("Location: ../admin_register.html");
    exit();
}
?>
