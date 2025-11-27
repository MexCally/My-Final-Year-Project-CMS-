<?php
session_start();
require_once 'db.php'; // your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address.";
        header("Location: ../../authentications/admin_login.html");
        exit();
    }

    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT admin_id, first_name FROM admintbl WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            $_SESSION['error'] = "No account found with that email.";
            header("Location: ../../authentications/admin_login.html");
            exit();
        }

        // Generate token
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour')); // token valid for 1 hour

        // Save token and expiration
        $stmt = $pdo->prepare("UPDATE admintbl SET reset_token = ?, reset_expires = ? WHERE admin_id = ?");
        $stmt->execute([$token, $expires, $admin['admin_id']]);

        // Prepare reset link
        $resetLink = "https://yourdomain.com/admin_reset_password.php?token=$token";

        // Send email (you can configure your SMTP or PHP mail)
        $subject = "Admin Password Reset Request";
        $message = "Hi " . $admin['first_name'] . ",\n\n";
        $message .= "We received a request to reset your password.\n";
        $message .= "Click the link below to reset your password (valid for 1 hour):\n\n";
        $message .= $resetLink . "\n\n";
        $message .= "If you did not request a password reset, please ignore this email.";

        $headers = "From: no-reply@yourdomain.com";

        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['success'] = "Password reset link sent to your email.";
        } else {
            $_SESSION['error'] = "Failed to send email. Try again later.";
        }

        header("Location: ../../authentications/admin_login.html");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../../authentications/admin_login.html");
        exit();
    }
} else {
    header("Location: ../../authentications/admin_login.html");
    exit();
}
