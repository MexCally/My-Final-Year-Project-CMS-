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
    $lecturer_id = $_POST["lecturer_id"] ?? "";
    $first_name  = trim($_POST["first_name"] ?? "");
    $last_name   = trim($_POST["last_name"] ?? "");
    $email       = trim($_POST["email"] ?? "");
    $phone_num   = trim($_POST["phone_num"] ?? "");
    $department  = trim($_POST["department"] ?? "");
    $gender      = trim($_POST["gender"] ?? "");

    // Validation
    if (empty($lecturer_id))  $response["errors"][] = "Lecturer ID is required";
    if (empty($first_name))   $response["errors"][] = "First name is required";
    if (empty($last_name))    $response["errors"][] = "Last name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["errors"][] = "Valid email is required";
    }
    if (empty($phone_num) || !preg_match('/^[0-9+\-\s()]{10,15}$/', $phone_num)) {
        $response["errors"][] = "Valid phone number is required";
    }
    if (empty($department))   $response["errors"][] = "Department is required";
    if (empty($gender))       $response["errors"][] = "Gender is required";

    if (!empty($response["errors"])) {
        echo json_encode($response);
        exit;
    }

    try {
        // Check if email already exists for another lecturer
        $check_stmt = $pdo->prepare("SELECT LecturerID FROM lecturertbl WHERE Email = ? AND LecturerID != ?");
        $check_stmt->execute([$email, $lecturer_id]);
        if ($check_stmt->fetch()) {
            $response["errors"][] = "Email already exists for another lecturer.";
            echo json_encode($response);
            exit;
        }

        $query = "UPDATE lecturertbl SET 
                    First_name = ?,
                    Last_Name = ?,
                    Email = ?,
                    Phone_Num = ?,
                    Department = ?,
                    Gender = ?
                  WHERE LecturerID = ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$first_name, $last_name, $email, $phone_num, $department, $gender, $lecturer_id]);

        if ($stmt->rowCount() > 0) {
            // Log the activity
            $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
            $activity_stmt->execute(['edit_lecturer', "Updated lecturer: $first_name $last_name ($email)", $_SESSION['admin_id'], 'admin']);

            $response["success"] = true;
        } else {
            $response["errors"][] = "No changes were made or lecturer not found.";
        }

    } catch (PDOException $e) {
        $response["errors"][] = "Database error: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    $response["errors"][] = "Method not allowed.";
}

echo json_encode($response);
