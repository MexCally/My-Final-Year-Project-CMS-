<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Get pending course registrations from students grouped by semester
    $stmt = $pdo->query("SELECT 
        s.student_id,
        s.Matric_No,
        s.first_name,
        s.last_name,
        s.email,
        s.Phone_Num as phone,
        s.Department as department,
        s.Level,
        COALESCE(cr.academic_year, '2024/2025') as academic_year,
        COALESCE(cr.semester, 'First') as semester,
        MIN(cr.date_registered) as application_date,
        cr.approval_status,
        COUNT(cr.course_id) as course_count
    FROM course_regtbl cr
    JOIN studenttbl s ON cr.student_id = s.student_id
    WHERE cr.approval_status = 'pending' OR cr.approval_status IS NULL
    GROUP BY cr.student_id, cr.academic_year, cr.semester
    ORDER BY MIN(cr.date_registered) DESC");
    
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($registrations);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>