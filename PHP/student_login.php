<?php
session_start();
require_once '../config/db.php';

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $matric_no = sanitize_input($_POST['matric_no'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($matric_no)) {
        $errors[] = 'Matriculation number is required.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        try {
            // Fetch student by matric_no
            $stmt = $pdo->prepare("SELECT student_id, first_name, last_name, matric_no, password FROM studenttbl WHERE matric_no = ?");
            $stmt->execute([$matric_no]);
            $student = $stmt->fetch();

            if ($student) {
                // Check password (support both hashed and plain text for backward compatibility)
                $password_valid = false;
                if (password_verify($password, $student['password'])) {
                    // Password is hashed
                    $password_valid = true;
                } elseif ($student['password'] === $password) {
                    // Password is plain text (for existing users)
                    $password_valid = true;
                    // Optionally hash and update the password for future logins
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("UPDATE studenttbl SET password = ? WHERE student_id = ?");
                    $update_stmt->execute([$hashed_password, $student['student_id']]);
                }

                if ($password_valid) {
                    // Login successful - start session
                    $_SESSION['student_id'] = $student['student_id'];
                    $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];
                    $_SESSION['student_matric'] = $student['matric_no'];

                    // Redirect to dashboard
                    header('Location: ../student_modules/student_dashboard.php');
                    exit();
                } else {
                    $errors[] = 'Invalid matriculation number or password.';
                }
            } else {
                $errors[] = 'Invalid matriculation number or password.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Login failed. Please try again.';
        }
    }

    // If errors, redirect back with errors
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../authentications/student_login.html');
        exit();
    }
} else {
    // If not POST, redirect to login page
    header('Location: ../authentications/student_login.html');
    exit();
}
?>
