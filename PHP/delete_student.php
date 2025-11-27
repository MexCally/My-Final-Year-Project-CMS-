<?php
session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit();
}

try {
    // Get form data
    $student_id = trim($_POST['student_id'] ?? '');

    // Validation
    if (empty($student_id)) {
        echo json_encode(['success' => false, 'errors' => ['Student ID is required']]);
        exit();
    }

    // Check if student exists
    $stmt = $pdo->prepare("SELECT first_name, last_name, Matric_No FROM studenttbl WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['success' => false, 'errors' => ['Student not found']]);
        exit();
    }

    // Delete student
    $stmt = $pdo->prepare("DELETE FROM studenttbl WHERE student_id = ?");
    $stmt->execute([$student_id]);

    // Log the activity
    $admin_id = $_SESSION['admin_id'];
    $description = "Deleted student: {$student['first_name']} {$student['last_name']} ({$student['Matric_No']})";
    $log_stmt = $pdo->prepare("INSERT INTO activity_log (action, description, user_id, user_type) VALUES (?, ?, ?, ?)");
    $log_stmt->execute(['delete_student', $description, $admin_id, 'admin']);

    echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]]);
}
?>
