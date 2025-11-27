<?php
session_start();
require_once '../config/db.php';

// Restrict to logged-in students
if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];
$message = '';
$message_type = '';

// Fetch current password hash
$stmt = $pdo->prepare("SELECT password FROM studenttbl WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    session_destroy();
    header('Location: ../authentications/student_login.html');
    exit();
}

$current_hashed_password = $student['password'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!password_verify($old_password, $current_hashed_password)) {
        $message = "🚫 <strong>Error:</strong> Old password is incorrect!";
        $message_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = "❌ <strong>Error:</strong> New password and confirmation do not match!";
        $message_type = 'error';
    } elseif (strlen($new_password) < 8) {
        $message = "⚠️ <strong>Error:</strong> New password must be at least 8 characters long.";
        $message_type = 'error';
    } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE studenttbl SET password = ? WHERE student_id = ?");

        if ($stmt->execute([$new_hashed_password, $student_id])) {
            $message = "✅ Your password has been updated successfully!";
            $message_type = 'success';
        } else {
            $message = "❌ <strong>Error:</strong> Failed to update password in the database.";
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to right, #e2e2e2, #c9d6ff);
            color: #333;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            animation: fadeInUp 0.8s ease-in-out;
        }
        h2 {
            text-align: center;
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        .password-container { position: relative; }
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            z-index: 10;
        }
        button[type="submit"] {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        button[type="submit"]:active {
            transform: translateY(0);
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background-color: #5a6268;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Change Password</h2>

    <?php if ($message): ?>
        <div class="alert <?= $message_type; ?>">
            <?= $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="old_password">Current Password:</label>
            <div class="password-container">
                <input type="password" id="old_password" name="old_password" required autofocus>
                <i class="bi bi-eye password-toggle" data-target="old_password"></i>
            </div>
        </div>

        <div class="form-group">
            <label for="new_password">New Password (min 8 characters):</label>
            <div class="password-container">
                <input type="password" id="new_password" name="new_password" required>
                <i class="bi bi-eye password-toggle" data-target="new_password"></i>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <div class="password-container">
                <input type="password" id="confirm_password" name="confirm_password" required>
                <i class="bi bi-eye password-toggle" data-target="confirm_password"></i>
            </div>
        </div>

        <button type="submit">Update Password</button>
    </form>

    <a href="../student_modules/student_profile.php" class="back-link">
        <i class="bi bi-arrow-left-circle me-2"></i> Back to Profile
    </a>
</div>

<script>
    document.querySelectorAll('.password-toggle').forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const type = targetInput.getAttribute('type') === 'password' ? 'text' : 'password';
            targetInput.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    });
</script>

</body>
</html>


