<?php
require_once '../config/db.php';

try {
    // Query to get all grades with student and course information
    $stmt = $pdo->prepare("
        SELECT
            r.student_id,
            r.course_code,
            r.grade,
            s.first_name,
            s.last_name,
            s.Matric_No,
            s.email,
            s.Department,
            s.Level,
            c.course_title,
            c.department as course_department,
            c.level as course_level,
            c.semester,
            c.course_unit,
            CONCAT(l.first_name, ' ', l.last_name) as lecturer_name
        FROM resulttbl r
        JOIN studenttbl s ON r.student_id = s.student_id
        JOIN coursetbl c ON r.course_code = c.course_code
        LEFT JOIN lecturertbl l ON c.lecturer_id = l.lecturer_id
        ORDER BY r.student_id, r.course_code
    ");

    $stmt->execute();
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($grades);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
