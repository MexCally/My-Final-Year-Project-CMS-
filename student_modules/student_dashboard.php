<?php
session_start();
require_once '../config/db.php';

// Redirect to login if student is not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch logged-in student details
$stmt = $pdo->prepare("SELECT student_id, Matric_No, first_name, last_name, email, Department, Level, academic_year 
                       FROM studenttbl 
                       WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    // If student record is missing, force re-login
    session_unset();
    session_destroy();
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_name       = $student['first_name'] . ' ' . $student['last_name'];
$student_matric     = $student['Matric_No'];
$student_department = $student['Department'] ?? 'Unknown Department';
$student_level      = $student['Level'] ?? '';

// Initials for avatar
$firstInitial = mb_substr($student['first_name'], 0, 1, 'UTF-8');
$lastInitial  = mb_substr($student['last_name'], 0, 1, 'UTF-8');
$student_initials = strtoupper($firstInitial . $lastInitial);

// Fetch registered courses for this student (approved and pending)
$courseStmt = $pdo->prepare("
    SELECT 
        c.course_id,
        c.course_code,
        c.course_title,
        c.course_unit,
        c.department,
        c.level,
        c.semester,
        cr.approval_status,
        CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name
    FROM course_regtbl cr
    JOIN coursetbl c ON cr.course_id = c.course_id
    LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
    WHERE cr.student_id = ?
    ORDER BY c.course_code
");
$courseStmt->execute([$student_id]);
$enrolled_courses = $courseStmt->fetchAll();
$registered_courses_count = is_array($enrolled_courses) ? count($enrolled_courses) : 0;

// Fetch upcoming deadlines for enrolled courses
$deadlineStmt = $pdo->prepare("
    SELECT 
        d.deadline_id,
        d.title,
        d.description,
        d.deadline_date,
        d.created_at,
        c.course_code,
        c.course_title,
        CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name
    FROM deadlinetbl d
    JOIN coursetbl c ON d.course_id = c.course_id
    LEFT JOIN lecturertbl l ON d.lecturer_id = l.LecturerID
    WHERE d.course_id IN (
        SELECT course_id FROM course_regtbl WHERE student_id = ?
    )
    AND d.deadline_date >= CURDATE()
    ORDER BY d.deadline_date ASC
    LIMIT 5
");
$deadlineStmt->execute([$student_id]);
$upcoming_deadlines = $deadlineStmt->fetchAll();

// Fetch results for this student (uses resulttbl structure)
$resultStmt = $pdo->prepare("
    SELECT 
        r.course_id,
        r.ca_score,
        r.test_score,
        r.exam_score,
        r.total_score,
        r.grade_letter,
        r.academic_year,
        r.semester,
        c.course_code,
        c.course_title,
        c.course_unit
    FROM resulttbl r
    JOIN coursetbl c ON r.course_id = c.course_id
    WHERE r.student_id = ?
    ORDER BY c.course_code
");
$resultStmt->execute([$student_id]);
$results = $resultStmt->fetchAll();

// Get current academic year for this student from their record
$current_academic_year = $student['academic_year'] ?? null;

// Load recent activities for this student
$activityStmt = $pdo->prepare("
    SELECT activity_type, activity_description, timestamp 
    FROM studentrecentactivitytbl 
    WHERE student_id = ? 
    ORDER BY timestamp DESC 
    LIMIT 10
");
$activityStmt->execute([$student_id]);
$recent_activities = $activityStmt->fetchAll();

// GPA calculation based on score → points (0–5) and letter mapping
// Excel logic provided:
// =IF(C4<40,"0",IF(C4<45,"1",IF(C4<50,"2",IF(C4<60,"3",IF(C4<70,"4","5")))))
// =IF(C4<40,"F",IF(C4<45,"E",IF(C4<50,"D",IF(C4<60,"C",IF(C4<70,"B","A")))))
$letter_points_map = [
    'A' => 5.0,
    'B' => 4.0,
    'C' => 3.0,
    'D' => 2.0,
    'E' => 1.0,
    'F' => 0.0,
];

$total_points = 0.0;
$total_credits = 0;

if (is_array($results)) {
    foreach ($results as $row) {
        // Prefer numeric total_score; fall back to stored letter_grade if needed
        $raw_score = $row['total_score'] ?? null;
        $raw_letter = $row['grade_letter'] ?? '';

        $credits = (int)($row['course_unit'] ?? 0);
        if ($credits <= 0) {
            $credits = 3; // fallback credit hours
        }

        // Determine points either from numeric score or letter grade (Excel logic)
        $points = null;

        if ($raw_score !== null && $raw_score !== '' && is_numeric($raw_score)) {
            // Treat as numeric score (0–100)
            $score = (float)$raw_score;
            if ($score < 40) {
                $points = 0.0;
            } elseif ($score < 45) {
                $points = 1.0;
            } elseif ($score < 50) {
                $points = 2.0;
            } elseif ($score < 60) {
                $points = 3.0;
            } elseif ($score < 70) {
                $points = 4.0;
            } else {
                $points = 5.0;
            }
        } else {
            // Treat as letter grade (A–F)
            $letter = strtoupper(trim($raw_letter));
            if (isset($letter_points_map[$letter])) {
                $points = $letter_points_map[$letter];
            }
        }

        if ($points === null) {
            continue; // skip unrecognized grades
        }

        $total_credits += $credits;
        $total_points  += $credits * $points;
    }
}

$current_gpa = $total_credits > 0 ? number_format($total_points / $total_credits, 2) : 'N/A';

// Calculate semester-specific statistics
$semester_credits = 0;
$semester_points = 0.0;
$current_semester = null;
$current_academic_year_for_semester = null;

// Find the latest semester from results
if (!empty($results)) {
    foreach ($results as $row) {
        if ($row['academic_year'] && $row['semester']) {
            $current_academic_year_for_semester = $row['academic_year'];
            $current_semester = $row['semester'];
            break; // Assuming results are ordered by latest first
        }
    }
}

// Calculate semester totals
if ($current_semester && $current_academic_year_for_semester) {
    foreach ($results as $row) {
        if ($row['academic_year'] == $current_academic_year_for_semester && $row['semester'] == $current_semester) {
            $raw_score = $row['total_score'] ?? null;
            $raw_letter = $row['grade_letter'] ?? '';
            $credits = (int)($row['course_unit'] ?? 0);
            if ($credits <= 0) {
                $credits = 3;
            }

            $points = null;
            if ($raw_score !== null && $raw_score !== '' && is_numeric($raw_score)) {
                $score = (float)$raw_score;
                if ($score < 40) {
                    $points = 0.0;
                } elseif ($score < 45) {
                    $points = 1.0;
                } elseif ($score < 50) {
                    $points = 2.0;
                } elseif ($score < 60) {
                    $points = 3.0;
                } elseif ($score < 70) {
                    $points = 4.0;
                } else {
                    $points = 5.0;
                }
            } else {
                $letter = strtoupper(trim($raw_letter));
                if (isset($letter_points_map[$letter])) {
                    $points = $letter_points_map[$letter];
                }
            }

            if ($points !== null) {
                $semester_credits += $credits;
                $semester_points += $credits * $points;
            }
        }
    }
}

$semester_gpa = $semester_credits > 0 ? number_format($semester_points / $semester_credits, 2) : 'N/A';

// Determine academic standing
$academic_standing = 'Not Available';
if ($current_gpa !== 'N/A' && is_numeric($current_gpa)) {
    $gpa_value = (float)$current_gpa;
    if ($gpa_value >= 3.5) {
        $academic_standing = "Dean's List - Excellent Performance";
    } elseif ($gpa_value >= 3.0) {
        $academic_standing = "Good Standing";
    } elseif ($gpa_value >= 2.0) {
        $academic_standing = "Academic Warning";
    } else {
        $academic_standing = "Academic Probation";
    }
}

// Group results by academic year and semester for transcript
$transcript_data = [];
if (is_array($results)) {
    foreach ($results as $row) {
        $year = $row['academic_year'] ?? '';
        $sem = $row['semester'] ?? '';
        if ($year && $sem) {
            if (!isset($transcript_data[$year])) {
                $transcript_data[$year] = [];
            }
            if (!isset($transcript_data[$year][$sem])) {
                $transcript_data[$year][$sem] = [];
            }
            $transcript_data[$year][$sem][] = $row;
        }
    }
}

// Get course IDs with results
$result_course_ids = is_array($results) ? array_column($results, 'course_id') : [];

// In-progress courses: enrolled but no results
$in_progress_courses = [];
if (is_array($enrolled_courses)) {
    foreach ($enrolled_courses as $course) {
        if (!in_array($course['course_id'], $result_course_ids)) {
            $in_progress_courses[] = $course;
        }
    }
}

// Calculate in-progress credits
$in_progress_credits = 0;
if (!empty($in_progress_courses)) {
    foreach ($in_progress_courses as $course) {
        $in_progress_credits += (int)($course['course_unit'] ?? 3);
    }
}

// Academic honors based on GPA
$academic_honors = [];
if ($current_gpa !== 'N/A' && is_numeric($current_gpa)) {
    $gpa_value = (float)$current_gpa;
    if ($gpa_value >= 3.5) {
        $academic_honors[] = "Dean's List - Excellent Performance";
    }
}

try {
    $degreeRequirementsStmt = $pdo->prepare("SELECT category, required_credits FROM degree_requirementstbl");
    $degreeRequirementsStmt->execute();
    $degree_requirements = $degreeRequirementsStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $degree_total_credits = array_sum($degree_requirements);
} catch (Exception $e) {
    $degree_requirements = [];
    $degree_total_credits = 0;
}


// Separate core and general education requirements
$core_requirements = [];
$gen_ed_requirements = [];

foreach ($degree_requirements as $category => $credits) {
    if (in_array($category, ['Computer Science Core', 'Mathematics', 'Science Requirements'])) {
        $core_requirements[$category] = $credits;
    } elseif (in_array($category, ['English & Communication', 'Liberal Arts', 'Electives'])) {
        $gen_ed_requirements[$category] = $credits;
    }
}

// Map departments to categories
$department_mapping = [
    'Computer Science' => 'Computer Science Core',
    'Mathematics' => 'Mathematics',
    'Science' => 'Science Requirements',
    'English' => 'English & Communication',
    'Communication' => 'English & Communication',
    'Liberal Arts' => 'Liberal Arts',
    // Others go to Electives
];

// Calculate completed credits by category
$completed_credits = [
    'Computer Science Core' => 0,
    'Mathematics' => 0,
    'Science Requirements' => 0,
    'English & Communication' => 0,
    'Liberal Arts' => 0,
    'Electives' => 0,
];

if (is_array($results)) {
    foreach ($results as $row) {
        $department = $row['department'] ?? '';
        $credits = (int)($row['course_unit'] ?? 0);
        if ($credits <= 0) $credits = 3;

        // Only count if has grade (completed course)
        $has_grade = ($row['total_score'] !== null && $row['total_score'] !== '' && is_numeric($row['total_score'])) ||
                     ($row['grade_letter'] !== null && $row['grade_letter'] !== '');

        if ($has_grade) {
            $category = $department_mapping[$department] ?? 'Electives';
            $completed_credits[$category] += $credits;
        }
    }
}

// Calculate percentages
$overall_percentage = $total_credits > 0 ? round(($total_credits / $degree_total_credits) * 100, 1) : 0;

// Graduation timeline calculation
$level = (int)$student_level;
$semesters_remaining = max(0, 8 - floor($level / 100) * 2); // Rough estimate: 8 semesters total
$current_year = (int)date('Y');
$expected_graduation_year = $current_year + ceil($semesters_remaining / 2);
$expected_graduation_semester = ($semesters_remaining % 2 == 0) ? 'Spring' : 'Fall';

$credits_needed = max(0, $degree_total_credits - $total_credits);

// Academic progress status
$on_track = $total_credits >= ($degree_total_credits * 0.5); // On track if >50% done

$progress_percentages = [
    'Computer Science Core' => $core_requirements['Computer Science Core'] > 0 ? round(($completed_credits['Computer Science Core'] / $core_requirements['Computer Science Core']) * 100, 1) : 0,
    'Mathematics' => $core_requirements['Mathematics'] > 0 ? round(($completed_credits['Mathematics'] / $core_requirements['Mathematics']) * 100, 1) : 0,
    'Science Requirements' => $core_requirements['Science Requirements'] > 0 ? round(($completed_credits['Science Requirements'] / $core_requirements['Science Requirements']) * 100, 1) : 0,
    'English & Communication' => $gen_ed_requirements['English & Communication'] > 0 ? round(($completed_credits['English & Communication'] / $gen_ed_requirements['English & Communication']) * 100, 1) : 0,
    'Liberal Arts' => $gen_ed_requirements['Liberal Arts'] > 0 ? round(($completed_credits['Liberal Arts'] / $gen_ed_requirements['Liberal Arts']) * 100, 1) : 0,
    'Electives' => $gen_ed_requirements['Electives'] > 0 ? round(($completed_credits['Electives'] / $gen_ed_requirements['Electives']) * 100, 1) : 0,
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/student_dashboard.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                EduManage Pro
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($student_name); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../student_modules/student_profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../student_modules/student_logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="user-profile">
                        <div class="profile-avatar"><?php echo htmlspecialchars($student_initials); ?></div>
                        <div>
                            <h6 class="mb-1"><?php echo htmlspecialchars($student_name); ?></h6>
                            <small class="text-muted">Matric No: <?php echo htmlspecialchars($student_matric); ?></small>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($student_department); ?></small>
                        </div>
                    </div>
                    
                    <nav class="nav flex-column px-3">
                        <a class="nav-link active" href="#dashboard" data-section="dashboard">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#courses" data-section="courses">
                            <i class="fas fa-book"></i>My Courses
                        </a>
                        <a class="nav-link" href="#results" data-section="results">
                            <i class="fas fa-chart-line"></i>Results
                        </a>
                        <a class="nav-link" href="#transcript" data-section="transcript">
                            <i class="fas fa-file-alt"></i>Transcript
                        </a>
                        <a class="nav-link" href="#progress" data-section="progress">
                            <i class="fas fa-tasks"></i>Academic Progress
                        </a>
                        <a class="nav-link" href="course_selection.php">
                            <i class="fas fa-plus-circle"></i>Course Selection
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    
                    <!-- Dashboard Section -->
                    <div id="dashboard" class="content-section">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Dashboard Overview</h2>
                            <span class="text-muted">
                                <?php if (!empty($current_academic_year)): ?>
                                    Academic Year <?php echo htmlspecialchars($current_academic_year); ?>
                                <?php else: ?>
                                    Academic Year not set
                                <?php endif; ?>
                            </span>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <h3><?php echo (int)$registered_courses_count; ?></h3>
                                    <p class="mb-0">Registered Courses</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, var(--success-color), #58d68d);">
                                    <h3><?php echo htmlspecialchars($current_gpa); ?></h3>
                                    <p class="mb-0">Current GPA</p>
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, var(--warning-color), #f7dc6f);">
                                    <h3>85%</h3>
                                    <p class="mb-0">Attendance Rate</p>
                                </div>
                            </div> -->
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, var(--accent-color), #ec7063);">
                                    <h3>4</h3>
                                    <p class="mb-0">Pending Assignments</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Activity</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($recent_activities)): ?>
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($recent_activities as $activity): ?>
                                                    <div class="list-group-item border-0 px-0">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-1">
                                                                    <?php echo htmlspecialchars($activity['activity_type']); ?>
                                                                </h6>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars($activity['activity_description']); ?>
                                                                </small>
                                                            </div>
                                                            <small class="text-muted">
                                                                <?php
                                                                    $ts = $activity['timestamp'] ?? '';
                                                                    echo $ts ? htmlspecialchars(date('M j, Y H:i', strtotime($ts))) : '';
                                                                ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-3">
                                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                                <p>No recent activity yet</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Upcoming Deadlines</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($upcoming_deadlines)): ?>
                                            <?php foreach ($upcoming_deadlines as $deadline): ?>
                                                <?php
                                                    $deadline_date = strtotime($deadline['deadline_date']);
                                                    $today = strtotime(date('Y-m-d'));
                                                    $days_remaining = ceil(($deadline_date - $today) / (60 * 60 * 24));

                                                    $progress_color = 'bg-success';
                                                    $text_color = 'text-success';
                                                    $progress_width = 50;

                                                    if ($days_remaining <= 2) {
                                                        $progress_color = 'bg-danger';
                                                        $text_color = 'text-danger';
                                                        $progress_width = 90;
                                                    } elseif ($days_remaining <= 5) {
                                                        $progress_color = 'bg-warning';
                                                        $text_color = 'text-warning';
                                                        $progress_width = 70;
                                                    }

                                                    $days_text = $days_remaining == 1 ? '1 day' : $days_remaining . ' days';
                                                ?>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between">
                                                        <small class="fw-bold">
                                                            <?php echo htmlspecialchars($deadline['title']); ?>
                                                            <br>
                                                            <span class="text-muted small"><?php echo htmlspecialchars($deadline['course_code'] . ' - ' . $deadline['course_title']); ?></span>
                                                        </small>
                                                        <small class="<?php echo $text_color; ?>"><?php echo $days_text; ?></small>
                                                    </div>
                                                    <div class="progress progress-custom mt-1">
                                                        <div class="progress-bar <?php echo $progress_color; ?>" style="width: <?php echo $progress_width; ?>%"></div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-3">
                                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                                <p>No upcoming deadlines</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- My Courses Section -->
                    <div id="courses" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">My Registered Courses</h2>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Spring 2024
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Spring 2024</a></li>
                                    <li><a class="dropdown-item" href="#">Fall 2023</a></li>
                                    <li><a class="dropdown-item" href="#">Summer 2023</a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <?php 
                        // Check if student has approved registrations
                        $approvedCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM course_regtbl WHERE student_id = ? AND approval_status = 'Registered'");
                        $approvedCheckStmt->execute([$student_id]);
                        $approvedCount = $approvedCheckStmt->fetchColumn();
                        $hasApprovedRegistrations = $approvedCount > 0;
                        
                        if ($hasApprovedRegistrations): ?>
                            <div class="alert alert-success mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Course Registration Approved!</strong>
                                        <p class="mb-0">Your course registration has been approved. You can now download your registration form.</p>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-download me-2"></i>Download Form
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="../PHP/download_course_form.php?student_id=<?php echo $student_id; ?>&format=pdf"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                                            <li><a class="dropdown-item" href="../PHP/download_course_form.php?student_id=<?php echo $student_id; ?>&format=doc"><i class="fas fa-file-word me-2"></i>Word (DOC)</a></li>
                                            <li><a class="dropdown-item" href="../PHP/download_course_form.php?student_id=<?php echo $student_id; ?>&format=docx"><i class="fas fa-file-word me-2"></i>Word (DOCX)</a></li>
                                            <li><a class="dropdown-item" href="../PHP/download_course_form.php?student_id=<?php echo $student_id; ?>&format=xls"><i class="fas fa-file-excel me-2"></i>Excel (XLS)</a></li>
                                            <li><a class="dropdown-item" href="../PHP/download_course_form.php?student_id=<?php echo $student_id; ?>&format=xlsx"><i class="fas fa-file-excel me-2"></i>Excel (XLSX)</a></li>
                                            <li><a class="dropdown-item" href="../PHP/download_course_form.php?student_id=<?php echo $student_id; ?>&format=txt"><i class="fas fa-file-alt me-2"></i>Text</a></li>
                                            <li><a class="dropdown-item" href="../PHP/download_course_form.php?student_id=<?php echo $student_id; ?>&format=csv"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                                            <li><a class="dropdown-item" href="../PHP/download_course_form.php?student_id=<?php echo $student_id; ?>&format=html"><i class="fas fa-file-code me-2"></i>HTML</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <?php if (!empty($enrolled_courses)): ?>
                                <?php foreach ($enrolled_courses as $course): ?>
                                    <div class="col-lg-6 mb-4">
                                        <div class="card dashboard-card course-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <h5 class="card-title text-primary">
                                                            <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?>
                                                        </h5>
                                                        <p class="text-muted mb-1">
                                                            <?php echo htmlspecialchars($course['lecturer_name'] ?? 'Lecturer TBD'); ?>
                                                        </p>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars($course['department'] . ' • Level ' . $course['level']); ?>
                                                            <br>
                                                            <small class="text-muted">Semester: <?php echo htmlspecialchars($course['semester'] ?? 'N/A'); ?></small>
                                                        </small>
                                                    </div>
                                                    <div>
                                                        <span class="badge grade-badge bg-secondary">
                                                            <?php echo (int)($course['course_unit'] ?? 0); ?> Units
                                                        </span>
                                                        <br>
                                                        <?php 
                                                        $status = $course['approval_status'] ?? 'Pending';
                                                        $statusClass = $status === 'Registered' ? 'bg-success' : ($status === 'Dropped' ? 'bg-danger' : 'bg-warning');
                                                        $displayStatus = $status === 'Registered' ? 'Approved' : $status;
                                                        ?>
                                                        <span class="badge <?php echo $statusClass; ?> mt-1">
                                                            <?php echo htmlspecialchars($displayStatus); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-primary btn-custom btn-sm" type="button">View Materials</button>
                                                    <button class="btn btn-outline-primary btn-sm" type="button">Assignments</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        You are not currently enrolled in any courses.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Results Section -->
                    <div id="results" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Academic Results</h2>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    All Semesters
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Spring 2024</a></li>
                                    <li><a class="dropdown-item" href="#">Fall 2023</a></li>
                                    <li><a class="dropdown-item" href="#">Summer 2023</a></li>
                                    <li><a class="dropdown-item" href="#">Spring 2023</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="card dashboard-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Semester Results</h5>
                            </div>
                            <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-custom">
                                                <thead>
                                                    <tr>
                                                        <th>S/N</th>
                                                        <th>Course Code</th>
                                                        <th>Course Title</th>
                                                        <th>Credit Hours</th>
                                                        <th>CA Score</th>
                                                        <th>Test Score</th>
                                                        <th>Exam Score</th>
                                                        <th>Total</th>
                                                        <th>Final Grade</th>
                                                        <th>GPA Points</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($results)): ?>
                                                        <?php $sn = 1; foreach ($results as $row): ?>
                                                            <?php
                                                                $ca_score   = $row['ca_score']   ?? null;
                                                                $test_score = $row['test_score'] ?? null;
                                                                $exam_score = $row['exam_score'] ?? null;
                                                                $total_score = $row['total_score'] ?? null;
                                                                $stored_letter = $row['grade_letter'] ?? '';

                                                                $display_letter = '';
                                                                $points = null;

                                                                $credits = (int)($row['course_unit'] ?? 0);
                                                                if ($credits <= 0) {
                                                                    $credits = 3;
                                                                }

                                                                if ($total_score !== null && $total_score !== '' && is_numeric($total_score)) {
                                                                    $score = (float)$total_score;
                                                                    if ($score < 40) {
                                                                        $points = 0.0;
                                                                        $display_letter = 'F';
                                                                    } elseif ($score < 45) {
                                                                        $points = 1.0;
                                                                        $display_letter = 'E';
                                                                    } elseif ($score < 50) {
                                                                        $points = 2.0;
                                                                        $display_letter = 'D';
                                                                    } elseif ($score < 60) {
                                                                        $points = 3.0;
                                                                        $display_letter = 'C';
                                                                    } elseif ($score < 70) {
                                                                        $points = 4.0;
                                                                        $display_letter = 'B';
                                                                    } else {
                                                                        $points = 5.0;
                                                                        $display_letter = 'A';
                                                                    }
                                                                } else {
                                                                    $letter = strtoupper(trim($stored_letter));
                                                                    $display_letter = $letter;
                                                                    if (isset($letter_points_map[$letter])) {
                                                                        $points = $letter_points_map[$letter];
                                                                    }
                                                                }

                                                                $gpa_points = $points !== null ? number_format($points, 1) : '-';
                                                            ?>
                                                            <tr>
                                                                <td class="text-center"><?php echo $sn++; ?></td>
                                                                <td class="fw-bold"><?php echo htmlspecialchars($row['course_code']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['course_title'] ?? ''); ?></td>
                                                                <td><?php echo (int)$credits; ?></td>
                                                                <td><span class="badge bg-<?php echo is_numeric($ca_score) ? 'success' : 'secondary'; ?>">
                                                                    <?php echo is_numeric($ca_score) ? htmlspecialchars($ca_score) : '-'; ?>
                                                                </span></td>
                                                                <td><span class="badge bg-<?php echo is_numeric($test_score) ? 'success' : 'secondary'; ?>">
                                                                    <?php echo is_numeric($test_score) ? htmlspecialchars($test_score) : '-'; ?>
                                                                </span></td>
                                                                <td><span class="badge bg-<?php echo is_numeric($exam_score) ? 'success' : 'secondary'; ?>">
                                                                    <?php echo is_numeric($exam_score) ? htmlspecialchars($exam_score) : '-'; ?>
                                                                </span></td>
                                                                <td><span class="badge bg-<?php echo is_numeric($total_score) ? 'primary' : 'secondary'; ?>">
                                                                    <?php echo is_numeric($total_score) ? htmlspecialchars($total_score) : '-'; ?>
                                                                </span></td>
                                                                <td>
                                                                    <span class="badge bg-success fs-6">
                                                                        <?php echo htmlspecialchars($display_letter); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($gpa_points); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="8" class="text-center text-muted">
                                                                No results available yet.
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="alert alert-info alert-custom">
                                            <h6><i class="fas fa-info-circle me-2"></i>Semester Summary</h6>
                                            <p class="mb-1"><strong>Total Credit Hours:</strong> <?php echo htmlspecialchars($semester_credits); ?></p>
                                            <p class="mb-1"><strong>Semester GPA:</strong> <?php echo htmlspecialchars($semester_gpa); ?></p>
                                            <p class="mb-0"><strong>Cumulative GPA:</strong> <?php echo htmlspecialchars($current_gpa); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-success alert-custom">
                                            <h6><i class="fas fa-trophy me-2"></i>Academic Standing</h6>
                                            <p class="mb-0"><?php echo htmlspecialchars($academic_standing); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transcript Section -->
                    <div id="transcript" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Academic Transcript</h2>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-custom">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </button>
                                <button class="btn btn-outline-primary btn-custom">
                                    <i class="fas fa-envelope me-2"></i>Request Official
                                </button>
                            </div>
                        </div>

                        <div class="card dashboard-card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="mb-1">Official Academic Transcript</h5>
                                        <p class="mb-0"><?php echo htmlspecialchars($student_department); ?> Program</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <p class="mb-1"><strong>Student ID:</strong> <?php echo htmlspecialchars($student_matric); ?></p>
                                        <p class="mb-0"><strong>Generated:</strong> <?php echo htmlspecialchars(date('F j, Y')); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($transcript_data)): ?>
                                    <?php foreach ($transcript_data as $year => $semesters): ?>
                                        <h6 class="text-primary border-bottom pb-2 mb-3">Academic Year <?php echo htmlspecialchars($year); ?></h6>
                                        <div class="row mb-4">
                                            <?php
                                            $semester_count = 0;
                                            foreach ($semesters as $sem => $courses):
                                                $semester_count++;
                                                $semester_credits = 0;
                                                $semester_points = 0.0;
                                                $is_current = ($year == $current_academic_year && $sem == $current_semester);
                                            ?>
                                            <div class="col-md-6">
                                                <h6 class="text-muted"><?php echo htmlspecialchars($sem); ?><?php echo $is_current ? ' (Current)' : ''; ?></h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <tbody>
                                                            <?php foreach ($courses as $course): ?>
                                                                <?php
                                                                $raw_score = $course['total_score'] ?? null;
                                                                $raw_letter = $course['grade_letter'] ?? '';
                                                                $credits = (int)($course['course_unit'] ?? 3);
                                                                $points = null;
                                                                $display_letter = '';
                                                                $gpa_points = '-';

                                                                if ($raw_score !== null && $raw_score !== '' && is_numeric($raw_score)) {
                                                                    $score = (float)$raw_score;
                                                                    if ($score < 40) {
                                                                        $points = 0.0;
                                                                        $display_letter = 'F';
                                                                    } elseif ($score < 45) {
                                                                        $points = 1.0;
                                                                        $display_letter = 'E';
                                                                    } elseif ($score < 50) {
                                                                        $points = 2.0;
                                                                        $display_letter = 'D';
                                                                    } elseif ($score < 60) {
                                                                        $points = 3.0;
                                                                        $display_letter = 'C';
                                                                    } elseif ($score < 70) {
                                                                        $points = 4.0;
                                                                        $display_letter = 'B';
                                                                    } else {
                                                                        $points = 5.0;
                                                                        $display_letter = 'A';
                                                                    }
                                                                } else {
                                                                    $letter = strtoupper(trim($raw_letter));
                                                                    $display_letter = $letter;
                                                                    if (isset($letter_points_map[$letter])) {
                                                                        $points = $letter_points_map[$letter];
                                                                    }
                                                                }

                                                                if ($points !== null) {
                                                                    $semester_credits += $credits;
                                                                    $semester_points += $credits * $points;
                                                                    $gpa_points = number_format($points, 1);
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?></td>
                                                                    <td><?php echo $credits; ?></td>
                                                                    <td>
                                                                        <?php if ($points !== null): ?>
                                                                            <span class="badge bg-success"><?php echo htmlspecialchars($display_letter); ?></span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-secondary">In Progress</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($gpa_points); ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>

                                                            <?php if ($is_current && !empty($in_progress_courses)): ?>
                                                                <?php foreach ($in_progress_courses as $course): ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_title']); ?></td>
                                                                        <td><?php echo (int)($course['course_unit'] ?? 3); ?></td>
                                                                        <td><span class="badge bg-secondary">In Progress</span></td>
                                                                        <td>-</td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php
                                                $semester_gpa_display = $semester_credits > 0 ? number_format($semester_points / $semester_credits, 2) : 'N/A';
                                                ?>
                                                <p class="small text-muted">
                                                    <?php if ($semester_credits > 0): ?>
                                                        Semester GPA: <?php echo htmlspecialchars($semester_gpa_display); ?> | Credit Hours: <?php echo $semester_credits; ?>
                                                    <?php else: ?>
                                                        Credit Hours: <?php echo $semester_credits; ?>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <?php if ($semester_count % 2 == 0): ?>
                                        </div>
                                        <div class="row mb-4">
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                                        <p>No transcript data available yet</p>
                                    </div>
                                <?php endif; ?>

                                <!-- Summary -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="alert alert-primary alert-custom">
                                            <h6><i class="fas fa-graduation-cap me-2"></i>Academic Summary</h6>
                                            <p class="mb-1"><strong>Total Credit Hours Completed:</strong> <?php echo htmlspecialchars($total_credits); ?></p>
                                            <p class="mb-1"><strong>Credit Hours in Progress:</strong> <?php echo htmlspecialchars($in_progress_credits); ?></p>
                                            <p class="mb-0"><strong>Cumulative GPA:</strong> <?php echo htmlspecialchars($current_gpa); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-success alert-custom">
                                            <h6><i class="fas fa-award me-2"></i>Academic Honors</h6>
                                            <?php if (!empty($academic_honors)): ?>
                                                <p class="mb-0">
                                                    <?php foreach ($academic_honors as $honor): ?>
                                                        • <?php echo htmlspecialchars($honor); ?><br>
                                                    <?php endforeach; ?>
                                                </p>
                                            <?php else: ?>
                                                <p class="mb-0">No academic honors yet</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Progress Section -->
                    <div id="progress" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Academic Progress Monitor</h2>
                            <span class="badge bg-<?php echo $on_track ? 'success' : 'warning'; ?> fs-6"><?php echo $on_track ? 'On Track for Graduation' : 'Off Track'; ?></span>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Degree Progress</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="fw-bold">Overall Degree Completion</span>
                                                <span class="fw-bold"><?php echo htmlspecialchars($total_credits); ?>/<?php echo htmlspecialchars($degree_total_credits); ?> Credits (<?php echo htmlspecialchars($overall_percentage); ?>%)</span>
                                            </div>
                                            <div class="progress progress-custom" style="height: 15px;">
                                                <div class="progress-bar bg-primary" style="width: <?php echo htmlspecialchars($overall_percentage); ?>%"></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary">Core Requirements</h6>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Computer Science Core</small>
                                                        <small><?php echo htmlspecialchars($completed_credits['Computer Science Core']); ?>/<?php echo htmlspecialchars($core_requirements['Computer Science Core']); ?> Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-success" style="width: <?php echo htmlspecialchars($progress_percentages['Computer Science Core']); ?>%"></div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Mathematics</small>
                                                        <small><?php echo htmlspecialchars($completed_credits['Mathematics']); ?>/<?php echo htmlspecialchars($core_requirements['Mathematics']); ?> Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-warning" style="width: <?php echo htmlspecialchars($progress_percentages['Mathematics']); ?>%"></div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Science Requirements</small>
                                                        <small><?php echo htmlspecialchars($completed_credits['Science Requirements']); ?>/<?php echo htmlspecialchars($core_requirements['Science Requirements']); ?> Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-info" style="width: <?php echo htmlspecialchars($progress_percentages['Science Requirements']); ?>%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-primary">General Education</h6>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>English & Communication</small>
                                                        <small><?php echo htmlspecialchars($completed_credits['English & Communication']); ?>/<?php echo htmlspecialchars($gen_ed_requirements['English & Communication']); ?> Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-success" style="width: <?php echo htmlspecialchars($progress_percentages['English & Communication']); ?>%"></div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Liberal Arts</small>
                                                        <small><?php echo htmlspecialchars($completed_credits['Liberal Arts']); ?>/<?php echo htmlspecialchars($gen_ed_requirements['Liberal Arts']); ?> Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-danger" style="width: <?php echo htmlspecialchars($progress_percentages['Liberal Arts']); ?>%"></div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small>Electives</small>
                                                        <small><?php echo htmlspecialchars($completed_credits['Electives']); ?>/<?php echo htmlspecialchars($gen_ed_requirements['Electives']); ?> Credits</small>
                                                    </div>
                                                    <div class="progress progress-custom">
                                                        <div class="progress-bar bg-secondary" style="width: <?php echo htmlspecialchars($progress_percentages['Electives']); ?>%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-target me-2"></i>Graduation Timeline</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h3 class="text-primary"><?php echo htmlspecialchars($expected_graduation_semester . ' ' . $expected_graduation_year); ?></h3>
                                            <p class="text-muted">Expected Graduation</p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">Semesters Remaining</small>
                                            <h4 class="text-success"><?php echo htmlspecialchars($semesters_remaining); ?></h4>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">Credits Needed</small>
                                            <h4 class="text-warning"><?php echo htmlspecialchars($credits_needed); ?></h4>
                                        </div>
                                        <div class="alert alert-info alert-custom">
                                            <small><i class="fas fa-lightbulb me-1"></i>
                                            Maintain 15+ credits per semester to graduate on time.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card dashboard-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Action Items & Recommendations</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-danger">Urgent Actions</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-circle text-danger me-2" style="font-size: 0.5rem;"></i>
                                                Register for Liberal Arts courses next semester
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-circle text-warning me-2" style="font-size: 0.5rem;"></i>
                                                Complete remaining Math requirements
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-circle text-info me-2" style="font-size: 0.5rem;"></i>
                                                Schedule advisor meeting for course planning
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success">Recommendations</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Consider summer courses to lighten future load
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Explore internship opportunities
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Join computer science student organizations
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Selection Section -->
                    <div id="course-selection" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-primary fw-bold">Course Selection</h2>
                            <div class="d-flex gap-2">
                                <span class="badge bg-info fs-6">Registration Opens: April 1, 2024</span>
                                <button class="btn btn-success btn-custom">Submit Registration</button>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Available Courses - Fall 2024</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <select class="form-select">
                                                    <option>All Departments</option>
                                                    <option>Computer Science</option>
                                                    <option>Mathematics</option>
                                                    <option>Liberal Arts</option>
                                                    <option>Science</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-select">
                                                    <option>All Levels</option>
                                                    <option>100-200 (Introductory)</option>
                                                    <option>300-400 (Intermediate)</option>
                                                    <option>500+ (Advanced)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" placeholder="Search courses...">
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Select</th>
                                                        <th>Course</th>
                                                        <th>Title</th>
                                                        <th>Credits</th>
                                                        <th>Schedule</th>
                                                        <th>Instructor</th>
                                                        <th>Seats</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="cs401">
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">CS401</td>
                                                        <td>Advanced Algorithms</td>
                                                        <td>3</td>
                                                        <td>MWF 10:00-11:00</td>
                                                        <td>Dr. Johnson</td>
                                                        <td><span class="badge bg-success">15/30</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="cs420">
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">CS420</td>
                                                        <td>Machine Learning</td>
                                                        <td>3</td>
                                                        <td>TTh 2:00-3:30</td>
                                                        <td>Prof. Chen</td>
                                                        <td><span class="badge bg-warning">25/25</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="cs450" checked>
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">CS450</td>
                                                        <td>Computer Networks</td>
                                                        <td>3</td>
                                                        <td>MW 1:00-2:30</td>
                                                        <td>Dr. Rodriguez</td>
                                                        <td><span class="badge bg-success">12/25</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="math301">
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">MATH301</td>
                                                        <td>Discrete Mathematics</td>
                                                        <td>3</td>
                                                        <td>MWF 9:00-10:00</td>
                                                        <td>Prof. Williams</td>
                                                        <td><span class="badge bg-success">18/30</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="eng201" checked>
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold">ENG201</td>
                                                        <td>Technical Writing</td>
                                                        <td>3</td>
                                                        <td>TTh 11:00-12:30</td>
                                                        <td>Ms. Davis</td>
                                                        <td><span class="badge bg-success">20/25</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Selected Courses</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                                <div>
                                                    <strong>CS450</strong>
                                                    <br>
                                                    <small>Computer Networks</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-primary">3 Credits</span>
                                                    <br>
                                                    <button class="btn btn-sm btn-outline-danger mt-1">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                                <div>
                                                    <strong>ENG201</strong>
                                                    <br>
                                                    <small>Technical Writing</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-primary">3 Credits</span>
                                                    <br>
                                                    <button class="btn btn-sm btn-outline-danger mt-1">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Credits:</strong>
                                            <strong>6</strong>
                                        </div>
                                        <div class="alert alert-info alert-custom mt-3">
                                            <small><i class="fas fa-info-circle me-1"></i>
                                            Minimum 12 credits required for full-time status.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card dashboard-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Schedule Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Time</th>
                                                        <th>Mon</th>
                                                        <th>Wed</th>
                                                        <th>Tue</th>
                                                        <th>Thu</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-bold">11:00</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="bg-primary text-white text-center small">ENG201</td>
                                                        <td class="bg-primary text-white text-center small">ENG201</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">1:00</td>
                                                        <td class="bg-success text-white text-center small">CS450</td>
                                                        <td class="bg-success text-white text-center small">CS450</td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/student_dashboard.js"></script>
    <script>
    function viewMaterials(courseId, courseCode) {
        const modal = new bootstrap.Modal(document.getElementById('materialsModal'));
        const content = document.getElementById('materialsContent');
        
        // Reset content
        content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p>Loading materials...</p></div>';
        
        // Update modal title
        document.querySelector('#materialsModal .modal-title').innerHTML = `<i class="fas fa-book me-2"></i>Materials - ${courseCode}`;
        
        modal.show();
        
        fetch(`../PHP/get_course_materials.php?course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.materials.length === 0) {
                        content.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No materials available for this course yet.</div>';
                    } else {
                        let html = '<div class="list-group">';
                        data.materials.forEach(material => {
                            const uploadDate = new Date(material.created_at).toLocaleDateString();
                            html += `
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">${material.title}</h6>
                                            <p class="mb-1 text-muted">${material.description || 'No description'}</p>
                                            <small class="text-muted">Uploaded: ${uploadDate} by ${material.lecturer_name}</small>
                                        </div>
                                        <div>
                                            <a href="${material.file_path_url}" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        content.innerHTML = html;
                    }
                } else {
                    content.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>${data.message}</div>`;
                }
            })
            .catch(error => {
                content.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading materials.</div>';
            });
    }
    
    function viewAssignments(courseId, courseCode) {
        const modal = new bootstrap.Modal(document.getElementById('assignmentsModal'));
        const content = document.getElementById('assignmentsContent');
        
        // Reset content
        content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p>Loading assignments...</p></div>';
        
        // Update modal title
        document.querySelector('#assignmentsModal .modal-title').innerHTML = `<i class="fas fa-tasks me-2"></i>Assignments - ${courseCode}`;
        
        modal.show();
        
        fetch(`../PHP/get_course_assignments.php?course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.assignments.length === 0) {
                        content.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No assignments available for this course yet.</div>';
                    } else {
                        let html = '<div class="list-group">';
                        data.assignments.forEach(assignment => {
                            const dueDate = new Date(assignment.due_date);
                            const isOverdue = dueDate < new Date();
                            const hasSubmission = assignment.sub_id !== null;
                            
                            let statusBadge = '';
                            if (hasSubmission) {
                                statusBadge = '<span class="badge bg-info">Submitted</span>';
                            } else if (isOverdue) {
                                statusBadge = '<span class="badge bg-danger">Overdue</span>';
                            } else {
                                statusBadge = '<span class="badge bg-warning">Pending</span>';
                            }
                            
                            html += `
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">${assignment.title} ${statusBadge}</h6>
                                            <p class="mb-1 text-muted">${assignment.description || 'No description'}</p>
                                            <small class="text-muted">Due: ${dueDate.toLocaleDateString()} | Max Score: ${assignment.max_score}</small>

                                        </div>
                                        <div>
                                            ${!hasSubmission && !isOverdue ? 
                                                `<button class="btn btn-sm btn-success" onclick="submitAssignment(${assignment.assignment_id}, '${assignment.title}')">
                                                    <i class="fas fa-upload me-1"></i>Submit
                                                </button>` : 
                                                hasSubmission ? 
                                                    `<small class="text-success">Submitted: ${new Date(assignment.submitted_at).toLocaleDateString()}</small>` :
                                                    '<small class="text-danger">Overdue</small>'
                                            }
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        content.innerHTML = html;
                    }
                } else {
                    content.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>${data.message}</div>`;
                }
            })
            .catch(error => {
                content.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading assignments.</div>';
            });
    }
    
    function submitAssignment(assignmentId, title) {
        alert(`Assignment submission functionality for "${title}" will be implemented next.`);
    }
    </script>
</body>
</html>