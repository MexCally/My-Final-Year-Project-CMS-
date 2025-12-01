<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$lecturer_id = (int)$_SESSION['lecturer_id'];

try {
    // Total courses assigned to this lecturer
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM coursetbl WHERE lecturer_id = ?");
    $stmt->execute([$lecturer_id]);
    $total_courses = $stmt->fetch()['count'];
    
    // Total unique students enrolled in ALL of lecturer's courses
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT cr.student_id) as count FROM course_regtbl cr 
                          JOIN coursetbl c ON cr.course_id = c.course_id 
                          WHERE c.lecturer_id = ? AND cr.approval_status = 'Approved'");
    $stmt->execute([$lecturer_id]);
    $total_students = $stmt->fetch()['count'];
    
    // Pending grades (results without grades for lecturer's courses)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM resulttbl r 
                          JOIN coursetbl c ON r.course_id = c.course_id 
                          WHERE c.lecturer_id = ? AND (r.grade IS NULL OR r.grade = '')");
    $stmt->execute([$lecturer_id]);
    $pending_grades = $stmt->fetch()['count'];
    
    // Assignments due
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM assignmenttbl a 
                              JOIN coursetbl c ON a.course_id = c.course_id 
                              WHERE c.lecturer_id = ? AND a.due_date >= CURDATE()");
        $stmt->execute([$lecturer_id]);
        $assignments_due = $stmt->fetch()['count'];
    } catch (PDOException $e2) {
        $assignments_due = 0;
    }
    
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
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>