<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode(['error' => 'Not authenticated as lecturer']);
    exit;
}

$lecturer_id = (int)$_SESSION['lecturer_id'];

try {
    // Get all courses assigned to this lecturer with their materials
    $stmt = $pdo->prepare("
        SELECT 
            c.course_id,
            c.course_code,
            c.course_title,
            m.material_id,
            m.title,
            m.description,
            m.file_path_url,
            m.file_type,
            m.is_published,
            m.created_at
        FROM coursetbl c
        LEFT JOIN course_materialtbl m ON c.course_id = m.course_id
        WHERE c.lecturer_id = ?
        ORDER BY c.course_code, m.created_at DESC
    ");
    
    $stmt->execute([$lecturer_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group materials by course
    $courses = [];
    
    foreach ($results as $row) {
        $course_id = $row['course_id'];
        
        // Initialize course if not exists
        if (!isset($courses[$course_id])) {
            $courses[$course_id] = [
                'course_id' => $course_id,
                'course_code' => $row['course_code'],
                'course_title' => $row['course_title'],
                'materials' => []
            ];
        }
        
        // Add material if exists
        if ($row['material_id']) {
            $courses[$course_id]['materials'][] = [
                'material_id' => $row['material_id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'file_path_url' => $row['file_path_url'],
                'file_type' => $row['file_type'],
                'is_published' => $row['is_published'],
                'created_at' => $row['created_at']
            ];
        }
    }
    
    echo json_encode($courses);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>