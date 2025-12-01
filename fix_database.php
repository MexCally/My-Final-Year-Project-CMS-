<?php
require_once 'config/db.php';

echo "<h2>Database Fix Script</h2>";

try {
    // Check current table structure
    echo "<h3>Current studenttbl structure:</h3>";
    $stmt = $pdo->query("DESCRIBE studenttbl");
    $columns = $stmt->fetchAll();
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check for students with empty academic_year
    echo "<h3>Students with empty academic_year:</h3>";
    $stmt = $pdo->query("SELECT student_id, first_name, last_name, academic_year FROM studenttbl WHERE academic_year = '' OR academic_year IS NULL");
    $empty_students = $stmt->fetchAll();
    
    if (count($empty_students) > 0) {
        echo "<p>Found " . count($empty_students) . " students with empty academic_year:</p>";
        echo "<ul>";
        foreach ($empty_students as $student) {
            echo "<li>ID: {$student['student_id']}, Name: {$student['first_name']} {$student['last_name']}, Academic Year: '{$student['academic_year']}'</li>";
        }
        echo "</ul>";
        
        // Fix empty academic years
        echo "<h3>Fixing empty academic years...</h3>";
        $update_stmt = $pdo->prepare("UPDATE studenttbl SET academic_year = '2024/2025' WHERE academic_year = '' OR academic_year IS NULL");
        $result = $update_stmt->execute();
        
        if ($result) {
            echo "<p style='color: green;'>✅ Fixed empty academic years</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to fix academic years</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ No students with empty academic_year found</p>";
    }

    // Test insert with proper data
    echo "<h3>Testing student insert...</h3>";
    
    // Delete test student if exists
    $pdo->prepare("DELETE FROM studenttbl WHERE Matric_No = 'TEST999'")->execute();
    
    $test_stmt = $pdo->prepare("INSERT INTO studenttbl (AdminID, Matric_No, first_name, last_name, email, Phone_Num, password, Department, Level, academic_year, Gender, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $test_result = $test_stmt->execute([
        5, // Admin ID
        'TEST999',
        'Test',
        'User',
        'test999@example.com',
        '1234567890',
        password_hash('testpass123', PASSWORD_DEFAULT),
        'Computer Science',
        'ND 1',
        '2024/2025',
        'Male'
    ]);
    
    if ($test_result) {
        echo "<p style='color: green;'>✅ Test student insert successful!</p>";
        
        // Clean up test student
        $pdo->prepare("DELETE FROM studenttbl WHERE Matric_No = 'TEST999'")->execute();
        echo "<p>Test student cleaned up.</p>";
    } else {
        echo "<p style='color: red;'>❌ Test student insert failed</p>";
        echo "<p>Error: " . implode(', ', $test_stmt->errorInfo()) . "</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>