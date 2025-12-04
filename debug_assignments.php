<?php
session_start();
require_once 'config/db.php';

// Set a test student ID (replace with actual logged-in student)
$student_id = $_SESSION['student_id'] ?? 4; // Use student ID 4 as example

echo "<h3>Debug: Assignment Query</h3>";

try {
    // Check student's course registrations
    echo "<h4>Student's Course Registrations:</h4>";
    $stmt = $pdo->prepare("SELECT cr.*, c.course_code, c.course_title FROM course_regtbl cr JOIN coursetbl c ON cr.course_id = c.course_id WHERE cr.student_id = ?");
    $stmt->execute([$student_id]);
    $registrations = $stmt->fetchAll();
    
    foreach ($registrations as $reg) {
        echo "Course: {$reg['course_code']} - {$reg['course_title']} | Status: {$reg['approval_status']}<br>";
    }
    
    echo "<h4>All Assignments:</h4>";
    $stmt = $pdo->prepare("SELECT a.*, c.course_code FROM assignmenttbl a JOIN coursetbl c ON a.course_id = c.course_id ORDER BY a.assignment_id");
    $stmt->execute();
    $all_assignments = $stmt->fetchAll();
    
    foreach ($all_assignments as $assignment) {
        echo "Assignment: {$assignment['title']} | Course: {$assignment['course_code']} | Active: {$assignment['is_active']}<br>";
    }
    
    echo "<h4>Assignments Query Result:</h4>";
    $stmt = $pdo->prepare("
        SELECT a.*, c.course_code, c.course_title,
               s.sub_id, s.submitted_at,
               CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name
        FROM assignmenttbl a
        JOIN coursetbl c ON a.course_id = c.course_id
        JOIN course_regtbl cr ON c.course_id = cr.course_id
        LEFT JOIN ass_subtbl s ON a.assignment_id = s.assignment_id AND s.student_id = ?
        LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
        WHERE cr.student_id = ? AND cr.approval_status IN ('Approved', 'Registered')
        AND a.is_active = 1
        ORDER BY a.due_date ASC
    ");
    $stmt->execute([$student_id, $student_id]);
    $assignments = $stmt->fetchAll();
    
    echo "Found " . count($assignments) . " assignments<br>";
    foreach ($assignments as $assignment) {
        echo "- {$assignment['title']} ({$assignment['course_code']}) - Due: {$assignment['due_date']}<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>