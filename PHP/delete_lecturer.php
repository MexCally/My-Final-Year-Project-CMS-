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

    if (empty($lecturer_id)) {
        $response["errors"][] = "Lecturer ID is required";
        echo json_encode($response);
        exit;
    }

    try {
        // First, get lecturer info for activity log
        $info_stmt = $pdo->prepare("SELECT First_name, Last_Name, Email FROM lecturertbl WHERE LecturerID = ?");
        $info_stmt->execute([$lecturer_id]);
        $lecturer_info = $info_stmt->fetch(PDO::FETCH_ASSOC);

        // Delete the lecturer
        $stmt = $pdo->prepare("DELETE FROM lecturertbl WHERE LecturerID = ?");
        $stmt->execute([$lecturer_id]);

        if ($stmt->rowCount() > 0) {
            // Log the activity
            if ($lecturer_info) {
                $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
                $activity_stmt->execute(['delete_lecturer', "Deleted lecturer: {$lecturer_info['First_name']} {$lecturer_info['Last_Name']} ({$lecturer_info['Email']})", $_SESSION['admin_id'], 'admin']);
            }

            $response["success"] = true;
        } else {
            $response["errors"][] = "Lecturer not found.";
        }

    } catch (PDOException $e) {
        $response["errors"][] = "Database error: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    $response["errors"][] = "Method not allowed.";
}

echo json_encode($response);
