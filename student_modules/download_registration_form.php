<?php
session_start();
require_once '../config/db.php';
require_once '../vendor/autoload.php'; // For TCPDF

use TCPDF;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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
    die('Your course registration is not yet approved. Please wait for admin approval.');
}

// Get format from URL parameter
$format = $_GET['format'] ?? 'pdf';
if (!in_array($format, ['pdf', 'docx'])) {
    $format = 'pdf';
}

// Fetch student info
$studentStmt = $pdo->prepare("SELECT Matric_No, first_name, last_name, Department, Level FROM studenttbl WHERE student_id = ?");
$studentStmt->execute([$student_id]);
$student = $studentStmt->fetch();

if (!$student) {
    die('Student not found.');
}

$currentSemester = 'Fall 2024';
$currentYear = '2024/2025';

// Fetch registered courses
$coursesStmt = $pdo->prepare("
    SELECT c.course_code, c.course_title, c.course_unit, 
           CONCAT(l.First_name, ' ', l.Last_Name) AS lecturer_name
    FROM course_regtbl cr
    JOIN coursetbl c ON cr.course_id = c.course_id
    LEFT JOIN lecturertbl l ON c.lecturer_id = l.LecturerID
    WHERE cr.student_id = ? AND cr.semester = ? AND cr.academic_year = ?
    ORDER BY c.course_code
");
$coursesStmt->execute([$student_id, $currentSemester, $currentYear]);
$courses = $coursesStmt->fetchAll();

if (empty($courses)) {
    die('No registered courses found.');
}

$totalUnits = array_sum(array_column($courses, 'course_unit'));

// Create PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Highland College of Technology');
$pdf->SetTitle('Course Registration Form');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

// Header
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'HIGHLAND COLLEGE OF TECHNOLOGY', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, 'SAMONDA, IBADAN, NIGERIA', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'COURSE REGISTRATION FORM', 0, 1, 'C');
$pdf->Ln(5);

// Student Information
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 8, 'DEPARTMENT:', 0, 0, 'L');
$pdf->Cell(100, 8, strtoupper($student['Department']), 'B', 1, 'L');
$pdf->Ln(2);

$pdf->Cell(80, 8, 'STUDENT REGISTRATION NUMBER/MATRIC NUMBER:', 0, 0, 'L');
$pdf->Cell(20, 8, $student['Matric_No'], 'B', 1, 'C');
$pdf->Ln(5);

$pdf->Cell(20, 8, 'SURNAME:', 0, 0, 'L');
$pdf->Cell(60, 8, strtoupper($student['last_name']), 'B', 0, 'L');
$pdf->Cell(20, 8, 'SEMESTER:', 0, 0, 'L');
$pdf->Cell(30, 8, $currentSemester, 'B', 0, 'C');
$pdf->Cell(15, 8, 'BLOCK:', 0, 0, 'L');
$pdf->Cell(25, 8, '', 'B', 1, 'C');
$pdf->Ln(2);

$pdf->Cell(25, 8, 'OTHER NAMES:', 0, 0, 'L');
$pdf->Cell(60, 8, strtoupper($student['first_name']), 'B', 0, 'L');
$pdf->Cell(15, 8, 'FIRST:', 0, 0, 'L');
$pdf->Cell(15, 8, '', 'B', 0, 'C');
$pdf->Cell(20, 8, 'SECOND:', 0, 0, 'L');
$pdf->Cell(15, 8, '', 'B', 1, 'C');
$pdf->Ln(5);

$pdf->Cell(30, 8, 'LEVEL (ND1/ND2):', 0, 0, 'L');
$pdf->Cell(40, 8, $student['Level'], 'B', 0, 'L');
$pdf->Cell(20, 8, 'SESSION:', 0, 0, 'L');
$pdf->Cell(40, 8, $currentYear, 'B', 1, 'L');
$pdf->Ln(5);

// Course Table
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(25, 10, 'COURSE CODE', 1, 0, 'C');
$pdf->Cell(80, 10, 'COURSE TITLE', 1, 0, 'C');
$pdf->Cell(20, 10, 'NO. OF UNITS', 1, 0, 'C');
$pdf->Cell(35, 10, 'STUDENT\'S SIGNATURE', 1, 0, 'C');
$pdf->Cell(35, 10, 'LECTURER\'S SIGNATURE & DATE', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 8);
foreach ($courses as $course) {
    $pdf->Cell(25, 8, $course['course_code'], 1, 0, 'C');
    $pdf->Cell(80, 8, $course['course_title'], 1, 0, 'L');
    $pdf->Cell(20, 8, $course['course_unit'], 1, 0, 'C');
    $pdf->Cell(35, 8, '', 1, 0, 'C');
    $pdf->Cell(35, 8, '', 1, 1, 'C');
}

// Add empty rows to fill the form
for ($i = count($courses); $i < 15; $i++) {
    $pdf->Cell(25, 8, '', 1, 0, 'C');
    $pdf->Cell(80, 8, '', 1, 0, 'L');
    $pdf->Cell(20, 8, '', 1, 0, 'C');
    $pdf->Cell(35, 8, '', 1, 0, 'C');
    $pdf->Cell(35, 8, '', 1, 1, 'C');
}

$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 8, 'SEMESTER TOTAL UNITS = ' . $totalUnits, 0, 1, 'C');
$pdf->Ln(5);

// Signature section
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 8, 'FOR OFFICIAL USE:', 0, 1, 'L');
$pdf->Ln(2);
$pdf->Cell(40, 8, 'BURSAR\'S SIGNATURE:', 0, 0, 'L');
$pdf->Cell(60, 8, '', 'B', 0, 'L');
$pdf->Cell(15, 8, 'DATE:', 0, 0, 'L');
$pdf->Cell(40, 8, '', 'B', 1, 'L');
$pdf->Ln(5);
$pdf->Cell(40, 8, 'DEAN\'S SIGNATURE:', 0, 0, 'L');
$pdf->Cell(60, 8, '', 'B', 0, 'L');
$pdf->Cell(15, 8, 'DATE:', 0, 0, 'L');
$pdf->Cell(40, 8, '', 'B', 1, 'L');

if ($format === 'pdf') {
    // Output PDF
    $filename = 'Course_Registration_' . $student['Matric_No'] . '_' . $currentSemester . '.pdf';
    $pdf->Output($filename, 'D');
} else {
    // Generate DOCX
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    
    // Header
    $section->addText('HIGHLAND COLLEGE OF TECHNOLOGY', array('bold' => true, 'size' => 16), array('alignment' => 'center'));
    $section->addText('SAMONDA, IBADAN, NIGERIA', array('size' => 12), array('alignment' => 'center'));
    $section->addTextBreak();
    $section->addText('COURSE REGISTRATION FORM', array('bold' => true, 'size' => 14), array('alignment' => 'center'));
    $section->addTextBreak(2);
    
    // Student Information
    $section->addText('DEPARTMENT: ' . strtoupper($student['Department']));
    $section->addText('STUDENT REGISTRATION NUMBER/MATRIC NUMBER: ' . $student['Matric_No']);
    $section->addTextBreak();
    $section->addText('SURNAME: ' . strtoupper($student['last_name']) . '    SEMESTER: ' . $currentSemester);
    $section->addText('OTHER NAMES: ' . strtoupper($student['first_name']));
    $section->addTextBreak();
    $section->addText('LEVEL: ' . $student['Level'] . '    SESSION: ' . $currentYear);
    $section->addTextBreak(2);
    
    // Course Table
    $table = $section->addTable();
    $table->addRow();
    $table->addCell(2000)->addText('COURSE CODE', array('bold' => true));
    $table->addCell(4000)->addText('COURSE TITLE', array('bold' => true));
    $table->addCell(1500)->addText('NO. OF UNITS', array('bold' => true));
    $table->addCell(2000)->addText('STUDENT SIGNATURE', array('bold' => true));
    $table->addCell(2500)->addText('LECTURER SIGNATURE & DATE', array('bold' => true));
    
    foreach ($courses as $course) {
        $table->addRow();
        $table->addCell(2000)->addText($course['course_code']);
        $table->addCell(4000)->addText($course['course_title']);
        $table->addCell(1500)->addText($course['course_unit']);
        $table->addCell(2000)->addText('');
        $table->addCell(2500)->addText('');
    }
    
    $section->addTextBreak();
    $section->addText('SEMESTER TOTAL UNITS = ' . $totalUnits, array('bold' => true), array('alignment' => 'center'));
    $section->addTextBreak(2);
    
    // Signature section
    $section->addText('FOR OFFICIAL USE:');
    $section->addText('BURSAR\'S SIGNATURE: ________________    DATE: ________________');
    $section->addText('DEAN\'S SIGNATURE: ________________    DATE: ________________');
    
    // Save and download
    $filename = 'Course_Registration_' . $student['Matric_No'] . '_' . $currentSemester . '.docx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save('php://output');
}
?>