<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$lecturer_id = $_SESSION['lecturer_id'];
$sub_id = $_POST['sub_id'] ?? '';
$score = $_POST['score'] ?? '';
$comments = $_POST['comments'] ?? '';

if (empty($sub_id) || $score === '') {
    echo json_encode(['success' => false, 'message' => 'Submission ID and score are required']);
    exit;
}

// Validate score
$score = floatval($score);
if ($score < 0 || $score > 100) {
    echo json_encode(['success' => false, 'message' => 'Score must be between 0 and 100']);
    exit;
}

try {
    // Verify that this submission belongs to an assignment of this lecturer
    $stmt = $pdo->prepare("
        SELECT s.sub_id, s.assignment_id, a.lecturer_id
        FROM ass_subtbl s
        JOIN assignmenttbl a ON s.assignment_id = a.assignment_id
        WHERE s.sub_id = ? AND a.lecturer_id = ?
    ");
    $stmt->execute([$sub_id, $lecturer_id]);
    $submission = $stmt->fetch();

    if (!$submission) {
        echo json_encode(['success' => false, 'message' => 'Submission not found or access denied']);
        exit;
    }

    // Update the grade
    $updateStmt = $pdo->prepare("
        UPDATE ass_subtbl
        SET score_received = ?, graded_at = NOW()
        WHERE sub_id = ?
    ");
    $updateStmt->execute([$score, $sub_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Grade saved successfully'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
