<?php
session_start();
require_once '../config/db.php';

// Sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Check POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../authentications/admin_login.html");
    exit();
}

$email = sanitize_input($_POST['email']);
$password = $_POST['password'];

$errors = [];

// Validate fields
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email is required.";
}

if (empty($password)) {
    $errors[] = "Password is required.";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../authentications/admin_login.html");
    exit();
}

try {
    // Fetch admin by email
    $stmt = $pdo->prepare("SELECT admin_id, first_name, last_name, email, password FROM admintbl WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        $errors[] = "Invalid email or password.";
        $_SESSION['errors'] = $errors;
        header("Location: ../authentications/admin_login.html");
        exit();
    }

    // Verify password
    if (!password_verify($password, $admin['password'])) {
        $errors[] = "Invalid email or password.";
        $_SESSION['errors'] = $errors;
        header("Location: ../authentications/admin_login.html");
        exit();
    }

    // Login success — store session
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_name'] = $admin['first_name'] . " " . $admin['last_name'];
    $_SESSION['admin_email'] = $admin['email'];

    // Redirect to dashboard
    header("Location: ../admin_modules/admin_dashboard.php");
    exit();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
