<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$student_id = $_SESSION['student_id'];

try {
    // Get materials for courses the student is registered for
    $stmt = $pdo->prepare("
        SELECT 
            cm.material_id,
            cm.title,
            cm.description,
            cm.file_path_url,
            cm.file_type,
            cm.created_at,
            c.course_code,
            c.course_title,
            CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name
        FROM course_materialtbl cm
        JOIN coursetbl c ON cm.course_id = c.course_id
        LEFT JOIN lecturertbl l ON cm.uploaded_by_lecturer_id = l.LecturerID
        WHERE cm.is_published = 1 
        AND cm.course_id IN (
            SELECT course_id FROM course_regtbl WHERE student_id = ?
        )
        ORDER BY c.course_code, cm.created_at DESC
    ");
    
    $stmt->execute([$student_id]);
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['materials' => $materials]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>