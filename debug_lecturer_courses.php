<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['lecturer_id'])) {
    echo "No lecturer logged in";
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];
echo "Session lecturer_id: " . $lecturer_id . "<br>";
echo "Data type: " . gettype($lecturer_id) . "<br><br>";

try {
    // Check all courses and their lecturer_id values
    $stmt = $pdo->prepare("SELECT course_id, course_code, lecturer_id FROM coursetbl ORDER BY course_id");
    $stmt->execute();
    $all_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "All courses in database:<br>";
    foreach ($all_courses as $course) {
        echo "Course ID: {$course['course_id']}, Code: {$course['course_code']}, Lecturer ID: {$course['lecturer_id']} (type: " . gettype($course['lecturer_id']) . ")<br>";
    }
    
    echo "<br>Courses for lecturer_id = $lecturer_id:<br>";
    
    // Test the exact query used in dashboard
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM coursetbl WHERE lecturer_id = ?");
    $stmt->execute([$lecturer_id]);
    $count = $stmt->fetch()['count'];
    echo "Count result: " . $count . "<br><br>";
    
    // Test with string comparison
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM coursetbl WHERE CAST(lecturer_id AS CHAR) = CAST(? AS CHAR)");
    $stmt->execute([$lecturer_id]);
    $count_str = $stmt->fetch()['count'];
    echo "Count with string cast: " . $count_str . "<br><br>";
    
    // Get actual courses
    $stmt = $pdo->prepare("SELECT course_id, course_code, course_title FROM coursetbl WHERE lecturer_id = ?");
    $stmt->execute([$lecturer_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Actual courses found:<br>";
    foreach ($courses as $course) {
        echo "- {$course['course_code']}: {$course['course_title']}<br>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>