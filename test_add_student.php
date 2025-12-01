<?php
session_start();
require_once 'config/db.php';

// Set a test admin session
$_SESSION['admin_id'] = 5; // Using existing admin ID from database

// Test data
$test_data = [
    'matric_no' => 'TEST123',
    'first_name' => 'Test',
    'last_name' => 'Student',
    'email' => 'test@example.com',
    'phone_num' => '1234567890',
    'department' => 'Computer Science',
    'level' => 'ND 1',
    'academic_year' => '2024/2025',
    'gender' => 'Male',
    'password' => 'testpassword123'
];

echo "<h2>Testing Add Student Functionality</h2>";
echo "<h3>Test Data:</h3>";
echo "<pre>" . print_r($test_data, true) . "</pre>";

try {
    // Check if student already exists
    $stmt = $pdo->prepare("SELECT student_id FROM studenttbl WHERE Matric_No = ? OR email = ?");
    $stmt->execute([$test_data['matric_no'], $test_data['email']]);
    if ($stmt->fetch()) {
        echo "<p style='color: orange;'>Student with this matric number or email already exists. Deleting first...</p>";
        
        // Delete existing test student
        $delete_stmt = $pdo->prepare("DELETE FROM studenttbl WHERE Matric_No = ? OR email = ?");
        $delete_stmt->execute([$test_data['matric_no'], $test_data['email']]);
        echo "<p style='color: green;'>Existing test student deleted.</p>";
    }

    // Hash password
    $hashed_password = password_hash($test_data['password'], PASSWORD_DEFAULT);
    
    // Insert new student
    $stmt = $pdo->prepare("INSERT INTO studenttbl (AdminID, Matric_No, first_name, last_name, email, Phone_Num, password, Department, Level, academic_year, Gender, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $result = $stmt->execute([
        $_SESSION['admin_id'],
        $test_data['matric_no'],
        $test_data['first_name'],
        $test_data['last_name'],
        $test_data['email'],
        $test_data['phone_num'],
        $hashed_password,
        $test_data['department'],
        $test_data['level'],
        $test_data['academic_year'],
        $test_data['gender']
    ]);

    if ($result) {
        $student_id = $pdo->lastInsertId();
        echo "<p style='color: green;'>✅ Student added successfully! Student ID: $student_id</p>";
        
        // Log the activity
        $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
        $activity_result = $activity_stmt->execute([
            'add_student',
            "Added new student: {$test_data['first_name']} {$test_data['last_name']} ({$test_data['matric_no']})",
            $_SESSION['admin_id'],
            'admin'
        ]);
        
        if ($activity_result) {
            echo "<p style='color: green;'>✅ Activity logged successfully!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Student added but activity logging failed.</p>";
        }
        
        // Verify the student was added
        $verify_stmt = $pdo->prepare("SELECT * FROM studenttbl WHERE student_id = ?");
        $verify_stmt->execute([$student_id]);
        $student = $verify_stmt->fetch();
        
        echo "<h3>Verified Student Data:</h3>";
        echo "<pre>" . print_r($student, true) . "</pre>";
        
    } else {
        echo "<p style='color: red;'>❌ Failed to add student</p>";
        echo "<p>Error Info: " . print_r($stmt->errorInfo(), true) . "</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Error Code: " . $e->getCode() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ General Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Database Connection Test:</h3>";
try {
    $test_query = $pdo->query("SELECT COUNT(*) as count FROM studenttbl");
    $count = $test_query->fetch();
    echo "<p style='color: green;'>✅ Database connection working. Current student count: " . $count['count'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Admin Session Test:</h3>";
if (isset($_SESSION['admin_id'])) {
    echo "<p style='color: green;'>✅ Admin session exists. Admin ID: " . $_SESSION['admin_id'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ No admin session found</p>";
}
?>