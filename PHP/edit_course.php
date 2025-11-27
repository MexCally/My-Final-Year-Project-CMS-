<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

$response = ["success" => false, "errors" => []];

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    $response["errors"][] = "Unauthorized access.";
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $course_id = $_POST["course_id"] ?? "";
    $course_code = htmlspecialchars(trim($_POST["course_code"] ?? ""));
    $course_title = htmlspecialchars(trim($_POST["course_title"] ?? ""));
    $course_description = htmlspecialchars(trim($_POST["course_description"] ?? ""));
    $course_unit = htmlspecialchars(trim($_POST["course_unit"] ?? ""));
    $lecturer_id = htmlspecialchars(trim($_POST["lecturer_id"] ?? ""));
    $department = htmlspecialchars(trim($_POST["department"] ?? ""));
    $level = htmlspecialchars(trim($_POST["level"] ?? ""));
    $semester = htmlspecialchars(trim($_POST["semester"] ?? ""));

    // Validation
    if (empty($course_id)) {
        $response["errors"][] = "Course ID is required";
    }
    if (empty($course_code)) {
        $response["errors"][] = "Course code is required";
    }
    if (empty($course_title)) {
        $response["errors"][] = "Course title is required";
    }
    if (empty($course_unit) || !is_numeric($course_unit) || $course_unit < 1) {
        $response["errors"][] = "Valid course unit (minimum 1) is required";
    }
    if (empty($lecturer_id) || !is_numeric($lecturer_id)) {
        $response["errors"][] = "Valid lecturer is required";
    }
    if (empty($department)) {
        $response["errors"][] = "Department is required";
    }
    if (empty($level)) {
        $response["errors"][] = "Level is required";
    }
    if (empty($semester)) {
        $response["errors"][] = "Semester is required";
    }

    if (!empty($response["errors"])) {
        echo json_encode($response);
        exit;
    }

    try {
        // Check if course code already exists for another course
        $check_stmt = $pdo->prepare("SELECT course_id FROM coursetbl WHERE course_code = ? AND course_id != ?");
        $check_stmt->execute([$course_code, $course_id]);
        if ($check_stmt->fetch()) {
            $response["errors"][] = "Course code already exists for another course.";
            echo json_encode($response);
            exit;
        }

        // Verify lecturer exists
        $lecturer_stmt = $pdo->prepare("SELECT LecturerID FROM lecturertbl WHERE LecturerID = ?");
        $lecturer_stmt->execute([$lecturer_id]);
        if (!$lecturer_stmt->fetch()) {
            $response["errors"][] = "Selected lecturer does not exist.";
            echo json_encode($response);
            exit;
        }

        $query = "UPDATE coursetbl SET 
                    course_code = ?,
                    course_title = ?,
                    course_description = ?,
                    course_unit = ?,
                    lecturer_id = ?,
                    department = ?,
                    level = ?,
                    semester = ?
                  WHERE course_id = ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$course_code, $course_title, $course_description, $course_unit, $lecturer_id, $department, $level, $semester, $course_id]);

        if ($stmt->rowCount() > 0) {
            // Log the activity
            $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
            $activity_stmt->execute(['edit_course', "Updated course: $course_code - $course_title", $_SESSION['admin_id'], 'admin']);

            $response["success"] = true;
        } else {
            $response["errors"][] = "No changes were made or course not found.";
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

