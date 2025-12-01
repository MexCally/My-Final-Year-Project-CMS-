<?php
session_start();
require_once 'config/db.php';

// Get the actual student ID from session or set for testing
if (!isset($_SESSION['student_id'])) {
    $_SESSION['student_id'] = 1; // Use the student ID you're testing with
}

$student_id = $_SESSION['student_id'];

echo "<h2>Debug Download Button Issue</h2>";
echo "<p>Student ID: $student_id</p>";

// Check course registrations
echo "<h3>All Course Registrations for this Student:</h3>";
$allRegsStmt = $pdo->prepare("SELECT * FROM course_regtbl WHERE student_id = ?");
$allRegsStmt->execute([$student_id]);
$allRegs = $allRegsStmt->fetchAll();

if (empty($allRegs)) {
    echo "<p style='color: red;'>❌ No registrations found for student ID $student_id</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Reg ID</th><th>Course ID</th><th>Academic Year</th><th>Semester</th><th>Approval Status</th><th>Date Registered</th><th>Approved By</th><th>Date Approved</th></tr>";
    foreach ($allRegs as $reg) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($reg['reg_id']) . "</td>";
        echo "<td>" . htmlspecialchars($reg['course_id']) . "</td>";
        echo "<td>" . htmlspecialchars($reg['academic_year']) . "</td>";
        echo "<td>" . htmlspecialchars($reg['semester']) . "</td>";
        echo "<td style='color: " . ($reg['approval_status'] === 'approved' ? 'green' : 'orange') . ";'>" . htmlspecialchars($reg['approval_status']) . "</td>";
        echo "<td>" . htmlspecialchars($reg['date_registered']) . "</td>";
        echo "<td>" . htmlspecialchars($reg['approved_by'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($reg['date_approved'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check the exact query used in dashboard
echo "<h3>Approved Registrations Check (Dashboard Query):</h3>";
$approvedCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM course_regtbl WHERE student_id = ? AND approval_status = 'approved'");
$approvedCheckStmt->execute([$student_id]);
$approvedCount = $approvedCheckStmt->fetchColumn();

echo "<p>Approved registrations count: <strong>$approvedCount</strong></p>";
echo "<p>Has approved registrations: <strong>" . ($approvedCount > 0 ? 'YES' : 'NO') . "</strong></p>";

if ($approvedCount > 0) {
    echo "<p style='color: green;'>✅ Download button should be visible</p>";
    
    // Get details of approved registrations
    echo "<h3>Approved Registration Details:</h3>";
    $approvedDetailsStmt = $pdo->prepare("
        SELECT 
            cr.*,
            c.course_code,
            c.course_title
        FROM course_regtbl cr
        JOIN coursetbl c ON cr.course_id = c.course_id
        WHERE cr.student_id = ? AND cr.approval_status = 'approved'
    ");
    $approvedDetailsStmt->execute([$student_id]);
    $approvedDetails = $approvedDetailsStmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Course Code</th><th>Course Title</th><th>Academic Year</th><th>Semester</th><th>Date Approved</th></tr>";
    foreach ($approvedDetails as $detail) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($detail['course_code']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['course_title']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['academic_year']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['semester']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['date_approved'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Download button should NOT be visible</p>";
}

// Test the download URL
if ($approvedCount > 0) {
    echo "<h3>Test Download URLs:</h3>";
    echo "<p><a href='PHP/download_course_form.php?student_id=$student_id&format=pdf' target='_blank'>Test PDF Download</a></p>";
    echo "<p><a href='PHP/download_course_form.php?student_id=$student_id&format=html' target='_blank'>Test HTML Download</a></p>";
}
?>