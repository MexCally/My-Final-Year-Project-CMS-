<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Check if specific course is requested
    if (isset($_GET['course_code'])) {
        $course_code = $_GET['course_code'];
        $stmt = $pdo->prepare("SELECT 
            c.course_id,
            c.course_code,
            c.course_title,
            c.course_description,
            c.course_unit,
            c.department,
            c.level,
            c.semester,
            c.lecturer_id,
            CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name,
            c.created_at
        FROM coursetbl c
        LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
        WHERE c.course_code = ?
        ORDER BY c.created_at DESC");
        $stmt->execute([$course_code]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Query to get all courses with lecturer information
        $stmt = $pdo->query("SELECT 
            c.course_id,
            c.course_code,
            c.course_title,
            c.course_description,
            c.course_unit,
            c.department,
            c.level,
            c.semester,
            c.lecturer_id,
            CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name,
            c.created_at
        FROM coursetbl c
        LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
        ORDER BY c.created_at DESC");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Debug: Log semester values to see what's in the database
    foreach ($courses as &$course) {
        // Keep the actual semester value from database, don't convert to empty string
        if (!isset($course['semester']) || $course['semester'] === null || trim($course['semester']) === '') {
            $course['semester'] = null; // Keep as null so JavaScript can handle it properly
        }
    }
    unset($course); // Break reference

    echo json_encode($courses);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
