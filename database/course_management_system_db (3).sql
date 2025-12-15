-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 05:19 PM
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
(24, 'add_course', 'Added new course: MAC 111 - Principles of Mass Communication', '2025-11-27 12:13:44', 5, 'admin'),
(25, 'add_student', 'Added new student: Kcee Olisa (248390)', '2025-11-29 01:19:48', 7, 'admin'),
(26, 'student_updated', 'Updated student: Kcee Olisa (6)', '2025-11-29 01:29:11', 7, 'admin'),
(27, 'student_updated', 'Updated student: Kcee Olisa (6)', '2025-11-29 01:29:24', 7, 'admin'),
(28, 'add_student', 'Added new student: Aaron Stephen (245360)', '2025-12-01 01:51:58', 7, 'admin'),
(29, 'add_course', 'Added new course: BUS 205 - Marketing Human Resources', '2025-12-01 01:55:40', 7, 'admin'),
(30, 'edit_course', 'Updated course: BUS 205 - Marketing Human Resources', '2025-12-01 01:57:15', 7, 'admin'),
(31, 'edit_course', 'Updated course: BUS 205 - Marketing Human Resources', '2025-12-01 01:57:28', 7, 'admin'),
(32, 'student_updated', 'Updated student: Aaron Stephen (7)', '2025-12-01 02:14:04', 7, 'admin'),
(33, 'student_updated', 'Updated student: Aaron Stephen (7)', '2025-12-01 02:14:14', 7, 'admin'),
(34, 'edit_lecturer', 'Updated lecturer: Francis Peter (francis@gmail.com)', '2025-12-01 02:14:32', 7, 'admin'),
(35, 'edit_lecturer', 'Updated lecturer: Francis Peter (francis@gmail.com)', '2025-12-01 02:14:41', 7, 'admin'),
(36, 'approve_course_registration', 'Approved course registration for student ID: 7', '2025-12-01 02:45:47', 7, 'admin'),
(37, 'approve_course_registration', 'Approved course registration for student ID: 4', '2025-12-01 03:22:26', 7, 'admin'),
(38, 'add_lecturer', 'Added new lecturer: Cally Nwa (nwa@gmail.com)', '2025-12-01 05:53:29', 7, 'admin'),
(39, 'add_course', 'Added new course: POP 205 - Password Manager', '2025-12-01 05:55:04', 7, 'admin'),
(40, 'add_course', 'Added new course: GNS 111 - Citizenship Education', '2025-12-02 12:52:55', 7, 'admin'),
(41, 'add_course', 'Added new course: GNS 101 - Use of English', '2025-12-02 13:09:08', 7, 'admin'),
(42, 'add_course', 'Added new course: MAC 111 - Media Writing and Style I', '2025-12-02 13:11:35', 7, 'admin'),
(43, 'add_course', 'Added new course: MAC 112 - Foreign Languages', '2025-12-02 15:17:30', 7, 'admin'),
(44, 'add_course', 'Added new course: MAC 113 - Computer Application for Media and Communication', '2025-12-02 15:52:04', 7, 'admin'),
(45, 'add_course', 'Added new course: MAC 114 - Foundation of Media and Communication', '2025-12-02 15:55:07', 7, 'admin'),
(46, 'add_course', 'Added new course: MAC 115 - Newswriting and Reporting I', '2025-12-02 21:18:33', 7, 'admin'),
(47, 'add_course', 'Added new course: MAC 116 - Fundamentals of Broadcasting', '2025-12-02 21:29:58', 7, 'admin'),
(48, 'add_course', 'Added new course: MAC 117 - Principles of Advertising', '2025-12-02 21:32:22', 7, 'admin'),
(49, 'add_course', 'Added new course: GNS 102 - Communication in English I', '2025-12-02 21:38:16', 7, 'admin'),
(50, 'add_course', 'Added new course: GNS 121 - Citizenship Education', '2025-12-02 21:44:40', 7, 'admin'),
(51, 'edit_course', 'Updated course: GNS 102 - Communication in English I', '2025-12-02 21:45:46', 7, 'admin'),
(52, 'add_course', 'Added new course: MAC 121 - Media Writing and style II', '2025-12-02 21:50:08', 7, 'admin'),
(53, 'edit_course', 'Updated course: MAC 121 - Media Writing and style II', '2025-12-02 21:51:54', 7, 'admin'),
(54, 'edit_course', 'Updated course: GNS 121 - Citizenship Education', '2025-12-02 21:52:16', 7, 'admin'),
(55, 'edit_course', 'Updated course: GNS 102 - Communication in English I', '2025-12-02 21:52:29', 7, 'admin'),
(56, 'add_course', 'Added new course: MAC 122 - Indigenous Communication System', '2025-12-02 22:07:08', 7, 'admin'),
(57, 'add_course', 'Added new course: MAC 123 - Digital Communication', '2025-12-03 03:04:03', 7, 'admin'),
(58, 'add_course', 'Added new course: MAC 124 - Graphics design for Media and Communication', '2025-12-03 03:06:14', 7, 'admin'),
(59, 'add_course', 'Added new course: MAC 125 - Newswriting and Reporting II', '2025-12-03 03:08:50', 7, 'admin'),
(60, 'add_course', 'Added new course: MAC 126 - Principles of Public Relations', '2025-12-03 03:11:00', 7, 'admin'),
(61, 'add_course', 'Added new course: ENT 126 - Introduction to Entrepreneurship I', '2025-12-03 03:13:01', 7, 'admin'),
(62, 'add_course', 'Added new course: GNS 201 - Use of English I', '2025-12-03 03:22:53', 7, 'admin'),
(63, 'student_updated', 'Updated student: Kcee Ethapemi (6)', '2025-12-03 03:26:51', 7, 'admin'),
(64, 'add_student', 'Added new student: Augustina Offorkansi (248389)', '2025-12-03 03:30:32', 7, 'admin'),
(65, 'add_student', 'Added new student: Jane Doe (248387)', '2025-12-03 03:32:33', 7, 'admin'),
(66, 'add_lecturer', 'Added new lecturer: Oshodi Michael (michael@gmail.com)', '2025-12-03 03:37:03', 7, 'admin'),
(67, 'add_lecturer', 'Added new lecturer: Adeyemi O.A (adeyemi@gmail.com)', '2025-12-03 03:44:37', 7, 'admin'),
(68, 'add_lecturer', 'Added new lecturer: Chizor Lois (lois@gmail.com)', '2025-12-03 03:45:55', 7, 'admin'),
(69, 'add_lecturer', 'Added new lecturer: Somide Stanislaus (somide@gmail.com)', '2025-12-03 03:46:58', 7, 'admin'),
(70, 'add_lecturer', 'Added new lecturer: Moses Kanu (kanu@gmail.com)', '2025-12-03 03:48:21', 7, 'admin'),
(71, 'add_lecturer', 'Added new lecturer: Tony D. Bamjo (bamjo@gmail.com)', '2025-12-03 04:39:30', 8, 'admin'),
(72, 'add_lecturer', 'Added new lecturer: Engr. Segun Fatoki (fatoki@gmail.com)', '2025-12-03 04:40:39', 8, 'admin'),
(73, 'edit_lecturer', 'Updated lecturer: Mr. Adeyemi O.A (adeyemi@gmail.com)', '2025-12-03 04:41:01', 8, 'admin'),
(74, 'edit_lecturer', 'Updated lecturer: Mrs. Chizor Lois (lois@gmail.com)', '2025-12-03 04:41:17', 8, 'admin'),
(75, 'edit_lecturer', 'Updated lecturer: Mr. Moses Kanu (kanu@gmail.com)', '2025-12-03 04:41:28', 8, 'admin'),
(76, 'edit_lecturer', 'Updated lecturer: Mr. Oshodi Michael (michael@gmail.com)', '2025-12-03 04:41:41', 8, 'admin'),
(77, 'edit_lecturer', 'Updated lecturer: Mr. Somide Stanislaus (somide@gmail.com)', '2025-12-03 04:41:51', 8, 'admin'),
(78, 'edit_lecturer', 'Updated lecturer: Mr. Tony D. Bamjo (bamjo@gmail.com)', '2025-12-03 04:42:03', 8, 'admin'),
(79, 'edit_course', 'Updated course: GNS 201 - Use of English I', '2025-12-03 05:02:59', 7, 'admin'),
(80, 'add_lecturer', 'Added new lecturer: Mrs. Esther Adeniji (esther@mail.com)', '2025-12-03 05:10:32', 7, 'admin'),
(81, 'add_lecturer', 'Added new lecturer: Mr. Ayodele Yusuff (yusuf@gmail.com)', '2025-12-03 05:11:17', 7, 'admin'),
(82, 'add_lecturer', 'Added new lecturer: Mr. Akintola Oloyede (oloyede@gmail.com)', '2025-12-03 05:12:23', 7, 'admin'),
(83, 'add_lecturer', 'Added new lecturer: Mr. Afolabi Kehinde (kehide@gmail.com)', '2025-12-03 05:13:11', 7, 'admin'),
(84, 'add_lecturer', 'Added new lecturer: Mr. Babajinmi Ojo Abiola (ojo@gmail.com)', '2025-12-03 05:13:55', 7, 'admin'),
(85, 'add_lecturer', 'Added new lecturer: Mr. Oyelakin Micheal (michaeloye@gmail.com)', '2025-12-03 05:15:27', 7, 'admin'),
(86, 'add_lecturer', 'Added new lecturer: Olugbenga Paul (paul@gmail.com)', '2025-12-03 05:16:02', 7, 'admin'),
(87, 'edit_course', 'Updated course: MAC 122 - Indigenous Communication System', '2025-12-03 05:18:16', 7, 'admin'),
(88, 'edit_course', 'Updated course: MAC 126 - Principles of Public Relations', '2025-12-03 05:19:31', 7, 'admin'),
(89, 'edit_course', 'Updated course: ENT 126 - Introduction to Entrepreneurship I', '2025-12-03 05:19:53', 7, 'admin'),
(90, 'edit_course', 'Updated course: GNS 101 - Use of English', '2025-12-03 05:20:53', 7, 'admin'),
(91, 'edit_course', 'Updated course: GNS 111 - Citizenship Education', '2025-12-03 05:21:22', 7, 'admin'),
(92, 'edit_course', 'Updated course: GNS 121 - Citizenship Education', '2025-12-03 05:21:35', 7, 'admin'),
(93, 'edit_course', 'Updated course: MAC 113 - Computer Application for Media and Communication', '2025-12-03 05:22:27', 7, 'admin'),
(94, 'edit_course', 'Updated course: MAC 125 - Newswriting and Reporting II', '2025-12-03 05:22:51', 7, 'admin'),
(95, 'add_lecturer', 'Added new lecturer: Mr. Onibiyo Olusola (olusola@gmail.com)', '2025-12-03 05:24:05', 7, 'admin'),
(96, 'add_lecturer', 'Added new lecturer: Mrs. Ayantade Olubunmi (ayantade@gmail.com)', '2025-12-03 05:24:51', 7, 'admin'),
(97, 'add_lecturer', 'Added new lecturer: Mr. Aikulola Emmanuel (aikulola@gmail.com)', '2025-12-03 05:25:52', 7, 'admin'),
(98, 'add_lecturer', 'Added new lecturer: Mr. Ajiboye John (ajiboye@gmail.com)', '2025-12-03 05:26:51', 7, 'admin'),
(99, 'add_lecturer', 'Added new lecturer: Mr. Oluwafunto Adeyinka (adeyinka@gmail.com)', '2025-12-03 05:27:36', 7, 'admin'),
(100, 'add_lecturer', 'Added new lecturer: Engr. Segun Fatoki (fat@gmail.com)', '2025-12-03 05:28:35', 7, 'admin'),
(101, 'delete_lecturer', 'Deleted lecturer: Engr. Segun Fatoki (fat@gmail.com)', '2025-12-03 05:28:48', 7, 'admin'),
(102, 'add_lecturer', 'Added new lecturer: Mr. Amoo Samuel (amoo@gmail.com)', '2025-12-03 05:29:33', 7, 'admin'),
(103, 'add_lecturer', 'Added new lecturer: Barr. Olatunbosun Temitope (temitope@gmail.com)', '2025-12-03 05:31:27', 7, 'admin'),
(104, 'add_lecturer', 'Added new lecturer: Mr. Adetoyi Michael (ayetoyi@gmail.com)', '2025-12-03 05:32:20', 7, 'admin'),
(105, 'add_lecturer', 'Added new lecturer: Mrs. Adenike Adegbite (adegbite@gmail.com)', '2025-12-03 05:34:23', 7, 'admin'),
(106, 'add_lecturer', 'Added new lecturer: Ayeri Jonathan Emeka (Jonathan@gmail.com)', '2025-12-03 05:35:02', 7, 'admin'),
(107, 'add_lecturer', 'Added new lecturer: Timileyin Fatoki (timi@gmail.com)', '2025-12-03 05:35:43', 7, 'admin'),
(108, 'add_lecturer', 'Added new lecturer: Mr. Awodeyi Tolulope (toluope@gmail.com)', '2025-12-03 05:36:51', 7, 'admin'),
(109, 'add_lecturer', 'Added new lecturer: Dr. Idowu Ifedotun (ifedotun@gmail.com)', '2025-12-03 05:37:36', 7, 'admin'),
(110, 'add_lecturer', 'Added new lecturer: Mr. Ibitola A. Gideon (gideon@gmail.com)', '2025-12-03 05:38:21', 7, 'admin'),
(111, 'add_lecturer', 'Added new lecturer: Mr. Ige Olufemi (olufemi@gmail.com)', '2025-12-03 05:39:40', 7, 'admin'),
(112, 'add_lecturer', 'Added new lecturer: Mr. Oyewole Lukman (lukman@gmail.com)', '2025-12-03 05:40:18', 7, 'admin'),
(113, 'add_lecturer', 'Added new lecturer: Mr. Mapayi Temitope (mapayi@gmail.com)', '2025-12-03 05:41:37', 7, 'admin'),
(114, 'add_lecturer', 'Added new lecturer: Dr. Sunday Adebisi (sunday@gmail.com)', '2025-12-03 05:43:51', 7, 'admin'),
(115, 'add_lecturer', 'Added new lecturer: Mr. Osamudiamen Miracle (osamudiamen@gmail.com)', '2025-12-03 05:45:24', 7, 'admin'),
(116, 'add_lecturer', 'Added new lecturer: Mr Ozenua Micahel (ozenua@gmail.com)', '2025-12-03 05:46:21', 7, 'admin'),
(117, 'add_course', 'Added new course: OTM 101-102 - Technical English 1', '2025-12-03 05:51:14', 7, 'admin'),
(118, 'add_course', 'Added new course: BFN 111 - Elements of Banking 1', '2025-12-03 05:54:44', 7, 'admin'),
(119, 'add_course', 'Added new course: BAM 112 - Business Mathematics 1', '2025-12-03 05:58:09', 7, 'admin'),
(120, 'add_course', 'Added new course: BAM 113 - Principles of Law', '2025-12-03 06:00:17', 8, 'admin'),
(121, 'add_course', 'Added new course: BAM 211 - Principles of Management 1', '2025-12-03 06:03:01', 8, 'admin'),
(122, 'add_course', 'Added new course: BFN 112 - Principles of Economics 1', '2025-12-03 06:05:07', 8, 'admin'),
(123, 'add_course', 'Added new course: ACC 111 - Principles of Accounts 1', '2025-12-03 08:17:47', 8, 'admin'),
(124, 'add_course', 'Added new course: BNF 116 - Information Communications Technology 1', '2025-12-03 08:19:37', 8, 'admin'),
(125, 'add_course', 'Added new course: OTM 201-202 - Technical English 2', '2025-12-03 08:21:53', 8, 'admin'),
(126, 'add_course', 'Added new course: BAM 126 - Introduction to Entrepreneurship', '2025-12-03 08:24:36', 8, 'admin'),
(127, 'add_course', 'Added new course: BAM 122 - Business Mathematics 2', '2025-12-03 08:26:58', 8, 'admin'),
(128, 'add_course', 'Added new course: BFN 121 - Elements of Banking 2', '2025-12-03 08:30:38', 8, 'admin'),
(129, 'add_course', 'Added new course: BAM 214 - Business Law', '2025-12-03 08:34:26', 8, 'admin'),
(130, 'add_course', 'Added new course: BAM 221 - Principles of Management 2', '2025-12-03 08:36:18', 8, 'admin'),
(131, 'edit_course', 'Updated course: BAM 214 - Business Law', '2025-12-03 08:36:34', 8, 'admin'),
(132, 'edit_course', 'Updated course: BFN 121 - Elements of Banking 2', '2025-12-03 08:37:05', 8, 'admin'),
(133, 'edit_course', 'Updated course: BAM 122 - Business Mathematics 2', '2025-12-03 08:37:53', 8, 'admin'),
(134, 'edit_course', 'Updated course: BAM 126 - Introduction to Entrepreneurship', '2025-12-03 08:38:18', 8, 'admin'),
(135, 'edit_course', 'Updated course: OTM 201-202 - Technical English 2', '2025-12-03 08:38:51', 8, 'admin'),
(136, 'edit_course', 'Updated course: MAC 124 - Graphics design for Media and Communication', '2025-12-03 08:40:03', 8, 'admin'),
(137, 'edit_course', 'Updated course: MAC 123 - Digital Communication', '2025-12-03 08:40:18', 8, 'admin'),
(138, 'edit_course', 'Updated course: MAC 121 - Media Writing and style II', '2025-12-03 08:40:30', 8, 'admin'),
(139, 'edit_course', 'Updated course: GNS 102 - Communication in English I', '2025-12-03 08:40:45', 8, 'admin'),
(140, 'edit_course', 'Updated course: MAC 117 - Principles of Advertising', '2025-12-03 08:40:58', 8, 'admin'),
(141, 'edit_course', 'Updated course: MAC 116 - Fundamentals of Broadcasting', '2025-12-03 08:42:19', 8, 'admin'),
(142, 'edit_course', 'Updated course: MAC 115 - Newswriting and Reporting I', '2025-12-03 08:42:34', 8, 'admin'),
(143, 'edit_course', 'Updated course: MAC 114 - Foundation of Media and Communication', '2025-12-03 08:42:44', 8, 'admin'),
(144, 'edit_course', 'Updated course: MAC 112 - Foreign Languages', '2025-12-03 08:43:00', 8, 'admin'),
(145, 'edit_course', 'Updated course: MAC 111 - Media Writing and Style I', '2025-12-03 08:43:09', 8, 'admin'),
(146, 'add_course', 'Added new course: BFN 122 - Principles of Economics 2', '2025-12-03 08:45:26', 8, 'admin'),
(147, 'add_course', 'Added new course: ACC 121 - Principles of Accounts 2', '2025-12-03 08:49:37', 8, 'admin'),
(148, 'add_course', 'Added new course: BNF 126 - Information Communications Technology 2', '2025-12-03 08:59:05', 8, 'admin'),
(149, 'add_course', 'Added new course: ACC 214 - Taxation 1', '2025-12-03 09:02:59', 8, 'admin'),
(150, 'add_course', 'Added new course: COM 111 - Introduction to computing', '2025-12-03 09:06:39', 8, 'admin'),
(151, 'add_course', 'Added new course: COM 112 - Introduction to Digital Electronics', '2025-12-03 09:08:50', 8, 'admin'),
(152, 'add_course', 'Added new course: COM 113 - Introduction to Programming', '2025-12-03 09:12:05', 8, 'admin'),
(153, 'add_course', 'Added new course: COM 114 - Statistics for Computing 1', '2025-12-03 09:14:51', 8, 'admin'),
(154, 'add_course', 'Added new course: COM 115 - Computer application packages I', '2025-12-03 09:18:15', 8, 'admin'),
(155, 'add_course', 'Added new course: MTH 111 - Logic and Linear Algebra', '2025-12-03 09:20:13', 8, 'admin'),
(156, 'add_course', 'Added new course: ICOM 121 - Programming using C Language', '2025-12-03 09:56:42', 8, 'admin'),
(157, 'add_course', 'Added new course: COM 122 - Introduction to Internet', '2025-12-03 09:58:58', 8, 'admin'),
(158, 'edit_course', 'Updated course: ICOM 121 - Programming using C Language', '2025-12-03 10:00:22', 8, 'admin'),
(159, 'add_course', 'Added new course: COM 123 - Programming Language using Java I', '2025-12-03 10:13:53', 8, 'admin'),
(160, 'add_course', 'Added new course: COM 124 - Data structure and Algorithms', '2025-12-03 10:15:51', 8, 'admin'),
(161, 'add_course', 'Added new course: COM 125 - Introduction to Systems Analysis and Design', '2025-12-03 10:17:55', 8, 'admin'),
(162, 'add_course', 'Added new course: COM 126 - PC Upgrade &amp; Maintenance', '2025-12-03 10:19:55', 8, 'admin'),
(163, 'add_course', 'Added new course: GNS 128 - Citizenship Education II', '2025-12-03 10:22:38', 8, 'admin'),
(164, 'add_course', 'Added new course: EED 126 - Practice of Entrepreneurship', '2025-12-03 10:26:21', 8, 'admin'),
(165, 'add_course', 'Added new course: GNS 228 - Research Methods', '2025-12-03 10:28:00', 8, 'admin'),
(166, 'add_course', 'Added new course: BAM 111 - Introduction to Business 1', '2025-12-03 10:30:12', 8, 'admin'),
(167, 'add_course', 'Added new course: BAM 114 - Principles of Economics 1', '2025-12-03 10:36:02', 8, 'admin'),
(168, 'delete_course', 'Deleted course: Principles of Economics 1 (BAM 114) - Lecturer: Engr. Segun Fatoki', '2025-12-03 10:36:31', 8, 'admin'),
(169, 'add_course', 'Added new course: BAM 116 - Elements of Public Administration', '2025-12-03 10:39:10', 8, 'admin'),
(170, 'approve_course_registration', 'Approved course registration for student ID: 7', '2025-12-03 22:18:57', 8, 'admin'),
(171, 'add_student', 'Added new student: Michael Osodi (248367)', '2025-12-04 10:26:00', 8, 'admin'),
(172, 'approve_course_registration', 'Approved course registration for student ID: 8', '2025-12-05 09:56:53', 8, 'admin'),
(173, 'add_course', 'Added new course: COM 211 - Programming Language using Java II', '2025-12-05 16:29:59', 8, 'admin'),
(174, 'add_course', 'Added new course: COM 212 - Introduction to systems Programming', '2025-12-05 16:35:55', 8, 'admin'),
(175, 'add_course', 'Added new course: COM 213 - Unified Modelling Language (UML)', '2025-12-05 16:38:02', 8, 'admin'),
(176, 'add_course', 'Added new course: COM 214 - Computer Systems Troubleshooting', '2025-12-05 16:40:08', 8, 'admin'),
(177, 'add_course', 'Added new course: COM 215 - Computer Application Packages II', '2025-12-05 16:43:00', 8, 'admin'),
(178, 'add_course', 'Added new course: COM 216 - Statistics for Computing II', '2025-12-05 16:46:50', 8, 'admin'),
(179, 'add_course', 'Added new course: SIW 219 - SIWES', '2025-12-05 16:48:31', 8, 'admin'),
(180, 'add_course', 'Added new course: EED 216 - Practice of Entrepreneurship', '2025-12-05 16:51:21', 8, 'admin'),
(181, 'add_course', 'Added new course: COM 227 - Project', '2025-12-05 16:53:50', 8, 'admin'),
(182, 'add_course', 'Added new course: COM 221 - Basic Computer Networking', '2025-12-05 16:58:40', 8, 'admin'),
(183, 'add_course', 'Added new course: COM 222 - Seminar on Computer and Society', '2025-12-05 17:03:20', 8, 'admin'),
(184, 'add_course', 'Added new course: COM 223 - Basic Hardware Maintenance', '2025-12-05 17:05:30', 8, 'admin'),
(185, 'add_course', 'Added new course: COM 224 - Management Information system', '2025-12-05 17:07:16', 8, 'admin'),
(186, 'add_course', 'Added new course: COM 225 - Web Technology', '2025-12-05 17:09:24', 8, 'admin'),
(187, 'add_course', 'Added new course: COM 226 - File Organisation and Management', '2025-12-05 17:11:53', 8, 'admin'),
(188, 'add_course', 'Added new course: GNS 204 - Communication in English II', '2025-12-05 17:14:43', 8, 'admin'),
(189, 'edit_course', 'Updated course: ENT 126 - Introduction to Entrepreneurship I', '2025-12-06 21:45:56', 8, 'admin'),
(190, 'add_course', 'Added new course: MAC 211 - Introduction to Media and Communication Theories', '2025-12-06 21:51:52', 8, 'admin'),
(191, 'add_course', 'Added new course: MAC 212 - Research Methods in Media and Communication', '2025-12-06 21:55:26', 8, 'admin'),
(192, 'student_updated', 'Updated student: Kcee Ethapemi (6)', '2025-12-06 22:50:45', 7, 'admin'),
(193, 'student_updated', 'Updated student: Kcee Ethapemi (6)', '2025-12-06 22:51:03', 7, 'admin'),
(194, 'student_updated', 'Updated student: Kcee Ethapemi (6)', '2025-12-06 22:51:19', 7, 'admin'),
(195, 'student_updated', 'Updated student: Kcee Ethapemi (6)', '2025-12-06 22:51:34', 7, 'admin'),
(196, 'decline_course_registration', 'Declined course registration for student ID: 9 - Reason: fee-not-paid', '2025-12-06 23:15:26', 7, 'admin'),
(197, 'approve_course_registration', 'Approved course registration for student ID: 9', '2025-12-06 23:26:20', 7, 'admin'),
(198, 'decline_course_registration', 'Declined course registration for student ID: 9 - Reason: fee-not-paid', '2025-12-06 23:32:10', 7, 'admin'),
(199, 'edit_course', 'Updated course: BAM 113 - Principles of Law', '2025-12-06 23:35:50', 7, 'admin'),
(200, 'add_course', 'Added new course: MAC 213 - Editing and fact checking', '2025-12-10 09:59:18', 7, 'admin'),
(201, 'add_course', 'Added new course: MAC 214 - Feature Writing', '2025-12-10 10:03:13', 7, 'admin'),
(202, 'add_course', 'Added new course: MAC 215 - Media Communication and Society', '2025-12-10 10:03:51', 7, 'admin'),
(203, 'add_course', 'Added new course: MAC 216 - Media and Communication Ethics', '2025-12-10 10:04:39', 7, 'admin'),
(204, 'add_course', 'Added new course: MAC 217 - Photography in Media and Communication', '2025-12-10 10:06:31', 7, 'admin'),
(205, 'add_course', 'Added new course: MAC 218 - Broadcast Production I', '2025-12-10 10:07:30', 7, 'admin'),
(206, 'add_course', 'Added new course: MAC 219 - Foundations of Film Production', '2025-12-10 10:08:01', 7, 'admin'),
(207, 'add_course', 'Added new course: ENT 216 - Introduction to Entrepreneurship II', '2025-12-10 10:08:35', 7, 'admin'),
(208, 'add_course', 'Added new course: MAC 100 - SIWES', '2025-12-10 10:12:09', 7, 'admin'),
(209, 'add_course', 'Added new course: GNS 202 - Communication in English II', '2025-12-10 10:13:05', 7, 'admin'),
(210, 'add_course', 'Added new course: GNS 222 - Economics', '2025-12-10 10:13:55', 7, 'admin'),
(211, 'add_course', 'Added new course: MAC 221 - Foundation of Child Rights Reporting and Advocacy', '2025-12-10 10:14:25', 7, 'admin'),
(212, 'add_course', 'Added new course: MAC 222 - Speech Communication', '2025-12-10 10:15:31', 7, 'admin'),
(213, 'add_course', 'Added new course: MAC 223 - Newspaper and Magazine Production', '2025-12-10 10:19:15', 7, 'admin'),
(214, 'add_course', 'Added new course: MAC 224 - Broadcast Production II', '2025-12-10 10:19:50', 7, 'admin'),
(215, 'add_course', 'Added new course: MAC 225 - Media and Communication Laws', '2025-12-10 10:20:28', 7, 'admin'),
(216, 'add_course', 'Added new course: MAC 226 - Investigative and Interpretative Reporting', '2025-12-10 10:21:37', 7, 'admin'),
(217, 'add_course', 'Added new course: MAC 227 - Media, Democracy and Governance', '2025-12-10 10:22:07', 7, 'admin'),
(218, 'add_course', 'Added new course: MAC 228 - Project', '2025-12-10 10:22:42', 7, 'admin'),
(219, 'add_course', 'Added new course: BFN 213 - Business Research Methods', '2025-12-10 10:24:14', 7, 'admin'),
(220, 'add_course', 'Added new course: BAM 212 - Business Statistics 1', '2025-12-10 10:25:10', 7, 'admin'),
(221, 'add_course', 'Added new course: ACC 213 - Auditing 1', '2025-12-10 10:25:44', 7, 'admin'),
(222, 'add_course', 'Added new course: ACC 212 - Cost Accounting 1', '2025-12-10 10:26:40', 7, 'admin'),
(223, 'add_course', 'Added new course: ACC 211 - Financial Accounting 1', '2025-12-10 10:27:19', 7, 'admin'),
(224, 'add_course', 'Added new course: BAM 216 - Practice of Entrepreneurship', '2025-12-10 10:28:25', 7, 'admin'),
(225, 'add_course', 'Added new course: BAM 224 - Company Law', '2025-12-10 10:29:28', 7, 'admin'),
(226, 'add_course', 'Added new course: BAM 222 - Business Statistics 2', '2025-12-10 10:30:30', 7, 'admin'),
(227, 'add_course', 'Added new course: BFN 211 - Business Finance', '2025-12-10 10:31:09', 7, 'admin'),
(228, 'add_course', 'Added new course: ACC 223 - Auditing 2', '2025-12-10 10:31:58', 7, 'admin'),
(229, 'add_course', 'Added new course: ACC 222 - Cost Accounting 2', '2025-12-10 10:32:39', 7, 'admin'),
(230, 'add_course', 'Added new course: ACC 224 - Taxation 2', '2025-12-10 10:33:30', 7, 'admin'),
(231, 'add_course', 'Added new course: ACC 221 - Financial Accounting 2', '2025-12-10 10:34:36', 7, 'admin'),
(232, 'add_course', 'Added new course: ACC 225 - Public Sector Accounting', '2025-12-10 10:35:19', 7, 'admin'),
(233, 'add_course', 'Added new course: ACC 229 - Project', '2025-12-10 10:36:06', 7, 'admin'),
(234, 'add_course', 'Added new course: BAM 121 - Introduction to Business 2', '2025-12-10 10:38:33', 7, 'admin'),
(235, 'add_course', 'Added new course: BAM 114 - Principles of Economics 1', '2025-12-10 10:51:27', 7, 'admin'),
(236, 'add_course', 'Added new course: ACC111 - Principles of Accounts 1', '2025-12-10 10:52:20', 7, 'admin'),
(237, 'add_course', 'Added new course: BAM 115 - Principles of Marketing', '2025-12-10 10:53:00', 7, 'admin'),
(238, 'add_course', 'Added new course: BAM117 - Principles of Purchasing', '2025-12-10 10:53:57', 7, 'admin'),
(239, 'add_course', 'Added new course: BAM 124 - Principles of Economics 2', '2025-12-10 10:56:27', 7, 'admin'),
(240, 'add_course', 'Added new course: BAM 125 - Information Technology 1 (Data Processing)', '2025-12-10 10:57:23', 7, 'admin'),
(241, 'add_course', 'Added new course: BAM 123 - Introduction to Social Psychology', '2025-12-10 10:59:03', 7, 'admin'),
(242, 'add_course', 'Added new course: OTM 112 - Technical English I', '2025-12-10 10:59:50', 7, 'admin'),
(243, 'add_course', 'Added new course: GNS 131 - Citizenship Education 2', '2025-12-10 11:00:29', 7, 'admin'),
(244, 'add_course', 'Added new course: BAM 213 - Office Management', '2025-12-10 11:02:21', 7, 'admin'),
(245, 'add_course', 'Added new course: BAM 215 - Information Technology 2', '2025-12-10 11:03:33', 7, 'admin'),
(246, 'add_course', 'Added new course: OTM 222 - Technical English II', '2025-12-10 11:06:42', 7, 'admin'),
(247, 'add_course', 'Added new course: BAM 223 - Elements of Production Management', '2025-12-15 13:39:24', 7, 'admin'),
(248, 'add_course', 'Added new course: BAM 225 - Project', '2025-12-15 13:40:43', 7, 'admin'),
(249, 'edit_course', 'Updated course: BAM 225 - Project', '2025-12-15 14:18:59', 7, 'admin'),
(250, 'edit_course', 'Updated course: BAM 223 - Elements of Production Management', '2025-12-15 14:20:09', 7, 'admin'),
(251, 'edit_course', 'Updated course: OTM 222 - Technical English II', '2025-12-15 14:23:52', 7, 'admin'),
(252, 'edit_course', 'Updated course: BAM 215 - Information Technology 2', '2025-12-15 14:30:11', 7, 'admin'),
(253, 'edit_lecturer', 'Updated lecturer: Mr Ozenua Micheal (ozenua@gmail.com)', '2025-12-15 14:30:56', 7, 'admin'),
(254, 'edit_course', 'Updated course: ACC 211 - Financial Accounting 1', '2025-12-15 14:33:21', 7, 'admin'),
(255, 'edit_course', 'Updated course: ACC 212 - Cost Accounting 1', '2025-12-15 14:35:23', 7, 'admin'),
(256, 'edit_course', 'Updated course: ACC 213 - Auditing 1', '2025-12-15 14:37:07', 7, 'admin'),
(257, 'edit_course', 'Updated course: ACC 221 - Financial Accounting 2', '2025-12-15 14:38:43', 7, 'admin'),
(258, 'edit_course', 'Updated course: ACC 222 - Cost Accounting 2', '2025-12-15 14:40:13', 7, 'admin'),
(259, 'edit_course', 'Updated course: ACC 223 - Auditing 2', '2025-12-15 14:46:44', 7, 'admin'),
(260, 'edit_course', 'Updated course: ACC 224 - Taxation 2', '2025-12-15 14:48:46', 7, 'admin'),
(261, 'edit_course', 'Updated course: ACC 225 - Public Sector Accounting', '2025-12-15 14:51:50', 7, 'admin'),
(262, 'edit_course', 'Updated course: ACC 229 - Project', '2025-12-15 14:53:01', 7, 'admin'),
(263, 'edit_course', 'Updated course: ACC111 - Principles of Accounts 1', '2025-12-15 14:54:38', 7, 'admin'),
(264, 'edit_course', 'Updated course: BAM 114 - Principles of Economics 1', '2025-12-15 14:56:59', 7, 'admin'),
(265, 'edit_course', 'Updated course: BAM 115 - Principles of Marketing', '2025-12-15 14:58:00', 7, 'admin'),
(266, 'edit_course', 'Updated course: BAM 121 - Introduction to Business 2', '2025-12-15 14:59:11', 7, 'admin'),
(267, 'edit_course', 'Updated course: BAM 123 - Introduction to Social Psychology', '2025-12-15 15:00:20', 7, 'admin'),
(268, 'edit_course', 'Updated course: BAM 124 - Principles of Economics 2', '2025-12-15 15:02:20', 7, 'admin'),
(269, 'edit_course', 'Updated course: BAM 125 - Information Technology 1 (Data Processing)', '2025-12-15 15:03:20', 7, 'admin'),
(270, 'edit_course', 'Updated course: BAM 212 - Business Statistics 1', '2025-12-15 15:04:25', 7, 'admin'),
(271, 'edit_course', 'Updated course: BAM 213 - Office Management', '2025-12-15 15:05:34', 7, 'admin'),
(272, 'edit_course', 'Updated course: BAM 216 - Practice of Entrepreneurship', '2025-12-15 15:06:56', 7, 'admin'),
(273, 'edit_course', 'Updated course: BAM 222 - Business Statistics 2', '2025-12-15 15:07:49', 7, 'admin'),
(274, 'edit_course', 'Updated course: BAM 224 - Company Law', '2025-12-15 15:08:52', 7, 'admin'),
(275, 'edit_course', 'Updated course: BAM117 - Principles of Purchasing', '2025-12-15 15:11:37', 7, 'admin'),
(276, 'edit_course', 'Updated course: BFN 211 - Business Finance', '2025-12-15 15:12:56', 7, 'admin'),
(277, 'edit_course', 'Updated course: BFN 213 - Business Research Methods', '2025-12-15 15:13:45', 7, 'admin'),
(278, 'edit_course', 'Updated course: COM 222 - Seminar on Computer and Society', '2025-12-15 15:14:46', 7, 'admin'),
(279, 'edit_course', 'Updated course: ENT 216 - Introduction to Entrepreneurship II', '2025-12-15 15:15:59', 7, 'admin'),
(280, 'edit_course', 'Updated course: GNS 131 - Citizenship Education 2', '2025-12-15 15:17:34', 7, 'admin'),
(281, 'edit_course', 'Updated course: GNS 202 - Communication in English II', '2025-12-15 15:18:27', 7, 'admin'),
(282, 'edit_course', 'Updated course: GNS 202 - Communication in English II', '2025-12-15 15:18:43', 7, 'admin'),
(283, 'edit_course', 'Updated course: GNS 222 - Economics', '2025-12-15 15:19:57', 7, 'admin'),
(284, 'edit_course', 'Updated course: MAC 100 - SIWES', '2025-12-15 15:20:54', 7, 'admin'),
(285, 'edit_course', 'Updated course: MAC 213 - Editing and fact checking', '2025-12-15 15:23:00', 7, 'admin'),
(286, 'edit_course', 'Updated course: MAC 214 - Feature Writing', '2025-12-15 15:24:09', 7, 'admin'),
(287, 'edit_course', 'Updated course: MAC 215 - Media Communication and Society', '2025-12-15 15:25:44', 7, 'admin'),
(288, 'edit_course', 'Updated course: MAC 216 - Media and Communication Ethics', '2025-12-15 15:26:30', 7, 'admin'),
(289, 'edit_course', 'Updated course: MAC 217 - Photography in Media and Communication', '2025-12-15 15:27:26', 7, 'admin'),
(290, 'edit_course', 'Updated course: MAC 218 - Broadcast Production I', '2025-12-15 15:28:26', 7, 'admin'),
(291, 'edit_course', 'Updated course: MAC 219 - Foundations of Film Production', '2025-12-15 15:29:28', 7, 'admin'),
(292, 'edit_course', 'Updated course: MAC 221 - Foundation of Child Rights Reporting and Advocacy', '2025-12-15 15:30:34', 7, 'admin'),
(293, 'edit_course', 'Updated course: MAC 222 - Speech Communication', '2025-12-15 15:31:30', 7, 'admin'),
(294, 'edit_course', 'Updated course: MAC 223 - Newspaper and Magazine Production', '2025-12-15 15:32:18', 7, 'admin'),
(295, 'edit_course', 'Updated course: MAC 224 - Broadcast Production II', '2025-12-15 15:33:07', 7, 'admin'),
(296, 'edit_course', 'Updated course: MAC 225 - Media and Communication Laws', '2025-12-15 15:33:55', 7, 'admin'),
(297, 'edit_course', 'Updated course: MAC 226 - Investigative and Interpretative Reporting', '2025-12-15 15:36:22', 7, 'admin'),
(298, 'edit_course', 'Updated course: MAC 227 - Media, Democracy and Governance', '2025-12-15 15:39:11', 7, 'admin'),
(299, 'edit_course', 'Updated course: MAC 228 - Project', '2025-12-15 15:41:39', 7, 'admin'),
(300, 'edit_course', 'Updated course: OTM 112 - Technical English I', '2025-12-15 15:43:01', 7, 'admin');

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
(7, 'Raymond', 'JohnCross', 'ray@gmail.com', '$2y$10$QSCoseTN54t/bjSFJlP8LeIgX04M5mvDQdubKinlhbTYE1MM030cW', '09055566419', '2025-11-27 13:19:13', 'yul.jpg'),
(8, 'Ukachukwu', 'Stella', 'stellaukas@gmail.com', '$2y$10$Wtjwp4XWZZuBSqor/9IT4OzcYsdtevQ4A.omcU0lU/iqIZ2tyDACG', '9055520202', '2025-12-03 04:17:05', 'admin.jpg'),
(9, 'Olisa', 'Callistus', 'cally@gmail.com', '$2y$10$rQJ2.xsaaTDcTrd9dVPGlu4.Xli2i3yj9BPJj1.VnaJA4pM2kNU3m', '8061234567', '2025-12-15 16:13:57', 'emekacallistus.jpeg');

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
(7, 59, 14, 'Final Year Project Proposal', 'Outline Chapters 1-5 of your final project. Max 2,000 words.', 60, '2025-11-30 12:01:00', '2023/2024', 'First', 1, '2025-12-03 21:53:35'),
(8, 59, 14, 'Best for End-to-End Generation', 'Uses AI to auto-format and redesign slides as you add content. Ensures high-quality design consistency with every element.\r\nGenerates presentations that are responsive and look great on any device. Focuses on speed and clean design.', 70, '2026-01-10 01:06:00', '2025/2026', 'First', 1, '2025-12-04 00:06:29'),
(9, 27, 12, 'The Target Market: University Students', 'Traditional study groups are often inefficient, difficult to organize across busy schedules, and lack accountability. Students are drowning in lecture recordings and readings but need quick, verified answers to specific, niche questions (e.g., &quot;What formula did the professor use for elasticity on slide 15?&quot;). They waste time searching through hours of content or waiting for a T.A. response.', 47, '2025-12-27 11:00:00', '2024/2025', 'First', 1, '2025-12-05 10:01:07'),
(10, 74, 14, 'What is Computer Networking?', '1. Explain in Detail everything you know about computer and computer networks.\r\n2. List and explain types of computer networks.', 20, '2026-01-10 23:52:00', '2024/2025', 'First', 1, '2025-12-06 22:52:55'),
(11, 32, 14, 'What is Law?', 'State &amp; Explain the constitution sources.', 20, '2026-01-10 00:37:00', '2024/2025', 'First', 1, '2025-12-06 23:37:10');

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
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `score_received` decimal(5,2) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `submission_status` varchar(50) NOT NULL DEFAULT 'Submitted',
  `graded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ass_subtbl`
--

INSERT INTO `ass_subtbl` (`sub_id`, `assignment_id`, `student_id`, `file_path`, `submitted_at`, `score_received`, `comments`, `submission_status`, `graded_at`) VALUES
(1, 8, 7, 'uploads/assignments/1764807089_7_8.docx', '2025-12-04 00:11:29', 65.00, NULL, 'Submitted', '2025-12-04 01:23:41'),
(2, 9, 8, 'uploads/assignments/1764929058_8_9.docx', '2025-12-05 10:04:18', 35.00, NULL, 'Submitted', '2025-12-05 11:04:49'),
(3, 11, 9, 'uploads/assignments/1765064700_9_11.pdf', '2025-12-06 23:45:00', 16.00, 'This is my assignment sir!', 'Submitted', '2025-12-07 00:47:17'),
(4, 11, 8, 'uploads/assignments/1765064791_8_11.pdf', '2025-12-06 23:46:31', 14.00, 'Yoooo!', 'Submitted', '2025-12-07 00:52:22');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coursetbl`
--

INSERT INTO `coursetbl` (`course_id`, `AdminID`, `lecturer_id`, `course_code`, `course_title`, `course_description`, `course_unit`, `department`, `level`, `semester`, `created_at`, `course_image`) VALUES
(10, 7, 16, 'GNS 111', 'Citizenship Education', 'This course provides a foundational understanding of the principles of Citizenship Education and its role in nation-building. The primary focus is on the student&amp;#039;s relationship with the state and society, emphasizing constitutional literacy, civic responsibilities, and the mechanics of government.\r\n\r\nThe course aims to equip students with the knowledge and critical thinking skills necessary to become informed, disciplined, and active participants in the democratic process.', 2, 'Mass Communication', 'ND 1', 'First', '2025-12-02 12:52:55', 'assets/img/courses/GNS_111.jpg'),
(11, 7, 13, 'GNS 101', 'Use of English', 'This course is designed to develop and refine the core language skills—Listening, Speaking, Reading, and Writing—with a strong emphasis on Academic English. The instruction is often task-based and focuses on practical application, aiming for fluency, accuracy, and appropriate use of language in diverse contexts.', 2, 'Mass Communication', 'ND 1', 'First', '2025-12-02 13:09:08', 'assets/img/courses/GNS_101.jpg'),
(12, 7, 16, 'MAC 111', 'Media Writing and Style I', 'This introductory course focuses on developing the essential skills for professional media communication. It emphasizes the concept of newsworthiness, the inverted pyramid structure, and the mastery of journalistic style (often based on AP Style or a locally adopted standard). The course ensures students understand how to gather information, structure basic news stories, and maintain objectivity and ethical standards in their writing.', 3, 'Mass Communication', 'ND 1', 'First', '2025-12-02 13:11:35', 'assets/img/courses/MAC_111.jpg'),
(13, 7, 13, 'MAC 112', 'Foreign Languages', 'This course provides students with a fundamental introduction to a selected foreign language, focusing on practical communication skills relevant to media professionals. The primary goal is to foster cultural competence and give future communicators a basic tool for working in multinational or multicultural settings, where proficiency in a second language can be a significant professional advantage.', 2, 'Mass Communication', 'ND 1', 'First', '2025-12-02 15:17:30', 'assets/img/courses/MAC_112.jpg'),
(14, 7, 17, 'MAC 113', 'Computer Application for Media and Communication', 'This course provides hands-on training in using various software packages and digital tools essential for contemporary media practice. It covers everything from basic computer maintenance and operating systems to specialized applications for word processing, data handling, internet research, basic design, and multimedia content creation. The emphasis is on using these tools efficiently and ethically to gather, process, and disseminate information.', 3, 'Mass Communication', 'ND 1', 'First', '2025-12-02 15:52:04', 'assets/img/courses/MAC_113.jpg'),
(15, 7, 18, 'MAC 114', 'Foundation of Media and Communication', 'This course serves as a comprehensive overview of the field of media and communication. It explores the evolution of human communication from traditional to digital forms, examines various media institutions (e.g., print, broadcast, digital), and analyzes their impact on culture, politics, and the economy. The focus is on understanding the processes, channels, and effects of communication at intrapersonal, interpersonal, and mass levels.', 2, 'Mass Communication', 'ND 1', 'First', '2025-12-02 15:55:07', 'assets/img/courses/MAC_114.jpg'),
(16, 7, 11, 'MAC 115', 'Newswriting and Reporting I', 'This practical course provides intensive training in the core mechanics of journalism. Students will learn the essential skills of reporting—gathering information through interviews, observation, and document analysis—and newswriting—structuring that information clearly and concisely. The course heavily emphasizes the principles of accuracy, fairness, and objectivity, and the application of journalistic style rules to produce professional, publishable hard news stories.', 3, 'Mass Communication', 'ND 1', 'First', '2025-12-02 21:18:33', 'assets/img/courses/MAC_115.jpg'),
(17, 7, 31, 'MAC 116', 'Fundamentals of Broadcasting', 'This course offers a comprehensive introduction to the world of broadcasting. It covers the historical development of both radio and television, the technological processes used for transmitting signals (analog and digital), the organizational structure of broadcast stations, and the regulatory and ethical framework governing the airwaves. The focus is on understanding broadcasting as a business, a cultural force, and a vital platform for news, education, and entertainment.', 3, 'Mass Communication', 'ND 1', 'First', '2025-12-02 21:29:58', 'assets/img/courses/MAC_116.png'),
(18, 7, 10, 'MAC 117', 'Principles of Advertising', 'This course explores the conceptual and practical aspects of advertising, treating it as both a business strategy and a creative art. It covers the historical evolution of advertising, the structure of the industry (agencies, advertisers, media), the principles of consumer behavior, and the steps involved in planning, creating, and evaluating effective advertising campaigns. The course also addresses the economic, ethical, and social responsibilities associated with the advertising profession.', 3, 'Mass Communication', 'ND 1', 'First', '2025-12-02 21:32:22', 'assets/img/courses/MAC_117.jpg'),
(19, 7, 29, 'GNS 102', 'Communication in English I', 'This course aims to enhance students&amp;amp;amp;#039; proficiency in English language usage, with a specific focus on developing academic literacy and effective communication skills. It provides intensive instruction in the fundamental rules of grammar, mechanics of writing, comprehension, and vocabulary development necessary for success in all other disciplines. The course also introduces essential study skills required for higher education.', 2, 'Mass Communication', 'ND 1', 'Second', '2025-12-02 21:38:16', 'assets/img/courses/GNS_102.jpg'),
(20, 7, 16, 'GNS 121', 'Citizenship Education', 'This course builds upon the foundational knowledge of constitutional law and civic duties by focusing on the practical responsibilities of a citizen within a developing democracy. The course delves into current national challenges, the importance of participation in governance, public morality, community development, and the role of the individual in ensuring political stability and national unity.', 2, 'Mass Communication', 'ND 1', 'Second', '2025-12-02 21:44:40', 'assets/img/courses/GNS_121.png'),
(21, 7, 19, 'MAC 121', 'Media Writing and style II', 'This course transitions students from writing basic, objective news reports to generating varied, specialized, and persuasive media content. The primary focus is on developing writing skills for soft news (features), interpretive reporting, and corporate communication. Students will learn advanced techniques for structuring long-form narratives, crafting effective public relations materials, and developing a distinctive, yet professional, voice.', 2, 'Mass Communication', 'ND 1', 'Second', '2025-12-02 21:50:07', 'assets/img/courses/MAC_121.jpg'),
(22, 7, 9, 'MAC 122', 'Indigenous Communication System', 'This course is dedicated to the study of traditional, endogenous, and culture-specific methods of communication used by various ethnic and local groups. It examines the unique channels, codes, and technologies (often oral, visual, or symbolic) that serve the communication needs of indigenous societies. The course analyzes how these systems function for purposes such as governance, education, social mobilization, and cultural preservation, highlighting their relevance in contemporary development communication.', 2, 'Mass Communication', 'ND 1', 'Second', '2025-12-02 22:07:08', 'assets/img/courses/MAC_122.jpg'),
(23, 7, 37, 'MAC 123', 'Digital Communication', 'This course explores the fundamental principles, technologies, and applications of digital communication. It moves beyond traditional mass media to focus on the Internet, social media platforms, mobile technology, and multimedia. The course analyzes the unique characteristics of digital communication—interactivity, speed, non-linearity, and convergence—and their impact on journalism, public relations, advertising, and society at large. Emphasis is placed on digital content strategy, audience engagement, and data analytics.', 3, 'Mass Communication', 'ND 1', 'Second', '2025-12-03 03:04:02', 'assets/img/courses/MAC_123.jpg'),
(24, 7, 10, 'MAC 124', 'Graphics design for Media and Communication', 'This practical course introduces the fundamental principles of graphic design and the use of industry-standard software to create visually compelling content. It covers the technical skills necessary for image editing, layout design, typography, and color theory, specifically applied to journalistic, advertising, and public relations materials. The goal is to enable students to design effective, audience-appropriate visuals that enhance written messages and communicate information clearly.', 3, 'Mass Communication', 'ND 1', 'Second', '2025-12-03 03:06:14', 'assets/img/courses/MAC_124.jpg'),
(25, 7, 19, 'MAC 125', 'Newswriting and Reporting II', 'This advanced course builds on fundamental news skills by focusing on interpretive, investigative, and specialized reporting. Students learn how to cover complex beats, conduct in-depth interviews, analyze documents, and structure longer narratives. The emphasis is on developing a critical perspective, utilizing multiple sources, and presenting information in compelling formats, including narrative features and basic data-driven stories. The course also includes training on court, legislative, and public affairs reporting.', 3, 'Mass Communication', 'ND 1', 'Second', '2025-12-03 03:08:50', 'assets/img/courses/MAC_125.png'),
(26, 7, 11, 'MAC 126', 'Principles of Public Relations', 'This course provides a comprehensive overview of Public Relations as a management function and a social science. It explores the history, theories, ethical framework, and essential practices involved in developing and maintaining mutually beneficial relationships between an organization and its key stakeholders (employees, consumers, government, community, media, etc.). The course emphasizes strategic planning, effective communication tools, and the crucial role of PR in achieving organizational goals.', 3, 'Mass Communication', 'ND 1', 'Second', '2025-12-03 03:11:00', 'assets/img/courses/MAC_126.jpg'),
(27, 7, 14, 'ENT 126', 'Introduction to Entrepreneurship I', 'This introductory course provides a comprehensive overview of the principles and practices of entrepreneurship. It covers the essential qualities and characteristics of successful entrepreneurs, the process of idea generation and opportunity recognition, and the critical first steps required to transition a business concept into a viable commercial enterprise. The course emphasizes innovation, risk management, and the socio-economic importance of small and medium-sized enterprises (SMEs).', 3, 'Mass Communication', 'ND 1', 'Second', '2025-12-03 03:13:00', 'assets/img/courses/ENT_126.jpg'),
(28, 7, 8, 'GNS 201', 'Use of English I', 'This course moves beyond basic grammar and comprehension to focus on the critical application of language skills. It emphasizes developing students&amp;#039; ability to read complex academic texts, engage in logical reasoning, and produce coherent, well-argued, and correctly documented academic essays, term papers, and reports. The course ensures students are prepared to handle the communication demands of their final year projects and professional careers.', 2, 'Mass Communication', 'ND 2', 'First', '2025-12-03 03:22:53', 'assets/img/courses/GNS_201.jpg'),
(29, 7, 23, 'OTM 101-102', 'Technical English 1', 'This course is designed to equip students with the advanced communication skills required for the modern office environment, with a heavy emphasis on accuracy, clarity, and professionalism in technical and business writing. It builds upon general English proficiency by introducing the specific linguistic, stylistic, and formatting demands of office management, administration, and technical fields. The course ensures students can effectively handle the documentation and communication tasks central to professional roles.', 4, 'Accountancy', 'ND 1', 'First', '2025-12-03 05:51:14', 'assets/img/courses/OTM_101-102.jpg'),
(30, 7, 24, 'BFN 111', 'Elements of Banking 1', 'This introductory course is designed to provide students with a foundational understanding of the principles and practices of banking. It covers the historical evolution of banking, the structure of the financial system, the different types of banks, and the core functions they perform in a modern economy. The course emphasizes the basic concepts related to money, credit, central banking, and the relationship between banks and their customers.', 2, 'Accountancy', 'ND 1', 'First', '2025-12-03 05:54:43', 'assets/img/courses/BFN_111.png'),
(31, 7, 28, 'BAM 112', 'Business Mathematics 1', 'This course introduces fundamental mathematical techniques and their specific application in a business context. The focus is on arithmetic and algebraic methods used in everyday commercial transactions, financial calculations, and basic statistical analysis. The goal is to develop students&#039; analytical and problem-solving skills necessary for making sound business decisions and interpreting financial data.', 3, 'Accountancy', 'ND 1', 'First', '2025-12-03 05:58:09', 'assets/img/courses/BAM_112.jpg'),
(32, 8, 14, 'BAM 113', 'Principles of Law', 'This course introduces students to the nature, sources, and general principles of law, with a primary focus on areas most relevant to the business environment. It covers foundational legal concepts, the structure of the judicial system, and specific aspects of commercial law, such as the law of contracts, agency, and basic forms of business organization. The emphasis is on understanding how legal principles affect daily business decisions.', 3, 'Accountancy', 'ND 1', 'First', '2025-12-03 06:00:17', 'assets/img/courses/BAM_113.png'),
(33, 8, 23, 'BAM 211', 'Principles of Management 1', 'This course introduces the basic concepts of management, focusing on the historical development of management thought and the four classical functions of management: Planning, Organizing, Leading, and Controlling (POLC). It examines the roles managers play at different organizational levels, the external factors that influence decision-making, and the ethical responsibilities inherent in managing resources and people. The goal is to equip students with the conceptual framework needed to understand how organizations achieve their goals efficiently and effectively.', 3, 'Accountancy', 'ND 1', 'First', '2025-12-03 06:03:01', 'assets/img/courses/BAM_211.png'),
(34, 8, 16, 'BFN 112', 'Principles of Economics 1', 'This course introduces students to the basic principles of economic theory, focusing on the behavior of individual economic units. It explores the central concept of scarcity and the resulting need for choice, analyzing how resources are allocated through market mechanisms. Key topics include demand and supply analysis, consumer behavior, production theory, costs, revenue, and different market structures. The course provides the analytical framework for understanding prices, resource allocation, and market efficiency.', 3, 'Accountancy', 'ND 1', 'First', '2025-12-03 06:05:07', 'assets/img/courses/BFN_112.jpg'),
(35, 8, 15, 'ACC 111', 'Principles of Accounts 1', 'This course provides a comprehensive introduction to the theory and practice of double-entry bookkeeping and the preparation of basic financial statements for sole proprietorships. It covers the accounting equation, the classification of assets, liabilities, and equity, the journaling and posting process, and the preparation of ledgers, trial balances, and final accounts (Statement of Profit or Loss and Statement of Financial Position). The emphasis is on accuracy, adherence to generally accepted accounting principles (GAAP), and meticulous record-keeping.', 4, 'Accountancy', 'ND 1', 'First', '2025-12-03 08:17:47', 'assets/img/courses/ACC_111.jpg'),
(36, 8, 27, 'BNF 116', 'Information Communications Technology 1', 'This course provides a comprehensive introduction to the foundational concepts of Information and Communications Technology (ICT). It focuses on the components of a computer system, the essential role of the Internet, and practical proficiency in common business productivity software. The primary goal is to equip students with the digital literacy and practical skills necessary to utilize technology effectively for communication, data processing, and decision-making within a modern business or financial environment.', 4, 'Accountancy', 'ND 1', 'First', '2025-12-03 08:19:37', 'assets/img/courses/BNF_116.png'),
(37, 8, 23, 'OTM 201-202', 'Technical English 2', 'This course focuses on developing mastery in the application of English for specialized administrative and technical communication tasks. It covers the principles of formal report writing, including structuring, research methods, data presentation, and proper referencing. It also emphasizes advanced oral communication skills, such as presentation delivery and effective participation in professional discussions. The goal is to polish students into competent communicators capable of handling all documentation and presentation requirements at a management level.', 4, 'Accountancy', 'ND 1', 'Second', '2025-12-03 08:21:53', 'assets/img/courses/OTM_201-202.jpg'),
(38, 8, 9, 'BAM 126', 'Introduction to Entrepreneurship', 'This course provides a comprehensive overview of the principles and practices of entrepreneurship. It explores the role of the entrepreneur in economic development and covers the entire process of transitioning a business idea from conception to launch. The course emphasizes opportunity recognition, creativity, innovation, and the practical steps required to assess viability, secure resources, and write a preliminary business plan. The objective is to equip students with the mindset and tools to move from being job seekers to job creators.', 3, 'Accountancy', 'ND 1', 'Second', '2025-12-03 08:24:36', 'assets/img/courses/BAM_126.jpg'),
(39, 8, 15, 'BAM 122', 'Business Mathematics 2', 'This course focuses on applying advanced mathematical tools—specifically calculus, linear algebra, and complex financial analysis—to solve intricate business problems. It covers topics crucial for areas like optimization, resource allocation, and advanced capital budgeting. The goal is to move students from basic calculation to sophisticated analytical modeling needed for managerial decision-making in finance, operations, and economics.', 3, 'Accountancy', 'ND 1', 'Second', '2025-12-03 08:26:58', 'assets/img/courses/BAM_122.jpg'),
(40, 8, 6, 'BFN 121', 'Elements of Banking 2', 'This course provides an in-depth exploration of the day-to-day operations and strategic functions of commercial banks and the crucial processes of credit creation and management. Key areas include advanced payment systems, international banking operations, managing risks associated with lending, and understanding the legal and ethical obligations of bankers. The course aims to equip students with the practical knowledge needed to analyze banking services and understand their economic impact.', 2, 'Accountancy', 'ND 1', 'Second', '2025-12-03 08:30:38', 'assets/img/courses/BFN_121.jpg'),
(41, 8, 24, 'BAM 214', 'Business Law', 'This course offers a comprehensive study of the legal framework that regulates commercial activities and modern business enterprises. It focuses heavily on the Law of Contract (including complex issues like remedies and discharge), the Law of Agency, the legal operation of different Business Organizations (especially company law), and relevant aspects of Commercial Torts. The primary objective is to equip students with the legal competence to recognize and mitigate legal risks in management and operational functions.', 3, 'Accountancy', 'ND 1', 'Second', '2025-12-03 08:34:26', 'assets/img/courses/BAM_214.jpg'),
(42, 8, 16, 'BAM 221', 'Principles of Management 2', 'This course provides an in-depth study of the functions of management related to mobilizing and motivating human and material resources to achieve organizational goals. It emphasizes leadership, motivation, communication, and control systems. Students will learn practical techniques for managing people, resolving conflicts, evaluating performance, and implementing corrective measures, all within the framework of modern organizational theories.', 3, 'Accountancy', 'ND 1', 'Second', '2025-12-03 08:36:18', 'assets/img/courses/BAM_221.jpg'),
(43, 8, 9, 'BFN 122', 'Principles of Economics 2', 'This course introduces the fundamental concepts, theories, and models used to analyze the performance, structure, and behavior of the entire national economy. It covers key macroeconomic indicators like Gross Domestic Product (GDP), inflation, unemployment, and economic growth. The course also examines the major tools governments and central banks use to influence the economy: Fiscal Policy and Monetary Policy. The goal is to provide students with the ability to understand and evaluate national economic issues and policies.', 3, 'Accountancy', 'ND 1', 'Second', '2025-12-03 08:45:26', 'assets/img/courses/BFN_122.jpg'),
(44, 8, 8, 'ACC 121', 'Principles of Accounts 2', 'This course provides students with an in-depth understanding of specialized financial accounting applications and the preparation of financial statements for business entities beyond the simple sole trader. The focus shifts to Partnership Accounting, Manufacturing Accounts, and the unique financial reporting requirements of Non-Profit Organizations. It also introduces the crucial aspects of accounting for basic assets and internal controls.', 4, 'Accountancy', 'ND 1', 'Second', '2025-12-03 08:49:37', 'assets/img/courses/ACC_121.jpg'),
(45, 8, 32, 'BNF 126', 'Information Communications Technology 2', 'This course delves into the sophisticated application of ICT, specifically how technology is leveraged to manage information, automate processes, and enhance decision-making in financial and commercial environments. It covers advanced database concepts, detailed use of spreadsheet functions for complex analysis, web development fundamentals, and critical issues related to e-commerce, IT security, and Management Information Systems (MIS).', 4, 'Accountancy', 'ND 1', 'Second', '2025-12-03 08:59:05', 'assets/img/courses/BNF_126.jpg'),
(46, 8, 27, 'ACC 214', 'Taxation 1', 'This course provides a comprehensive introduction to the fundamental concepts of taxation, covering the relevant tax laws, the administration of the tax system, and the practical application of calculating tax liabilities for individuals. The course focuses on understanding the various sources of taxable income, allowable deductions, reliefs, and the procedures for assessment and payment. The goal is to equip students with the ability to interpret tax legislation and perform basic tax computations accurately.', 3, 'Accountancy', 'ND 2', 'First', '2025-12-03 09:02:59', 'assets/img/courses/ACC_214.jpg'),
(47, 8, 32, 'COM 111', 'Introduction to computing', 'This course provides a comprehensive foundation in the principles and applications of computer science and information technology. It covers the basic components of a computer system, the fundamental role of software and operating systems, the structure of the internet, and essential concepts of programming and data representation. The course emphasizes both the theoretical concepts of computing and the practical skills needed for digital literacy and problem-solving in a technological environment.', 3, 'Computer Science', 'ND 1', 'First', '2025-12-03 09:06:39', 'assets/img/courses/COM_111.jpg'),
(48, 8, 36, 'COM 112', 'Introduction to Digital Electronics', 'This course introduces the fundamental principles, theories, and components of digital electronic circuits. It covers the essential concepts of digital logic, including number systems, Boolean algebra, logic gates, and their application in designing combinational and sequential circuits. The goal is to provide students with the ability to analyze, design, and implement basic digital systems, which form the building blocks of microprocessors, memory, and all other digital devices.', 3, 'Computer Science', 'ND 1', 'First', '2025-12-03 09:08:50', 'assets/img/courses/COM_112.png'),
(49, 8, 32, 'COM 113', 'Introduction to Programming', 'This course provides a comprehensive introduction to the art and science of computer programming. It focuses on developing algorithmic thinking and problem-solving skills necessary to translate real-world problems into solutions that a computer can execute. Students learn the basic syntax, structure, and control flow concepts of a specific high-level programming language (often Python, C++, or Java). Emphasis is placed on using fundamental programming constructs and developing well-structured, logical programs.', 4, 'Computer Science', 'ND 1', 'First', '2025-12-03 09:12:05', 'assets/img/courses/COM_113.png'),
(50, 8, 34, 'COM 114', 'Statistics for Computing 1', 'This introductory statistics course focuses on descriptive statistics and basic probability theory. It equips students with the ability to collect, organize, present, and analyze numerical data, which is crucial for tasks like algorithm analysis, database query optimization, and initial data preparation in machine learning. The course emphasizes practical application using computational tools rather than complex mathematical proofs.', 2, 'Computer Science', 'ND 1', 'First', '2025-12-03 09:14:51', 'assets/img/courses/COM_114.png'),
(51, 8, 32, 'COM 115', 'Computer application packages I', 'This course provides students with practical skills in using essential application software packages for productivity, data analysis, and professional communication. The primary focus is on word processing and spreadsheet management. Students learn to create, format, edit, and print professional documents, manage and analyze data using formulas, and present information effectively through charts and presentations.', 3, 'Computer Science', 'ND 1', 'First', '2025-12-03 09:18:15', 'assets/img/courses/COM_115.jpg'),
(52, 8, 15, 'MTH 111', 'Logic and Linear Algebra', 'This course introduces the foundational concepts of Mathematical Logic necessary for deductive reasoning and computer science, and the tools of Linear Algebra essential for solving systems of equations and understanding vector spaces. The logic component covers propositional and predicate calculus, while the linear algebra component focuses on matrices, determinants, and their application in solving simultaneous linear equations.', 2, 'Computer Science', 'ND 1', 'First', '2025-12-03 09:20:13', 'assets/img/courses/MTH_111.jpg'),
(53, 8, 35, 'ICOM 121', 'Programming using C Language', 'This course provides a comprehensive and hands-on study of the C programming language. It moves beyond basic control structures to focus on C&amp;#039;s powerful features, including pointers, arrays, memory management, and data structures. The goal is to train students to write efficient, low-level, and robust programs, giving them a detailed understanding of how data and instructions are handled at the machine level, which is critical for system programming and software development.', 3, 'Computer Science', 'ND 1', 'Second', '2025-12-03 09:56:42', 'assets/img/courses/ICOM_121.png'),
(54, 8, 32, 'COM 122', 'Introduction to Internet', 'This course explores the fundamental principles behind the global network that defines modern communication and commerce. It covers the history and evolution of the Internet, the underlying networking protocols (TCP/IP), the functioning of the World Wide Web (WWW), and the structure of popular Internet services. Emphasis is placed on practical skills for effective searching, professional communication (email, video conferencing), cloud services, security awareness, and the basic concepts of creating web content.', 3, 'Computer Science', 'ND 1', 'Second', '2025-12-03 09:58:58', 'assets/img/courses/COM_122.jpg'),
(55, 8, 32, 'COM 123', 'Programming Language using Java I', 'This course introduces the fundamentals of Object-Oriented Programming (OOP) using the Java language. It covers basic Java syntax, data types, control structures, and essential features like classes, objects, methods, and inheritance. The emphasis is on developing a strong conceptual understanding of the object-oriented paradigm, writing clean, efficient, and well-structured Java code, and preparing students for advanced software development.', 2, 'Computer Science', 'ND 1', 'Second', '2025-12-03 10:13:53', 'assets/img/courses/COM_123.jpg'),
(56, 8, 34, 'COM 124', 'Data structure and Algorithms', 'This course provides a comprehensive study of the fundamental ways data can be organized and stored (Data Structures) and the efficient methods used to solve computational problems (Algorithms). The course emphasizes abstract data types (ADTs), implementation details, and the critical skill of algorithmic analysis (using Big O notation) to evaluate the time and space efficiency of solutions. Mastery of DSA is essential for designing high-performance software and passing technical interviews in the industry.', 3, 'Computer Science', 'ND 1', 'Second', '2025-12-03 10:15:51', 'assets/img/courses/COM_124.jpg'),
(57, 8, 34, 'COM 125', 'Introduction to Systems Analysis and Design', 'This course introduces the concepts, principles, and tools used by Systems Analysts to plan, develop, and manage organizational information systems. It provides a structured approach to solving business problems through technology, covering the entire System Development Life Cycle (SDLC), from initial requirements gathering (Analysis) to system creation (Design) and final implementation. Emphasis is placed on using various modeling techniques to document system requirements and logical design.', 3, 'Computer Science', 'ND 1', 'Second', '2025-12-03 10:17:55', 'assets/img/courses/COM_125.jpg'),
(58, 8, 36, 'COM 126', 'PC Upgrade &amp; Maintenance', 'This course focuses on the practical aspects of computer hardware, software, and networking troubleshooting. Students learn to identify, install, configure, and replace all major internal and external components of a personal computer. Key areas include safety procedures, component testing, operating system installation and configuration, data backup, and preventive maintenance. The goal is to develop competent technicians who can service and maintain computer systems professionally.', 3, 'Computer Science', 'ND 1', 'Second', '2025-12-03 10:19:55', 'assets/img/courses/COM_126.jpg'),
(59, 8, 14, 'GNS 128', 'Citizenship Education II', 'This course provides a detailed examination of the mechanisms of governance, political participation, and the critical issues facing the nation. It explores the role of essential state institutions, the importance of public opinion and the media, the mechanics of elections, and the factors that promote economic stability and national cohesion. The goal is to cultivate active and informed citizens capable of contributing positively to democratic development and social justice.', 2, 'Computer Science', 'ND 1', 'Second', '2025-12-03 10:22:38', 'assets/img/courses/GNS_128.jpg'),
(60, 8, 29, 'EED 126', 'Practice of Entrepreneurship', 'This course is designed to transition students from theoretical knowledge to practical execution in establishing a new venture. It focuses on the tactical planning, resource mobilization, and operational management required to launch a business. Key areas include detailed market research, business plan development, sourcing capital, managing small business operations, and navigating legal and ethical challenges. The ultimate goal is for students to develop a comprehensive, ready-to-implement business plan and understand the day-to-day realities of being an entrepreneur.', 2, 'Computer Science', 'ND 1', 'Second', '2025-12-03 10:26:21', 'assets/img/courses/EED_126.jpg'),
(61, 8, 35, 'GNS 228', 'Research Methods', 'This course provides a comprehensive introduction to the principles and procedures of academic and scientific research. It covers the entire research process, from formulating a problem and reviewing literature to selecting a methodology, collecting and analyzing data, and writing the final report. The primary goal is to empower students across all disciplines to undertake original research that is valid, reliable, and ethically conducted.', 2, 'Computer Science', 'ND 1', 'Second', '2025-12-03 10:28:00', 'assets/img/courses/GNS_228.jpg'),
(62, 8, 29, 'BAM 111', 'Introduction to Business 1', 'This course provides a comprehensive overview of the business environment, fundamental business functions, and the types of economic systems that govern commerce. It is designed to equip students with a broad understanding of how businesses are created, organized, and managed. Key areas covered include the concept of profit, different forms of business ownership, basic management functions, and the ethical responsibilities of business in society.', 3, 'Business Administration', 'ND 1', 'First', '2025-12-03 10:30:12', 'assets/img/courses/BAM_111.jpg'),
(64, 8, 6, 'BAM 116', 'Elements of Public Administration', 'This course introduces the fundamental concepts and analytical tools of economics, centered on the core problem of scarcity and the necessity of making choices. It provides a detailed study of market mechanisms, analyzing the behavior of consumers and producers. Key topics include demand and supply analysis, elasticity, consumer choice, production theory, costs, revenue, and the characteristics of different market structures. The course provides the analytical framework for understanding price determination and resource allocation in a market economy.', 3, 'Business Administration', 'ND 1', 'First', '2025-12-03 10:39:10', 'assets/img/courses/BAM_116.png'),
(65, 8, 32, 'COM 211', 'Programming Language using Java II', 'This course is a direct continuation of an introductory Java course (often COM 111 or COM 210). It deepens the student&#039;s understanding of Object-Oriented Programming (OOP) principles and introduces advanced language features, data structures, and application development concepts. The focus shifts from simply writing code to writing well-structured, efficient, and maintainable code.', 4, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:29:59', 'assets/img/courses/COM_211.jpg'),
(66, 8, 35, 'COM 212', 'Introduction to systems Programming', 'This course introduces students to the core principles of systems programming, focusing on how software interacts directly with hardware and the operating system. Emphasis is placed on low-level programming using C, understanding system calls, and managing essential resources such as memory, processes, and files. Students gain practical experience in writing efficient programs that operate close to the system level.\r\n\r\nKey concepts covered include program execution, memory allocation, file handling, process control, concurrency basics, and debugging techniques. By the end of the course, students will be able to write and optimize system-level programs, interact confidently with operating system interfaces, and apply systems programming concepts to real-world problem-solving.', 2, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:35:54', 'assets/img/courses/COM_212.jpg'),
(67, 8, 35, 'COM 213', 'Unified Modelling Language (UML)', 'This course provides students with a solid foundation in analyzing, visualizing, and documenting software systems using the Unified Modelling Language (UML). It focuses on the standard diagrams, tools, and techniques used in software engineering to represent system structure, behavior, and interactions.\r\n\r\nStudents learn how to create use case diagrams, class diagrams, sequence diagrams, activity diagrams, state machine diagrams, and other essential UML models. The course emphasizes practical application by guiding students through modeling real-world systems, defining system requirements, and transforming user needs into structured visual representations.\r\n\r\nBy the end of the course, students will be able to interpret and design UML diagrams confidently, apply modeling principles to various software development methodologies, and effectively communicate system designs within a development team.', 3, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:38:01', 'assets/img/courses/COM_213.png'),
(68, 8, 36, 'COM 214', 'Computer Systems Troubleshooting', 'This course equips students with the knowledge and practical skills needed to diagnose, repair, and maintain computer systems. It covers the fundamentals of hardware and software troubleshooting, including identifying faults, isolating problems, and applying systematic repair techniques. Students learn to handle common issues related to operating systems, storage devices, memory, power supply, peripherals, and network connectivity.\r\n\r\nThe course emphasizes hands-on practice with diagnostic tools, BIOS/UEFI configuration, driver management, system optimization, and preventive maintenance. Safety procedures, component testing, and replacement techniques are also highlighted to ensure proper handling of computer hardware.\r\n\r\nBy the end of the course, students will be capable of efficiently identifying system faults, performing repairs, restoring system functionality, and implementing best practices to prevent future failures.', 3, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:40:08', 'assets/img/courses/COM_214.jpg'),
(69, 8, 35, 'COM 215', 'Computer Application Packages II', 'This course builds on foundational computer application skills by introducing students to more advanced productivity tools used in business, education, and professional environments. It covers intermediate to advanced features of word processing, spreadsheets, databases, and presentation software.\r\n\r\nStudents gain hands-on experience creating structured documents, performing data analysis, designing simple databases, and developing effective presentations. By the end of the course, they will be able to apply these tools confidently to real-world tasks, improve productivity, and support decision-making processes.', 3, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:43:00', 'assets/img/courses/COM_215.jpg'),
(70, 8, 15, 'COM 216', 'Statistics for Computing II', 'This course extends students’ understanding of statistical concepts used in computing, focusing on data analysis, probability distributions, estimation, hypothesis testing, and regression techniques. It emphasizes practical application of statistical methods in solving computing-related problems.\r\n\r\nStudents learn how to analyze datasets, interpret statistical results, and apply statistical tools to support decision-making in software development, data management, and system evaluation. By the end of the course, they will be able to use statistical reasoning to model real-world scenarios and evaluate system performance effectively.', 2, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:46:50', 'assets/img/courses/COM_216.jpg'),
(71, 8, 10, 'SIW 219', 'SIWES', 'This course provides students with practical industry exposure through supervised work experience in relevant computing or ICT-related organizations. It allows students to apply the theories, skills, and concepts learned in the classroom to real workplace environments.\r\n\r\nDuring the training period, students gain hands-on experience with professional tools, real-world problem-solving, teamwork, and industry standards. By the end of the program, they develop stronger technical competence, workplace discipline, and a better understanding of career expectations in the computing field.', 4, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:48:31', 'assets/img/courses/SIW_219.jpg'),
(72, 8, 14, 'EED 216', 'Practice of Entrepreneurship', 'This course provides students with practical knowledge and skills required to start, manage, and sustain small or medium-scale enterprises. It focuses on idea generation, opportunity identification, business planning, and understanding the factors that influence entrepreneurial success.\r\n\r\nStudents are exposed to real-life case studies, feasibility analysis, basic financial planning, marketing strategies, and the challenges faced by entrepreneurs. By the end of the course, they will be able to develop viable business ideas, prepare simple business plans, and apply entrepreneurial skills in both personal and professional settings.', 2, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:51:21', 'assets/img/courses/EED_216.jpg'),
(73, 8, 32, 'COM 227', 'Project', 'This course allows students to apply the knowledge and skills acquired throughout their program to a real-world computing problem. It involves selecting a topic, conducting research or system analysis, designing a solution, and implementing a functional project under supervision.\r\n\r\nStudents gain hands-on experience in proposal writing, software development, documentation, testing, and project presentation. By the end of the course, they demonstrate their ability to work independently, solve practical problems, and deliver a complete, well-documented computing project.', 6, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:53:50', 'assets/img/courses/COM_227.jpg'),
(74, 8, 14, 'COM 221', 'Basic Computer Networking', 'This course provides an introduction to computer networking concepts, covering the fundamentals of network types, architectures, and protocols. Students learn about data communication, network devices, IP addressing, routing, and the basics of network security. Practical exercises equip students with skills to set up, manage, and troubleshoot simple networks, laying a foundation for more advanced networking studies.', 3, 'Computer Science', 'ND 2', 'Second', '2025-12-05 16:58:40', 'assets/img/courses/COM_221.jpg'),
(75, 8, 14, 'COM 222', 'Seminar on Computer and Society', 'This course examines the dynamic relationship between computers and society, highlighting how technology influences social structures, communication, and daily life. It introduces students to key issues such as digital privacy, cybersecurity, intellectual property, and the ethical use of information technology. By exploring real-world scenarios, students gain insight into the responsibilities of both users and developers in the digital age.\r\n\r\nThe course also emphasizes critical thinking and discussion, encouraging students to analyze the broader impact of computing on employment, education, and cultural interactions. Through case studies, seminars, and debates, learners develop the ability to evaluate technological developments and their societal implications, preparing them to make informed and responsible decisions in a technology-driven world.', 2, 'Computer Science', 'ND 2', 'Second', '2025-12-05 17:03:20', 'assets/img/courses/COM_222.jpg'),
(76, 8, 36, 'COM 223', 'Basic Hardware Maintenance', 'This course introduces students to the fundamentals of computer hardware and its maintenance. It covers the components of a computer system, including the motherboard, CPU, memory, storage devices, and peripheral devices, as well as their functions and interconnections. Students learn practical skills for assembling, disassembling, and troubleshooting computer hardware.\r\n\r\nEmphasis is placed on preventive maintenance, diagnosing common hardware problems, and applying appropriate repair techniques. The course equips students with the knowledge and hands-on experience needed to ensure the optimal performance and longevity of computer systems in both personal and professional settings.', 2, 'Computer Science', 'ND 2', 'Second', '2025-12-05 17:05:30', 'assets/img/courses/COM_223.jpg'),
(77, 8, 34, 'COM 224', 'Management Information system', 'This course introduces students to Management Information Systems (MIS) and their role in organizational decision-making. It covers the concepts, components, and functions of information systems, including data processing, storage, and retrieval, as well as the use of software and technologies to support business operations and management.\r\n\r\nStudents learn how MIS facilitates planning, control, and strategic decision-making within organizations. The course also explores system development life cycles, database management, and the ethical and security considerations of information systems, preparing students to effectively leverage technology for efficient organizational management.', 2, 'Computer Science', 'ND 2', 'Second', '2025-12-05 17:07:16', 'assets/img/courses/COM_224.png'),
(78, 8, 35, 'COM 225', 'Web Technology', 'This course provides an introduction to web technologies and the development of web-based applications. It covers the fundamentals of web design, including HTML, CSS, and JavaScript, as well as client-server architecture, web protocols, and web hosting. Students gain practical skills in creating interactive and user-friendly websites.\r\n\r\nThe course also explores the integration of multimedia, databases, and server-side scripting to build dynamic web applications. Emphasis is placed on best practices, web standards, and security considerations, equipping students with the knowledge to develop functional, secure, and professional web solutions.', 3, 'Computer Science', 'ND 2', 'Second', '2025-12-05 17:09:24', 'assets/img/courses/COM_225.jpg'),
(79, 8, 32, 'COM 226', 'File Organisation and Management', 'This course introduces the principles and techniques of file organization and management in computer systems. It covers different types of file structures, storage methods, indexing, and access methods, emphasizing efficient data storage, retrieval, and manipulation. Students learn how files are organized in both sequential and random-access systems.\r\n\r\nThe course also addresses file management strategies, including file naming, directory structures, and security considerations. Practical exercises equip students with the skills to design, implement, and manage files effectively, ensuring data integrity, accessibility, and optimal system performance.', 2, 'Computer Science', 'ND 2', 'Second', '2025-12-05 17:11:53', 'assets/img/courses/COM_226.jpg'),
(80, 8, 29, 'GNS 204', 'Communication in English II', 'This course focuses on developing advanced English communication skills for academic and professional contexts. It emphasizes reading comprehension, essay writing, report preparation, and effective oral communication, including presentations and discussions. Students learn strategies to express ideas clearly and coherently in both written and spoken English.\r\n\r\nThe course also covers critical thinking and analysis, helping students interpret texts, construct logical arguments, and engage in meaningful dialogue. Through practical exercises and assignments, learners enhance their proficiency, confidence, and ability to communicate effectively in diverse settings.', 2, 'Computer Science', 'ND 2', 'Second', '2025-12-05 17:14:43', 'assets/img/courses/GNS_204.jpg'),
(81, 8, 19, 'MAC 211', 'Introduction to Media and Communication Theories', 'This introductory course provides students with the foundational knowledge needed to critically analyze the relationship between mass media, society, and the individual. It systematically explores the core processes of communication, moving from a simple model of how messages are sent and received to complex theories on media influence. The primary goal is to equip students with the analytical frameworks to understand media&#039;s essential roles, such as providing information (surveillance), interpreting events (correlation), transmitting culture, and offering entertainment.\r\nBy studying these diverse theoretical perspectives—from the functionalist views of transmission to the critical analysis of cultural power—students develop a comprehensive understanding of media&#039;s multifaceted impact. The course enables them to move beyond simply consuming media to becoming informed, critical evaluators of media content, technology, and industry practices in today&#039;s digital landscape.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-06 21:51:52', 'assets/img/courses/MAC_211.jpg'),
(82, 8, 10, 'MAC 212', 'Research Methods in Media and Communication', 'This course aims to transition students from being consumers of media research to producers and critical evaluators of it. The key objectives are:\r\n\r\nUnderstanding the Research Process: To master the steps of the social research cycle, from defining a problem and reviewing literature to designing a study and drawing conclusions.\r\n\r\nMethodological Proficiency: To gain a working knowledge of both Quantitative and Qualitative research approaches and the ability to choose the appropriate method for a given research question.\r\n\r\nPractical Skills: To develop hands-on skills in executing primary research, including data collection, analysis, and report writing, necessary for academic papers and professional reports.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-06 21:55:25', 'assets/img/courses/MAC_212.jpg');
INSERT INTO `coursetbl` (`course_id`, `AdminID`, `lecturer_id`, `course_code`, `course_title`, `course_description`, `course_unit`, `department`, `level`, `semester`, `created_at`, `course_image`) VALUES
(83, 7, 6, 'MAC 213', 'Editing and fact checking', 'This course introduces students to the principles and techniques of editing and fact-checking in written and digital content. It covers grammar, style, clarity, consistency, accuracy, and verification of information to ensure high-quality communication.\r\n\r\nStudents also learn how to evaluate sources, detect errors or misinformation, and apply ethical standards in content review. The course equips learners with the skills to produce clear, accurate, and reliable content for professional, academic, and media contexts.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 09:59:18', 'assets/img/courses/MAC_213.jpg'),
(84, 7, 19, 'MAC 214', 'Feature Writing', 'This course introduces students to the principles and techniques of feature writing in journalism and media. It covers the structure, style, and content of feature articles, including human interest stories, profiles, investigative pieces, and specialized reporting.\r\n\r\nStudents learn how to research, interview, and craft compelling narratives that engage readers while maintaining accuracy and ethical standards. The course emphasizes creativity, clarity, and effective communication, equipping learners with the skills to produce professional-quality feature stories for various media platforms.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:03:13', 'assets/img/courses/MAC_214.jpg'),
(85, 7, 10, 'MAC 215', 'Media Communication and Society', 'This course examines the relationship between media, communication, and society. It explores how media influences public opinion, culture, social behavior, and political processes, as well as the role of communication in shaping societal values.\r\n\r\nStudents also study media theories, ethics, and the impact of traditional and digital media on communities. The course equips learners with critical thinking and analytical skills to understand, evaluate, and engage with media in a responsible and informed manner.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:03:51', 'assets/img/courses/MAC_215.jpg'),
(86, 7, 35, 'MAC 216', 'Media and Communication Ethics', 'This course explores the ethical principles and standards that guide media and communication practices. It covers topics such as professional responsibility, accuracy, fairness, objectivity, privacy, and the ethical challenges posed by digital and traditional media.\r\n\r\nStudents learn to analyze ethical dilemmas, make informed decisions, and apply ethical frameworks in reporting, content creation, and public communication. The course equips learners with the knowledge and skills to practice responsible, transparent, and socially accountable media and communication.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:04:39', 'assets/img/courses/MAC_216.jpg'),
(87, 7, 10, 'MAC 217', 'Photography in Media and Communication', 'This course introduces students to the principles and techniques of photography as applied in media and communication. It covers camera operation, composition, lighting, visual storytelling, and photo editing for print and digital platforms.\r\n\r\nStudents also learn how to use photography to enhance journalistic reporting, marketing, and multimedia content. The course emphasizes creativity, technical skills, and ethical considerations, equipping learners to produce compelling and professional photographic content for various media contexts.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:06:31', 'assets/img/courses/MAC_217.jpg'),
(88, 7, 27, 'MAC 218', 'Broadcast Production I', 'This course introduces students to the fundamentals of broadcast production for radio and television. It covers the production process, including planning, scripting, recording, editing, and post-production techniques.\r\n\r\nStudents also learn about the technical equipment, studio operations, and team coordination required for producing broadcast content. The course emphasizes creativity, technical proficiency, and effective communication, preparing learners to produce professional-quality audio and visual media for diverse audiences.', 3, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:07:30', 'assets/img/courses/MAC_218.jpg'),
(89, 7, 16, 'MAC 219', 'Foundations of Film Production', 'This course introduces students to the basic principles and techniques of film production. It covers the stages of filmmaking, including scriptwriting, pre-production planning, cinematography, sound recording, editing, and post-production.\r\n\r\nStudents also explore visual storytelling, narrative structure, and the creative and technical aspects of film-making. The course emphasizes practical skills, collaboration, and ethical considerations, equipping learners to produce short films and other visual media content professionally.', 3, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:08:01', 'assets/img/courses/MAC_219.jpg'),
(90, 7, 10, 'ENT 216', 'Introduction to Entrepreneurship II', 'This course builds on the foundations laid in Introduction to Entrepreneurship I, focusing on the practical aspects of developing and managing business ventures. It covers business planning, resource mobilization, marketing strategies, financial management, and risk assessment for new enterprises.\r\n\r\nStudents also explore innovation, problem-solving, and decision-making in entrepreneurial activities. The course emphasizes hands-on projects, case studies, and real-world applications to equip learners with the skills and confidence to launch and sustain successful business ventures.', 3, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:08:35', 'assets/img/courses/ENT_216.jpg'),
(91, 7, 10, 'MAC 100', 'SIWES', 'This course provides students with practical, hands-on experience in a real-world industrial or organizational setting. It is designed to bridge the gap between theoretical knowledge and professional practice, allowing students to apply the concepts learned in their academic programs.\r\n\r\nStudents are exposed to workplace operations, professional ethics, problem-solving, and industry-specific practices. The course emphasizes skill development, adaptability, and the ability to work effectively in a professional environment, preparing learners for future careers in their chosen fields.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:12:09', 'assets/img/courses/MAC_100.jpg'),
(92, 7, 29, 'GNS 202', 'Communication in English II', 'This course focuses on developing advanced English language skills for effective communication in academic and professional contexts. It emphasizes reading comprehension, essay writing, report preparation, and oral communication, including presentations and discussions.\r\n\r\nStudents also learn strategies for critical thinking, argument development, and effective interaction in various settings. Through practical exercises and assignments, the course enhances learners’ proficiency, confidence, and ability to communicate clearly and professionally.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:13:05', 'assets/img/courses/GNS_202.jpg'),
(93, 7, 26, 'GNS 222', 'Economics', 'This course provides an introduction to the principles and concepts of economics, focusing on both microeconomic and macroeconomic perspectives. It covers topics such as supply and demand, market structures, production and costs, national income, inflation, and unemployment.\r\n\r\nStudents also explore the role of government, businesses, and consumers in economic decision-making. The course equips learners with the analytical tools to understand economic behavior, evaluate policies, and make informed decisions in personal, business, and societal contexts.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:13:55', 'assets/img/courses/GNS_222.png'),
(94, 7, 32, 'MAC 221', 'Foundation of Child Rights Reporting and Advocacy', 'This course introduces students to the principles and practices of reporting and advocating for child rights. It covers topics such as international and national child rights frameworks, ethical reporting, and the role of media in protecting and promoting the welfare of children.\r\n\r\nStudents also learn strategies for effective advocacy, research, and storytelling that raise awareness on child-related issues. The course emphasizes accuracy, sensitivity, and social responsibility, equipping learners with the skills to responsibly report, educate, and advocate for the rights and well-being of children.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:14:25', 'assets/img/courses/MAC_221.jpg'),
(95, 7, 6, 'MAC 222', 'Speech Communication', 'This course explores the principles and practices of effective oral communication. It covers speech preparation, organization, delivery techniques, audience analysis, and the use of verbal and nonverbal cues to enhance clarity and impact.\r\n\r\nStudents also learn strategies for persuasive, informative, and ceremonial speaking, as well as methods for managing public speaking anxiety. The course emphasizes practical exercises, critical thinking, and feedback, equipping learners with the skills to communicate confidently and effectively in various personal, academic, and professional contexts.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:15:31', 'assets/img/courses/MAC_222.jpg'),
(96, 7, 26, 'MAC 223', 'Newspaper and Magazine Production', 'This course introduces students to the principles and practices of producing newspapers and magazines. It covers editorial planning, content creation, layout and design, printing processes, and distribution strategies for print and digital media.\r\n\r\nStudents also learn about audience analysis, writing for different sections, advertising integration, and ethical considerations in publishing. The course emphasizes practical skills, creativity, and professionalism, equipping learners to produce engaging and high-quality print and digital publications.', 3, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:19:15', 'assets/img/courses/MAC_223.jpg'),
(97, 7, 9, 'MAC 224', 'Broadcast Production II', 'This course builds on the concepts introduced in Broadcast Production I, focusing on advanced techniques in radio and television production. It covers multi-camera production, live broadcasting, sound design, post-production editing, and the integration of graphics and special effects.\r\n\r\nStudents also learn project management, team coordination, and quality control in broadcast production. The course emphasizes creativity, technical proficiency, and professionalism, preparing learners to produce polished, audience-ready broadcast content across various media platforms.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:19:50', 'assets/img/courses/MAC_224.jpg'),
(98, 7, 14, 'MAC 225', 'Media and Communication Laws', 'This course introduces students to the legal frameworks governing media and communication practice. It covers topics such as freedom of the press, defamation, copyright, intellectual property, privacy, censorship, and regulatory bodies in the media industry.\r\n\r\nStudents also learn about legal responsibilities, compliance, and ethical considerations in reporting, broadcasting, and digital media. The course equips learners with the knowledge to navigate legal issues, ensure lawful media practice, and uphold professional standards in communication.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:20:28', 'assets/img/courses/MAC_225.jpg'),
(99, 7, 35, 'MAC 226', 'Investigative and Interpretative Reporting', 'This course introduces students to the principles and techniques of investigative and interpretative journalism. It covers methods for uncovering, researching, and verifying news stories, as well as analyzing and interpreting complex information for public understanding.\r\n\r\nStudents also learn ethical considerations, source evaluation, data analysis, and effective storytelling strategies. The course emphasizes critical thinking, accuracy, and clarity, equipping learners to produce in-depth, well-researched, and insightful news reports that inform and engage audiences.', 3, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:21:37', 'assets/img/courses/MAC_226.jpg'),
(100, 7, 40, 'MAC 227', 'Media, Democracy and Governance', 'This course examines the relationship between media, democratic processes, and governance. It covers the role of media in promoting transparency, accountability, citizen participation, and informed decision-making in democratic societies.\r\n\r\nStudents also explore media policies, regulation, political communication, and the impact of digital media on governance. The course emphasizes critical analysis and practical understanding, equipping learners to engage responsibly with media as a tool for democratic development and good governance.', 2, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:22:07', 'assets/img/courses/MAC_227.jpg'),
(101, 7, 19, 'MAC 228', 'Project', 'This course provides students with the opportunity to apply theoretical knowledge and practical skills acquired in media and communication studies to an independent research or creative project. Students identify a topic or problem, conduct research, gather and analyze data, and present their findings under the supervision of a faculty member.\r\n\r\nEmphasis is placed on project planning, report writing, presentation, and critical evaluation. The course equips learners with analytical, problem-solving, and professional communication skills, preparing them for real-world media practice or further academic research.', 4, 'Mass Communication', 'ND 2', 'Second', '2025-12-10 10:22:42', 'assets/img/courses/MAC_228.jpg'),
(102, 7, 34, 'BFN 213', 'Business Research Methods', 'This course introduces students to the principles and techniques of conducting research in business and management. It covers topics such as research design, data collection methods, sampling techniques, data analysis, and interpretation of research findings.\r\n\r\nStudents also learn how to formulate research problems, develop hypotheses, and prepare research reports. The course emphasizes practical application, critical thinking, and ethical considerations, equipping learners with the skills to conduct effective and reliable business research.', 3, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:24:14', 'assets/img/courses/BFN_213.jpg'),
(103, 7, 29, 'BAM 212', 'Business Statistics 1', 'This course introduces students to the basic concepts and techniques of statistics as applied in business and management. It covers data collection, organization, presentation, and descriptive statistical measures such as mean, median, mode, variance, and standard deviation.\r\n\r\nStudents also learn the fundamentals of probability, correlation, and regression analysis. The course emphasizes practical applications of statistical methods to support business decision-making, enabling learners to interpret data and draw meaningful conclusions in real-world business contexts.', 3, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:25:10', 'assets/img/courses/BAM_212.jpg'),
(104, 7, 15, 'ACC 213', 'Auditing 1', 'This course introduces the principles and practices of auditing. It covers the objectives of auditing, types of audits, and the roles and responsibilities of auditors in examining financial records and internal control systems. Students learn the fundamentals of audit planning, evidence gathering, and audit reporting.\r\n\r\nEmphasis is placed on professional ethics, auditing standards, and the evaluation of internal controls. The course equips students with the knowledge needed to understand how audits enhance the reliability and credibility of financial information.', 3, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:25:44', 'assets/img/courses/ACC_213.jpg'),
(105, 7, 24, 'ACC 212', 'Cost Accounting 1', 'This course introduces the fundamental principles and techniques of cost accounting. It covers cost concepts, classification of costs, cost behavior, and methods of cost accumulation for materials, labor, and overheads. Students learn how costs are measured, recorded, and analyzed to support production and operational activities.\r\n\r\nThe course also emphasizes the use of cost information for planning, control, and decision-making within organizations. Topics such as job costing, process costing, and basic budgeting are explored to help students understand how cost accounting aids management in improving efficiency and profitability.', 4, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:26:40', 'assets/img/courses/ACC_212.jpg'),
(106, 7, 24, 'ACC 211', 'Financial Accounting 1', 'This course introduces the basic principles and concepts of financial accounting. It covers the recording, classification, and summarization of financial transactions, as well as the preparation of fundamental financial statements such as the income statement, balance sheet, and cash flow statement.\r\n\r\nEmphasis is placed on understanding accounting standards, the accounting cycle, and the use of financial information for decision-making. The course provides students with a solid foundation in accounting practices necessary for further studies in business and finance.', 4, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:27:19', 'assets/img/courses/ACC_211.jpg'),
(107, 7, 14, 'BAM 216', 'Practice of Entrepreneurship', 'This course introduces students to the practical aspects of starting and managing a business venture. It covers idea generation, opportunity recognition, business planning, resource mobilization, and strategies for launching and sustaining a small business.\r\n\r\nStudents also explore marketing, financial management, risk assessment, and innovation in entrepreneurship. Emphasis is placed on hands-on activities, case studies, and real-world applications to develop entrepreneurial skills, creativity, and the ability to turn business ideas into viable ventures.', 3, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:28:25', 'assets/img/courses/BAM_216.jpg'),
(108, 7, 24, 'BAM 224', 'Company Law', 'This course introduces students to the legal framework governing the formation, management, and regulation of companies. It covers topics such as types of business organizations, incorporation procedures, corporate governance, duties and responsibilities of directors, and shareholders’ rights.\r\n\r\nStudents also examine statutory requirements, compliance, and the legal implications of business decisions. The course equips learners with the knowledge to understand corporate legal structures, ensure regulatory compliance, and navigate legal issues in business operations effectively.', 3, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:29:28', 'assets/img/courses/BAM_224.jpg'),
(109, 7, 13, 'BAM 222', 'Business Statistics 2', 'This course builds on the concepts introduced in Business Statistics I, focusing on advanced statistical methods and their applications in business decision-making. Topics include probability distributions, sampling techniques, estimation, hypothesis testing, analysis of variance (ANOVA), and regression analysis.\r\n\r\nStudents also learn how to apply statistical tools to solve real-world business problems, interpret data, and make informed decisions. The course emphasizes practical applications, critical thinking, and the use of statistical software for data analysis in business contexts.', 3, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:30:30', 'assets/img/courses/BAM_222.jpg'),
(110, 7, 24, 'BFN 211', 'Business Finance', 'This course introduces the principles and practices of financial management in business. It covers topics such as sources of finance, financial planning, working capital management, investment appraisal, and capital structure decisions.\r\n\r\nStudents also learn how to analyze financial statements, assess financial performance, and make informed decisions to optimize the use of financial resources. The course equips learners with the knowledge and skills needed to manage business finances effectively and support organizational growth.', 3, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:31:08', 'assets/img/courses/BFN_211.jpg'),
(111, 7, 26, 'ACC 223', 'Auditing 2', 'This course builds on the principles introduced in Auditing I, focusing on advanced auditing practices and applications. It covers detailed audit planning, internal control evaluation, risk assessment, and the use of audit evidence in forming professional judgments.\r\n\r\nThe course also examines statutory and regulatory requirements, audit reports, and specialized audits. Emphasis is placed on professional ethics, compliance with auditing standards, and the auditor’s role in ensuring accountability and transparency in organizations.', 3, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:31:58', 'assets/img/courses/ACC_223.jpg'),
(112, 7, 36, 'ACC 222', 'Cost Accounting 2', 'This course builds on the concepts introduced in Cost Accounting I, focusing on advanced cost analysis and management techniques. It covers topics such as standard costing, variance analysis, marginal costing, and budgeting, with emphasis on cost control and performance evaluation.\r\n\r\nStudents learn how cost information supports managerial decision-making, pricing, and profit planning. The course also examines modern cost management approaches and their role in improving efficiency, productivity, and organizational profitability.', 4, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:32:39', 'assets/img/courses/ACC_222.jpg'),
(113, 7, 15, 'ACC 224', 'Taxation 2', 'This course builds on the fundamentals of Taxation I and explores advanced principles and practices of taxation. It covers corporate and personal tax computations, tax planning, tax compliance, and the interpretation of tax laws and regulations.\r\n\r\nStudents also learn about value-added tax (VAT), withholding taxes, and other relevant tax obligations. Emphasis is placed on the practical application of tax principles to real-world scenarios, equipping students with the skills to prepare accurate tax returns, advise on tax matters, and ensure compliance with statutory requirements.', 3, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:33:30', 'assets/img/courses/ACC_224.jpg'),
(114, 7, 27, 'ACC 221', 'Financial Accounting 2', 'This course builds on the foundations of Financial Accounting I and focuses on more advanced accounting principles and practices. It covers the preparation and interpretation of complex financial statements, accounting for partnerships, companies, and specialized transactions, as well as adjustments and corrections in accounting records.\r\n\r\nThe course also emphasizes compliance with accounting standards and the analysis of financial information for decision-making. Students develop a deeper understanding of how financial data is used to evaluate organizational performance and support managerial and investment decisions.', 4, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:34:36', 'assets/img/courses/ACC_221.jpg'),
(115, 7, 32, 'ACC 225', 'Public Sector Accounting', 'This course introduces the principles and practices of accounting in the public sector. It covers the unique features of public sector financial management, including budgeting, fund accounting, revenue collection, and expenditure control. Students learn how public sector entities record, report, and analyze financial transactions.\r\n\r\nThe course also emphasizes accountability, transparency, and compliance with governmental regulations and standards. Through practical exercises and case studies, students develop the skills to manage public funds effectively and produce reliable financial reports for decision-making and oversight.', 2, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:35:19', 'assets/img/courses/ACC_225.jpg'),
(116, 7, 24, 'ACC 229', 'Project', 'This course provides students with the opportunity to apply accounting knowledge and skills to an independent research or practical project. Students identify a problem or topic in accounting, conduct research, analyze data, and develop solutions or recommendations under the guidance of a supervisor.\r\n\r\nEmphasis is placed on project planning, report writing, presentation, and critical evaluation. The course helps students develop analytical, problem-solving, and professional communication skills, preparing them for real-world accounting practice or further academic research.', 6, 'Accountancy', 'ND 2', 'Second', '2025-12-10 10:36:06', 'assets/img/courses/ACC_229.png'),
(117, 7, 22, 'BAM 121', 'Introduction to Business 2', 'This course builds on the foundations of Introduction to Business I, exploring more advanced concepts and practices in the business environment. It covers topics such as organizational structures, management principles, business operations, entrepreneurship, and strategic planning.\r\n\r\nStudents also examine the impact of technology, ethics, and globalization on business activities. Through practical examples and case studies, the course equips learners with the knowledge and skills to understand, analyze, and participate effectively in modern business organizations.', 3, 'Business Administration', 'ND 1', 'Second', '2025-12-10 10:38:33', 'assets/img/courses/BAM_121.jpg'),
(118, 7, 39, 'BAM 114', 'Principles of Economics 1', 'This course introduces students to the fundamental concepts of economics, focusing on both microeconomic and macroeconomic principles. It covers topics such as supply and demand, market structures, production and cost, national income, and basic economic problems of scarcity and choice.\r\n\r\nStudents also explore the role of government, consumers, and firms in the economy, and learn how economic principles guide decision-making. The course provides a foundation for understanding economic behavior and the functioning of markets, preparing students for more advanced studies in economics and related fields.', 3, 'Business Administration', 'ND 1', 'First', '2025-12-10 10:51:27', 'assets/img/courses/BAM_114.jpg'),
(119, 7, 29, 'ACC111', 'Principles of Accounts 1', 'This course introduces the basic concepts and principles of accounting, providing a foundation for understanding financial record-keeping. It covers the accounting cycle, including the recording, classification, and summarization of business transactions, as well as the preparation of basic financial statements such as the income statement and balance sheet.\r\n\r\nStudents also learn fundamental accounting principles, including double-entry bookkeeping, the concept of assets, liabilities, and equity, and the importance of accuracy and consistency in financial reporting. The course equips learners with the essential skills needed for further study in accounting and finance.', 4, 'Business Administration', 'ND 1', 'First', '2025-12-10 10:52:20', 'assets/img/courses/ACC111.jpg'),
(120, 7, 31, 'BAM 115', 'Principles of Marketing', 'This course introduces students to the fundamental concepts and practices of marketing. It covers topics such as the marketing mix (product, price, place, promotion), market segmentation, consumer behavior, and marketing research. Students learn how businesses identify customer needs and develop strategies to satisfy them effectively.\r\n\r\nThe course also emphasizes the role of marketing in achieving organizational goals, building customer relationships, and creating value. Through case studies and practical examples, students gain insights into real-world marketing strategies and the application of marketing principles in various business contexts.', 3, 'Business Administration', 'ND 1', 'First', '2025-12-10 10:53:00', 'assets/img/courses/BAM_115.jpg'),
(121, 7, 29, 'BAM117', 'Principles of Purchasing', 'This course introduces the fundamentals of purchasing and procurement in business organizations. It covers topics such as sourcing and selection of suppliers, purchasing processes, inventory management, negotiation strategies, and cost control.\r\n\r\nStudents also learn about ethical and legal considerations in procurement, supplier relationship management, and the role of purchasing in achieving organizational efficiency. The course equips learners with practical skills to manage procurement activities effectively and support overall business operations.', 3, 'Business Administration', 'ND 1', 'First', '2025-12-10 10:53:57', 'assets/img/courses/BAM117.jpg'),
(122, 7, 28, 'BAM 124', 'Principles of Economics 2', 'This course builds on the concepts introduced in Principles of Economics I, focusing on advanced microeconomic and macroeconomic topics. It covers areas such as market failures, government intervention, fiscal and monetary policies, inflation, unemployment, and international trade.\r\n\r\nStudents also explore economic growth, development, and the impact of economic policies on businesses and society. The course equips learners with analytical tools to understand economic behavior, evaluate policy decisions, and make informed choices in personal, business, and governmental contexts.', 3, 'Business Administration', 'ND 1', 'First', '2025-12-10 10:56:27', 'assets/img/courses/BAM_124.jpg'),
(123, 7, 35, 'BAM 125', 'Information Technology 1 (Data Processing)', 'This course introduces students to the fundamentals of information technology with a focus on data processing concepts and techniques. It covers data collection, storage, processing, and retrieval, as well as the use of software tools for managing and analyzing data.\r\n\r\nStudents also learn about computer hardware, software applications, and basic system operations relevant to data processing. The course equips learners with practical skills to handle information efficiently, laying a foundation for advanced IT studies and applications in organizational settings.', 6, 'Business Administration', 'ND 1', 'First', '2025-12-10 10:57:23', 'assets/img/courses/BAM_125.jpg'),
(124, 7, 35, 'BAM 123', 'Introduction to Social Psychology', 'This course explores how individuals’ thoughts, feelings, and behaviors are influenced by the social environment. It covers key topics such as social perception, attitudes, group dynamics, conformity, aggression, prejudice, and interpersonal relationships.\r\n\r\nStudents also examine theories and research methods used in social psychology to understand human behavior in social contexts. The course emphasizes practical applications, helping learners analyze social interactions and apply psychological principles to everyday life, community issues, and organizational settings.', 3, 'Business Administration', 'ND 1', 'First', '2025-12-10 10:59:03', 'assets/img/courses/BAM_123.jpg'),
(125, 7, 29, 'OTM 112', 'Technical English I', 'This course provides students with the opportunity to apply theoretical knowledge and practical skills acquired in media and communication studies to an independent research or creative project. Students identify a topic or problem, conduct research, gather and analyze data, and present their findings under the supervision of a faculty member.\r\n\r\nEmphasis is placed on project planning, report writing, presentation, and critical evaluation. The course equips learners with analytical, problem-solving, and professional communication skills, preparing them for real-world media practice or further academic research.', 4, 'Business Administration', 'ND 1', 'First', '2025-12-10 10:59:50', 'assets/img/courses/OTM_112.jpg'),
(126, 7, 13, 'GNS 131', 'Citizenship Education 2', 'This course explores the rights, responsibilities, and roles of citizens in a democratic society. It covers topics such as civic duties, governance, human rights, social justice, and community participation, emphasizing the importance of active and informed citizenship.\r\n\r\nStudents also examine contemporary social, political, and ethical issues, developing critical thinking and problem-solving skills. The course aims to prepare learners to engage responsibly in society, contribute to national development, and promote social cohesion and good governance.', 2, 'Business Administration', 'ND 1', 'First', '2025-12-10 11:00:29', 'assets/img/courses/GNS_131.jpg'),
(127, 7, 40, 'BAM 213', 'Office Management', 'This course introduces the principles and practices of managing modern office operations. It covers topics such as office organization, workflow management, records and document handling, communication systems, and the use of office technologies to enhance efficiency.\r\n\r\nStudents also learn about office planning, supervisory skills, and the coordination of administrative activities. The course equips learners with the knowledge and practical skills needed to manage office environments effectively, ensuring smooth operations and support for organizational objectives.', 3, 'Business Administration', 'ND 2', 'Second', '2025-12-10 11:02:21', 'assets/img/courses/BAM_213.jpg'),
(128, 7, 40, 'BAM 215', 'Information Technology 2', 'This course advances students’ understanding of information technology and its applications in modern organizations. It covers topics such as database systems, networking concepts, internet technologies, and the use of software tools to support business and management activities. Emphasis is placed on practical skills and real-world applications.\r\n\r\nThe course also examines emerging technologies, data management, and information security issues. Students learn how information technology enhances efficiency, decision-making, and communication in organizations, preparing them for effective use of IT in professional environments.', 6, 'Business Administration', 'ND 2', 'Second', '2025-12-10 11:03:33', 'assets/img/courses/BAM_215.jpg'),
(129, 7, 36, 'OTM 222', 'Technical English II', 'This course is designed to equip students with the advanced English language skills necessary for effective academic and professional communication. The focus is on applying linguistic knowledge to practical, real-world tasks, with an emphasis on clarity, economy, and technical accuracy.', 4, 'Business Administration', 'ND 1', 'Second', '2025-12-10 11:06:42', 'assets/img/courses/OTM_222.jpg'),
(130, 7, 28, 'BAM 223', 'Elements of Production Management', 'This course introduces the fundamental concepts and principles of production management in organizations. It covers topics such as production planning and control, capacity planning, facility layout, inventory management, and quality control, with emphasis on efficient use of resources to achieve organizational goals.\r\n\r\nStudents also learn how production systems are designed and managed to improve productivity and reduce costs. The course highlights the role of management in coordinating materials, labor, and technology to ensure effective and sustainable production operations.', 3, 'Business Administration', 'ND 2', 'Second', '2025-12-15 13:39:24', 'assets/img/courses/BAM_223.jpg'),
(131, 7, 27, 'BAM 225', 'Project', 'This course focuses on the planning, execution, and documentation of an independent academic project. Students apply theoretical knowledge and practical skills to identify a problem, conduct research, design solutions, and implement a project under supervision.\r\n\r\nEmphasis is placed on project management, report writing, presentation, and evaluation. The course develops students’ ability to work independently, think critically, and demonstrate competence in applying computing principles to real-world problems.', 5, 'Business Administration', 'ND 2', 'Second', '2025-12-15 13:40:43', 'assets/img/courses/BAM_225.jpg');

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
(5, 59, 'Intro to Systems Analysis and Design', 'This record is crucial because it tracks the entire lifecycle of the student&#039;s work—from when they submitted it to when they received their final grade and feedback.\r\n\r\nWould you like a sample record for the student&#039;s overall Gradebook entry for this course?', 'uploads/materials/1764798952_6930b1e88ad38.pdf', 'reference', 1, 14, '2025-12-03 22:55:52', '2025-12-03 22:55:52'),
(6, 59, 'Best for Integration and Design Enhancement', 'These tools integrate directly or work closely with Microsoft PowerPoint to improve the final result.\r\nThese tools create custom, unique images or icons to illustrate your presentation points, which you then import into PowerPoint.', 'uploads/materials/1764806867_6930d0d3c7d51.docx', 'reading', 1, 14, '2025-12-04 01:07:47', '2025-12-04 01:07:47'),
(7, 27, 'Semiu', 'This is a sample material', 'uploads/materials/1764928946_6932adb245358.docx', 'reading', 1, 12, '2025-12-05 11:02:26', '2025-12-05 11:02:26');

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
  `approval_status` enum('Pending','Registered','Dropped','Completed') DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` datetime DEFAULT NULL,
  `decline_status` tinyint(1) DEFAULT 0,
  `decline_reason` varchar(255) DEFAULT NULL,
  `approval_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_regtbl`
--

INSERT INTO `course_regtbl` (`reg_id`, `student_id`, `course_id`, `academic_year`, `semester`, `date_registered`, `approval_status`, `approved_by`, `date_approved`, `decline_status`, `decline_reason`, `approval_reason`) VALUES
(47, 7, 47, '2024/2025', '', '2025-12-03 22:17:52', 'Registered', 8, '2025-12-03 23:18:57', 0, NULL, NULL),
(48, 7, 48, '2024/2025', '', '2025-12-03 22:17:53', 'Registered', 8, '2025-12-03 23:18:57', 0, NULL, NULL),
(49, 7, 49, '2024/2025', '', '2025-12-03 22:17:54', 'Registered', 8, '2025-12-03 23:18:57', 0, NULL, NULL),
(50, 7, 50, '2024/2025', '', '2025-12-03 22:17:55', 'Registered', 8, '2025-12-03 23:18:57', 0, NULL, NULL),
(51, 7, 51, '2024/2025', '', '2025-12-03 22:17:55', 'Registered', 8, '2025-12-03 23:18:57', 0, NULL, NULL),
(52, 7, 59, '2024/2025', '', '2025-12-03 22:17:55', 'Registered', 8, '2025-12-03 23:18:57', 0, NULL, NULL),
(53, 8, 35, '2024/2025', '', '2025-12-05 09:55:59', 'Registered', 8, '2025-12-05 10:56:53', 0, NULL, NULL),
(54, 8, 44, '2024/2025', '', '2025-12-05 09:55:59', 'Registered', 8, '2025-12-05 10:56:53', 0, NULL, NULL),
(55, 8, 46, '2024/2025', '', '2025-12-05 09:56:00', 'Registered', 8, '2025-12-05 10:56:53', 0, NULL, NULL),
(56, 8, 31, '2024/2025', '', '2025-12-05 09:56:00', 'Registered', 8, '2025-12-05 10:56:53', 0, NULL, NULL),
(57, 8, 32, '2024/2025', '', '2025-12-05 09:56:00', 'Registered', 8, '2025-12-05 10:56:53', 0, NULL, NULL),
(58, 8, 39, '2024/2025', '', '2025-12-05 09:56:00', 'Registered', 8, '2025-12-05 10:56:53', 0, NULL, NULL),
(59, 8, 38, '2024/2025', '', '2025-12-05 09:56:00', 'Registered', 8, '2025-12-05 10:56:53', 0, NULL, NULL),
(60, 8, 27, '2024/2025', '', '2025-12-05 09:56:00', 'Registered', 8, '2025-12-05 10:56:53', 0, NULL, NULL),
(61, 9, 43, '2024/2025', '', '2025-12-06 23:14:11', 'Dropped', 7, '2025-12-07 00:15:26', 0, NULL, NULL),
(62, 9, 36, '2024/2025', '', '2025-12-06 23:14:11', 'Dropped', 7, '2025-12-07 00:15:26', 0, NULL, NULL),
(63, 9, 45, '2024/2025', '', '2025-12-06 23:14:11', 'Dropped', 7, '2025-12-07 00:15:26', 0, NULL, NULL),
(64, 9, 54, '2024/2025', '', '2025-12-06 23:14:11', 'Dropped', 7, '2025-12-07 00:15:26', 0, NULL, NULL),
(65, 9, 58, '2024/2025', '', '2025-12-06 23:14:12', 'Dropped', 7, '2025-12-07 00:15:26', 0, NULL, NULL),
(66, 9, 74, '2024/2025', '', '2025-12-06 23:14:12', 'Dropped', 7, '2025-12-07 00:15:26', 0, NULL, NULL),
(67, 9, 76, '2024/2025', '', '2025-12-06 23:14:12', 'Dropped', 7, '2025-12-07 00:15:26', 0, NULL, NULL),
(68, 9, 35, '2024/2025', '', '2025-12-06 23:25:33', 'Registered', 7, '2025-12-07 00:26:20', 0, NULL, NULL),
(69, 9, 44, '2024/2025', '', '2025-12-06 23:25:33', 'Registered', 7, '2025-12-07 00:26:20', 0, NULL, NULL),
(70, 9, 46, '2024/2025', '', '2025-12-06 23:25:33', 'Registered', 7, '2025-12-07 00:26:20', 0, NULL, NULL),
(71, 9, 31, '2024/2025', '', '2025-12-06 23:25:33', 'Registered', 7, '2025-12-07 00:26:20', 0, NULL, NULL),
(72, 9, 32, '2024/2025', '', '2025-12-06 23:25:33', 'Registered', 7, '2025-12-07 00:26:20', 0, NULL, NULL),
(73, 9, 68, '2024/2025', '', '2025-12-06 23:25:33', 'Registered', 7, '2025-12-07 00:26:20', 0, NULL, NULL),
(76, 9, 75, '2024/2025', '', '2025-12-06 23:30:51', '', 7, '2025-12-07 00:32:09', 0, 'fee-not-paid', 'do the needful!'),
(78, 9, 77, '2024/2025', '', '2025-12-06 23:30:52', '', 7, '2025-12-07 00:32:09', 0, 'fee-not-paid', 'do the needful!'),
(79, 9, 79, '2024/2025', '', '2025-12-06 23:30:52', '', 7, '2025-12-07 00:32:09', 0, 'fee-not-paid', 'do the needful!');

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

--
-- Dumping data for table `deadlinetbl`
--

INSERT INTO `deadlinetbl` (`deadline_id`, `course_id`, `lecturer_id`, `title`, `description`, `deadline_date`, `created_at`) VALUES
(1, 27, 12, 'Assignment: The Target Market: University Students', 'Traditional study groups are often inefficient, difficult to organize across busy schedules, and lack accountability. Students are drowning in lecture recordings and readings but need quick, verified answers to specific, niche questions (e.g., &quot;What formula did the professor use for elasticity on slide 15?&quot;). They waste time searching through hours of content or waiting for a T.A. response.', '2025-12-27 11:00:00', '2025-12-05 10:01:07'),
(2, 74, 14, 'Assignment: What is Computer Networking?', '1. Explain in Detail everything you know about computer and computer networks.\r\n2. List and explain types of computer networks.', '2026-01-10 23:52:00', '2025-12-06 22:52:55'),
(3, 32, 14, 'Assignment: What is Law?', 'State &amp; Explain the constitution sources.', '2026-01-10 00:37:00', '2025-12-06 23:37:10');

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

--
-- Dumping data for table `evaluationtbl`
--

INSERT INTO `evaluationtbl` (`eval_id`, `student_id`, `course_id`, `lecturer_id`, `academic_year`, `semester`, `ca_score`, `test_score`, `exam_score`, `grade`, `grade_point`, `credit_units`, `entered_by`, `entered_at`) VALUES
(1, 7, 59, 14, '2025/2026', 'First', 25.00, 18.00, 38.00, 'A', 5.00, 2, '14', '2025-12-04 00:30:36'),
(2, 8, 27, 12, '2025/2026', 'First', 25.00, 18.00, 25.00, 'B', 4.00, 3, '12', '2025-12-05 10:06:28'),
(3, 8, 32, 14, '2025/2026', 'First', 22.00, 17.00, 34.00, 'A', 5.00, 3, '14', '2025-12-06 23:54:18'),
(4, 9, 32, 14, '2025/2026', 'First', 28.00, 16.00, 32.00, 'A', 5.00, 3, '14', '2025-12-06 23:54:18');

-- --------------------------------------------------------

--
-- Table structure for table `grade_submissions`
--

CREATE TABLE `grade_submissions` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `academic_year` varchar(20) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `published_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_submissions`
--

INSERT INTO `grade_submissions` (`id`, `course_id`, `lecturer_id`, `submitted_at`, `academic_year`, `semester`, `approved_at`, `approved_by`, `published`, `published_at`, `published_by`) VALUES
(1, 59, 14, '2025-12-04 00:46:51', '2025', 'First', '2025-12-04 00:55:30', 8, 1, '2025-12-04 00:55:30', 8),
(2, 27, 12, '2025-12-05 10:06:42', '2025', 'First', '2025-12-05 10:07:14', 8, 1, '2025-12-05 10:07:14', 8),
(3, 32, 14, '2025-12-06 23:54:25', '2025', 'First', '2025-12-06 23:54:55', 7, 1, '2025-12-06 23:54:55', 7);

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
(8, 14, 'assignment_created', 'Created assignment: Final Year Project Proposal', '2025-12-03 21:53:36'),
(9, 14, 'material_uploaded', 'Uploaded material: Intro to Systems Analysis and Design', '2025-12-03 21:55:52'),
(10, 14, 'assignment_created', 'Created assignment: Best for End-to-End Generation', '2025-12-04 00:06:29'),
(11, 14, 'material_uploaded', 'Uploaded material: Best for Integration and Design Enhancement', '2025-12-04 00:07:48'),
(12, 12, 'assignment_created', 'Created assignment: The Target Market: University Students', '2025-12-05 10:01:07'),
(13, 12, 'material_uploaded', 'Uploaded material: Semiu', '2025-12-05 10:02:26'),
(14, 14, 'assignment_created', 'Created assignment: What is Computer Networking?', '2025-12-06 22:52:55'),
(15, 14, 'assignment_created', 'Created assignment: What is Law?', '2025-12-06 23:37:10');

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
(6, 7, 'Mr.', 'Oshodi Michael', 'michael@gmail.com', '9055520202', '$2y$10$4r22IdeSf.L3jJl6W3tteurQop3NNskZqDNe4tydjEp20dIlk2MPu', 'Mass Communication', 'Male', '2025-12-03 03:37:02', NULL),
(7, 7, 'Mr.', 'Adeyemi O.A', 'adeyemi@gmail.com', '90555202021', '$2y$10$WdgQdfEDtpf5oJTHhYLNguVZ1wPcQv67kGEFGT5EPw/ONBU7paUp.', 'Mass Communication', 'Male', '2025-12-03 03:44:37', NULL),
(8, 7, 'Mrs.', 'Chizor Lois', 'lois@gmail.com', '08142406151', '$2y$10$rxxMd729njSs8WtgcQKsE.TujY9By41h4RoLrdmTY6EmLx1w/0vBG', 'Mass Communication', 'Female', '2025-12-03 03:45:55', NULL),
(9, 7, 'Mr.', 'Somide Stanislaus', 'somide@gmail.com', '9055520202', '$2y$10$0LzSs7t4L2wqxwgr0bZ9g.LDmTTES4dGC4bYAlGpoE5W3DFfzFF3O', 'Mass Communication', 'Male', '2025-12-03 03:46:58', NULL),
(10, 7, 'Mr.', 'Moses Kanu', 'kanu@gmail.com', '08031234567', '$2y$10$U/1c/h7t3562WAotHgRLOueyuP6lr6WppQ4rSq707K9beFR5pVX.W', 'Mass Communication', 'Male', '2025-12-03 03:48:21', '1764733733_mck.jpeg'),
(11, 8, 'Mr.', 'Tony D. Bamjo', 'bamjo@gmail.com', '9055520202', '$2y$10$5VkEeaCZc8OtIbMiszIDkeap0ACzobRQmM9iM6RJ/.NlO/aIcl7jy', 'Mass Communication', 'Male', '2025-12-03 04:39:30', NULL),
(12, 8, 'Engr. Segun', 'Fatoki', 'fatoki@gmail.com', '9055520202', '$2y$10$WNo03WnAwwFRjc21xM9s0uewYt5hD0IjOlVxAIMn9eOKiWTZ6UjfK', 'Mass Communication', 'Male', '2025-12-03 04:40:39', NULL),
(13, 7, 'Mrs. Esther', 'Adeniji', 'esther@mail.com', '08031234567', '$2y$10$uMkeNB/wkUhtgng9f4SFnuaHwbs2cSnMC3ueZbRmVe8rIpNEyttdu', 'Mass Communication', 'Female', '2025-12-03 05:10:32', NULL),
(14, 7, 'Mr. Ayodele', 'Yusuff', 'yusuf@gmail.com', '08142406151', '$2y$10$I4Ye5BnDCoapzj33LA9K8eC.PBYYIVE6/gdf0/vLjJEqpiVb/XGq6', 'Mass Communication', 'Male', '2025-12-03 05:11:17', '1765059360_mrTy.jpg'),
(15, 7, 'Mr. Akintola', 'Oloyede', 'oloyede@gmail.com', '90555202029', '$2y$10$BeKsIu513k4za6y4af1JpeO/VRn.wIEc5DeJQYlRQ2GWNXkJ8H7.G', 'Mass Communication', 'Male', '2025-12-03 05:12:23', NULL),
(16, 7, 'Mr. Afolabi', 'Kehinde', 'kehide@gmail.com', '08142406151', '$2y$10$gouD92EYrHwQhxUKP6MTNOj3pfIJLAyPGR4dUIdg0ZlRtcxHyOhhq', 'Mass Communication', 'Male', '2025-12-03 05:13:11', NULL),
(17, 7, 'Mr. Babajinmi', 'Ojo Abiola', 'ojo@gmail.com', '90555202021', '$2y$10$91cjHZuIHLEdCK3HhxNUN.sMIc2OyDZ6ATZ1ZADqCWXRUwohsY.hi', 'Mass Communication', 'Male', '2025-12-03 05:13:55', NULL),
(18, 7, 'Mr. Oyelakin', 'Micheal', 'michaeloye@gmail.com', '9055520208', '$2y$10$w/DOcXgqAaM3XNLLgN.X6e75DOAd.5JhcLmkjkVS9zL93oJasZLIK', 'Mass Communication', 'Male', '2025-12-03 05:15:27', NULL),
(19, 7, 'Olugbenga', 'Paul', 'paul@gmail.com', '90555202021', '$2y$10$kAQLKo4GQRdrYSJtPd3mgOHKNqkmfAoMryUkdzVPVTK0sCbUhXfdS', 'Mass Communication', 'Male', '2025-12-03 05:16:02', NULL),
(20, 7, 'Mr. Onibiyo', 'Olusola', 'olusola@gmail.com', '9055520202', '$2y$10$HURIBnT6QwGLsk0pUm8tkO/j6WU/1GgCMTDw5wnCDkLwU4AjbRjL2', 'Accountancy', 'Male', '2025-12-03 05:24:05', NULL),
(21, 7, 'Mrs. Ayantade', 'Olubunmi', 'ayantade@gmail.com', '9055520202', '$2y$10$GjTPI8kVQm0lDeMiXdfPZujhVnSYQc/8GyBVOj3PbnbXWGhxZ8eIi', 'Accountancy', 'Male', '2025-12-03 05:24:51', NULL),
(22, 7, 'Mr. Aikulola', 'Emmanuel', 'aikulola@gmail.com', '9055520202', '$2y$10$EWf5RYBMRx10vVm9OWAH7ePUSXn4ZrjXr48Tz7K.cJABwxQXEiw4C', 'Accountancy', 'Male', '2025-12-03 05:25:52', NULL),
(23, 7, 'Mr. Ajiboye', 'John', 'ajiboye@gmail.com', '08142406151', '$2y$10$WZIYifvhAn7RgRNASHDc6.7ORamHsaQGwEiDEZsfchaiduPEJ0806', 'Accountancy', 'Male', '2025-12-03 05:26:51', NULL),
(24, 7, 'Mr. Oluwafunto', 'Adeyinka', 'adeyinka@gmail.com', '8061234567', '$2y$10$LbtH0d0ErGrG4uFo9Cp2WeUFJIptqSXZAeMQvao.Vu9.uEIu7kCUC', 'Accountancy', 'Male', '2025-12-03 05:27:36', NULL),
(26, 7, 'Mr. Amoo', 'Samuel', 'amoo@gmail.com', '9055520202', '$2y$10$vL5qgh0kAYDMG1il9iFJZeSk7SyqmqQQTXJEud4j9GrnGxRo4ZTL6', 'Computer Science', 'Male', '2025-12-03 05:29:33', NULL),
(27, 7, 'Barr. Olatunbosun', 'Temitope', 'temitope@gmail.com', '8109876543', '$2y$10$D5SExD/jJ5yFiKZjm2TN9ukfDKs7l4psVS.595vp93t8xRR2qEDU2', 'Business Administration', 'Male', '2025-12-03 05:31:27', NULL),
(28, 7, 'Mr. Adetoyi', 'Michael', 'ayetoyi@gmail.com', '9055520202', '$2y$10$mOh4Qju0sKDiwnVA7iAA4.4UED0R5LK/zTpMW7eYyyzwfvyu8KNim', 'Business Administration', 'Male', '2025-12-03 05:32:20', NULL),
(29, 7, 'Mrs. Adenike', 'Adegbite', 'adegbite@gmail.com', '9055520202', '$2y$10$j6CLnFukIXPK3aApIhqGjeLqOW4pw5NwoImCE1XneBn8BuWmvu.2m', 'Business Administration', 'Female', '2025-12-03 05:34:23', NULL),
(30, 7, 'Ayeri Jonathan', 'Emeka', 'Jonathan@gmail.com', '9055520202', '$2y$10$vD5004DrcSFDGBCCRyl/Y.vlyiOGlP/U9rLyw3NmOchW1jCuJzHYe', 'Business Administration', 'Male', '2025-12-03 05:35:02', NULL),
(31, 7, 'Timileyin', 'Fatoki', 'timi@gmail.com', '8061234567', '$2y$10$1vIPDLgpLUuECc9ExQHk9Os8uG9PiFGUri0gVSCyN8PCRyHtktrb6', 'Business Administration', 'Male', '2025-12-03 05:35:43', NULL),
(32, 7, 'Mr. Awodeyi', 'Tolulope', 'toluope@gmail.com', '90555202021', '$2y$10$SQBk.UP52hUAhn7P5M9GiO8mXsP5ZoWUc0LWVxKWOF1gOx0FV/bHW', 'Computer Science', 'Male', '2025-12-03 05:36:51', NULL),
(33, 7, 'Dr. Idowu', 'Ifedotun', 'ifedotun@gmail.com', '08142406190', '$2y$10$tkGDgnPYAa8jTAgfq9BVDORuCkmLhI7SmipvVTz.3iYtcGV1Kuur6', 'Computer Science', 'Female', '2025-12-03 05:37:36', NULL),
(34, 7, 'Mr. Ibitola A.', 'Gideon', 'gideon@gmail.com', '9055520200', '$2y$10$JXvGbyVKGr7xQxezUo./JeFq28AwVyFsNOluwneHkFamTjDnfOV5i', 'Computer Science', 'Male', '2025-12-03 05:38:21', NULL),
(35, 7, 'Mr. Ige', 'Olufemi', 'olufemi@gmail.com', '9055520290', '$2y$10$Rf5/qp2sAUnYcJ8cy0b5oebDdNXt3Vdt2W.sHIbszFkuTUqCEalWG', 'Computer Science', 'Male', '2025-12-03 05:39:40', NULL),
(36, 7, 'Mr. Oyewole', 'Lukman', 'lukman@gmail.com', '9055520202', '$2y$10$JmRhQ4mC0KHsNAvjymApIuC5CqE92TIoZ4bx7BquwCaqWPQTeKtpG', 'Computer Science', 'Male', '2025-12-03 05:40:18', NULL),
(37, 7, 'Mr. Mapayi', 'Temitope', 'mapayi@gmail.com', '08031234562', '$2y$10$BuKbiSYzopuNndR4SlzNf.Up5EWYS0m0zMa3JRkV1ApDowjxdOuwO', 'Computer Science', 'Male', '2025-12-03 05:41:37', NULL),
(38, 7, 'Dr. Sunday', 'Adebisi', 'sunday@gmail.com', '9055520202', '$2y$10$yzjrtM3inUlPncQe9dfaHunA7bSk5MbxuTkRFzoImjIPumEBTuKP6', 'Computer Science', 'Male', '2025-12-03 05:43:51', NULL),
(39, 7, 'Mr. Osamudiamen', 'Miracle', 'osamudiamen@gmail.com', '09051178677', '$2y$10$NDDVG4BZqWrdqaUL5sdKP.ihaKb7bXwpojk9KzRC/WQpSzBUt8e7.', 'Computer Science', 'Male', '2025-12-03 05:45:24', NULL),
(40, 7, 'Mr', 'Ozenua Micheal', 'ozenua@gmail.com', '08031234590', '$2y$10$2ft9jlPf35XuuOOHmvQkwemF4smFkuQucqW/OBqcryaavQpugRjga', 'Computer Science', 'Male', '2025-12-03 05:46:21', NULL);

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

--
-- Dumping data for table `resulttbl`
--

INSERT INTO `resulttbl` (`result_id`, `student_id`, `course_id`, `ca_score`, `test_score`, `exam_score`, `grade_letter`, `remark`, `academic_year`, `semester`, `created_at`) VALUES
(1, 7, 59, 25.00, 18.00, 38.00, 'A', NULL, '2025/2026', 'First', '2025-12-04 00:35:05'),
(4, 8, 27, 25.00, 18.00, 25.00, 'B', NULL, '2025/2026', 'First', '2025-12-05 10:06:28'),
(5, 8, 32, 22.00, 17.00, 34.00, 'A', NULL, '2025/2026', 'First', '2025-12-06 23:54:18'),
(6, 9, 32, 28.00, 16.00, 32.00, 'A', NULL, '2025/2026', 'First', '2025-12-06 23:54:18');

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

--
-- Dumping data for table `studentrecentactivitytbl`
--

INSERT INTO `studentrecentactivitytbl` (`activity_id`, `student_id`, `activity_type`, `activity_description`, `timestamp`) VALUES
(1, 8, 'Course Registration', 'Registered for 8 courses in First Semester', '2025-12-05 09:56:00'),
(2, 8, 'Assignment Submission', 'Submitted assignment for Assignment ID: 9', '2025-12-05 10:04:18'),
(3, 9, 'Course Registration', 'Registered for 7 courses in First Semester', '2025-12-06 23:14:12'),
(4, 9, 'Course Registration', 'Registered for 6 courses in First Semester', '2025-12-06 23:25:34'),
(5, 9, 'Course Registration', 'Registered for 3 courses in First Semester', '2025-12-06 23:30:52'),
(6, 9, 'Assignment Submission', 'Submitted assignment for Assignment ID: 11', '2025-12-06 23:45:00'),
(7, 8, 'Assignment Submission', 'Submitted assignment for Assignment ID: 11', '2025-12-06 23:46:31');

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
(6, 7, '248390', 'Kcee', 'Ethapemi', 'kcee@gmail.com', '09056678915', '$2y$10$vSBXuAh/qjIXHMddQDlTEOnUIGpfraMH6b90/G6qxQNWBUoa37ZWS', 'Accountancy', 'ND 1', '2024/2025', 'Male', '2025-11-29 01:19:48', NULL),
(7, 7, '245360', 'Aaron', 'Stephen', 'aaron@gmail.com', '08142406151', '$2y$10$rnL4mJFCG30GRmcyO.cVl..5lcX5cDM.3Cdy/UNdXPmHcPYhGloy.', 'Computer Science', 'ND 1', '2024/2025', 'Male', '2025-12-01 01:51:58', '1764927604_yul.jpg'),
(8, 7, '248389', 'Augustina', 'Offorkansi', 'august@gmail.com', '8061234567', '$2y$10$zdcQpu766IwuCtCcp8enOeNZikxHEmLh1EMQX88ilG/f4.HD.8gxe', 'Accountancy', 'ND 1', '2023/2024', 'Female', '2025-12-03 03:30:32', NULL),
(9, 7, '248387', 'Jane', 'Doe', 'jane.doe@example.com', '08031234567', '$2y$10$9UPVVB28kn/Unp6QFX7iFel9znx/gKgjxCcqe7yWa.xYg0YpRizGu', 'Mass Communication', 'ND 1', '2023/2024', 'Female', '2025-12-03 03:32:32', NULL),
(10, 8, '248367', 'Michael', 'Osodi', 'oso@gmail.com', '8061234567', '$2y$10$LcN0ZbEGLZxrfa35fgrJquO9rTr5oat9UKiXK5ApJGjAdvWsAqaGq', 'Business Administration', 'ND 1', '2023/2024', 'Male', '2025-12-04 10:25:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transcript_requests`
--

CREATE TABLE `transcript_requests` (
  `request_id` int(11) NOT NULL,
  `student_matric_no` varchar(50) NOT NULL,
  `student_name` varchar(150) DEFAULT NULL,
  `recipient_type` varchar(50) NOT NULL,
  `recipient_details` text NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) NOT NULL DEFAULT 'Pending Payment',
  `tracking_number` varchar(100) DEFAULT NULL,
  `fulfillment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `grade_submissions`
--
ALTER TABLE `grade_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_submission` (`course_id`,`lecturer_id`,`academic_year`,`semester`),
  ADD KEY `lecturer_id` (`lecturer_id`);

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
-- Indexes for table `transcript_requests`
--
ALTER TABLE `transcript_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `status` (`status`),
  ADD KEY `request_date` (`request_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT for table `admintbl`
--
ALTER TABLE `admintbl`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `assignmenttbl`
--
ALTER TABLE `assignmenttbl`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ass_gradetbl`
--
ALTER TABLE `ass_gradetbl`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ass_subtbl`
--
ALTER TABLE `ass_subtbl`
  MODIFY `sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `coursetbl`
--
ALTER TABLE `coursetbl`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `course_materialtbl`
--
ALTER TABLE `course_materialtbl`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_regtbl`
--
ALTER TABLE `course_regtbl`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `deadlinetbl`
--
ALTER TABLE `deadlinetbl`
  MODIFY `deadline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grade_submissions`
--
ALTER TABLE `grade_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lecturerrecentactivitytbl`
--
ALTER TABLE `lecturerrecentactivitytbl`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `lecturertbl`
--
ALTER TABLE `lecturertbl`
  MODIFY `LecturerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `resulttbl`
--
ALTER TABLE `resulttbl`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `studentrecentactivitytbl`
--
ALTER TABLE `studentrecentactivitytbl`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `studenttbl`
--
ALTER TABLE `studenttbl`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transcript_requests`
--
ALTER TABLE `transcript_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `grade_submissions`
--
ALTER TABLE `grade_submissions`
  ADD CONSTRAINT `grade_submissions_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `coursetbl` (`course_id`),
  ADD CONSTRAINT `grade_submissions_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturertbl` (`LecturerID`);

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
