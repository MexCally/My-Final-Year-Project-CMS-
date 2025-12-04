<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'] ?? '';

// Get all assignments for enrolled courses
try {
    $stmt = $pdo->prepare("
        SELECT a.*, c.course_code, c.course_title,
               s.sub_id, s.submitted_at, s.score_received,
               CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name
        FROM assignmenttbl a
        JOIN coursetbl c ON a.course_id = c.course_id
        JOIN course_regtbl cr ON c.course_id = cr.course_id
        LEFT JOIN ass_subtbl s ON a.assignment_id = s.assignment_id AND s.student_id = ?
        LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
        WHERE cr.student_id = ? AND cr.approval_status IN ('Approved', 'Registered')
        AND a.is_active = 1
        ORDER BY a.due_date ASC
    ");
    $stmt->execute([$student_id, $student_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $assignments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assignments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/student_dashboard.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="student_dashboard.php">
                <i class="fas fa-graduation-cap me-2"></i>Student Portal
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($student_name); ?></span>
                <a class="nav-link" href="student_logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="student_dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="student_assignments.php">
                                <i class="fas fa-tasks me-2"></i>Assignments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="student_profile.php">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">My Assignments</h1>
                </div>

                <?php if (empty($assignments)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-tasks fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Assignments Available</h4>
                        <p class="text-muted">You don't have any assignments at the moment.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($assignments as $assignment): 
                            $dueDate = new DateTime($assignment['due_date']);
                            $now = new DateTime();
                            $isOverdue = $dueDate < $now;
                            $isSubmitted = !empty($assignment['sub_id']);
                            $statusClass = $isSubmitted ? 'success' : ($isOverdue ? 'danger' : 'warning');
                            $statusText = $isSubmitted ? 'Submitted' : ($isOverdue ? 'Overdue' : 'Pending');
                        ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border-<?php echo $statusClass; ?>">
                                    <div class="card-header bg-<?php echo $statusClass; ?> text-white">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($assignment['course_code']); ?></h6>
                                        <small><?php echo htmlspecialchars($assignment['course_title']); ?></small>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($assignment['title']); ?></h5>
                                        <p class="card-text text-muted small">
                                            <?php echo htmlspecialchars(substr($assignment['description'], 0, 100)) . '...'; ?>
                                        </p>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars($assignment['lecturer_name']); ?>
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Due: <?php echo $dueDate->format('M j, Y g:i A'); ?>
                                            </small>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-star me-1"></i>
                                                Max Score: <?php echo $assignment['max_score']; ?> points
                                            </small>
                                        </div>
                                        <?php if ($isSubmitted): ?>
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Submitted: <?php echo (new DateTime($assignment['submitted_at']))->format('M j, Y'); ?>
                                                </small>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-success">Submitted</span>
                                                <?php if ($assignment['score_received'] !== null): ?>
                                                    <div class="text-end">
                                                        <strong class="text-success"><?php echo $assignment['score_received']; ?>/<?php echo $assignment['max_score']; ?></strong>
                                                        <br><small class="text-muted">Grade: Graded</small>
                                                    </div>
                                                <?php else: ?>
                                                    <small class="text-warning">Grade: Pending</small>
                                                <?php endif; ?>
                                            </div>
                                        <?php elseif (!$isOverdue): ?>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-warning">Pending</span>
                                                <button class="btn btn-primary btn-sm" 
                                                        onclick="openSubmitModal(<?php echo $assignment['assignment_id']; ?>, '<?php echo htmlspecialchars($assignment['title'], ENT_QUOTES); ?>')">
                                                    <i class="fas fa-upload me-1"></i>Submit
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-danger">Overdue</span>
                                                <small class="text-danger">No submission allowed</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Submit Assignment Modal -->
    <div class="modal fade" id="submitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="submitForm" enctype="multipart/form-data">
                        <input type="hidden" id="assignmentId" name="assignment_id">
                        <div class="mb-3">
                            <label class="form-label">Assignment Title</label>
                            <p id="assignmentTitle" class="form-control-plaintext fw-bold"></p>
                        </div>
                        <div class="mb-3">
                            <label for="assignmentFile" class="form-label">Upload File</label>
                            <input type="file" class="form-control" id="assignmentFile" name="assignment_file" required>
                            <div class="form-text">Accepted formats: PDF, DOC, DOCX, TXT (Max: 10MB)</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitBtn">Submit Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openSubmitModal(assignmentId, title) {
            document.getElementById('assignmentId').value = assignmentId;
            document.getElementById('assignmentTitle').textContent = title;
            document.getElementById('assignmentFile').value = '';
            new bootstrap.Modal(document.getElementById('submitModal')).show();
        }

        document.getElementById('submitBtn').addEventListener('click', function() {
            const form = document.getElementById('submitForm');
            const formData = new FormData(form);
            
            if (!formData.get('assignment_file')) {
                alert('Please select a file to upload');
                return;
            }
            
            this.disabled = true;
            this.textContent = 'Submitting...';
            
            fetch('../PHP/submit_assignment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Assignment submitted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Failed to submit assignment');
            })
            .finally(() => {
                this.disabled = false;
                this.textContent = 'Submit Assignment';
            });
        });
    </script>
</body>
</html>