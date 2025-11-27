<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['lecturer_id'])) {
    header('Location: ../authentications/lecturer_login.html');
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];

// Fetch current lecturer info
$stmt = $pdo->prepare("SELECT First_name, Last_Name, Email, Phone_Num FROM lecturertbl WHERE LecturerID = ?");
$stmt->execute([$lecturer_id]);
$lecturer = $stmt->fetch();

$success_message = '';
$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_first_name = trim($_POST['first_name'] ?? '');
    $new_last_name = trim($_POST['last_name'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_phone = trim($_POST['phone'] ?? '');

    if (empty($new_first_name)) {
        $error_messages[] = 'Please enter your first name.';
    }

    if (empty($new_last_name)) {
        $error_messages[] = 'Please enter your last name.';
    }

    if (empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_messages[] = 'Please enter a valid email address.';
    }

    if (empty($new_phone) || !preg_match('/^\+?[0-9\s\-\(\)]+$/', $new_phone)) {
        $error_messages[] = 'Please enter a valid phone number.';
    }

    if (empty($error_messages)) {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM lecturertbl WHERE Email = ? AND LecturerID != ?");
        $checkStmt->execute([$new_email, $lecturer_id]);
        if ($checkStmt->fetchColumn() > 0) {
            $error_messages[] = 'Email is already in use by another lecturer.';
        } else {
            $updateStmt = $pdo->prepare("UPDATE lecturertbl SET First_name = ?, Last_Name = ?, Email = ?, Phone_Num = ? WHERE LecturerID = ?");
            if ($updateStmt->execute([$new_first_name, $new_last_name, $new_email, $new_phone, $lecturer_id])) {
                $success_message = 'Contact information updated successfully.';
                $lecturer['First_name'] = $new_first_name;
                $lecturer['Last_Name'] = $new_last_name;
                $lecturer['Email'] = $new_email;
                $lecturer['Phone_Num'] = $new_phone;
            } else {
                $error_messages[] = 'Failed to update contact information.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact Information</title>
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
        .edit-container {
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
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="text-center mb-4">
            <h2><i class="bi bi-pencil-square me-2"></i>Edit Personal Information</h2>
            <p class="text-muted">Update your name, email and phone number</p>
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
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       value="<?php echo htmlspecialchars($lecturer['First_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       value="<?php echo htmlspecialchars($lecturer['Last_Name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($lecturer['Email']); ?>" required>
            </div>

            <div class="mb-4">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" 
                       value="<?php echo htmlspecialchars($lecturer['Phone_Num'] ?? ''); ?>" required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-custom">
                    <i class="bi bi-check-lg me-2"></i>Update Contact Info
                </button>
                <a href="lecturer_profile.php" class="btn btn-secondary btn-custom">
                    <i class="bi bi-arrow-left me-2"></i>Back to Profile
                </a>
            </div>
        </form>
    </div>
</body>
</html>