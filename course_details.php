<?php
// Get course ID from URL parameter
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Simulate database queries
function getCourseById($id) {
    // Simulate course data from database
    $course_codes = ['CS301', 'MTH201', 'PHY301', 'ENG101', 'BIO201'];
    $departments = ['Computer Science', 'Mathematics', 'Physics', 'English', 'Biology'];
    $levels = ['100', '200', '300', '400'];
    $semesters = ['Fall 2024', 'Spring 2024', 'Summer 2024'];
    
    $course_code = $course_codes[($id - 1) % count($course_codes)];
    $dept = $departments[($id - 1) % count($departments)];
    $level = $levels[($id - 1) % count($levels)] . ' Level';
    
    return [
        'id' => $id,
        'course_code' => $course_code,
        'course_title' => generateCourseTitle($course_code),
        'course_description' => generateDescription($dept),
        'department' => $dept,
        'level' => $level,
        'course_unit' => rand(2, 4) . ' Units',
        'semester' => $semesters[($id - 1) % count($semesters)],
        'lecturer_id' => ($id - 1) % 5 + 1
    ];
}

function getLecturerById($id) {
    $lecturers = [
        1 => ['name' => 'Dr. Sarah Johnson', 'email' => 'sarah.johnson@university.edu', 'office' => 'CS Building, Room 205'],
        2 => ['name' => 'Prof. Michael Chen', 'email' => 'michael.chen@university.edu', 'office' => 'Math Building, Room 301'],
        3 => ['name' => 'Dr. Emily Rodriguez', 'email' => 'emily.rodriguez@university.edu', 'office' => 'Physics Lab, Room 405'],
        4 => ['name' => 'Prof. James Wilson', 'email' => 'james.wilson@university.edu', 'office' => 'English Dept, Room 102'],
        5 => ['name' => 'Dr. Lisa Thompson', 'email' => 'lisa.thompson@university.edu', 'office' => 'Biology Lab, Room 501']
    ];
    return $lecturers[$id] ?? $lecturers[1];
}

function generateCourseTitle($code) {
    $titles = [
        'CS301' => 'Advanced Web Development',
        'MTH201' => 'Calculus II',
        'PHY301' => 'Quantum Physics',
        'ENG101' => 'English Composition',
        'BIO201' => 'Cell Biology'
    ];
    return $titles[$code] ?? 'Course Title';
}

function generateDescription($dept) {
    $descriptions = [
        'Computer Science' => 'Advanced programming concepts and software development methodologies.',
        'Mathematics' => 'Mathematical theories and problem-solving techniques.',
        'Physics' => 'Fundamental principles of physics and their applications.',
        'English' => 'Writing skills and literary analysis techniques.',
        'Biology' => 'Biological processes and life sciences fundamentals.'
    ];
    return $descriptions[$dept] ?? 'Course description not available.';
}

function generateCourseData($course) {
    $topics = [
        'CS301' => ['Web Frameworks', 'Frontend Development', 'Backend APIs', 'Database Integration'],
        'MTH201' => ['Integration Techniques', 'Series and Sequences', 'Multivariable Calculus'],
        'PHY301' => ['Quantum Mechanics', 'Wave Functions', 'Particle Physics'],
        'ENG101' => ['Essay Writing', 'Grammar', 'Literature Analysis'],
        'BIO201' => ['Cell Structure', 'Metabolism', 'Genetics']
    ];
    
    $course_topics = $topics[$course['course_code']] ?? ['Topic 1', 'Topic 2', 'Topic 3'];
    
    $course['learning_outcomes'] = array_map(function($topic) {
        return 'Understand and apply ' . strtolower($topic) . ' concepts';
    }, $course_topics);
    
    $course['course_outline'] = [];
    foreach($course_topics as $i => $topic) {
        $course['course_outline'][] = ['week' => $i + 1, 'topic' => $topic];
    }
    
    $course['assessment'] = [
        ['type' => 'Assignments', 'percentage' => '30%'],
        ['type' => 'Mid-term Exam', 'percentage' => '25%'],
        ['type' => 'Final Exam', 'percentage' => '35%'],
        ['type' => 'Participation', 'percentage' => '10%']
    ];
    
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    $times = ['9:00 AM - 11:00 AM', '11:00 AM - 1:00 PM', '2:00 PM - 4:00 PM'];
    $locations = ['Room 101', 'Lab 3', 'Lecture Hall A', 'Library'];
    
    $course['schedule'] = [
        ['day' => $days[($course['id'] - 1) % count($days)], 
         'time' => $times[($course['id'] - 1) % count($times)], 
         'location' => $locations[($course['id'] - 1) % count($locations)]]
    ];
    
    $course['resources'] = [
        'Textbook: ' . $course['course_title'] . ' by Academic Press',
        'Online Resources: University Learning Portal'
    ];
    
    $course['prerequisites'] = $course['level'] === '100 Level' ? [] : [
        'Previous level course in ' . $course['department']
    ];
    
    return $course;
}

// Get course and lecturer data
$course = getCourseById($course_id);
$lecturer = getLecturerById($course['lecturer_id']);
$course = generateCourseData($course);

// Merge lecturer data into course
$course['lecturer_name'] = $lecturer['name'];
$course['lecturer_email'] = $lecturer['email'];
$course['lecturer_office'] = $lecturer['office'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?> - Course Manager</title>
  <meta name="description" content="Detailed information about <?php echo htmlspecialchars($course['course_title']); ?>">
  <meta name="keywords" content="course, education, <?php echo htmlspecialchars($course['department']); ?>">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    .course-hero {
      background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
      color: white;
      padding: 80px 0;
      margin-top: 70px;
    }
    
    .course-details-container {
      padding: 60px 0;
    }
    
    .detail-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      padding: 30px;
      margin-bottom: 30px;
      transition: transform 0.3s ease;
    }
    
    .detail-card:hover {
      transform: translateY(-5px);
    }
    
    .detail-card h3 {
      color: #2d3436;
      border-bottom: 3px solid #00b894;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    
    .lecturer-info {
      background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
      color: white;
      border-radius: 15px;
      padding: 25px;
    }
    
    .lecturer-info h4 {
      margin-bottom: 15px;
    }
    
    .info-item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }
    
    .info-item i {
      margin-right: 10px;
      width: 20px;
    }
    
    .outcome-item, .topic-item {
      background: #f1f9f8;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 10px;
      border-left: 4px solid #00b894;
    }
    
    .assessment-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }
    
    .assessment-item {
      background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
      color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
    }
    
    .schedule-table {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .schedule-table th {
      background: #00b894;
      color: white;
      padding: 15px;
    }
    
    .schedule-table td {
      padding: 15px;
      border-bottom: 1px solid #eee;
    }
    
    .resource-item {
      background: #e8f8f5;
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 8px;
      border-left: 4px solid #00b894;
    }
    
    .enroll-btn {
      background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
      color: white;
      padding: 15px 30px;
      border: none;
      border-radius: 50px;
      font-size: 18px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .enroll-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0, 184, 148, 0.4);
      color: white;
    }
    
    .back-btn {
      background: #636e72;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 25px;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .back-btn:hover {
      background: #2d3436;
      color: white;
      text-decoration: none;
    }
    
    .course-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }
    
    .stat-item {
      background: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .stat-number {
      font-size: 2rem;
      font-weight: bold;
      color: #00b894;
    }
    
    .breadcrumb-custom {
      background: rgba(255,255,255,0.9);
      padding: 10px 20px;
      border-radius: 25px;
      margin-bottom: 20px;
    }
  </style>
</head>

<body class="course-details-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center me-auto me-lg-0">
        <h1 class="sitename">Course Manager</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="searchcourse.php" class="active">Search Course</a></li>
          <li><a href="admin_register.php">Register</a></li>
          <li class="dropdown"><a href="#"><span>Login</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="authentications/student_login.php">Student</a></li>
              <li><a href="authentications/lecturer_login.php">Lecturer</a></li>
              <li><a href="authentications/admin_login.php">Admin</a></li>
            </ul>
          </li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="help.php">Help</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">


    <!-- Course Hero Section -->
    <section class="course-hero">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <nav class="breadcrumb-custom">
              <a href="index.php" class="text-decoration-none">Home</a> 
              <span class="mx-2">/</span>
              <a href="searchcourse.php" class="text-decoration-none">Courses</a>
              <span class="mx-2">/</span>
              <span><?php echo htmlspecialchars($course['course_code']); ?></span>
            </nav>
            
            <h1 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($course['course_code']); ?></h1>
            <h2 class="h3 mb-4"><?php echo htmlspecialchars($course['course_title']); ?></h2>
            <p class="lead mb-4"><?php echo htmlspecialchars($course['course_description']); ?></p>
            
            <div class="d-flex gap-3 flex-wrap">
              <button class="enroll-btn">
                <i class="bi bi-person-plus me-2"></i>Enroll Now
              </button>
              <a href="searchcourse.php" class="back-btn">
                <i class="bi bi-arrow-left me-2"></i>Back to Courses
              </a>
            </div>
          </div>
          
          <div class="col-lg-4">
            <div class="course-stats">
              <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(str_replace(' Units', '', $course['course_unit'])); ?></div>
                <div>Credits</div>
              </div>
              <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(str_replace(' Level', '', $course['level'])); ?></div>
                <div>Level</div>
              </div>
              <div class="stat-item">
                <div class="stat-number"><?php echo count($course['course_outline']); ?></div>
                <div>Weeks</div>
              </div>
              <div class="stat-item">
                <div class="stat-number"><?php echo rand(15 + ($course_id * 5), 25 + ($course_id * 10)); ?></div>
                <div>Students</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Course Details Section -->
    <section class="course-details-container">
      <div class="container">
        <div class="row">
          <!-- Main Content -->
          <div class="col-lg-8">
            
            <!-- Learning Outcomes -->
            <div class="detail-card" data-aos="fade-up">
              <h3><i class="bi bi-target me-2"></i>Learning Outcomes</h3>
              <p class="mb-4">By the end of this course, students will be able to:</p>
              <?php foreach ($course['learning_outcomes'] as $outcome): ?>
                <div class="outcome-item">
                  <i class="bi bi-check-circle-fill text-success me-2"></i>
                  <?php echo htmlspecialchars($outcome); ?>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Course Outline -->
            <div class="detail-card" data-aos="fade-up" data-aos-delay="100">
              <h3><i class="bi bi-list-ol me-2"></i>Course Outline</h3>
              <div class="row">
                <?php foreach ($course['course_outline'] as $topic): ?>
                  <div class="col-md-6 mb-3">
                    <div class="topic-item">
                      <strong>Week <?php echo $topic['week']; ?>:</strong><br>
                      <?php echo htmlspecialchars($topic['topic']); ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Assessment -->
            <div class="detail-card" data-aos="fade-up" data-aos-delay="200">
              <h3><i class="bi bi-clipboard-check me-2"></i>Assessment Breakdown</h3>
              <div class="assessment-grid">
                <?php foreach ($course['assessment'] as $assessment): ?>
                  <div class="assessment-item">
                    <h5><?php echo htmlspecialchars($assessment['type']); ?></h5>
                    <div class="h4 mb-0"><?php echo htmlspecialchars($assessment['percentage']); ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Schedule -->
            <div class="detail-card" data-aos="fade-up" data-aos-delay="300">
              <h3><i class="bi bi-calendar-week me-2"></i>Class Schedule</h3>
              <div class="table-responsive">
                <table class="table schedule-table mb-0">
                  <thead>
                    <tr>
                      <th>Day</th>
                      <th>Time</th>
                      <th>Location</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($course['schedule'] as $class): ?>
                      <tr>
                        <td><strong><?php echo htmlspecialchars($class['day']); ?></strong></td>
                        <td><?php echo htmlspecialchars($class['time']); ?></td>
                        <td><?php echo htmlspecialchars($class['location']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Resources -->
            <div class="detail-card" data-aos="fade-up" data-aos-delay="400">
              <h3><i class="bi bi-book me-2"></i>Course Resources</h3>
              <?php foreach ($course['resources'] as $resource): ?>
                <div class="resource-item">
                  <i class="bi bi-file-text me-2"></i>
                  <?php echo htmlspecialchars($resource); ?>
                </div>
              <?php endforeach; ?>
            </div>

          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">
            
            <!-- Lecturer Information -->
            <div class="detail-card" data-aos="fade-up">
              <div class="lecturer-info">
                <h4><i class="bi bi-person-circle me-2"></i>Course Instructor</h4>
                <div class="info-item">
                  <i class="bi bi-person-fill"></i>
                  <strong><?php echo htmlspecialchars($course['lecturer_name']); ?></strong>
                </div>
                <div class="info-item">
                  <i class="bi bi-envelope-fill"></i>
                  <a href="mailto:<?php echo htmlspecialchars($course['lecturer_email']); ?>" class="text-white">
                    <?php echo htmlspecialchars($course['lecturer_email']); ?>
                  </a>
                </div>
                <div class="info-item">
                  <i class="bi bi-geo-alt-fill"></i>
                  <?php echo htmlspecialchars($course['lecturer_office']); ?>
                </div>
              </div>
            </div>

            <!-- Course Information -->
            <div class="detail-card" data-aos="fade-up" data-aos-delay="100">
              <h3><i class="bi bi-info-circle me-2"></i>Course Information</h3>
              <div class="info-item mb-3">
                <i class="bi bi-building"></i>
                <div>
                  <strong>Department:</strong><br>
                  <?php echo htmlspecialchars($course['department']); ?>
                </div>
              </div>
              <div class="info-item mb-3">
                <i class="bi bi-calendar3"></i>
                <div>
                  <strong>Semester:</strong><br>
                  <?php echo htmlspecialchars($course['semester']); ?>
                </div>
              </div>
              <div class="info-item mb-3">
                <i class="bi bi-award"></i>
                <div>
                  <strong>Credit Units:</strong><br>
                  <?php echo htmlspecialchars($course['course_unit']); ?>
                </div>
              </div>
              <div class="info-item">
                <i class="bi bi-graph-up"></i>
                <div>
                  <strong>Level:</strong><br>
                  <?php echo htmlspecialchars($course['level']); ?>
                </div>
              </div>
            </div>

            <!-- Prerequisites -->
            <div class="detail-card" data-aos="fade-up" data-aos-delay="200">
              <h3><i class="bi bi-list-check me-2"></i>Prerequisites</h3>
              <?php if (!empty($course['prerequisites'])): ?>
                <?php foreach ($course['prerequisites'] as $prereq): ?>
                  <div class="outcome-item">
                    <i class="bi bi-arrow-right-circle-fill text-primary me-2"></i>
                    <?php echo htmlspecialchars($prereq); ?>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p class="text-muted">No prerequisites required</p>
              <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="detail-card" data-aos="fade-up" data-aos-delay="300">
              <h3><i class="bi bi-lightning me-2"></i>Quick Actions</h3>
              <div class="d-grid gap-2">
                <button class="btn btn-primary">
                  <i class="bi bi-download me-2"></i>Download Syllabus
                </button>
                <button class="btn btn-outline-primary">
                  <i class="bi bi-share me-2"></i>Share Course
                </button>
                <button class="btn btn-outline-secondary">
                  <i class="bi bi-heart me-2"></i>Add to Wishlist
                </button>
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
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">CourseManager</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Educational Zone</p>
            <p>Ibadan 200132, Oyo</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+234 708 016 3502</span></p>
            <p><strong>Email:</strong> <span>stellaukas@gmail.com</span></p>
          </div>
        </div>
        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="searchcourse.php">Search Courses</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="help.php">Help</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="container copyright text-center mt-4">
      <p>&copy; <?php echo date('Y'); ?> CourseManager. All Rights Reserved.</p>
    </div>
  </footer>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  <script>
    // Initialize AOS
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  </script>

</body>
</html>