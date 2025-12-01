<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Get approved today count
    $stmt = $pdo->query("SELECT COUNT(DISTINCT student_id) as approved_today 
                        FROM course_regtbl 
                        WHERE approval_status = 'approved' 
                        AND DATE(date_approved) = CURDATE()");
    $approvedToday = $stmt->fetch(PDO::FETCH_ASSOC)['approved_today'];

    // Get total approved count
    $stmt = $pdo->query("SELECT COUNT(DISTINCT student_id) as total_approved 
                        FROM course_regtbl 
                        WHERE approval_status = 'approved'");
    $totalApproved = $stmt->fetch(PDO::FETCH_ASSOC)['total_approved'];

    echo json_encode([
        'success' => true,
        'approved_today' => $approvedToday,
        'total_approved' => $totalApproved
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>