<?php
require_once '../config/db.php';

try {
    // Query to count pending grades (counting all records in resulttbl as pending grades)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM resulttbl");
    $result = $stmt->fetch();
    $count = $result['count'];

    echo json_encode(['count' => $count]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
