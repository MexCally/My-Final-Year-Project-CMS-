 <?php
session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'errors' => ['Unauthorized access.']]);
    exit();
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $success = false;

    // Sanitize inputs
    $matric_no = htmlspecialchars(trim($_POST['matric_no']));
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone_num = htmlspecialchars(trim($_POST['phone_num']));
    $department = htmlspecialchars(trim($_POST['department']));
    $level = htmlspecialchars(trim($_POST['level']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $password = $_POST['password'];

    // Validation
    if (empty($matric_no)) {
        $errors[] = "Matriculation number is required.";
    }
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($phone_num) || !preg_match('/^[0-9+\-\s()]{10,15}$/', $phone_num)) {
        $errors[] = "Valid phone number is required.";
    }
    if (empty($department)) {
        $errors[] = "Department is required.";
    }
    if (empty($level)) {
        $errors[] = "Level is required.";
    }
    if (empty($gender) || !in_array($gender, ['Male', 'Female'])) {
        $errors[] = "Valid gender is required.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check uniqueness
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT student_id FROM studenttbl WHERE Matric_No = ? OR email = ?");
            $stmt->execute([$matric_no, $email]);
            if ($stmt->fetch()) {
                $errors[] = "Matriculation number or email already exists.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Insert if no errors
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO studenttbl (AdminID, Matric_No, first_name, last_name, email, Phone_Num, password, Department, Level, Gender, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$_SESSION['admin_id'], $matric_no, $first_name, $last_name, $email, $phone_num, $hashed_password, $department, $level, $gender]);

            // Log the activity
            $student_id = $pdo->lastInsertId();
            $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
            $activity_stmt->execute(['add_student', "Added new student: $first_name $last_name ($matric_no)", $_SESSION['admin_id'], 'admin']);

            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Failed to add student: " . $e->getMessage();
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'errors' => $errors]);
    exit();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'errors' => ['Method not allowed.']]);
}
?>
