<?php
require_once 'config/db.php';

try {
    // Create grade_submissions table
    $sql = "CREATE TABLE IF NOT EXISTS grade_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        lecturer_id INT NOT NULL,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        academic_year VARCHAR(20),
        semester VARCHAR(20),
        approved_at TIMESTAMP NULL,
        approved_by INT NULL,
        published TINYINT(1) DEFAULT 0,
        published_at TIMESTAMP NULL,
        published_by INT NULL,
        UNIQUE KEY unique_submission (course_id, lecturer_id, academic_year, semester),
        FOREIGN KEY (course_id) REFERENCES coursetbl(course_id),
        FOREIGN KEY (lecturer_id) REFERENCES lecturertbl(LecturerID)
    )";
    
    $pdo->exec($sql);
    echo "Grade submissions table created successfully\n";
    
    // Add columns to evaluationtbl
    $columns = [
        "submitted_to_records TINYINT(1) DEFAULT 0",
        "submitted_at TIMESTAMP NULL",
        "submitted_by INT NULL",
        "published TINYINT(1) DEFAULT 0",
        "published_at TIMESTAMP NULL",
        "published_by INT NULL"
    ];
    
    foreach ($columns as $column) {
        try {
            $pdo->exec("ALTER TABLE evaluationtbl ADD COLUMN $column");
            echo "Added column: $column\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "Column already exists: $column\n";
            } else {
                echo "Error adding column $column: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "Database update completed successfully!";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>