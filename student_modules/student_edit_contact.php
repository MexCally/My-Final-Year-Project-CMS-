<?php
session_start();
require_once '../config/db.php';

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];
$message = '';
$message_type = '';

// Fetch existing contact info
$stmt = $pdo->prepare("SELECT first_name, last_name, email, Phone_Num FROM studenttbl WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    session_unset();
    session_destroy();
    header('Location: ../authentications/student_login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone_num'] ?? '');

    $errors = [];

    if ($first_name === '' || !preg_match('/^[a-zA-Z\s]+$/', $first_name)) {
        $errors[] = 'Please enter a valid first name (letters and spaces only).';
    }
    if ($last_name === '' || !preg_match('/^[a-zA-Z\s]+$/', $last_name)) {
        $errors[] = 'Please enter a valid last name (letters and spaces only).';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($phone === '' || !preg_match('/^\+?[0-9\s\-\(\)]+$/', $phone)) {
        $errors[] = 'Please enter a valid phone number.';
    }

    // Check email uniqueness for other students
    if (empty($errors)) {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM studenttbl WHERE email = ? AND student_id != ?");
        $checkStmt->execute([$email, $student_id]);
        if ($checkStmt->fetchColumn() > 0) {
            $errors[] = 'Email is already in use by another student.';
        }
    }

    if (!empty($errors)) {
        $message = implode(' ', $errors);
        $message_type = 'error';
    } else {
        $updateStmt = $pdo->prepare("
            UPDATE studenttbl 
            SET first_name = ?, last_name = ?, email = ?, Phone_Num = ?
            WHERE student_id = ?
        ");
        if ($updateStmt->execute([$first_name, $last_name, $email, $phone, $student_id])) {
            $message = 'Contact information updated successfully.';
            $message_type = 'success';
            $student['first_name'] = $first_name;
            $student['last_name'] = $last_name;
            $student['email'] = $email;
            $student['Phone_Num'] = $phone;
        } else {
            $message = 'Failed to update contact information. Please try again.';
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
    <title>Edit Contact Info - Student</title>
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
            max-width: 500px;
            animation: fadeInUp 0.8s ease-in-out;
        }
        h2 {
            text-align: center;
            color: #333;
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; color: #555; }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #1d976c;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(29, 151, 108, 0.15);
            transform: translateY(-1px);
        }
        .alert {
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 6px;
            font-weight: 500;
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
      /* margin-bottom: 20px; */
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
        /* .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1d976c 0%, #93f9b9 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(29, 151, 108, 0.3);
        } */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
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
    <h2>Edit Contact Information</h2>

    <?php if ($message): ?>
        <div class="alert <?= $message_type; ?>">
            <?= $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required
                   value="<?php echo htmlspecialchars($student['first_name']); ?>">
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required
                   value="<?php echo htmlspecialchars($student['last_name']); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required
                   value="<?php echo htmlspecialchars($student['email']); ?>">
        </div>

        <div class="form-group">
            <label for="phone_num">Phone Number:</label>
            <input type="text" id="phone_num" name="phone_num" required
                   value="<?php echo htmlspecialchars($student['Phone_Num']); ?>">
        </div>

        <button type="submit" class="btn-primary">
            <i class="bi bi-save me-2"></i>Save Changes
        </button>
    </form>

    <a href="../student_modules/student_profile.php" class="back-link">
        <i class="bi bi-arrow-left-circle me-2"></i> Back to Profile
    </a>
</div>

</body>
</html>


