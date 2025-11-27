<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];

// Check if registration is approved
$currentSemester = 'Fall 2024';
$currentYear = '2024/2025';

$approvalStmt = $pdo->prepare("SELECT approval_status FROM course_regtbl WHERE student_id = ? AND semester = ? AND academic_year = ? LIMIT 1");
$approvalStmt->execute([$student_id, $currentSemester, $currentYear]);
$approvalStatus = $approvalStmt->fetchColumn();

if ($approvalStatus !== 'approved') {
    header('Location: course_selection.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(to right, #e2e2e2, #c9d6ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .download-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        .format-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .format-option:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .format-option.selected {
            border-color: #667eea;
            background: #e3f2fd;
        }
        .btn-download {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="download-card">
        <div class="text-center mb-4">
            <i class="bi bi-download text-primary" style="font-size: 3rem;"></i>
            <h2 class="mt-3">Download Registration Form</h2>
            <p class="text-muted">Choose your preferred format</p>
        </div>

        <form id="downloadForm">
            <div class="format-option" data-format="pdf">
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark-pdf text-danger me-3" style="font-size: 2rem;"></i>
                    <div>
                        <h5 class="mb-1">PDF Format</h5>
                        <small class="text-muted">Portable Document Format - Best for printing and official use</small>
                    </div>
                    <input type="radio" name="format" value="pdf" class="ms-auto" checked>
                </div>
            </div>

            <div class="format-option" data-format="docx">
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark-word text-primary me-3" style="font-size: 2rem;"></i>
                    <div>
                        <h5 class="mb-1">Word Document</h5>
                        <small class="text-muted">Microsoft Word format - Editable document</small>
                    </div>
                    <input type="radio" name="format" value="docx" class="ms-auto">
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-download">
                    <i class="bi bi-download me-2"></i>Download Form
                </button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="course_selection.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Courses
            </a>
        </div>
    </div>

    <script>
        document.querySelectorAll('.format-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.format-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        document.getElementById('downloadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const format = document.querySelector('input[name="format"]:checked').value;
            window.location.href = `download_registration_form.php?format=${format}`;
        });
    </script>
</body>
</html>