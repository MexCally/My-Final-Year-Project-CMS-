<?php
require_once '../config/db.php';

// Sample activities to insert
$activities = [
    [
        'LecturerID' => 1, // Adjust this to match an existing lecturer ID
        'activity_type' => 'assignment_created',
        'activity_description' => 'Created new assignment "Data Structures Quiz" for CS301',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours'))
    ],
    [
        'LecturerID' => 1,
        'activity_type' => 'grade_submitted',
        'activity_description' => 'Submitted grades for CS201 midterm examination',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day'))
    ],
    [
        'LecturerID' => 1,
        'activity_type' => 'material_uploaded',
        'activity_description' => 'Uploaded lecture slides for "Advanced Algorithms" course',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-2 days'))
    ],
    [
        'LecturerID' => 1,
        'activity_type' => 'course_updated',
        'activity_description' => 'Updated course syllabus for Programming Fundamentals',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-3 days'))
    ]
];

try {
    $stmt = $pdo->prepare("INSERT INTO lecturerrecentactivitytbl (LecturerID, activity_type, activity_description, timestamp) VALUES (?, ?, ?, ?)");
    
    foreach ($activities as $activity) {
        $stmt->execute([
            $activity['LecturerID'],
            $activity['activity_type'],
            $activity['activity_description'],
            $activity['timestamp']
        ]);
    }
    
    echo "Sample activities added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>