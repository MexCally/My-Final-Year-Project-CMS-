<?php
require_once '../config/db.php';

$registration_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT c.course_code, c.course_title, c.course_unit, c.department,
           CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name
    FROM course_registration_items cri
    JOIN coursetbl c ON cri.course_id = c.course_id
    LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
    WHERE cri.registration_id = ?
    ORDER BY c.course_code
");
$stmt->execute([$registration_id]);
$courses = $stmt->fetchAll();

if (empty($courses)) {
    echo '<p class="text-muted">No courses found.</p>';
    exit;
}
?>

<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Units</th>
                <th>Department</th>
                <th>Lecturer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_title']); ?></td>
                    <td><?php echo $course['course_unit']; ?></td>
                    <td><?php echo htmlspecialchars($course['department']); ?></td>
                    <td><?php echo htmlspecialchars($course['lecturer_name'] ?? 'TBD'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>