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
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        try {
            // Fetch lecturer by email - using proper column names with aliases for compatibility
            $stmt = $pdo->prepare("SELECT LecturerID as lecturer_id, First_name as first_name, Last_Name as last_name, Email as email, Password as password FROM lecturertbl WHERE Email = ?");
            $stmt->execute([$email]);
            $lecturer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($lecturer) {
                // Check password (support both hashed and plain text for backward compatibility)
                $password_valid = false;
                if (password_verify($password, $lecturer['password'])) {
                    // Password is hashed
                    $password_valid = true;
                } elseif ($lecturer['password'] === $password) {
                    // Password is plain text (for existing users)
                    $password_valid = true;
                    // Optionally hash and update the password for future logins
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("UPDATE lecturertbl SET Password = ? WHERE LecturerID = ?");
                    $update_stmt->execute([$hashed_password, $lecturer['lecturer_id']]);
                }

                if ($password_valid) {
                    // Login successful - start session
                    $_SESSION['lecturer_id'] = $lecturer['lecturer_id'];
                    $_SESSION['lecturer_name'] = $lecturer['first_name'] . ' ' . $lecturer['last_name'];
                    $_SESSION['lecturer_email'] = $lecturer['email'];

                    // Redirect to dashboard
                    header('Location: ../lecturer_modules/lecturer_dashboard.php');
                    exit();
                } else {
                    $errors[] = 'Invalid email or password.';
                }
            } else {
                $errors[] = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Login failed. Please try again.';
        }
    }

    // If errors, redirect back with errors
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../authentications/lecturer_login.html');
        exit();
    }
} else {
    // If not POST, redirect to login page
    header('Location: ../authentications/lecturer_login.html');
    exit();
}
?>
