<?php
require_once 'config/db.php';

// Get course ID from URL
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($course_id <= 0) {
    header('Location: searchcourse.php');
    exit;
}

// Fetch course details
try {
    $stmt = $pdo->prepare("
        SELECT c.*, l.lecturer_name
        FROM coursetbl c
        LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
        WHERE c.course_id = ?
    ");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        header('Location: searchcourse.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: searchcourse.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo htmlspecialchars($course['course_title']); ?> - Course Details</title>
    
    <!-- Favicons -->
    <link href="assets/img/logo1.jpg" rel="icon">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    
    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="course-details-page">
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">
            <a href="index.html" class="logo d-flex align-items-center me-auto">
                <h1 class="sitename">Course Manager</h1>
            </a>
            
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="searchcourse.php" class="active">Search Course</a></li>
                    <li><a href="admin_register.html">Register</a></li>
                    <li class="dropdown"><a href="#"><span>Login</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="authentications/student_login.html">Student</a></li>
                            <li><a href="authentications/lecturer_login.html">Lecturer</a></li>
                            <li><a href="authentications/admin_login.html">Admin</a></li>
                        </ul>
                    </li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="help.html">Help</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </header>

    <main class="main">
        <section class="course-details-section py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="course-details-card">
                            <div class="course-header mb-4">
                                <h1 class="course-title"><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?></h1>
                                <p class="course-meta text-muted">
                                    <i class="fas fa-building"></i> <?php echo htmlspecialchars($course['department']); ?> | 
                                    <i class="fas fa-layer-group"></i> Level <?php echo htmlspecialchars($course['level']); ?> | 
                                    <i class="fas fa-credit-card"></i> <?php echo htmlspecialchars($course['course_unit']); ?> Units
                                </p>
                            </div>
                            
                            <div class="course-image mb-4">
                                <img src="assets/img/masonry-portfolio/masonry-portfolio-9.jpg" alt="<?php echo htmlspecialchars($course['course_title']); ?>" class="img-fluid rounded">
                            </div>
                            
                            <div class="course-description mb-4">
                                <h3>Course Description</h3>
                                <p><?php echo nl2br(htmlspecialchars($course['course_description'])); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="course-sidebar">
                            <div class="course-info-card mb-4">
                                <h4>Course Information</h4>
                                <ul class="list-unstyled">
                                    <li><strong>Course Code:</strong> <?php echo htmlspecialchars($course['course_code']); ?></li>
                                    <li><strong>Department:</strong> <?php echo htmlspecialchars($course['department']); ?></li>
                                    <li><strong>Level:</strong> <?php echo htmlspecialchars($course['level']); ?></li>
                                    <li><strong>Units:</strong> <?php echo htmlspecialchars($course['course_unit']); ?></li>
                                    <li><strong>Semester:</strong> <?php echo htmlspecialchars($course['semester'] ?? 'N/A'); ?></li>
                                    <li><strong>Lecturer:</strong> <?php echo htmlspecialchars($course['lecturer_name'] ?? 'Not Assigned'); ?></li>
                                </ul>
                            </div>
                            
                            <div class="course-actions">
                                <a href="searchcourse.php" class="btn btn-secondary w-100 mb-2">
                                    <i class="fas fa-arrow-left"></i> Back to Search
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer" class="footer dark-background">
        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="index.html" class="logo d-flex align-items-center">
                        <span class="sitename">CourseManager</span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>Educational Zone</p>
                        <p>Ibadan 200132, Oyo</p>
                        <p class="mt-3"><strong>Phone:</strong> <span>+234 708 016 3502</span></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    
    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>
</body>
</html>