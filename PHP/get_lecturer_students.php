<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];
$course_id = $_GET['course_id'] ?? '';

try {
    $sql = "
        SELECT DISTINCT
            s.student_id,
            s.Matric_No as matric_no,
            CONCAT(s.first_name, ' ', s.last_name) as full_name,
            s.Department as department,
            s.Level as level,
            c.course_code,
            c.course_title,
            cr.approval_status,
            cr.date_registered
        FROM course_regtbl cr
        JOIN studenttbl s ON cr.student_id = s.student_id
        JOIN coursetbl c ON cr.course_id = c.course_id
        WHERE c.lecturer_id = ?
    ";
    
    $params = [$lecturer_id];
    
    if (!empty($course_id)) {
        $sql .= " AND c.course_id = ?";
        $params[] = $course_id;
    }
    
    $sql .= " ORDER BY s.first_name, s.last_name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'students' => $students
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>