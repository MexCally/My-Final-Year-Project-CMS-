<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";
require_once __DIR__ . '/image_utils.php';

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

        // Fetch existing course image (if any) so we can delete it if replaced
        $existing_course_image = null;
        try {
            $getStmt = $pdo->prepare("SELECT course_image FROM coursetbl WHERE course_id = ?");
            $getStmt->execute([$course_id]);
            $row = $getStmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['course_image'])) {
                $existing_course_image = $row['course_image'];
            }
        } catch (PDOException $e) {
            // ignore, will continue without deletion
        }

        // Handle optional image upload
        $course_image_path = null;
        if (!empty($_FILES['course_image']['name'])) {
            $uploadDir = __DIR__ . '/../assets/img/courses/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $fileTmp = $_FILES['course_image']['tmp_name'];
            $fileType = mime_content_type($fileTmp);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (!array_key_exists($fileType, $allowed)) {
                    $response["errors"][] = 'Invalid image type. Allowed: jpg, png, webp.';
                    echo json_encode($response);
                    exit;
            } elseif ($_FILES['course_image']['size'] > 2 * 1024 * 1024) {
                $response["errors"][] = 'Image size must be <= 2MB.';
                echo json_encode($response);
                exit;
            } else {
                $ext = $allowed[$fileType];
                $safeCode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $course_code);
                $filename = $safeCode . '.' . $ext;
                $target = $uploadDir . $filename;
                if (move_uploaded_file($fileTmp, $target)) {
                    $course_image_path = 'assets/img/courses/' . $filename;
                        // Create thumbnails directory
                        $thumbsDir = $uploadDir . 'thumbs/';
                        if (!is_dir($thumbsDir)) mkdir($thumbsDir, 0755, true);

                        // Create a thumbnail (max width 400px)
                        try {
                            $srcPath = $target;
                            $thumbPath = $thumbsDir . $filename;
                            create_image_thumbnail($srcPath, $thumbPath, 400);
                        } catch (Exception $thumbEx) {
                            // non-fatal
                        }

                        // Delete previous image and thumb if exists
                        if (!empty($existing_course_image)) {
                            $prevPath = __DIR__ . '/../' . $existing_course_image;
                            $prevThumb = dirname($prevPath) . '/thumbs/' . basename($prevPath);
                            if (file_exists($prevPath) && is_writable($prevPath)) @unlink($prevPath);
                            if (file_exists($prevThumb) && is_writable($prevThumb)) @unlink($prevThumb);
                        }
                } else {
                    $response["errors"][] = 'Failed to move uploaded image.';
                    echo json_encode($response);
                    exit;
                }
            }
        }

        if ($course_image_path !== null) {
            $query = "UPDATE coursetbl SET 
                        course_code = ?,
                        course_title = ?,
                        course_description = ?,
                        course_unit = ?,
                        lecturer_id = ?,
                        department = ?,
                        level = ?,
                        semester = ?,
                        course_image = ?
                      WHERE course_id = ?";

            $stmt = $pdo->prepare($query);
            $stmt->execute([$course_code, $course_title, $course_description, $course_unit, $lecturer_id, $department, $level, $semester, $course_image_path, $course_id]);
        } else {
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
        }

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

