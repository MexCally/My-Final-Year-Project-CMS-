<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];

try {
    // Total courses
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM coursetbl WHERE lecturer_id = ?");
    $stmt->execute([$lecturer_id]);
    $total_courses = $stmt->fetch()['count'];
    
    // Total students
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT cr.student_id) as count FROM course_regtbl cr 
                          JOIN coursetbl c ON cr.course_id = c.course_id 
                          WHERE c.lecturer_id = ? AND cr.approval_status IN ('Approved', 'Registered')");
    $stmt->execute([$lecturer_id]);
    $total_students = $stmt->fetch()['count'];
    
    // Pending grades
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT s.student_id) as count FROM studenttbl s
                          JOIN course_regtbl cr ON s.student_id = cr.student_id
                          JOIN coursetbl c ON cr.course_id = c.course_id
                          LEFT JOIN evaluationtbl e ON s.student_id = e.student_id AND c.course_id = e.course_id
                          WHERE c.lecturer_id = ? AND cr.approval_status IN ('Approved', 'Registered') 
                          AND (e.grade IS NULL OR e.grade = '')");
    $stmt->execute([$lecturer_id]);
    $pending_grades = $stmt->fetch()['count'];
    
    // Assignments due
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM assignmenttbl a 
                          JOIN coursetbl c ON a.course_id = c.course_id 
                          WHERE c.lecturer_id = ? AND a.due_date >= CURDATE() AND a.is_active = 1");
    $stmt->execute([$lecturer_id]);
    $assignments_due = $stmt->fetch()['count'];
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_courses' => $total_courses,
            'total_students' => $total_students,
            'pending_grades' => $pending_grades,
            'assignments_due' => $assignments_due
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>