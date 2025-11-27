<?php
require_once 'config/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS degree_requirementstbl (
        id INT AUTO_INCREMENT PRIMARY KEY,
        department VARCHAR(100) NOT NULL,
        category VARCHAR(100) NOT NULL,
        required_credits INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Degree requirements table created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
