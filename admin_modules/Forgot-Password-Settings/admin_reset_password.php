<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT admin_id, reset_expires FROM admintbl WHERE reset_token = ?");
    $stmt->execute([$token]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin || strtotime($admin['reset_expires']) < time()) {
        echo "Invalid or expired token.";
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: admin_reset_password.php?token=$token");
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters.";
        header("Location: admin_reset_password.php?token=$token");
        exit();
    }

    $stmt = $pdo->prepare("SELECT admin_id FROM admintbl WHERE reset_token = ? AND reset_expires >= NOW()");
    $stmt->execute([$token]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo "Invalid or expired token.";
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE admintbl SET password = ?, reset_token = NULL, reset_expires = NULL WHERE admin_id = ?");
    $stmt->execute([$hashed, $admin['admin_id']]);

    echo "Password successfully reset. You can now <a href='../../authentications/admin_login.html'>login</a>.";
    exit();
} else {
    echo "Invalid request.";
    exit();
}
?>
