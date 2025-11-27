<?php
session_start();
require_once '../config/db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['assignment_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Assignment ID is required']);
    exit();
}

$assignment_id = $_GET['assignment_id'];
$lecturer_id = $_SESSION['lecturer_id'];

try {
    // First verify that this assignment belongs to the lecturer
    $stmt = $pdo->prepare("SELECT a.assignment_id, a.title, c.course_code, c.course_title 
                          FROM assignmenttbl a 
                          JOIN coursetbl c ON a.course_id = c.course_id 
                          WHERE a.assignment_id = ? AND a.lecturer_id = ?");
    $stmt->execute([$assignment_id, $lecturer_id]);
    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$assignment) {
        http_response_code(403);
        echo json_encode(['error' => 'Assignment not found or access denied']);
        exit();
    }
    
    // Get submissions for this assignment
    $stmt = $pdo->prepare("SELECT 
        s.sub_id,
        s.student_id,
        s.file_path,
        s.submitted_at,
        CONCAT(st.first_name, ' ', st.last_name) as student_name,
        st.email as student_email,
        st.Matric_No as matric_no
    FROM ass_subtbl s
    JOIN studenttbl st ON s.student_id = st.student_id
    WHERE s.assignment_id = ?
    ORDER BY s.submitted_at DESC");
    
    $stmt->execute([$assignment_id]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the submitted_at dates
    foreach ($submissions as &$submission) {
        $submission['submitted_at_formatted'] = date('M j, Y g:i A', strtotime($submission['submitted_at']));
        $submission['file_name'] = basename($submission['file_path']);
    }
    
    echo json_encode([
        'success' => true,
        'assignment' => $assignment,
        'submissions' => $submissions,
        'total_submissions' => count($submissions)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>