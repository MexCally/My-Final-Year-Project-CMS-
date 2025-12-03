<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];
$format = $_GET['format'] ?? 'pdf';

// Fetch student details
$stmt = $pdo->prepare("SELECT student_id, Matric_No, first_name, last_name, email, Department, Level, academic_year FROM studenttbl WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    die('Student not found');
}

// Fetch results
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
    ORDER BY r.academic_year, r.semester, c.course_code
");
$resultStmt->execute([$student_id]);
$results = $resultStmt->fetchAll();

// Calculate GPA
$letter_points_map = ['A' => 5.0, 'B' => 4.0, 'C' => 3.0, 'D' => 2.0, 'E' => 1.0, 'F' => 0.0];
$total_points = 0.0;
$total_credits = 0;

foreach ($results as $row) {
    $raw_score = $row['total_score'] ?? null;
    $raw_letter = $row['grade_letter'] ?? '';
    $credits = (int)($row['course_unit'] ?? 3);
    
    $points = null;
    if ($raw_score !== null && is_numeric($raw_score)) {
        $score = (float)$raw_score;
        if ($score < 40) $points = 0.0;
        elseif ($score < 45) $points = 1.0;
        elseif ($score < 50) $points = 2.0;
        elseif ($score < 60) $points = 3.0;
        elseif ($score < 70) $points = 4.0;
        else $points = 5.0;
    } else {
        $letter = strtoupper(trim($raw_letter));
        if (isset($letter_points_map[$letter])) {
            $points = $letter_points_map[$letter];
        }
    }
    
    if ($points !== null) {
        $total_credits += $credits;
        $total_points += $credits * $points;
    }
}

$current_gpa = $total_credits > 0 ? number_format($total_points / $total_credits, 2) : 'N/A';

// Generate content based on format
$student_name = $student['first_name'] . ' ' . $student['last_name'];
$filename = 'transcript_' . $student['Matric_No'] . '_' . date('Y-m-d');

switch ($format) {
    case 'pdf':
        // For PDF, we'll generate HTML and let browser handle PDF conversion
        header('Content-Type: text/html');
        echo generateTranscriptHTML($student, $results, $current_gpa, $total_credits);
        break;
        
    case 'docx':
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '.docx"');
        echo generateTranscriptText($student, $results, $current_gpa, $total_credits);
        break;
        
    case 'xlsx':
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        echo generateTranscriptCSV($student, $results, $current_gpa, $total_credits);
        break;
        
    default:
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '.txt"');
        echo generateTranscriptText($student, $results, $current_gpa, $total_credits);
}

function generateTranscriptHTML($student, $results, $gpa, $credits) {
    $student_name = $student['first_name'] . ' ' . $student['last_name'];
    $html = '<!DOCTYPE html><html><head><title>Academic Transcript</title>';
    $html .= '<style>body{font-family:Arial,sans-serif;margin:20px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background-color:#f2f2f2;}.header{text-align:center;margin-bottom:30px;}.summary{margin-top:20px;}</style>';
    $html .= '</head><body>';
    $html .= '<div class="header"><h1>OFFICIAL ACADEMIC TRANSCRIPT</h1>';
    $html .= '<h2>Course Management System</h2></div>';
    $html .= '<p><strong>Student Name:</strong> ' . htmlspecialchars($student_name) . '</p>';
    $html .= '<p><strong>Matric Number:</strong> ' . htmlspecialchars($student['Matric_No']) . '</p>';
    $html .= '<p><strong>Department:</strong> ' . htmlspecialchars($student['Department']) . '</p>';
    $html .= '<p><strong>Level:</strong> ' . htmlspecialchars($student['Level']) . '</p>';
    $html .= '<p><strong>Generated:</strong> ' . date('F j, Y') . '</p>';
    
    $html .= '<table><thead><tr><th>Course Code</th><th>Course Title</th><th>Credits</th><th>Grade</th><th>Semester</th><th>Year</th></tr></thead><tbody>';
    
    foreach ($results as $result) {
        $grade = '';
        if ($result['total_score'] !== null && is_numeric($result['total_score'])) {
            $score = (float)$result['total_score'];
            if ($score < 40) $grade = 'F';
            elseif ($score < 45) $grade = 'E';
            elseif ($score < 50) $grade = 'D';
            elseif ($score < 60) $grade = 'C';
            elseif ($score < 70) $grade = 'B';
            else $grade = 'A';
        } else {
            $grade = $result['grade_letter'] ?? 'N/A';
        }
        
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($result['course_code']) . '</td>';
        $html .= '<td>' . htmlspecialchars($result['course_title']) . '</td>';
        $html .= '<td>' . htmlspecialchars($result['course_unit']) . '</td>';
        $html .= '<td>' . htmlspecialchars($grade) . '</td>';
        $html .= '<td>' . htmlspecialchars($result['semester']) . '</td>';
        $html .= '<td>' . htmlspecialchars($result['academic_year']) . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '<div class="summary">';
    $html .= '<p><strong>Total Credits Completed:</strong> ' . $credits . '</p>';
    $html .= '<p><strong>Cumulative GPA:</strong> ' . $gpa . '</p>';
    $html .= '<p><strong>Generated on:</strong> ' . date('F j, Y g:i A') . '</p>';
    $html .= '</div></body></html>';
    
    return $html;
}

function generateTranscriptText($student, $results, $gpa, $credits) {
    $student_name = $student['first_name'] . ' ' . $student['last_name'];
    $text = "OFFICIAL ACADEMIC TRANSCRIPT\n";
    $text .= "Course Management System\n";
    $text .= str_repeat("=", 50) . "\n\n";
    $text .= "Student Name: " . $student_name . "\n";
    $text .= "Matric Number: " . $student['Matric_No'] . "\n";
    $text .= "Department: " . $student['Department'] . "\n";
    $text .= "Level: " . $student['Level'] . "\n";
    $text .= "Generated: " . date('F j, Y') . "\n\n";
    
    $text .= str_repeat("-", 80) . "\n";
    $text .= sprintf("%-12s %-30s %-8s %-6s %-15s %-10s\n", "Course Code", "Course Title", "Credits", "Grade", "Semester", "Year");
    $text .= str_repeat("-", 80) . "\n";
    
    foreach ($results as $result) {
        $grade = '';
        if ($result['total_score'] !== null && is_numeric($result['total_score'])) {
            $score = (float)$result['total_score'];
            if ($score < 40) $grade = 'F';
            elseif ($score < 45) $grade = 'E';
            elseif ($score < 50) $grade = 'D';
            elseif ($score < 60) $grade = 'C';
            elseif ($score < 70) $grade = 'B';
            else $grade = 'A';
        } else {
            $grade = $result['grade_letter'] ?? 'N/A';
        }
        
        $text .= sprintf("%-12s %-30s %-8s %-6s %-15s %-10s\n",
            $result['course_code'],
            substr($result['course_title'], 0, 30),
            $result['course_unit'],
            $grade,
            $result['semester'],
            $result['academic_year']
        );
    }
    
    $text .= str_repeat("-", 80) . "\n";
    $text .= "Total Credits Completed: " . $credits . "\n";
    $text .= "Cumulative GPA: " . $gpa . "\n";
    $text .= "Generated on: " . date('F j, Y g:i A') . "\n";
    
    return $text;
}

function generateTranscriptCSV($student, $results, $gpa, $credits) {
    $student_name = $student['first_name'] . ' ' . $student['last_name'];
    $csv = "Course Code,Course Title,Credits,Grade,Semester,Year\n";
    
    foreach ($results as $result) {
        $grade = '';
        if ($result['total_score'] !== null && is_numeric($result['total_score'])) {
            $score = (float)$result['total_score'];
            if ($score < 40) $grade = 'F';
            elseif ($score < 45) $grade = 'E';
            elseif ($score < 50) $grade = 'D';
            elseif ($score < 60) $grade = 'C';
            elseif ($score < 70) $grade = 'B';
            else $grade = 'A';
        } else {
            $grade = $result['grade_letter'] ?? 'N/A';
        }
        
        $csv .= '"' . $result['course_code'] . '",';
        $csv .= '"' . $result['course_title'] . '",';
        $csv .= '"' . $result['course_unit'] . '",';
        $csv .= '"' . $grade . '",';
        $csv .= '"' . $result['semester'] . '",';
        $csv .= '"' . $result['academic_year'] . '"' . "\n";
    }
    
    $csv .= "\nSummary\n";
    $csv .= "Student Name," . $student_name . "\n";
    $csv .= "Matric Number," . $student['Matric_No'] . "\n";
    $csv .= "Total Credits," . $credits . "\n";
    $csv .= "Cumulative GPA," . $gpa . "\n";
    
    return $csv;
}
?>