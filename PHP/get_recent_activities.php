<?php
session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

try {
    // Fetch last 5 recent activities
    $stmt = $pdo->query("SELECT action, description, timestamp FROM activity_log ORDER BY timestamp DESC LIMIT 5");
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format activities for frontend
    $formattedActivities = [];
    foreach ($activities as $activity) {
        $icon = 'fas fa-info-circle text-info'; // default icon
        $action = $activity['action'];

        // Set appropriate icon based on action
        switch ($action) {
            case 'student_added':
                $icon = 'fas fa-user-plus text-success';
                break;
            case 'student_updated':
                $icon = 'fas fa-user-edit text-primary';
                break;
            case 'course_updated':
                $icon = 'fas fa-book text-info';
                break;
            case 'grade_submitted':
                $icon = 'fas fa-grade text-warning';
                break;
            case 'lecturer_added':
                $icon = 'fas fa-chalkboard-teacher text-success';
                break;
        }

        $formattedActivities[] = [
            'icon' => $icon,
            'description' => $activity['description'],
            'timestamp' => $activity['timestamp']
        ];
    }

    echo json_encode($formattedActivities);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
