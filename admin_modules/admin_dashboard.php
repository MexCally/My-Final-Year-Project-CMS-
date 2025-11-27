<?php
session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../authentications/admin_login.html');
    exit();
}

// Get admin info from session
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin_dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-graduation-cap"></i> EduCMS</h3>
            </div>
            <ul class="list-unstyled components">
                <li class="active">
                    <a href="#" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" data-section="students">
                        <i class="fas fa-user-graduate"></i> Students
                    </a>
                </li>
                <li>
                    <a href="#" data-section="courses">
                        <i class="fas fa-book"></i> Courses
                    </a>
                </li>
                <li>
                    <a href="#" data-section="lecturers">
                        <i class="fas fa-chalkboard-teacher"></i> Lecturers
                    </a>
                </li>
                <li>
                    <a href="#" data-section="grades">
                        <i class="fas fa-chart-line"></i> Grades
                    </a>
                </li>
                <li>
                    <a href="#" data-section="reports">
                        <i class="fas fa-file-alt"></i> Reports
                    </a>
                </li>
                <!-- Added Pending Registrations navigation link -->
                <li>
                    <a href="#" data-section="registrations">
                        <i class="fas fa-clipboard-list"></i> Pending Registrations
                    </a>
                </li>
                <!-- Settings navigation link -->
                <!-- <li>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li> -->
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content" class="content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <div class="ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Dashboard Section -->
            <div id="dashboard-section" class="section active">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col">
                            <h2 class="text-primary">Dashboard Overview</h2>
                            <p class="text-muted">Welcome <?php echo htmlspecialchars($_SESSION['admin_name']); ?> to the Educational Content Management System!</p>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalStudents">Loading...</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Courses</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeCourses">Loading...</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Lecturers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalLecturers">Loading...</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Grades</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingGrades">Loading...</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <button class="btn btn-primary btn-block" data-section="students">
                                                <i class="fas fa-user-plus"></i> Add Student
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <button class="btn btn-success btn-block" data-section="courses">
                                                <i class="fas fa-plus"></i> Create Course
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <button class="btn btn-info btn-block" data-section="lecturers">
                                                <i class="fas fa-user-tie"></i> Add Lecturer
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <button class="btn btn-warning btn-block" data-section="reports">
                                                <i class="fas fa-chart-bar"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity --> 
                        <div class="col-lg-6">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                                </div>
                                <div class="card-body">
                                    <div id="recentActivitiesContainer">
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                                            <p>No recent activity yet</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Section -->
            <div id="students-section" class="section">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col">
                            <h2 class="text-primary">Student Management</h2>
                            <p class="text-muted">Manage student registrations and information</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                <i class="fas fa-plus"></i> Add Student
                            </button>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="studentSearchInput" placeholder="Search students...">
                                <button class="btn btn-outline-secondary" type="button" id="studentSearchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="departmentFilter">
                                <option value="">All Programs</option>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Mass Communication">Mass Communication</option>
                                    <option value="Business Administration">Business Administration</option>
                                    <option value="Accountancy">Accountancy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Graduated">Graduated</option>
                            </select>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                        <th>S/N</th>
                                            <th>Matric_No</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Department</th>
                                            <th>Year</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentsTableBody">
                                        <!-- Students will be loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Section -->
            <div id="courses-section" class="section">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col">
                            <h2 class="text-primary">Course Management</h2>
                            <p class="text-muted">Manage courses and lecturer assignments</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                                <i class="fas fa-plus"></i> Add Course
                            </button>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="courseSearchInput" placeholder="Search courses, lecturers, code, title, description...">
                                <button class="btn btn-outline-secondary" type="button" id="courseSearchBtn" title="Search courses">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-outline-danger" type="button" id="courseSearchClear" title="Clear search" style="display: none;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="courseDepartmentFilter">
                                <option value="">All Departments</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Mass Communication">Mass Communication</option>
                                <option value="Business Administration">Business Administration</option>
                                <option value="Accountancy">Accountancy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="courseLevelFilter">
                                <option value="">All Levels</option>
                                <option value="ND 1">ND 1</option>
                                <option value="ND 2">ND 2</option>
                            </select>
                        </div>
                    </div>

                    <!-- Courses Table -->
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Course Code</th>
                                            <th>Course Title</th>
                                            <th>Department</th>
                                            <th>Lecturer</th>
                                            <th>Level</th>
                                            <th>Semester</th>
                                            <th>Units</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="coursesTableBody">
                                        <!-- Courses will be loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lecturers Section -->
            <div id="lecturers-section" class="section">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col">
                            <h2 class="text-primary">Lecturer Management</h2>
                            <p class="text-muted">Manage lecturers and staff information</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLecturerModal">
                                <i class="fas fa-plus"></i> Add Lecturer
                            </button>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="lecturerSearchInput" placeholder="Search lecturers...">
                                <button class="btn btn-outline-secondary" type="button" id="lecturerSearchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="lecturerDepartmentFilter">
                                <option value="">All Departments</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Mass Communication">Mass Communication</option>
                                <option value="Business Administration">Business Administration</option>
                                <option value="Accountancy">Accountancy</option>
                            </select>
                        </div>
                    </div>

                    <!-- Lecturers Table -->
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Department</th>
                                            <th>Gender</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lecturersTableBody">
                                        <!-- Lecturers will be loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grades Section -->
            <div id="grades-section" class="section">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col">
                            <h2 class="text-primary">Grade Management</h2>
                            <p class="text-muted">Manage student grades and evaluations</p>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <select class="form-select">
                                        <option>Select Course</option>
                                        <option>CS101 - Introduction to Programming</option>
                                        <option>MATH201 - Calculus II</option>
                                        <option>PHY301 - Quantum Physics</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select">
                                        <option>Select Assessment</option>
                                        <option>Midterm Exam</option>
                                        <option>Final Exam</option>
                                        <option>Assignment 1</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary">Load Grades</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Assessment</th>
                                            <th>Score</th>
                                            <th>Grade</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>STU001</td>
                                            <td>John Doe</td>
                                            <td>Midterm Exam</td>
                                            <td>85/100</td>
                                            <td>B+</td>
                                            <td><span class="badge bg-success">Graded</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Edit</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>STU002</td>
                                            <td>Jane Smith</td>
                                            <td>Midterm Exam</td>
                                            <td>92/100</td>
                                            <td>A-</td>
                                            <td><span class="badge bg-success">Graded</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Edit</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>STU003</td>
                                            <td>Mike Johnson</td>
                                            <td>Midterm Exam</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Add Grade</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Section -->
            <div id="reports-section" class="section">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col">
                            <h2 class="text-primary">Reports & Transcripts</h2>
                            <p class="text-muted">Generate reports and student transcripts</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Student Reports</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Select Student</label>
                                        <select class="form-select">
                                            <option>Choose student...</option>
                                            <option>John Doe (STU001)</option>
                                            <option>Jane Smith (STU002)</option>
                                            <option>Mike Johnson (STU003)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Report Type</label>
                                        <select class="form-select">
                                            <option>Academic Transcript</option>
                                            <option>Grade Report</option>
                                            <option>Course History</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-primary">Generate Report</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Course Reports</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Select Course</label>
                                        <select class="form-select">
                                            <option>Choose course...</option>
                                            <option>CS101 - Introduction to Programming</option>
                                            <option>MATH201 - Calculus II</option>
                                            <option>PHY301 - Quantum Physics</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Report Type</label>
                                        <select class="form-select">
                                            <option>Enrollment Report</option>
                                            <option>Grade Distribution</option>
                                            <option>Performance Analysis</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-success">Generate Report</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Added Pending Registrations Section -->
            <div id="registrations-section" class="section">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col">
                            <h2 class="text-primary">Pending Student Registrations</h2>
                            <p class="text-muted">Review and approve or decline new student registrations</p>
                        </div>
                    </div>

                    <!-- Registration Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-warning shadow">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Approvals</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingApprovalsCount">Loading...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success shadow">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved Today</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="approvedTodayCount">Loading...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-danger shadow">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Approved</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalApprovedCount">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Registrations Table -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Registration Applications</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Program</th>
                                            <th>Application Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="registrationsTableBody">
                                        <!-- Registrations will be loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addStudentForm">
                        <div class="mb-3">
                            <label class="form-label">Matriculation Number *</label>
                            <input type="text" class="form-control" id="matric_no" name="matric_no" placeholder="e.g., 238369" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter first name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter last name" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="student@university.edu" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" id="phone_num" name="phone_num" placeholder="+1234567890" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department *</label>
                                <select class="form-select" id="department" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Mass Communication">Mass Communication</option>
                                    <option value="Business Administration">Business Administration</option>
                                    <option value="Accountancy">Accountancy</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Level *</label>
                                <select class="form-select" id="level" name="level" required>
                                    <option value="">Select Level</option>
                                    <option value="ND 1">ND 1</option>
                                    <option value="ND 2">ND 2</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender *</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 8 characters" required>
                            <div class="form-text">Password must be at least 8 characters long.</div>
                        </div>
                    </form>
                    <div id="addStudentErrors" class="alert alert-danger" style="display: none;"></div>
                    <div id="addStudentSuccess" class="alert alert-success" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addStudentBtn">Add Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addCourseForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Course Code *</label>
                                <input type="text" class="form-control" id="course_code" name="course_code" placeholder="e.g., CS101" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Course Unit *</label>
                                <input type="number" class="form-control" id="course_unit" name="course_unit" placeholder="e.g., 3" min="1" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Title *</label>
                            <input type="text" class="form-control" id="course_title" name="course_title" placeholder="e.g., Introduction to Programming" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Description</label>
                            <textarea class="form-control" id="course_description" name="course_description" rows="3" placeholder="Enter course description..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department *</label>
                                <select class="form-select" id="course_department" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Mass Communication">Mass Communication</option>
                                    <option value="Business Administration">Business Administration</option>
                                    <option value="Accountancy">Accountancy</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Level *</label>
                                <select class="form-select" id="course_level" name="level" required>
                                    <option value="">Select Level</option>
                                    <option value="ND 1">ND 1</option>
                                    <option value="ND 2">ND 2</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester *</label>
                                <select class="form-select" id="course_semester" name="semester" required>
                                    <option value="">Select Semester</option>
                                    <option value="First Semester">First Semester</option>
                                    <option value="Second Semester">Second Semester</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lecturer *</label>
                                <select class="form-select" id="course_lecturer" name="lecturer_id" required>
                                    <option value="">Select Lecturer</option>
                                    <!-- Lecturers will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                    </form>
                    <div id="addCourseErrors" class="alert alert-danger" style="display: none;"></div>
                    <div id="addCourseSuccess" class="alert alert-success" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addCourseBtn">Add Course</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Lecturer Modal -->
    <div class="modal fade" id="addLecturerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Lecturer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addLecturerForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="lecturer_first_name" name="first_name" placeholder="Enter first name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="lecturer_last_name" name="last_name" placeholder="Enter last name" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" id="lecturer_email" name="email" placeholder="lecturer@university.edu" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" id="lecturer_phone_num" name="phone_num" placeholder="+1234567890" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department *</label>
                                <select class="form-select" id="lecturer_department" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Mass Communication">Mass Communication</option>
                                    <option value="Business Administration">Business Administration</option>
                                    <option value="Accountancy">Accountancy</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender *</label>
                                <select class="form-select" id="lecturer_gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" id="lecturer_password" name="password" placeholder="Minimum 8 characters" required>
                            <div class="form-text">Password must be at least 8 characters long.</div>
                        </div>
                    </form>
                    <div id="addLecturerErrors" class="alert alert-danger" style="display: none;"></div>
                    <div id="addLecturerSuccess" class="alert alert-success" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addLecturerBtn">Add Lecturer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editStudentForm">
                        <div class="mb-3">
                            <label class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="editStudentId" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editStudentName" placeholder="Enter full name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="editStudentEmail" placeholder="student@email.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="editStudentPhone" placeholder="+1234567890">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Program</label>
                            <select class="form-select" id="editStudentProgram">
                                <option value="Computer Science">Computer Science</option>
                                <option value="Mass Communication">Mass Communication</option>
                                <option value="Accountancy">Accountancy</option>
                                <option value="Business administration">Business administration</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <select class="form-select" id="editStudentYear">
                                <option value="ND 1">ND 1</option>
                                <option value="ND 2">ND 2</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-select" id="editStudentGender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveStudentBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCourseForm">
                        <input type="hidden" id="editCourseId" name="course_id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Course Code *</label>
                                <input type="text" class="form-control" id="editCourseCode" name="course_code" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Course Unit *</label>
                                <input type="number" class="form-control" id="editCourseUnit" name="course_unit" min="1" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Title *</label>
                            <input type="text" class="form-control" id="editCourseTitle" name="course_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Description</label>
                            <textarea class="form-control" id="editCourseDescription" name="course_description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department *</label>
                                <select class="form-select" id="editCourseDepartment" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Mass Communication">Mass Communication</option>
                                    <option value="Business Administration">Business Administration</option>
                                    <option value="Accountancy">Accountancy</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Level *</label>
                                <select class="form-select" id="editCourseLevel" name="level" required>
                                    <option value="">Select Level</option>
                                    <option value="ND 1">ND 1</option>
                                    <option value="ND 2">ND 2</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester *</label>
                                <select class="form-select" id="editCourseSemester" name="semester" required>
                                    <option value="">Select Semester</option>
                                    <option value="First Semester">First Semester</option>
                                    <option value="Second Semester">Second Semester</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lecturer *</label>
                                <select class="form-select" id="editCourseLecturer" name="lecturer_id" required>
                                    <option value="">Select Lecturer</option>
                                    <!-- Lecturers will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                    </form>
                    <div id="editCourseErrors" class="alert alert-danger" style="display: none;"></div>
                    <div id="editCourseSuccess" class="alert alert-success" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveCourseBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Lecturer Modal -->
    <div class="modal fade" id="editLecturerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Lecturer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editLecturerForm">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="editLecturerName" placeholder="First Last" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" id="editLecturerEmail" placeholder="lecturer@university.edu" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department *</label>
                            <select class="form-select" id="editLecturerDepartment" required>
                                <option value="">Select Department</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Mass Communication">Mass Communication</option>
                                <option value="Business Administration">Business Administration</option>
                                <option value="Accountancy">Accountancy</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender *</label>
                            <select class="form-select" id="editLecturerGender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="tel" class="form-control" id="editLecturerPhone" placeholder="+1234567890" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveLecturerBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Added Approve Registration Modal -->
    <div class="modal fade" id="approveRegistrationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Student Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Approving registration for: <strong id="approveApplicantName"></strong>
                    </div>
                    <form id="approveRegistrationForm">
                        <div class="mb-3">
                            <label class="form-label">Approval Comments (Optional)</label>
                            <textarea class="form-control" id="approveComments" rows="3" placeholder="Add any approval notes..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmApproveBtn">
                        <i class="fas fa-check"></i> Approve Registration
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Added Decline Registration Modal -->
    <div class="modal fade" id="declineRegistrationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Decline Student Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Declining registration for: <strong id="declineApplicantName"></strong>
                    </div>
                    <form id="declineRegistrationForm">
                        <div class="mb-3">
                            <label class="form-label">Decline Reason *</label>
                            <select class="form-select" id="declineReason" required>
                                <option value="">Select a reason...</option>
                                <option value="incomplete-documents">Incomplete Documents</option>
                                <option value="invalid-credentials">Invalid Credentials</option>
                                <option value="prerequisite-not-met">Prerequisite Not Met</option>
                                <option value="duplicate-registration">Duplicate Registration</option>
                                <option value="fee-not-paid">Outstanding Fees</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Detailed Reason *</label>
                            <textarea class="form-control" id="declineDetailedReason" rows="4" placeholder="Provide specific details about the decline..." required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeclineBtn">
                        <i class="fas fa-times"></i> Decline Registration
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Added View Student Modal -->
    <div class="modal fade" id="viewStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Student ID</strong></label>
                                <p class="text-muted" id="viewStudentId">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Status</strong></label>
                                <p id="viewStudentStatus"><span class="badge bg-success">Active</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Full Name</strong></label>
                        <p class="text-muted" id="viewStudentName">-</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Email</strong></label>
                        <p class="text-muted" id="viewStudentEmail">-</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Program</strong></label>
                        <p class="text-muted" id="viewStudentProgram">-</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Year</strong></label>
                        <p class="text-muted" id="viewStudentYear">-</p>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Courses Enrolled</strong></label>
                                <p class="text-muted" id="viewStudentCourses">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>GPA</strong></label>
                                <p class="text-muted" id="viewStudentGPA">-</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Course Students Modal -->
    <div class="modal fade" id="viewCourseStudentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Students Enrolled in <span id="courseTitle"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Matric_No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Level</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="courseStudentsTableBody">
                                <!-- Students will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Photo Modal -->
    <div class="modal fade" id="uploadPhotoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Profile Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="photoInput" accept="image/*">
                    </div>
                    <div id="photoPreview" class="text-center mb-3">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect width='150' height='150' fill='%23ddd'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%23999'%3EPreview%3C/text%3E%3C/svg%3E" class="img-fluid rounded" alt="Preview" style="display: none;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="uploadPhotoBtn">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin_dashboard.js"></script>

</body>
</html>
