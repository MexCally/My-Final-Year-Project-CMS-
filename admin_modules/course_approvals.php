<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin_login.html');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_id = $_POST['registration_id'];
    $action = $_POST['action'];
    
    if ($action === 'approve' || $action === 'reject') {
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $stmt = $pdo->prepare("UPDATE course_registrations SET status = ?, approval_date = NOW(), approved_by = ? WHERE registration_id = ?");
        $stmt->execute([$status, $admin_id, $registration_id]);
        
        $message = "Registration " . ($action === 'approve' ? 'approved' : 'rejected') . " successfully!";
    }
}

// Fetch pending registrations
$stmt = $pdo->prepare("
    SELECT cr.*, s.Matric_No, s.first_name, s.last_name, s.Department, s.Level,
           COUNT(cri.course_id) as course_count
    FROM course_registrations cr
    JOIN studenttbl s ON cr.student_id = s.student_id
    LEFT JOIN course_registration_items cri ON cr.registration_id = cri.registration_id
    WHERE cr.status = 'pending'
    GROUP BY cr.registration_id
    ORDER BY cr.submission_date ASC
");
$stmt->execute();
$pending_registrations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration Approvals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="bi bi-clipboard-check me-2"></i>Course Registration Approvals</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($message)): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i><?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($pending_registrations)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>No pending registrations found.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Student</th>
                                            <th>Matric No</th>
                                            <th>Department</th>
                                            <th>Level</th>
                                            <th>Semester</th>
                                            <th>Courses</th>
                                            <th>Total Units</th>
                                            <th>Submitted</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pending_registrations as $reg): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($reg['Matric_No']); ?></td>
                                                <td><?php echo htmlspecialchars($reg['Department']); ?></td>
                                                <td><?php echo htmlspecialchars($reg['Level']); ?></td>
                                                <td><?php echo htmlspecialchars($reg['semester']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info" onclick="viewCourses(<?php echo $reg['registration_id']; ?>)">
                                                        View <?php echo $reg['course_count']; ?> Courses
                                                    </button>
                                                </td>
                                                <td><?php echo $reg['total_units']; ?></td>
                                                <td><?php echo date('M j, Y', strtotime($reg['submission_date'])); ?></td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="registration_id" value="<?php echo $reg['registration_id']; ?>">
                                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success me-1">
                                                            <i class="bi bi-check-lg"></i> Approve
                                                        </button>
                                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-x-lg"></i> Reject
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Details Modal -->
    <div class="modal fade" id="courseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registered Courses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="courseModalBody">
                    Loading...
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewCourses(registrationId) {
            fetch('get_registration_courses.php?id=' + registrationId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('courseModalBody').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('courseModal')).show();
                });
        }
    </script>
</body>
</html>