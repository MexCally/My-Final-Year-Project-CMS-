<?php
session_start();
require_once '../config/db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];

try {
    // Fetch materials for courses taught by this lecturer
    $stmt = $pdo->prepare("SELECT 
        cm.material_id,
        cm.course_id,
        cm.title,
        cm.description,
        cm.file_path_url,
        cm.file_type,
        cm.is_published,
        cm.created_at,
        cm.updated_at,
        cm.uploaded_by_lecturer_id,
        c.course_code,
        c.course_title
    FROM course_materialtbl cm
    JOIN coursetbl c ON cm.course_id = c.course_id
    WHERE cm.uploaded_by_lecturer_id = ?
    ORDER BY c.course_code, cm.created_at DESC");
    
    $stmt->execute([$lecturer_id]);
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group materials by course
    $groupedMaterials = [];
    foreach ($materials as $material) {
        $courseKey = $material['course_code'];
        if (!isset($groupedMaterials[$courseKey])) {
            $groupedMaterials[$courseKey] = [
                'course_id' => $material['course_id'],
                'course_code' => $material['course_code'],
                'course_title' => $material['course_title'],
                'materials' => []
            ];
        }
        $groupedMaterials[$courseKey]['materials'][] = $material;
    }

    echo json_encode($groupedMaterials);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>