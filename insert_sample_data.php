<?php
require_once 'config/db.php';

try {
    // Insert sample students
    $pdo->exec("INSERT INTO studenttbl (Matric_No, first_name, last_name, email, Phone_Num, password, Department, Level, Gender) VALUES
        ('STU001', 'John', 'Doe', 'john.doe@example.com', '1234567890', '\$2y\$10\$dummyhash1', 'Computer Science', '100', 'Male'),
        ('STU002', 'Jane', 'Smith', 'jane.smith@example.com', '0987654321', '\$2y\$10\$dummyhash2', 'Mathematics', '200', 'Female')");

    // Insert sample lecturers
    $pdo->exec("INSERT INTO lecturertbl (first_name, last_name, email, phone, password, department, specialization) VALUES
        ('Dr. Alice', 'Johnson', 'alice.johnson@example.com', '1112223333', '\$2y\$10\$dummyhash3', 'Computer Science', 'AI'),
        ('Prof. Bob', 'Williams', 'bob.williams@example.com', '4445556666', '\$2y\$10\$dummyhash4', 'Mathematics', 'Calculus')");

    // Insert sample courses
    $pdo->exec("INSERT INTO coursetbl (course_code, course_title, course_name, department, level, lecturer_name, credits) VALUES
        ('CS101', 'Introduction to Programming', 'Programming 101', 'Computer Science', '100', 'Dr. Alice Johnson', 3),
        ('MATH201', 'Advanced Calculus', 'Calculus II', 'Mathematics', '200', 'Prof. Bob Williams', 4)");

    // Insert sample results
    $pdo->exec("INSERT INTO resulttbl (student_id, course_code, grade) VALUES
        (1, 'CS101', 'A'),
        (2, 'MATH201', 'B+')");

    echo "Sample data inserted successfully!";
} catch (PDOException $e) {
    echo "Error inserting sample data: " . $e->getMessage();
}
?>
