<?php
function logStudentActivity($pdo, $student_id, $activity_type, $activity_description) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO studentrecentactivitytbl (student_id, activity_type, activity_description, timestamp)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$student_id, $activity_type, $activity_description]);
    } catch (PDOException $e) {
        // Silently fail to avoid breaking main functionality
    }
}
?>