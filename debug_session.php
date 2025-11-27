<?php
session_start();
require_once 'config/db.php';

echo "<h3>Session Debug Info</h3>";
echo "<p>Lecturer ID in session: " . ($_SESSION['lecturer_id'] ?? 'NOT SET') . "</p>";
echo "<p>Lecturer Name in session: " . ($_SESSION['lecturer_name'] ?? 'NOT SET') . "</p>";

echo "<h3>All Lecturers in Database</h3>";
try {
    $stmt = $pdo->prepare("SELECT LecturerID, First_name, Last_Name, Email FROM lecturertbl");
    $stmt->execute();
    $lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>LecturerID</th><th>Name</th><th>Email</th></tr>";
    foreach ($lecturers as $lecturer) {
        echo "<tr>";
        echo "<td>" . $lecturer['LecturerID'] . "</td>";
        echo "<td>" . $lecturer['First_name'] . " " . $lecturer['Last_Name'] . "</td>";
        echo "<td>" . $lecturer['Email'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<h3>All Materials in Database</h3>";
try {
    $stmt = $pdo->prepare("SELECT material_id, title, uploaded_by_lecturer_id, course_id FROM course_materialtbl");
    $stmt->execute();
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Material ID</th><th>Title</th><th>Uploaded By Lecturer ID</th><th>Course ID</th></tr>";
    foreach ($materials as $material) {
        echo "<tr>";
        echo "<td>" . $material['material_id'] . "</td>";
        echo "<td>" . $material['title'] . "</td>";
        echo "<td>" . $material['uploaded_by_lecturer_id'] . "</td>";
        echo "<td>" . $material['course_id'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<h3>All Courses in Database</h3>";
try {
    $stmt = $pdo->prepare("SELECT course_id, course_code, course_title, lecturer_id FROM coursetbl");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Course ID</th><th>Course Code</th><th>Course Title</th><th>Lecturer ID</th></tr>";
    foreach ($courses as $course) {
        echo "<tr>";
        echo "<td>" . $course['course_id'] . "</td>";
        echo "<td>" . $course['course_code'] . "</td>";
        echo "<td>" . $course['course_title'] . "</td>";
        echo "<td>" . $course['lecturer_id'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>