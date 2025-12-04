<?php
session_start();
// Prevent caching so users can't view dashboard via browser back after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
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

// Fetch distinct semesters from student's course registrations
$semesterStmt = $pdo->prepare("
    SELECT DISTINCT cr.semester, cr.academic_year
    FROM course_regtbl cr
    WHERE cr.student_id = ?
    ORDER BY cr.academic_year DESC, cr.semester DESC
");
$semesterStmt->execute([$student_id]);
$available_semesters = $semesterStmt->fetchAll();
$current_semester_display = !empty($available_semesters) ? htmlspecialchars($available_semesters[0]['semester'] . ' ' . $available_semesters[0]['academic_year']) : 'Current Semester';

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

// Fetch pending assignments count
$pendingAssignmentsStmt = $pdo->prepare("
    SELECT COUNT(*) as pending_count
    FROM assignmenttbl a
    WHERE a.course_id IN (
        SELECT course_id FROM course_regtbl WHERE student_id = ?
    )
    AND a.due_date >= CURDATE()
    AND a.assignment_id NOT IN (
        SELECT assignment_id FROM ass_subtbl WHERE student_id = ?
    )
");
$pendingAssignmentsStmt->execute([$student_id, $student_id]);
$pending_assignments_count = $pendingAssignmentsStmt->fetchColumn();

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
    <title>Student Dashboard</title>
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
                Student Dashboard
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($student_name); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../student_modules/student_profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <!-- <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li> -->
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
                        <a class="nav-link" href="student_assignments.php">
                            <i class="fas fa-tasks"></i>Assignments
                        </a>
                        <a class="nav-link" href="#results" data-section="results">
                            <i class="fas fa-chart-line"></i>Results
                        </a>
                        <a class="nav-link" href="#transcript" data-section="transcript">
                            <i class="fas fa-file-alt"></i>Transcript
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
                                    <h3><?php echo (int)$pending_assignments_count; ?></h3>
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
                                    <?php echo htmlspecialchars($current_semester_display); ?>
                                </button>
                                <ul class="dropdown-menu">
                                    <?php if (!empty($available_semesters)): ?>
                                        <?php foreach ($available_semesters as $sem): ?>
                                            <li><a class="dropdown-item" href="#"><?php echo htmlspecialchars($sem['semester'] . ' ' . $sem['academic_year']); ?></a></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li><span class="dropdown-item disabled">No semesters available</span></li>
                                    <?php endif; ?>
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
                                                    <button class="btn btn-primary btn-custom btn-sm" type="button" onclick="viewMaterials(<?php echo $course['course_id']; ?>, '<?php echo htmlspecialchars($course['course_code']); ?>')">View Materials</button>
                                                    <button class="btn btn-outline-primary btn-sm" type="button" onclick="viewAssignments(<?php echo $course['course_id']; ?>, '<?php echo htmlspecialchars($course['course_code']); ?>')">Assignments</button>
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
                                    <?php if (!empty($available_semesters)): ?>
                                        <?php foreach ($available_semesters as $sem): ?>
                                            <li><a class="dropdown-item" href="#"><?php echo htmlspecialchars($sem['semester'] . ' ' . $sem['academic_year']); ?></a></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li><span class="dropdown-item disabled">No semesters available</span></li>
                                    <?php endif; ?>
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
                                                        <th>Credit Units</th>
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
                                            <p class="mb-1"><strong>Total Credit Units:</strong> <?php echo htmlspecialchars($semester_credits); ?></p>
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
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-custom dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-download me-2"></i>Download PDF
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="../PHP/download_transcript.php?student_id=<?php echo $student_id; ?>&format=pdf"><i class="fas fa-file-pdf me-2"></i>PDF Format</a></li>
                                        <li><a class="dropdown-item" href="../PHP/download_transcript.php?student_id=<?php echo $student_id; ?>&format=docx"><i class="fas fa-file-word me-2"></i>Word Document</a></li>
                                        <li><a class="dropdown-item" href="../PHP/download_transcript.php?student_id=<?php echo $student_id; ?>&format=xlsx"><i class="fas fa-file-excel me-2"></i>Excel Spreadsheet</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary btn-custom dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-envelope me-2"></i>Request Official
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#transcriptRequestModal"><i class="fas fa-envelope me-2"></i>Request Official Transcript</a></li>
                                    </ul>
                                </div>
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
                                            <p class="mb-1"><strong>Total Credit Units Completed:</strong> <?php echo htmlspecialchars($total_credits); ?></p>
                                            <p class="mb-1"><strong>Credit Units in Progress:</strong> <?php echo htmlspecialchars($in_progress_credits); ?></p>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Materials Modal -->
    <div class="modal fade" id="materialsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-book me-2"></i>Course Materials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="materialsContent">
                        <!-- Content loaded via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Modal -->
    <div class="modal fade" id="assignmentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-tasks me-2"></i>Course Assignments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="assignmentsContent">
                        <!-- Content loaded via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Submission Modal -->
    <div class="modal fade" id="submissionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Submit Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="submissionForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="assignmentId" name="assignment_id">
                        <div class="mb-3">
                            <label class="form-label">Assignment Title</label>
                            <input type="text" class="form-control" id="assignmentTitle" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="submissionFile" class="form-label">Upload File</label>
                            <input type="file" class="form-control" id="submissionFile" name="submission_file" required>
                            <div class="form-text">Accepted formats: PDF, DOC, DOCX, TXT (Max: 10MB)</div>
                        </div>
                        <div class="mb-3">
                            <label for="submissionComments" class="form-label">Comments (Optional)</label>
                            <textarea class="form-control" id="submissionComments" name="comments" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Official Transcript Request Modal -->
    <div class="modal fade" id="transcriptRequestModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-certificate me-2"></i>Request Official Transcript</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="transcriptRequestForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="transcriptPurpose" class="form-label">Purpose of Request *</label>
                            <select class="form-select" id="transcriptPurpose" name="purpose" required>
                                <option value="">Select purpose...</option>
                                <option value="job_application">Job Application</option>
                                <option value="graduate_school">Graduate School Application</option>
                                <option value="transfer">Transfer to Another Institution</option>
                                <option value="scholarship">Scholarship Application</option>
                                <option value="personal">Personal Records</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="deliveryAddress" class="form-label">Delivery Address *</label>
                            <textarea class="form-control" id="deliveryAddress" name="delivery_address" rows="3" placeholder="Enter complete delivery address..." required></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Processing Time:</strong> 3-5 business days<br>
                            <strong>Fee:</strong> Official transcripts may incur processing fees
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/student_dashboard.js"></script>
    <script>
    // Client-side session verification: calls server endpoint and redirects if session invalid
    (function(){
        fetch('../PHP/check_session.php', { credentials: 'include' })
            .then(function(res){ return res.json(); })
            .then(function(data){
                if (!data || !data.ok) {
                    var role = data && data.user_role ? data.user_role : '';
                    if (role === 'admin') window.location.href = '../authentications/admin_login.html';
                    else if (role === 'lecturer') window.location.href = '../authentications/lecturer_login.html';
                    else window.location.href = '../authentications/student_login.html';
                }
            }).catch(function(){
                // On network error, force redirect to login
                window.location.href = '../authentications/student_login.html';
            });
    })();
    </script>
    <script>
    function viewMaterials(courseId, courseCode) {
        const modal = new bootstrap.Modal(document.getElementById('materialsModal'));
        const content = document.getElementById('materialsContent');
        
        // Reset content
        content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p>Loading materials...</p></div>';
        
        // Update modal title
        document.querySelector('#materialsModal .modal-title').innerHTML = `<i class="fas fa-book me-2"></i>Materials - ${courseCode}`;
        
        modal.show();
        
        fetch(`../PHP/get_course_materials.php?course_id=${courseId}`, { credentials: 'include' })
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
                                            <a href="../PHP/download_course_material.php?material_id=${material.material_id}" class="btn btn-sm btn-primary">
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
                                                `<button class="btn btn-sm btn-success" onclick="submitAssignment(${assignment.assignment_id}, '${assignment.title}', ${courseId}, '${courseCode}')">
                                                    <i class="fas fa-upload me-1"></i>Submit
                                                </button>` :
                                                hasSubmission ?
                                                    `<div class="text-end">
                                                        <small class="text-success d-block">Submitted: ${new Date(assignment.submitted_at).toLocaleDateString()}</small>
                                                        ${assignment.score_received !== null && assignment.score_received !== undefined ?
                                                            `<small class="text-primary fw-bold">Grade: ${assignment.score_received}/100</small>` :
                                                            `<small class="text-warning">Grade: Pending</small>`
                                                        }
                                                    </div>` :
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
    
    function submitAssignment(assignmentId, title, courseId, courseCode) {
        const assignmentIdField = document.getElementById('assignmentId');
        assignmentIdField.value = assignmentId;
        assignmentIdField.dataset.courseId = courseId;
        assignmentIdField.dataset.courseCode = courseCode;
        document.getElementById('assignmentTitle').value = title;
        
        const modal = new bootstrap.Modal(document.getElementById('submissionModal'));
        modal.show();
    }
    
    // Handle assignment submission
    document.getElementById('submissionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Submitting...';
        
        fetch('../PHP/submit_assignment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment submitted successfully!');
                bootstrap.Modal.getInstance(document.getElementById('submissionModal')).hide();
                // Refresh assignments modal if open
                const assignmentsModal = document.getElementById('assignmentsModal');
                if (assignmentsModal.classList.contains('show')) {
                    const courseId = document.getElementById('assignmentId').dataset.courseId;
                    const courseCode = document.getElementById('assignmentId').dataset.courseCode;
                    if (courseId && courseCode) {
                        viewAssignments(courseId, courseCode);
                    }
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error submitting assignment. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
    
    // Handle transcript request form
    document.getElementById('transcriptRequestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Submitting...';
        
        fetch('../PHP/request_official_transcript.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Official transcript request submitted successfully!');
                bootstrap.Modal.getInstance(document.getElementById('transcriptRequestModal')).hide();
                this.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error submitting request. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
    </script>
</body>
</html>