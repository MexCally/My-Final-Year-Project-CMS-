<?php
require_once 'config/db.php';

try {
    // SQL to create activity_log table if it doesn't exist
    $sql = "
        CREATE TABLE IF NOT EXISTS activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            action VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            user_id INT,
            user_type ENUM('admin', 'lecturer', 'student') DEFAULT 'admin'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";

    $pdo->exec($sql);
    echo "activity_log table created or already exists.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
