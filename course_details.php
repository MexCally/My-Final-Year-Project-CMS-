<?php
session_start();
require_once 'config/db.php';

$course_id = $_GET['id'] ?? null;
$course = null;
$error = null;

if (!$course_id || !is_numeric($course_id)) {
    $error = "Invalid course ID";
} else {
    try {
        $stmt = $pdo->prepare("SELECT c.*, CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name 
                              FROM coursetbl c 
                              LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID 
                              WHERE c.course_id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            $error = "Course not found";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo $course ? htmlspecialchars($course['course_title']) : 'Course Details'; ?> - CourseManager</title>
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="course-details-page">
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">
            <a href="index.html" class="logo d-flex align-items-center me-auto">
                <h1 class="sitename">CourseManager</h1>
            </a>
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="searchcourse.php">Search Courses</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="page-title" data-aos="fade">
            <div class="container">
                <nav class="breadcrumbs">
                    <ol>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="searchcourse.php">Search Courses</a></li>
                        <li class="current">Course Details</li>
                    </ol>
                </nav>
                <h1><?php echo $course ? htmlspecialchars($course['course_title']) : 'Course Details'; ?></h1>
            </div>
        </div>

        <section id="course-details" class="course-details section">
            <div class="container">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                    <div class="text-center">
                        <a href="searchcourse.php" class="btn btn-primary">Back to Search</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="course-info">
                                <h2><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?></h2>
                                
                                <div class="course-meta mb-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Department:</strong> <?php echo htmlspecialchars($course['department']); ?></p>
                                            <p><strong>Level:</strong> <?php echo htmlspecialchars($course['level']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Credit Units:</strong> <?php echo htmlspecialchars($course['course_unit']); ?></p>
                                            <p><strong>Semester:</strong> <?php echo htmlspecialchars($course['semester'] ?? 'N/A'); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="course-description">
                                    <h3>Course Description</h3>
                                    <p><?php echo nl2br(htmlspecialchars($course['course_description'] ?? 'No description available.')); ?></p>
                                </div>

                                <div class="course-instructor mt-4">
                                    <h3>Instructor</h3>
                                    <p><?php echo htmlspecialchars($course['lecturer_name'] ?? 'Not Assigned'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="course-sidebar">
                                <?php if (!empty($course['course_image'])): ?>
                                    <div class="course-image mb-4">
                                        <img src="<?php echo htmlspecialchars($course['course_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($course['course_title']); ?>" 
                                             class="img-fluid rounded">
                                    </div>
                                <?php endif; ?>

                                <div class="course-actions">
                                    <h4>Course Actions</h4>
                                    <div class="d-grid gap-2">
                                        <a href="authentications/student_login.html" class="btn btn-primary">Login to Enroll</a>
                                        <a href="searchcourse.php" class="btn btn-outline-secondary">Back to Search</a>
                                    </div>
                                </div>

                                <div class="course-info-box mt-4">
                                    <h4>Course Information</h4>
                                    <ul class="list-unstyled">
                                        <li><strong>Course Code:</strong> <?php echo htmlspecialchars($course['course_code']); ?></li>
                                        <li><strong>Department:</strong> <?php echo htmlspecialchars($course['department']); ?></li>
                                        <li><strong>Level:</strong> <?php echo htmlspecialchars($course['level']); ?></li>
                                        <li><strong>Units:</strong> <?php echo htmlspecialchars($course['course_unit']); ?></li>
                                        <li><strong>Semester:</strong> <?php echo htmlspecialchars($course['semester'] ?? 'N/A'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer id="footer" class="footer dark-background">
        <div class="container copyright text-center mt-4">
            <p>&copy; <?php echo date('Y'); ?> CourseManager. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>