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
    $academic_year = htmlspecialchars(trim($_POST['academic_year']));
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
    if (empty($academic_year)) {
        $errors[] = "Academic year is required.";
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
            
            // Debug: Log the data being inserted
            error_log("Adding student with data: " . json_encode([
                'admin_id' => $_SESSION['admin_id'],
                'matric_no' => $matric_no,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone_num' => $phone_num,
                'department' => $department,
                'level' => $level,
                'academic_year' => $academic_year,
                'gender' => $gender
            ]));
            
            $stmt = $pdo->prepare("INSERT INTO studenttbl (AdminID, Matric_No, first_name, last_name, email, Phone_Num, password, Department, Level, academic_year, Gender, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$_SESSION['admin_id'], $matric_no, $first_name, $last_name, $email, $phone_num, $hashed_password, $department, $level, $academic_year, $gender]);
            
            if (!$result) {
                $errors[] = "Failed to execute insert statement: " . implode(', ', $stmt->errorInfo());
            } else {
                // Log the activity
                $student_id = $pdo->lastInsertId();
                try {
                    $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
                    $activity_stmt->execute(['add_student', "Added new student: $first_name $last_name ($matric_no)", $_SESSION['admin_id'], 'admin']);
                } catch (PDOException $activity_error) {
                    // Log activity error but don't fail the student creation
                    error_log("Activity log error: " . $activity_error->getMessage());
                }
                $success = true;
            }
        } catch (PDOException $e) {
            error_log("PDO Error in add_student.php: " . $e->getMessage());
            $errors[] = "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            error_log("General Error in add_student.php: " . $e->getMessage());
            $errors[] = "Server error: " . $e->getMessage();
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
