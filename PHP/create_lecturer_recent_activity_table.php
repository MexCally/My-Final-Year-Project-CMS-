<?php
require_once '../config/db.php';

try {
    // SQL to create lecturerrecentactivitytbl table if it doesn't exist
    $sql = "
        CREATE TABLE IF NOT EXISTS lecturerrecentactivitytbl (
            activity_id INT AUTO_INCREMENT PRIMARY KEY,
            LecturerID INT NOT NULL,
            activity_type VARCHAR(50) NOT NULL,
            activity_description TEXT NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (LecturerID) REFERENCES lecturertbl(LecturerID) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";

    $pdo->exec($sql);
    echo "lecturerrecentactivitytbl table created or already exists.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
