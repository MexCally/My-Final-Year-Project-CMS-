<?php
session_start();
require_once 'config/db.php';

$student_id = 1; // The student we're testing with
$admin_id = 5;   // Admin ID for approval

echo "<h2>Create Test Course Registrations</h2>";

// First, let's see what courses are available
echo "<h3>Available Courses:</h3>";
$coursesStmt = $pdo->prepare("SELECT course_id, course_code, course_title, course_unit FROM coursetbl LIMIT 10");
$coursesStmt->execute();
$courses = $coursesStmt->fetchAll();

if (empty($courses)) {
    echo "<p style='color: red;'>❌ No courses found in database</p>";
    exit;
}

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Course ID</th><th>Course Code</th><th>Course Title</th><th>Units</th></tr>";
foreach ($courses as $course) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($course['course_id']) . "</td>";
    echo "<td>" . htmlspecialchars($course['course_code']) . "</td>";
    echo "<td>" . htmlspecialchars($course['course_title']) . "</td>";
    echo "<td>" . htmlspecialchars($course['course_unit']) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Clean up any existing registrations for this student
echo "<h3>Cleaning up existing registrations...</h3>";
$cleanupStmt = $pdo->prepare("DELETE FROM course_regtbl WHERE student_id = ?");
$cleanupStmt->execute([$student_id]);
echo "<p>✅ Cleaned up existing registrations</p>";

// Register student for first 5 courses
echo "<h3>Creating course registrations...</h3>";
$selectedCourses = array_slice($courses, 0, 5);
$registrationCount = 0;

foreach ($selectedCourses as $course) {
    try {
        $regStmt = $pdo->prepare("INSERT INTO course_regtbl (student_id, course_id, academic_year, semester, date_registered, approval_status) VALUES (?, ?, ?, ?, NOW(), 'pending')");
        $result = $regStmt->execute([$student_id, $course['course_id'], '2024/2025', 'First Semester']);
        
        if ($result) {
            $registrationCount++;
            echo "<p>✅ Registered for " . htmlspecialchars($course['course_code']) . " - " . htmlspecialchars($course['course_title']) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to register for " . htmlspecialchars($course['course_code']) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error registering for " . htmlspecialchars($course['course_code']) . ": " . $e->getMessage() . "</p>";
    }
}

echo "<p><strong>Total registrations created: $registrationCount</strong></p>";

// Now approve all registrations
echo "<h3>Approving registrations...</h3>";
$approveStmt = $pdo->prepare("UPDATE course_regtbl SET approval_status = 'approved', approved_by = ?, date_approved = NOW() WHERE student_id = ?");
$approveResult = $approveStmt->execute([$admin_id, $student_id]);

if ($approveResult) {
    $approvedCount = $approveStmt->rowCount();
    echo "<p style='color: green;'>✅ Approved $approvedCount registrations</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to approve registrations</p>";
}

// Verify the results
echo "<h3>Final Verification:</h3>";
$verifyStmt = $pdo->prepare("SELECT COUNT(*) FROM course_regtbl WHERE student_id = ? AND approval_status = 'approved'");
$verifyStmt->execute([$student_id]);
$approvedCount = $verifyStmt->fetchColumn();

echo "<p>Approved registrations for student $student_id: <strong>$approvedCount</strong></p>";

if ($approvedCount > 0) {
    echo "<p style='color: green; font-size: 18px;'>✅ SUCCESS! Download button should now be visible on the student dashboard.</p>";
    echo "<p><a href='student_modules/student_dashboard.php' target='_blank'>Go to Student Dashboard</a></p>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ Something went wrong. No approved registrations found.</p>";
}

// Show the created registrations
echo "<h3>Created Registrations:</h3>";
$detailsStmt = $pdo->prepare("
    SELECT 
        cr.*,
        c.course_code,
        c.course_title
    FROM course_regtbl cr
    JOIN coursetbl c ON cr.course_id = c.course_id
    WHERE cr.student_id = ?
    ORDER BY cr.reg_id
");
$detailsStmt->execute([$student_id]);
$details = $detailsStmt->fetchAll();

if (!empty($details)) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Reg ID</th><th>Course Code</th><th>Course Title</th><th>Status</th><th>Date Registered</th><th>Date Approved</th></tr>";
    foreach ($details as $detail) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($detail['reg_id']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['course_code']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['course_title']) . "</td>";
        echo "<td style='color: " . ($detail['approval_status'] === 'approved' ? 'green' : 'orange') . ";'>" . htmlspecialchars($detail['approval_status']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['date_registered']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['date_approved'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>