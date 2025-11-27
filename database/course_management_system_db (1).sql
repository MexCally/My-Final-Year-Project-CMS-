-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2025 at 12:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `course_management_system_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('admin','lecturer','student') DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `action`, `description`, `timestamp`, `user_id`, `user_type`) VALUES
(1, 'add_student', 'Added new student: Razaq Amoo (243670)', '2025-11-25 00:08:10', 4, 'admin'),
(2, 'student_updated', 'Updated student: Razaq Amoo (2)', '2025-11-25 00:09:10', 4, 'admin'),
(3, 'student_updated', 'Updated student: Razaq Amoo (2)', '2025-11-25 00:09:26', 4, 'admin'),
(4, 'add_lecturer', 'Added new lecturer: Ife Olufemi (ife@gmail.com)', '2025-11-25 23:03:30', 5, 'admin'),
(5, 'edit_lecturer', 'Updated lecturer: Ife Olufemi (ife@gmail.com)', '2025-11-25 23:04:40', 5, 'admin'),
(6, 'edit_lecturer', 'Updated lecturer: Ife Olufemi (ife@gmail.comk)', '2025-11-25 23:04:51', 5, 'admin'),
(7, 'add_lecturer', 'Added new lecturer: Razaq Chris (cash@gmail.com)', '2025-11-25 23:05:36', 5, 'admin'),
(8, 'add_course', 'Added new course: CSE 205 - Advanced Algorithm Design and Analysis', '2025-11-25 23:15:24', 5, 'admin'),
(9, 'edit_course', 'Updated course: CSE 205 - Advanced Algorithm Design and Analysis', '2025-11-25 23:24:09', 5, 'admin'),
(10, 'edit_course', 'Updated course: CSE 205 - Advanced Algorithm Design and Analysis', '2025-11-25 23:24:30', 5, 'admin'),
(11, 'add_course', 'Added new course: FIN 215 - Financial Accounting Principles', '2025-11-25 23:29:27', 5, 'admin'),
(12, 'add_course', 'Added new course: CSE 105 - Intro to Python', '2025-11-25 23:40:23', 5, 'admin'),
(13, 'add_course', 'Added new course: CSE 100 - Modernist European Fiction', '2025-11-25 23:53:55', 5, 'admin'),
(14, 'add_course', 'Added new course: MMP 102 - Intro to Python', '2025-11-25 23:54:43', 5, 'admin'),
(15, 'add_course', 'Added new course: STATS 205 - Peace and Conflict', '2025-11-26 00:09:12', 5, 'admin'),
(16, 'edit_course', 'Updated course: STATS 205 - Peace and Conflict', '2025-11-26 00:09:54', 5, 'admin'),
(17, 'add_student', 'Added new student: Samuel Amoo (248350)', '2025-11-26 00:10:55', 5, 'admin'),
(18, 'add_lecturer', 'Added new lecturer: Okoroma Grace (grace@gmail.com)', '2025-11-26 00:11:31', 5, 'admin'),
(19, 'add_student', 'Added new student: Hannah Crash (248356)', '2025-11-26 00:19:53', 4, 'admin'),
(20, 'add_lecturer', 'Added new lecturer: Francis Peter (francis@gmail.com)', '2025-11-27 11:57:41', 5, 'admin'),
(21, 'edit_lecturer', 'Updated lecturer: Francis Peter (francis@gmail.com)', '2025-11-27 11:58:57', 5, 'admin'),
(22, 'edit_lecturer', 'Updated lecturer: Okoroma Grace (grace@gmail.com)', '2025-11-27 11:59:16', 5, 'admin'),
(23, 'add_student', 'Added new student: Emmanuel Kenechi (234567)', '2025-11-27 12:01:08', 5, 'admin'),
(24, 'add_course', 'Added new course: MAC 111 - Principles of Mass Communication', '2025-11-27 12:13:44', 5, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `admintbl`
--

CREATE TABLE `admintbl` (
  `admin_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_num` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admintbl`
--

INSERT INTO `admintbl` (`admin_id`, `first_name`, `last_name`, `email`, `password`, `phone_num`, `created_at`, `profile_image`) VALUES
(1, 'Peter', 'Akinwale', 'admin1@example.com', 'Admin@123', '07010000001', '2025-11-19 10:04:23', NULL),
(2, 'Grace', 'Johnson', 'admin2@example.com', 'Admin@456', '07010000002', '2025-11-19 10:04:23', NULL),
(3, 'Michael', 'Benson', 'admin3@example.com', 'Admin@789', '07010000003', '2025-11-19 10:04:23', NULL),
(4, 'Okoroma', 'Godwin', 'chris@gmail.com', '$2y$10$216.n8qye02YABWI7bq0MekuBe7fby20rhHVCkweDCHBwEvxKnxzm', '08142406151', '2025-11-19 10:46:14', 'man-with-arms-crossed.jpg'),
(5, 'Razaq', 'Cash', 'cash@gmail.com', '$2y$10$EjWaOC3bI1bdJX6.4grL/ef6ZK2C2052hSYQjUGDdMJdFfV6sQI4m', '2348109876543', '2025-11-19 11:11:29', 'gzxggzh.jpg'),
(6, 'Daniel', 'John', 'dan@gmail.com', '$2y$10$U9whtwgVboP2.e42rc76r.AJSb6iCCiZutSecPjjD9OkmwtidKbEe', '09055520202', '2025-11-20 15:35:24', 'image 3.jpg'),
(7, 'Raymond', 'JohnCross', 'ray@gmail.com', '$2y$10$QSCoseTN54t/bjSFJlP8LeIgX04M5mvDQdubKinlhbTYE1MM030cW', '09055566419', '2025-11-27 13:19:13', 'yul.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `assignmenttbl`
--

CREATE TABLE `assignmenttbl` (
  `assignment_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `max_score` int(11) DEFAULT 100,
  `due_date` datetime NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `semester` enum('First','Second') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignmenttbl`
--

INSERT INTO `assignmenttbl` (`assignment_id`, `course_id`, `lecturer_id`, `title`, `description`, `max_score`, `due_date`, `academic_year`, `semester`, `is_active`, `created_at`) VALUES
(1, 5, 2, 'Create  a website', 'Rich Text / HTML. This explains what to do. It often allows the lecturer to embed images, PDFs, or links.', 70, '2025-11-27 16:23:00', '2023/2024', 'First', 1, '2025-11-27 15:23:30'),
(2, 2, 2, 'kayy', 'qwertyuiop', 70, '2025-10-31 17:30:00', '2024/2025', 'Second', 1, '2025-11-27 15:27:47'),
(3, 6, 2, 'Expand Manage Definition', 'Now incorporating detailed examples of the &amp;amp;quot;Manage&amp;amp;quot; function&amp;amp;#039;s scope. This includes handling TAs, guest access, and enabling integrations. I am also working in high-level content tasks, like Import/Export/Reset. I have the &amp;amp;quot;control panel&amp;amp;quot; concept down and am clarifying the &amp;amp;quot;Manage&amp;amp;quot; vs. &amp;amp;quot;Edit Content&amp;amp;quot; distinction. I plan to incorporate a visual aid in the response. I&amp;amp;#039;m focusing on LMS-specific terminology to provide precise examples in my final answer', 60, '2025-11-27 17:00:00', '2024/2025', 'First', 1, '2025-11-27 15:53:23'),
(4, 7, 4, 'Mass communication via mass media', 'Mass communication can be defined as the effective sharing of information with a large audience. The media through which this communication takes place are known as mass media and include radio, television, mobile devices, direct mail and the internet.', 50, '2025-12-05 18:00:00', '2024/2025', 'First', 1, '2025-11-27 17:00:29');

-- --------------------------------------------------------

--
-- Table structure for table `ass_gradetbl`
--

CREATE TABLE `ass_gradetbl` (
  `grade_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `graded_by` varchar(150) DEFAULT NULL,
  `score` int(11) NOT NULL,
  `max_score` int(11) DEFAULT 100,
  `grade_letter` char(2) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `academic_year` varchar(20) NOT NULL,
  `semester` enum('First','Second') NOT NULL,
  `graded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ass_subtbl`
--

CREATE TABLE `ass_subtbl` (
  `sub_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coursetbl`
--

CREATE TABLE `coursetbl` (
  `course_id` int(11) NOT NULL,
  `AdminID` int(11) DEFAULT NULL,
  `lecturer_id` int(11) DEFAULT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_title` varchar(200) NOT NULL,
  `course_description` text DEFAULT NULL,
  `course_unit` int(11) NOT NULL,
  `department` varchar(150) NOT NULL,
  `level` varchar(20) NOT NULL,
  `semester` enum('First','Second') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coursetbl`
--

INSERT INTO `coursetbl` (`course_id`, `AdminID`, `lecturer_id`, `course_code`, `course_title`, `course_description`, `course_unit`, `department`, `level`, `semester`, `created_at`) VALUES
(1, 5, 1, 'CSE 205', 'Advanced Algorithm Design and Analysis', 'This unit provides a deep dive into the design, complexity analysis, and implementation of sophisticated algorithms. Topics include randomized algorithms, amortized analysis, dynamic programming, network flow, NP-completeness, and approximation algorithms. Students will focus on proving algorithm efficiency using formal mathematical notation, emphasizing applications in resource optimization and large-scale data processing.', 3, 'Computer Science', 'ND 1', '', '2025-11-25 23:15:24'),
(2, 5, 2, 'FIN 215', 'Financial Accounting Principles', 'A foundational unit covering the principles and practices of external financial reporting. Students will learn the accounting cycle, including journalizing, posting, and preparation of the primary financial statements (Income Statement, Balance Sheet, and Statement of Cash Flows). Emphasis is placed on interpreting financial data, understanding GAAP (Generally Accepted Accounting Principles), and analyzing the economic impact of business transactions.', 2, 'Accountancy', 'ND 2', '', '2025-11-25 23:29:27'),
(3, 5, 1, 'CSE 105', 'Intro to Python', 'eer', 2, 'Mass Communication', 'ND 1', '', '2025-11-25 23:40:23'),
(4, 5, 1, 'CSE 100', 'Modernist European Fiction', 'An exploration of seminal fiction produced in Europe between 1900 and 1945, focusing on how authors broke with traditional narrative forms. The unit examines themes of alienation, consciousness, the collapse of grand narratives, and shifts in temporal and spatial representation. Key authors studied include James Joyce, Virginia Woolf, Franz Kafka, and Marcel Proust. Critical theory surrounding modernism and its impact on contemporary literature will also be addressed.', 3, 'Business Administration', 'ND 1', '', '2025-11-25 23:53:55'),
(5, 5, 2, 'MMP 102', 'Intro to Python', 'Modernist European FictionAn exploration of seminal fiction produced in Europe between 1900 and 1945, focusing on how authors broke with traditional narrative forms. The unit examines themes of alienation, consciousness, the collapse of grand narratives, and shifts in temporal and spatial representation. Key authors studied include James Joyce, Virginia Woolf, Franz Kafka, and Marcel Proust. Critical theory surrounding modernism and its impact on contemporary literature will also be addressed.', 3, 'Computer Science', 'ND 1', '', '2025-11-25 23:54:43'),
(6, 5, 2, 'STATS 205', 'Peace and Conflict', 'also the registration is for student Registration for cousre not add student, that feature is to come from a relationship of the stdent dashboard', 4, 'Business Administration', 'ND 1', '', '2025-11-26 00:09:12'),
(7, 5, 4, 'MAC 111', 'Principles of Mass Communication', 'An overview of various media forms and their role in society.', 3, 'Mass Communication', 'ND 1', '', '2025-11-27 12:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `course_materialtbl`
--

CREATE TABLE `course_materialtbl` (
  `material_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path_url` varchar(512) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `uploaded_by_lecturer_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_materialtbl`
--

INSERT INTO `course_materialtbl` (`material_id`, `course_id`, `title`, `description`, `file_path_url`, `file_type`, `is_published`, `uploaded_by_lecturer_id`, `created_at`, `updated_at`) VALUES
(1, 5, 'createdrag', 'qwerty', 'uploads/materials/1764260091_692878fb65d5c.pdf', 'lecture', 1, 2, '2025-11-27 17:14:51', '2025-11-27 17:14:51'),
(2, 6, 'Report of Activities', 'Interactive Next Step\r\nWould you like to move on to the Assignmenttbl now, or would you like to see how a Student Roster table would be structured to track course enrollment?', 'uploads/materials/1764260496_69287a90c39b6.pdf', 'reference', 1, 2, '2025-11-27 17:21:36', '2025-11-27 17:21:36'),
(3, 5, 'small boy', 'educational video on life', 'uploads/materials/1764260563_69287ad34457d.mp4', 'video', 1, 2, '2025-11-27 17:22:43', '2025-11-27 17:22:43'),
(4, 7, 'dissemination of information', 'Mass Communication studies how people exchange information through media, combining journalism, broadcasting, public relations, advertising, and digital media.', 'uploads/materials/1764262923_6928840bdb03e.pdf', 'reference', 1, 4, '2025-11-27 18:02:03', '2025-11-27 18:02:03');

-- --------------------------------------------------------

--
-- Table structure for table `course_regtbl`
--

CREATE TABLE `course_regtbl` (
  `reg_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `semester` enum('First','Second') NOT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_status` varchar(50) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_regtbl`
--

INSERT INTO `course_regtbl` (`reg_id`, `student_id`, `course_id`, `academic_year`, `semester`, `date_registered`, `approval_status`) VALUES
(1, 4, 2, '2024/2025', '', '2025-11-26 18:14:37', 'Pending'),
(2, 4, 4, '2024/2025', '', '2025-11-26 18:14:37', 'Pending'),
(3, 4, 6, '2024/2025', '', '2025-11-26 18:14:37', 'Pending'),
(4, 4, 1, '2024/2025', '', '2025-11-26 18:14:37', 'Pending'),
(5, 4, 5, '2024/2025', '', '2025-11-26 18:14:37', 'Pending'),
(20, 4, 3, '2024/2025', '', '2025-11-26 18:25:23', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `deadlinetbl`
--

CREATE TABLE `deadlinetbl` (
  `deadline_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `deadline_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `degree_requirementstbl`
--

CREATE TABLE `degree_requirementstbl` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `required_credits` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `degree_requirementstbl`
--

INSERT INTO `degree_requirementstbl` (`id`, `department`, `category`, `required_credits`, `created_at`) VALUES
(1, 'Computer Science', 'Computer Science Core', 45, '2025-11-27 23:27:28'),
(2, 'Mathematics', 'Mathematics', 12, '2025-11-27 23:27:28'),
(3, 'Science', 'Science Requirements', 8, '2025-11-27 23:27:28'),
(4, 'English', 'English & Communication', 9, '2025-11-27 23:27:28'),
(5, 'Communication', 'English & Communication', 9, '2025-11-27 23:27:28'),
(6, 'Liberal Arts', 'Liberal Arts', 15, '2025-11-27 23:27:28'),
(7, 'Electives', 'Electives', 31, '2025-11-27 23:27:28');

-- --------------------------------------------------------

--
-- Table structure for table `enrollmenttbl`
--

CREATE TABLE `enrollmenttbl` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluationtbl`
--

CREATE TABLE `evaluationtbl` (
  `eval_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `semester` enum('First','Second') NOT NULL,
  `ca_score` decimal(5,2) DEFAULT 0.00,
  `test_score` decimal(5,2) DEFAULT 0.00,
  `exam_score` decimal(5,2) DEFAULT 0.00,
  `total_score` decimal(5,2) GENERATED ALWAYS AS (`ca_score` + `test_score` + `exam_score`) STORED,
  `grade` char(2) DEFAULT NULL,
  `grade_point` decimal(3,2) DEFAULT NULL,
  `credit_units` int(11) DEFAULT NULL,
  `quality_points` decimal(5,2) GENERATED ALWAYS AS (`grade_point` * `credit_units`) STORED,
  `entered_by` varchar(150) DEFAULT NULL,
  `entered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturerrecentactivitytbl`
--

CREATE TABLE `lecturerrecentactivitytbl` (
  `activity_id` int(11) NOT NULL,
  `LecturerID` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `activity_description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturerrecentactivitytbl`
--

INSERT INTO `lecturerrecentactivitytbl` (`activity_id`, `LecturerID`, `activity_type`, `activity_description`, `timestamp`) VALUES
(1, 2, 'material_uploaded', 'Uploaded material: createdrag', '2025-11-27 16:14:51'),
(2, 2, 'material_uploaded', 'Uploaded material: Report of Activities', '2025-11-27 16:21:36'),
(3, 2, 'material_uploaded', 'Uploaded material: small boy', '2025-11-27 16:22:43'),
(4, 4, 'assignment_created', 'Created assignment: Mass communication via mass media', '2025-11-27 17:00:29'),
(5, 4, 'material_uploaded', 'Uploaded material: dissemination of information', '2025-11-27 17:02:03');

-- --------------------------------------------------------

--
-- Table structure for table `lecturertbl`
--

CREATE TABLE `lecturertbl` (
  `LecturerID` int(11) NOT NULL,
  `AdminID` int(11) DEFAULT NULL,
  `First_name` varchar(100) NOT NULL,
  `Last_Name` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone_Num` varchar(20) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Department` varchar(150) NOT NULL,
  `Gender` enum('Male','Female','Other') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturertbl`
--

INSERT INTO `lecturertbl` (`LecturerID`, `AdminID`, `First_name`, `Last_Name`, `Email`, `Phone_Num`, `Password`, `Department`, `Gender`, `created_at`, `profile_image`) VALUES
(1, NULL, 'Ife', 'Olufemi', 'ife@gmail.comk', '90555202021', '$2y$10$ZMF8kab9WgHHEQXPjQyFfehtfnJ7kfcbW07kBsBauSdeLrb.XqwuC', 'Mass Communication', 'Female', '2025-11-25 23:03:30', NULL),
(2, NULL, 'Razaq', 'Chris', 'cash@gmail.comm', '90555202021', '$2y$10$VYSOwNnF6ItCcQjFlhDIpejVsYbUnrSv6ToZfX64e9lzXSPhpQh1K', 'Business Administration', 'Male', '2025-11-25 23:05:36', '1764262290_man-with-arms-crossed.jpg'),
(3, NULL, 'Okoroma', 'Grace', 'grace@gmail.com', '9055520202', '$2y$10$o7XZH2Bgvm3HCzZWeB2ZIufCDXQtjCHVpIRcPG7VVphtvyYuPsiy2', 'Accountancy', 'Female', '2025-11-26 00:11:31', NULL),
(4, 5, 'Francis', 'Peter', 'francis@gmail.com', '08142406151', '$2y$10$hZGMe8cbagNYG9o8l6DeieIwuW3v3QjqHcKtK2LeJmAaVZ9C626Z.', 'Computer Science', 'Male', '2025-11-27 11:57:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `resulttbl`
--

CREATE TABLE `resulttbl` (
  `result_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `ca_score` decimal(5,2) DEFAULT 0.00,
  `test_score` decimal(5,2) DEFAULT 0.00,
  `exam_score` decimal(5,2) DEFAULT 0.00,
  `total_score` decimal(5,2) GENERATED ALWAYS AS (`ca_score` + `test_score` + `exam_score`) STORED,
  `grade_letter` char(2) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `academic_year` varchar(20) NOT NULL,
  `semester` enum('First','Second') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studentrecentactivitytbl`
--

CREATE TABLE `studentrecentactivitytbl` (
  `activity_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `activity_description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studenttbl`
--

CREATE TABLE `studenttbl` (
  `student_id` int(11) NOT NULL,
  `AdminID` int(11) DEFAULT NULL,
  `Matric_No` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `Phone_Num` varchar(20) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Department` varchar(150) NOT NULL,
  `Level` varchar(20) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studenttbl`
--

INSERT INTO `studenttbl` (`student_id`, `AdminID`, `Matric_No`, `first_name`, `last_name`, `email`, `Phone_Num`, `Password`, `Department`, `Level`, `academic_year`, `Gender`, `created_at`, `profile_image`) VALUES
(1, 4, '234444', 'Okoroma', 'Grace', 'chris@gmail.com', '2348109876543', '$2y$10$8xhRk/JFSR5j116rHah.YusTrYCI/96SymDqtc9RfIQJrTr6VXNhC', 'Accountancy', 'ND 1', '', 'Female', '2025-11-24 23:41:54', NULL),
(2, NULL, '243670', 'Razaq', 'Amoo', 'amoo@gmail.com', '09056678915', '$2y$10$4dyWqqB.sFqVMkqPmvM.IecWXFaUvX00pocm3XHCXzvDi0Jjn0QKi', 'Computer Science', 'ND 2', '', 'Male', '2025-11-25 00:08:10', NULL),
(3, NULL, '248350', 'Samuel', 'Amoo', 'casamu@gmail.com', '2348109876543', '$2y$10$n/zct5rh0mAi.hQwUUUYNuUzLwMdUdB8/2LxjJlfDnprt56vLkBF.', 'Computer Science', 'ND 1', '', 'Male', '2025-11-26 00:10:55', NULL),
(4, NULL, '248356', 'Hannah', 'Crash', 'crush@gmail.com', '8061234567905555', '$2y$10$O9av0H1HlJgkteHwT940m.44f9.cF/aDY6FcfdhXjpYuIXPSnbLnC', 'Business Administration', 'ND 1', '', 'Female', '2025-11-26 00:19:52', '1764178574_medium-shot-smiley-woman-posing (2).jpg'),
(5, 5, '234567', 'Emmanuel', 'Kenechi', 'ken@gmail.com', '8061234567', '$2y$10$5eAB8tkOQzJsX1EJEoJT1Ocskqr99vd0QTnmDdTGonXJmY43ohe2i', 'Business Administration', 'ND 1', '', 'Male', '2025-11-27 12:01:08', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admintbl`
--
ALTER TABLE `admintbl`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `assignmenttbl`
--
ALTER TABLE `assignmenttbl`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `fk_assignment_course` (`course_id`),
  ADD KEY `fk_assignment_lecturer` (`lecturer_id`);

--
-- Indexes for table `ass_gradetbl`
--
ALTER TABLE `ass_gradetbl`
  ADD PRIMARY KEY (`grade_id`),
  ADD UNIQUE KEY `unique_grade` (`submission_id`,`lecturer_id`),
  ADD KEY `fk_grade_assignment` (`assignment_id`),
  ADD KEY `fk_grade_student` (`student_id`),
  ADD KEY `fk_grade_lecturer` (`lecturer_id`);

--
-- Indexes for table `ass_subtbl`
--
ALTER TABLE `ass_subtbl`
  ADD PRIMARY KEY (`sub_id`),
  ADD UNIQUE KEY `unique_submission` (`assignment_id`,`student_id`),
  ADD KEY `fk_sub_student` (`student_id`);

--
-- Indexes for table `coursetbl`
--
ALTER TABLE `coursetbl`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `fk_course_admin` (`AdminID`),
  ADD KEY `fk_course_lecturer` (`lecturer_id`);

--
-- Indexes for table `course_materialtbl`
--
ALTER TABLE `course_materialtbl`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `fk_material_course` (`course_id`),
  ADD KEY `fk_material_uploader` (`uploaded_by_lecturer_id`);

--
-- Indexes for table `course_regtbl`
--
ALTER TABLE `course_regtbl`
  ADD PRIMARY KEY (`reg_id`),
  ADD UNIQUE KEY `unique_course_registration` (`student_id`,`course_id`,`academic_year`,`semester`),
  ADD KEY `fk_reg_course` (`course_id`);

--
-- Indexes for table `deadlinetbl`
--
ALTER TABLE `deadlinetbl`
  ADD PRIMARY KEY (`deadline_id`),
  ADD KEY `fk_deadline_coursetbl` (`course_id`),
  ADD KEY `fk_deadline_lecturertbl` (`lecturer_id`);

--
-- Indexes for table `degree_requirementstbl`
--
ALTER TABLE `degree_requirementstbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollmenttbl`
--
ALTER TABLE `enrollmenttbl`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `evaluationtbl`
--
ALTER TABLE `evaluationtbl`
  ADD PRIMARY KEY (`eval_id`),
  ADD UNIQUE KEY `unique_eval_record` (`student_id`,`course_id`,`academic_year`,`semester`),
  ADD KEY `fk_eval_course` (`course_id`),
  ADD KEY `fk_eval_lecturer` (`lecturer_id`);

--
-- Indexes for table `lecturerrecentactivitytbl`
--
ALTER TABLE `lecturerrecentactivitytbl`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `LecturerID` (`LecturerID`);

--
-- Indexes for table `lecturertbl`
--
ALTER TABLE `lecturertbl`
  ADD PRIMARY KEY (`LecturerID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `fk_lecturer_admin` (`AdminID`);

--
-- Indexes for table `resulttbl`
--
ALTER TABLE `resulttbl`
  ADD PRIMARY KEY (`result_id`),
  ADD UNIQUE KEY `unique_student_course` (`student_id`,`course_id`,`academic_year`,`semester`),
  ADD KEY `fk_result_course` (`course_id`);

--
-- Indexes for table `studentrecentactivitytbl`
--
ALTER TABLE `studentrecentactivitytbl`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `studenttbl`
--
ALTER TABLE `studenttbl`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `Matric_No` (`Matric_No`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_student_admin` (`AdminID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `admintbl`
--
ALTER TABLE `admintbl`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `assignmenttbl`
--
ALTER TABLE `assignmenttbl`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ass_gradetbl`
--
ALTER TABLE `ass_gradetbl`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ass_subtbl`
--
ALTER TABLE `ass_subtbl`
  MODIFY `sub_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coursetbl`
--
ALTER TABLE `coursetbl`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_materialtbl`
--
ALTER TABLE `course_materialtbl`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_regtbl`
--
ALTER TABLE `course_regtbl`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `deadlinetbl`
--
ALTER TABLE `deadlinetbl`
  MODIFY `deadline_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `degree_requirementstbl`
--
ALTER TABLE `degree_requirementstbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `enrollmenttbl`
--
ALTER TABLE `enrollmenttbl`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluationtbl`
--
ALTER TABLE `evaluationtbl`
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lecturerrecentactivitytbl`
--
ALTER TABLE `lecturerrecentactivitytbl`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lecturertbl`
--
ALTER TABLE `lecturertbl`
  MODIFY `LecturerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `resulttbl`
--
ALTER TABLE `resulttbl`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `studentrecentactivitytbl`
--
ALTER TABLE `studentrecentactivitytbl`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `studenttbl`
--
ALTER TABLE `studenttbl`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignmenttbl`
--
ALTER TABLE `assignmenttbl`
  ADD CONSTRAINT `fk_assignment_course` FOREIGN KEY (`course_id`) REFERENCES `coursetbl` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assignment_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturertbl` (`LecturerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ass_gradetbl`
--
ALTER TABLE `ass_gradetbl`
  ADD CONSTRAINT `fk_grade_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignmenttbl` (`assignment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grade_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturertbl` (`LecturerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grade_student` FOREIGN KEY (`student_id`) REFERENCES `studenttbl` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grade_submission` FOREIGN KEY (`submission_id`) REFERENCES `ass_subtbl` (`sub_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ass_subtbl`
--
ALTER TABLE `ass_subtbl`
  ADD CONSTRAINT `fk_sub_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignmenttbl` (`assignment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sub_student` FOREIGN KEY (`student_id`) REFERENCES `studenttbl` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coursetbl`
--
ALTER TABLE `coursetbl`
  ADD CONSTRAINT `fk_course_admin` FOREIGN KEY (`AdminID`) REFERENCES `admintbl` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_course_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturertbl` (`LecturerID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `course_materialtbl`
--
ALTER TABLE `course_materialtbl`
  ADD CONSTRAINT `fk_material_course` FOREIGN KEY (`course_id`) REFERENCES `coursetbl` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_material_uploader` FOREIGN KEY (`uploaded_by_lecturer_id`) REFERENCES `lecturertbl` (`LecturerID`) ON DELETE SET NULL;

--
-- Constraints for table `course_regtbl`
--
ALTER TABLE `course_regtbl`
  ADD CONSTRAINT `fk_reg_course` FOREIGN KEY (`course_id`) REFERENCES `coursetbl` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reg_student` FOREIGN KEY (`student_id`) REFERENCES `studenttbl` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deadlinetbl`
--
ALTER TABLE `deadlinetbl`
  ADD CONSTRAINT `fk_deadline_coursetbl` FOREIGN KEY (`course_id`) REFERENCES `coursetbl` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_deadline_lecturertbl` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturertbl` (`LecturerID`) ON DELETE CASCADE;

--
-- Constraints for table `enrollmenttbl`
--
ALTER TABLE `enrollmenttbl`
  ADD CONSTRAINT `enrollmenttbl_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `studenttbl` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrollmenttbl_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `coursetbl` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `evaluationtbl`
--
ALTER TABLE `evaluationtbl`
  ADD CONSTRAINT `fk_eval_course` FOREIGN KEY (`course_id`) REFERENCES `coursetbl` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturertbl` (`LecturerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_student` FOREIGN KEY (`student_id`) REFERENCES `studenttbl` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lecturerrecentactivitytbl`
--
ALTER TABLE `lecturerrecentactivitytbl`
  ADD CONSTRAINT `lecturerrecentactivitytbl_ibfk_1` FOREIGN KEY (`LecturerID`) REFERENCES `lecturertbl` (`LecturerID`);

--
-- Constraints for table `lecturertbl`
--
ALTER TABLE `lecturertbl`
  ADD CONSTRAINT `fk_lecturer_admin` FOREIGN KEY (`AdminID`) REFERENCES `admintbl` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `resulttbl`
--
ALTER TABLE `resulttbl`
  ADD CONSTRAINT `fk_result_course` FOREIGN KEY (`course_id`) REFERENCES `coursetbl` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_result_student` FOREIGN KEY (`student_id`) REFERENCES `studenttbl` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `studentrecentactivitytbl`
--
ALTER TABLE `studentrecentactivitytbl`
  ADD CONSTRAINT `studentrecentactivitytbl_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `studenttbl` (`student_id`);

--
-- Constraints for table `studenttbl`
--
ALTER TABLE `studenttbl`
  ADD CONSTRAINT `fk_student_admin` FOREIGN KEY (`AdminID`) REFERENCES `admintbl` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
