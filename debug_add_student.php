<?php
session_start();
require_once 'config/db.php';

// Set admin session for testing
$_SESSION['admin_id'] = 5;
$_SESSION['admin_name'] = 'Test Admin';

echo "<h2>Debug Add Student Issue</h2>";

// Simulate the exact POST data that would come from the form
$_POST = [
    'matric_no' => 'DEBUG123',
    'first_name' => 'Debug',
    'last_name' => 'Student',
    'email' => 'debug@test.com',
    'phone_num' => '1234567890',
    'department' => 'Computer Science',
    'level' => 'ND 1',
    'academic_year' => '2024/2025',
    'gender' => 'Male',
    'password' => 'debugpass123'
];

echo "<h3>Simulated POST data:</h3>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

// Clean up any existing test student
try {
    $cleanup = $pdo->prepare("DELETE FROM studenttbl WHERE Matric_No = ? OR email = ?");
    $cleanup->execute(['DEBUG123', 'debug@test.com']);
    echo "<p>✅ Cleaned up any existing test student</p>";
} catch (Exception $e) {
    echo "<p>⚠️ Cleanup warning: " . $e->getMessage() . "</p>";
}

// Now run the exact same logic as add_student.php
$errors = [];
$success = false;

// Sanitize inputs (same as add_student.php)
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

echo "<h3>Sanitized data:</h3>";
echo "<pre>";
echo "matric_no: '$matric_no'\n";
echo "first_name: '$first_name'\n";
echo "last_name: '$last_name'\n";
echo "email: '$email'\n";
echo "phone_num: '$phone_num'\n";
echo "department: '$department'\n";
echo "level: '$level'\n";
echo "academic_year: '$academic_year'\n";
echo "gender: '$gender'\n";
echo "password length: " . strlen($password) . "\n";
echo "</pre>";

// Validation (same as add_student.php)
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

echo "<h3>Validation Results:</h3>";
if (empty($errors)) {
    echo "<p style='color: green;'>✅ All validation passed</p>";
} else {
    echo "<p style='color: red;'>❌ Validation errors:</p>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
}

// Check uniqueness (same as add_student.php)
if (empty($errors)) {
    try {
        $stmt = $pdo->prepare("SELECT student_id FROM studenttbl WHERE Matric_No = ? OR email = ?");
        $stmt->execute([$matric_no, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Matriculation number or email already exists.";
            echo "<p style='color: red;'>❌ Student already exists</p>";
        } else {
            echo "<p style='color: green;'>✅ No duplicate student found</p>";
        }
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
        echo "<p style='color: red;'>❌ Database error during uniqueness check: " . $e->getMessage() . "</p>";
    }
}

// Insert if no errors (same as add_student.php)
if (empty($errors)) {
    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        echo "<p>🔐 Password hashed successfully</p>";
        
        echo "<h3>Attempting database insert...</h3>";
        $stmt = $pdo->prepare("INSERT INTO studenttbl (AdminID, Matric_No, first_name, last_name, email, Phone_Num, password, Department, Level, academic_year, Gender, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $insert_data = [
            $_SESSION['admin_id'],
            $matric_no,
            $first_name,
            $last_name,
            $email,
            $phone_num,
            $hashed_password,
            $department,
            $level,
            $academic_year,
            $gender
        ];
        
        echo "<p>Insert data:</p>";
        echo "<pre>" . print_r($insert_data, true) . "</pre>";
        
        $result = $stmt->execute($insert_data);
        
        if (!$result) {
            $errors[] = "Failed to execute insert statement: " . implode(', ', $stmt->errorInfo());
            echo "<p style='color: red;'>❌ Insert failed: " . implode(', ', $stmt->errorInfo()) . "</p>";
        } else {
            // Log the activity
            $student_id = $pdo->lastInsertId();
            echo "<p style='color: green;'>✅ Student inserted successfully! Student ID: $student_id</p>";
            
            try {
                $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
                $activity_result = $activity_stmt->execute(['add_student', "Added new student: $first_name $last_name ($matric_no)", $_SESSION['admin_id'], 'admin']);
                
                if ($activity_result) {
                    echo "<p style='color: green;'>✅ Activity logged successfully!</p>";
                } else {
                    echo "<p style='color: orange;'>⚠️ Student added but activity logging failed: " . implode(', ', $activity_stmt->errorInfo()) . "</p>";
                }
            } catch (PDOException $activity_error) {
                echo "<p style='color: orange;'>⚠️ Student added but activity logging failed: " . $activity_error->getMessage() . "</p>";
            }
            $success = true;
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ PDO Error: " . $e->getMessage() . "</p>";
        echo "<p>Error Code: " . $e->getCode() . "</p>";
        $errors[] = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ General Error: " . $e->getMessage() . "</p>";
        $errors[] = "Server error: " . $e->getMessage();
    }
}

// Final result
echo "<h3>Final Result:</h3>";
if ($success) {
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ SUCCESS: Student added successfully!</p>";
} else {
    echo "<p style='color: red; font-size: 18px; font-weight: bold;'>❌ FAILED: Could not add student</p>";
    if (!empty($errors)) {
        echo "<p>Errors:</p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
}

// Clean up test student
try {
    $cleanup = $pdo->prepare("DELETE FROM studenttbl WHERE Matric_No = ? OR email = ?");
    $cleanup->execute(['DEBUG123', 'debug@test.com']);
    echo "<p>🧹 Test student cleaned up</p>";
} catch (Exception $e) {
    echo "<p>⚠️ Cleanup warning: " . $e->getMessage() . "</p>";
}
?>