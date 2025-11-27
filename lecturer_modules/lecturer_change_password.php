<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['lecturer_id'])) {
    header('Location: ../authentications/lecturer_login.html');
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];

$success_message = '';
$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Fetch current password hash
    $stmt = $pdo->prepare("SELECT password FROM lecturertbl WHERE lecturer_id = ?");
    $stmt->execute([$lecturer_id]);
    $lecturer = $stmt->fetch();

    if (!password_verify($current_password, $lecturer['password'])) {
        $error_messages[] = 'Current password is incorrect.';
    }

    if (strlen($new_password) < 6) {
        $error_messages[] = 'New password must be at least 6 characters long.';
    }

    if ($new_password !== $confirm_password) {
        $error_messages[] = 'New password and confirmation do not match.';
    }

    if (empty($error_messages)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE lecturertbl SET password = ? WHERE lecturer_id = ?");
        if ($updateStmt->execute([$hashed_password, $lecturer_id])) {
            $success_message = 'Password changed successfully.';
        } else {
            $error_messages[] = 'Failed to update password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(to right, #e2e2e2, #c9d6ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-container {
            max-width: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        .btn-custom {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        .position-relative {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="password-container">
        <div class="text-center mb-4">
            <h2><i class="bi bi-key-fill me-2"></i>Change Password</h2>
            <p class="text-muted">Update your account password</p>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_messages)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo htmlspecialchars(implode(' ', $error_messages)); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                    <i class="bi bi-eye password-toggle" onclick="togglePassword('current_password')"></i>
                </div>
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                    <i class="bi bi-eye password-toggle" onclick="togglePassword('new_password')"></i>
                </div>
                <div class="form-text">Password must be at least 6 characters long.</div>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <i class="bi bi-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-warning btn-custom">
                    <i class="bi bi-check-lg me-2"></i>Change Password
                </button>
                <a href="lecturer_profile.php" class="btn btn-secondary btn-custom">
                    <i class="bi bi-arrow-left me-2"></i>Back to Profile
                </a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>