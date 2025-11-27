<?php
session_start();
require_once '../config/db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];

try {
    // Fetch last 10 recent activities for this lecturer
    $stmt = $pdo->prepare("SELECT activity_id, LecturerID, activity_type, activity_description, timestamp FROM lecturerrecentactivitytbl WHERE LecturerID = ? ORDER BY timestamp DESC LIMIT 10");
    $stmt->execute([$lecturer_id]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format activities for frontend
    $formattedActivities = [];
    foreach ($activities as $activity) {
        $icon = 'fas fa-info-circle text-info'; // default icon
        $activity_type = $activity['activity_type'];

        // Set appropriate icon based on activity type
        switch ($activity_type) {
            case 'assignment_created':
                $icon = 'fas fa-plus text-success';
                break;
            case 'grade_submitted':
                $icon = 'fas fa-check text-primary';
                break;
            case 'material_uploaded':
                $icon = 'fas fa-upload text-info';
                break;
            case 'course_updated':
                $icon = 'fas fa-edit text-warning';
                break;
            case 'student_feedback':
                $icon = 'fas fa-comments text-secondary';
                break;
        }

        $formattedActivities[] = [
            'icon' => $icon,
            'description' => $activity['activity_description'],
            'timestamp' => $activity['timestamp'],
            'type' => $activity_type
        ];
    }

    echo json_encode($formattedActivities);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
