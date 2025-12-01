<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$student_id = $_SESSION['student_id'];
$course_id = $_GET['course_id'] ?? '';

if (empty($course_id)) {
    echo json_encode(['success' => false, 'message' => 'Course ID required']);
    exit;
}

try {
    // Verify student is enrolled in this course
    $enrollStmt = $pdo->prepare("SELECT COUNT(*) FROM course_regtbl WHERE student_id = ? AND course_id = ?");
    $enrollStmt->execute([$student_id, $course_id]);
    
    if ($enrollStmt->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => 'Not enrolled in this course']);
        exit;
    }
    
    // Get course materials
    $materialsStmt = $pdo->prepare("
        SELECT 
            m.material_id,
            m.title,
            m.description,
            m.file_path_url,
            m.file_type,
            m.created_at,
            c.course_code,
            c.course_title,
            CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name
        FROM course_materialtbl m
        JOIN coursetbl c ON m.course_id = c.course_id
        LEFT JOIN lecturertbl l ON m.uploaded_by_lecturer_id = l.LecturerID
        WHERE m.course_id = ?
        ORDER BY m.created_at DESC
    ");
    $materialsStmt->execute([$course_id]);
    $materials = $materialsStmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'materials' => $materials
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>