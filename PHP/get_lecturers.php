<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Query to get all lecturers with proper column names
    // Using LecturerID as primary key and mapping column names for consistency
    $stmt = $pdo->query("SELECT LecturerID as lecturer_id, AdminID as adminid, First_name as first_name, Last_Name as last_name, Email as email, Phone_Num as phone, Department as department, Gender as gender, created_at FROM lecturertbl ORDER BY First_name, Last_Name");
    $lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($lecturers);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
