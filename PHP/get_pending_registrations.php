<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Get pending student registrations (students with status = 'pending' or similar)
    // Assuming we add a status field to studenttbl or create a separate pending_registrations table
    
    // For now, let's assume all students are "approved" and we'll show recent registrations
    // You may need to modify this based on your actual database structure
    
    $stmt = $pdo->query("SELECT 
        student_id,
        Matric_No,
        first_name,
        last_name,
        email,
        Phone_Num as phone,
        Department as department,
        Level,
        Gender,
        created_at as application_date
    FROM studenttbl 
    ORDER BY created_at DESC 
    LIMIT 50");
    
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($registrations);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>