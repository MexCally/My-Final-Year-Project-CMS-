<?php
session_start();
header("Content-Type: application/json");
require_once '../config/db.php';

$response = ["success" => false, "errors" => []];

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    $response["errors"][] = "Unauthorized access.";
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $course_code = trim($_POST["course_code"] ?? "");

    if (empty($course_code)) {
        $response["errors"][] = "Course code is required";
        echo json_encode($response);
        exit;
    }

    try {
        // First, get course info for activity log
        $info_stmt = $pdo->prepare("SELECT c.course_id, c.course_code, c.course_title, 
            CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name
            FROM coursetbl c
            LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
            WHERE c.course_code = ?");
        $info_stmt->execute([$course_code]);
        $course_info = $info_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$course_info) {
            $response["errors"][] = "Course not found.";
            echo json_encode($response);
            exit;
        }

        // Delete the course
        $stmt = $pdo->prepare("DELETE FROM coursetbl WHERE course_code = ?");
        $stmt->execute([$course_code]);

        if ($stmt->rowCount() > 0) {
            // Log the activity
            $lecturer_name = $course_info['lecturer_name'] ? $course_info['lecturer_name'] : 'Unassigned';
            $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
            $activity_stmt->execute(['delete_course', "Deleted course: {$course_info['course_title']} ({$course_code}) - Lecturer: {$lecturer_name}", $_SESSION['admin_id'], 'admin']);

            $response["success"] = true;
        } else {
            $response["errors"][] = "Course not found or already deleted.";
        }

    } catch (PDOException $e) {
        $response["errors"][] = "Database error: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    $response["errors"][] = "Method not allowed.";
}

echo json_encode($response);
?>
