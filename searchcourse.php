<?php
require_once 'config/db.php';

// Fetch all courses with lecturer information
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.course_id,
            c.course_code,
            c.course_title,
            c.course_description,
            c.course_unit,
            c.department,
            c.level,
            c.semester,
            CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name
        FROM coursetbl c
        LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
        ORDER BY c.course_code
    ");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $courses = [];
}

// Handle search
$search_results = $courses;
if (isset($_POST['searchbtn']) && !empty($_POST['search_field'])) {
    $search_term = '%' . $_POST['search_field'] . '%';
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.course_id,
                c.course_code,
                c.course_title,
                c.course_description,
                c.course_unit,
                c.department,
                c.level,
                c.semester,
                CONCAT(l.First_name, ' ', l.Last_Name) as lecturer_name
            FROM coursetbl c
            LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
            WHERE c.course_code LIKE ? OR c.course_title LIKE ? OR c.department LIKE ?
            ORDER BY c.course_code
        ");
        $stmt->execute([$search_term, $search_term, $search_term]);
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $search_results = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Search Courses</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/logo1.jpg" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  <link href="assets/css/course-cards.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Sailor
  * Template URL: https://bootstrapmade.com/sailor-free-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="searchcourse page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.html" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1 class="sitename">Course Manager</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.html" class="active">Home</a></li>
          <li><a href="searchcourse.html">Search Course</a></li>
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
    <section class="searchcourse-section">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <div class="search-container">
          <h2>Search for Course</h2>
          <p>Find the courses you need quickly and easily.</p>
          
          <div class="search-box">
            <form method="post" action="">
              <input type="text" name="search_field" placeholder="Search for courses..." required>
              <button type="submit" name="searchbtn"><i class="fas fa-search"></i></button>
            </form>
          </div>
        </div>

        <h2 class="available-courses-header">Available Courses</h2>

        <div class="row justify-content-center">
          <div class="col-lg-8">

            <form action="#" method="post" class="searchcourse-form" role="form" data-aos="fade-up" data-aos-delay="100">
              <div class="row gy-4">
                <div class="col-md-6">
                  <input type="text" name="course-name" class="form-control" placeholder="Course Name" required>
                </div>
                <div class="col-md-6">
                  <input type="text" name="course-code" class="form-control" placeholder="Course Code" required>
                </div>
                <div class="col-md-6">
                  <input type="text" name="instructor" class="form-control" placeholder="Instructor Name">
                </div>
                <div class="col-md-6">
                  <select name="semester" class="form-select">
                    <option value="" disabled selected>Select Semester</option>
                    <option value="fall">Fall</option>
                    <option value="spring">Spring</option>
                    <option value="summer">Summer</option>
                  </select>
                </div>
                <div class="col-md-12 text-center">
                  <button type="submit" class="btn btn-primary">Search Courses</button>
                </div>
              </div>
            </form>

          </div>
        </div>

      </div>
    </section>
    </main> <!-- End Search Course Section -->

    <!-- Dynamic Courses -->
    <div class="container mt-5">
        <div class="row">
            <?php if (empty($search_results)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No courses found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($search_results as $course): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="course-card">
                            <div class="course-image">
                                <img src="assets/img/masonry-portfolio/masonry-portfolio-9.jpg" alt="<?php echo htmlspecialchars($course['course_title']); ?>">
                            </div>
                            <div class="course-content">
                                <h3 class="course-title"><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?></h3>
                                <p class="course-description">
                                    <?php echo htmlspecialchars(substr($course['course_description'], 0, 150) . '...'); ?>
                                </p>
                                <div class="course-details">
                                    <p><strong>Department:</strong> <?php echo htmlspecialchars($course['department']); ?></p>
                                    <p><strong>Level:</strong> <?php echo htmlspecialchars($course['level']); ?></p>
                                    <p><strong>Units:</strong> <?php echo htmlspecialchars($course['course_unit']); ?></p>
                                    <p><strong>Semester:</strong> <?php echo htmlspecialchars($course['semester'] ?? 'N/A'); ?></p>
                                </div>
                                <p class="course-teacher">
                                    Teacher: <span><?php echo htmlspecialchars($course['lecturer_name'] ?? 'Not Assigned'); ?></span>
                                </p>
                                <a href="course_details.php" class="read-more-btn">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>




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
            <p><strong>Email:</strong> <span>stellaukas@gmail.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="#about">About us</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="searchcourse.html">Search Courses</a></li>
            <li><a href="help.html">Help</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">Course Management</a></li>
            <li><a href="#">Assignment Tracking</a></li>
            <li><a href="#">Student-Lecturer Collaboration</a></li>
            <li><a href="#">Performance Analytics</a></li>
            <li><a href="#">Administration Access</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-12 footer-newsletter">
          <h4>About Our Mission</h4>
          <p>Our mission is to simplify course delivery, improve student engagement, and support institutions with smart analytics.</p> 
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p id="copyright"></p>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  <script>
        document.getElementById("copyright").innerHTML = "Copyright &copy; " + new Date().getFullYear() + " CourseManager. All Rights Reserved.";
  </script>
</body>
</html>