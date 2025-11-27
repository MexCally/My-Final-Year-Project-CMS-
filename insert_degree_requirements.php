<?php
require_once 'config/db.php';

try {
    // Insert degree requirements
    $pdo->exec("INSERT INTO degree_requirementstbl (department, category, required_credits) VALUES
        ('Computer Science', 'Computer Science Core', 45),
        ('Mathematics', 'Mathematics', 12),
        ('Science', 'Science Requirements', 8),
        ('English', 'English & Communication', 9),
        ('Communication', 'English & Communication', 9),
        ('Liberal Arts', 'Liberal Arts', 15),
        ('Electives', 'Electives', 31)
    ");
    echo "Degree requirements inserted successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
