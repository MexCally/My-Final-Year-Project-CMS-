<?php
require_once 'config/db.php';

try {
    $stmt = $pdo->prepare("SELECT material_id, title, file_path_url FROM course_materialtbl ORDER BY material_id");
    $stmt->execute();
    $materials = $stmt->fetchAll();
    
    echo "<h3>Materials in Database:</h3>";
    foreach ($materials as $material) {
        echo "<p>ID: {$material['material_id']}, Title: {$material['title']}, Path: {$material['file_path_url']}</p>";
        
        // Check if file exists
        $full_path = $material['file_path_url'];
        $exists = file_exists($full_path) ? "EXISTS" : "NOT FOUND";
        echo "<p>File check: {$full_path} - {$exists}</p><br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>