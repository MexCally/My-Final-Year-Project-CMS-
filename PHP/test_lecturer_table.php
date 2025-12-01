<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Check if lecturertbl table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'lecturertbl'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo json_encode(['error' => 'lecturertbl table does not exist']);
        exit;
    }
    
    // Get table structure
    $stmt = $pdo->query("DESCRIBE lecturertbl");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Try to get existing lecturers count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM lecturertbl");
    $count = $stmt->fetch()['count'];
    
    echo json_encode([
        'table_exists' => $tableExists,
        'columns' => $columns,
        'lecturer_count' => $count
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>