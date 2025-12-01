<?php
session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

try {
    // Check if specific student is requested
    $student_id = $_GET['student_id'] ?? null;
    
    if ($student_id) {
        // Fetch specific student
        $stmt = $pdo->prepare("SELECT student_id, Matric_No, first_name, last_name, email, Phone_Num, Department, Level, Gender, academic_year, created_at FROM studenttbl WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($student) {
            echo json_encode([$student]); // Return as array for consistency
        } else {
            echo json_encode(['error' => 'Student not found']);
        }
    } else {
        // Fetch all students
        $stmt = $pdo->query("SELECT student_id, Matric_No, first_name, last_name, email, Phone_Num, Department, Level, Gender, academic_year, created_at FROM studenttbl ORDER BY created_at DESC");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($students);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
