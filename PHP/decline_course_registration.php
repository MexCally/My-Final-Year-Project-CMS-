<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'errors' => ['Unauthorized access.']]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $academic_year = $_POST['academic_year'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $detailed_reason = $_POST['detailed_reason'] ?? '';

    if (empty($student_id) || empty($academic_year) || empty($semester) || empty($reason) || empty($detailed_reason)) {
        echo json_encode(['success' => false, 'errors' => ['Missing required parameters.']]);
        exit();
    }

    try {
        // Update course registration status to declined
        $stmt = $pdo->prepare("UPDATE course_regtbl SET approval_status = 'Dropped', approved_by = ?, date_approved = NOW() WHERE student_id = ? AND (approval_status = 'Pending' OR approval_status IS NULL)");
        $stmt->execute([$_SESSION['admin_id'], $student_id]);

        if ($stmt->rowCount() > 0) {
            // Log the activity
            $activity_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
            $activity_stmt->execute(['decline_course_registration', "Declined course registration for student ID: $student_id - Reason: $reason", $_SESSION['admin_id'], 'admin']);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'errors' => ['No registration found to decline.']]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]]);
    }
} else {
    echo json_encode(['success' => false, 'errors' => ['Invalid request method.']]);
}
?>