<?php
require_once 'config/db.php';

try {
    // SQL to create enrollmenttbl table if it doesn't exist
    $sql = "
        CREATE TABLE IF NOT EXISTS enrollmenttbl (
            enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            course_id INT NOT NULL,
            enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('Active', 'Inactive', 'Completed', 'Dropped') DEFAULT 'Active',
            FOREIGN KEY (student_id) REFERENCES studenttbl(student_id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES coursetbl(course_id) ON DELETE CASCADE,
            UNIQUE KEY unique_enrollment (student_id, course_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";

    $pdo->exec($sql);
    echo "enrollmenttbl table created or already exists.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
