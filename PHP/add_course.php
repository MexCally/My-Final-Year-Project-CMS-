<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/image_utils.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'errors' => ['Unauthorized access.']]);
    exit();
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $success = false;

    // Sanitize inputs
    $course_code = htmlspecialchars(trim($_POST['course_code'] ?? ''));
    $course_title = htmlspecialchars(trim($_POST['course_title'] ?? ''));
    $course_description = htmlspecialchars(trim($_POST['course_description'] ?? ''));
    $course_unit = htmlspecialchars(trim($_POST['course_unit'] ?? ''));
    $lecturer_id = htmlspecialchars(trim($_POST['lecturer_id'] ?? ''));
    $department = htmlspecialchars(trim($_POST['department'] ?? ''));
    $level = htmlspecialchars(trim($_POST['level'] ?? ''));
    $semester = htmlspecialchars(trim($_POST['semester'] ?? ''));

    // Validation
    if (empty($course_code)) {
        $errors[] = "Course code is required.";
    }
    if (empty($course_title)) {
        $errors[] = "Course title is required.";
    }
    if (empty($course_unit) || !is_numeric($course_unit) || $course_unit < 1) {
        $errors[] = "Valid course unit (minimum 1) is required.";
    }
    if (empty($lecturer_id) || !is_numeric($lecturer_id)) {
        $errors[] = "Valid lecturer is required.";
    }
    if (empty($department)) {
        $errors[] = "Department is required.";
    }
    if (empty($level)) {
        $errors[] = "Level is required.";
    }
    if (empty($semester)) {
        $errors[] = "Semester is required.";
    }

    // Check if course code already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT course_id FROM coursetbl WHERE course_code = ?");
            $stmt->execute([$course_code]);
            if ($stmt->fetch()) {
                $errors[] = "Course code already exists.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Verify lecturer exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT LecturerID FROM lecturertbl WHERE LecturerID = ?");
            $stmt->execute([$lecturer_id]);
            if (!$stmt->fetch()) {
                $errors[] = "Selected lecturer does not exist.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Insert if no errors
    if (empty($errors)) {
        try {
            $admin_id = $_SESSION['admin_id'];
            // Handle optional image upload
            $course_image_path = null;
            if (!empty($_FILES['course_image']['name'])) {
                $uploadDir = __DIR__ . '/../assets/img/courses/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $fileTmp = $_FILES['course_image']['tmp_name'];
                $fileType = mime_content_type($fileTmp);
                $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                if (!array_key_exists($fileType, $allowed)) {
                    $errors[] = 'Invalid image type. Allowed: jpg, png, webp.';
                } elseif ($_FILES['course_image']['size'] > 2 * 1024 * 1024) {
                    $errors[] = 'Image size must be <= 2MB.';
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
                                // non-fatal: thumbnail failed
                            }
                        } else {
                            $errors[] = 'Failed to move uploaded image.';
                        }
                }
            }

            if (empty($errors)) {
                $stmt = $pdo->prepare("INSERT INTO coursetbl (AdminID, lecturer_id, course_code, course_title, course_description, course_unit, department, level, semester, course_image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$admin_id, $lecturer_id, $course_code, $course_title, $course_description, $course_unit, $department, $level, $semester, $course_image_path]);
            }

            // Log the activity
            $course_id = $pdo->lastInsertId();
            $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
            $activity_stmt->execute(['add_course', "Added new course: $course_code - $course_title", $_SESSION['admin_id'], 'admin']);

            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Failed to add course: " . $e->getMessage();
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'errors' => $errors]);
    exit();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'errors' => ['Method not allowed.']]);
}
?>

