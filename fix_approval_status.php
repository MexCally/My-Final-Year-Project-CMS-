<?php
session_start();
require_once 'config/db.php';

echo "<h2>Fix Approval Status Issue</h2>";

$student_id = 1;

// First, let's check the table structure
echo "<h3>Checking course_regtbl structure:</h3>";
try {
    $structureStmt = $pdo->prepare("DESCRIBE course_regtbl");
    $structureStmt->execute();
    $columns = $structureStmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking table structure: " . $e->getMessage() . "</p>";
}

// Check current registrations
echo "<h3>Current registrations for student $student_id:</h3>";
$currentStmt = $pdo->prepare("SELECT * FROM course_regtbl WHERE student_id = ?");
$currentStmt->execute([$student_id]);
$current = $currentStmt->fetchAll();

if (!empty($current)) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr>";
    foreach (array_keys($current[0]) as $key) {
        if (!is_numeric($key)) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
    }
    echo "</tr>";
    
    foreach ($current as $row) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            if (!is_numeric($key)) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Try to manually update the approval status
echo "<h3>Manually updating approval status:</h3>";
try {
    $updateStmt = $pdo->prepare("UPDATE course_regtbl SET approval_status = 'approved', approved_by = 5, date_approved = NOW() WHERE student_id = ?");
    $result = $updateStmt->execute([$student_id]);
    
    if ($result) {
        $rowsAffected = $updateStmt->rowCount();
        echo "<p style='color: green;'>✅ Update successful! Rows affected: $rowsAffected</p>";
    } else {
        echo "<p style='color: red;'>❌ Update failed</p>";
        print_r($updateStmt->errorInfo());
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Update error: " . $e->getMessage() . "</p>";
}

// Verify the update
echo "<h3>Verification after update:</h3>";
$verifyStmt = $pdo->prepare("SELECT * FROM course_regtbl WHERE student_id = ?");
$verifyStmt->execute([$student_id]);
$verified = $verifyStmt->fetchAll();

if (!empty($verified)) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr>";
    foreach (array_keys($verified[0]) as $key) {
        if (!is_numeric($key)) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
    }
    echo "</tr>";
    
    foreach ($verified as $row) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            if (!is_numeric($key)) {
                $style = ($key === 'approval_status' && $value === 'approved') ? 'color: green; font-weight: bold;' : '';
                echo "<td style='$style'>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Final check for approved count
echo "<h3>Final approved count check:</h3>";
$finalCountStmt = $pdo->prepare("SELECT COUNT(*) FROM course_regtbl WHERE student_id = ? AND approval_status = 'approved'");
$finalCountStmt->execute([$student_id]);
$finalCount = $finalCountStmt->fetchColumn();

echo "<p>Approved registrations count: <strong>$finalCount</strong></p>";

if ($finalCount > 0) {
    echo "<p style='color: green; font-size: 18px;'>✅ SUCCESS! Download button should now be visible.</p>";
    echo "<p><a href='student_modules/student_dashboard.php' target='_blank'>Go to Student Dashboard</a></p>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ Still no approved registrations found.</p>";
}
?>