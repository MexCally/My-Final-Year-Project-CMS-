<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit;
}

$student_id = $_SESSION['student_id'];

try {
    // Get student info
    $stmt = $pdo->prepare("SELECT * FROM studenttbl WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get published grades
    $stmt = $pdo->prepare("
        SELECT 
            e.ca_score, e.test_score, e.exam_score, e.total_score,
            e.grade, e.grade_point, e.academic_year, e.semester,
            c.course_code, c.course_title, c.course_unit
        FROM evaluationtbl e
        JOIN coursetbl c ON e.course_id = c.course_id
        JOIN grade_submissions gs ON e.course_id = gs.course_id AND e.lecturer_id = gs.lecturer_id
        WHERE e.student_id = ? AND gs.published = 1
        ORDER BY e.academic_year DESC, e.semester DESC, c.course_code
    ");
    $stmt->execute([$student_id]);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate GPA
    $totalPoints = 0;
    $totalUnits = 0;
    foreach ($grades as $grade) {
        $totalPoints += $grade['grade_point'] * $grade['course_unit'];
        $totalUnits += $grade['course_unit'];
    }
    $gpa = $totalUnits > 0 ? round($totalPoints / $totalUnits, 2) : 0;
    
    // Generate PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="transcript_' . $student['Matric_No'] . '.pdf"');
    
    // Simple PDF generation (you can use libraries like TCPDF for better formatting)
    $pdf_content = "ACADEMIC TRANSCRIPT\n\n";
    $pdf_content .= "Student: {$student['first_name']} {$student['last_name']}\n";
    $pdf_content .= "Matric No: {$student['Matric_No']}\n";
    $pdf_content .= "Department: {$student['Department']}\n";
    $pdf_content .= "Level: {$student['Level']}\n\n";
    $pdf_content .= "CGPA: $gpa\n\n";
    $pdf_content .= "COURSES:\n";
    
    foreach ($grades as $grade) {
        $pdf_content .= "{$grade['course_code']} - {$grade['course_title']} ({$grade['course_unit']} units) - Grade: {$grade['grade']} (GP: {$grade['grade_point']})\n";
    }
    
    echo $pdf_content;
    
} catch (PDOException $e) {
    echo "Error generating transcript";
}
?>