<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$studentId = $_GET['student_id'] ?? null;

if (!$studentId) {
    echo json_encode(['error' => 'Student ID required']);
    exit();
}

try {
    // Get enrolled courses count
    $stmt = $pdo->prepare("SELECT COUNT(*) as course_count FROM course_regtbl WHERE student_id = ?");
    $stmt->execute([$studentId]);
    $courseData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get current semester (latest academic_year + semester combination)
    $currentSemStmt = $pdo->prepare("
        SELECT academic_year, semester 
        FROM evaluationtbl 
        WHERE student_id = ? 
        ORDER BY academic_year DESC, semester DESC 
        LIMIT 1
    ");
    $currentSemStmt->execute([$studentId]);
    $currentSem = $currentSemStmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate Semester GPA (current semester only)
    $semesterGPA = 'N/A';
    if ($currentSem) {
        $semGpaStmt = $pdo->prepare("
            SELECT 
                SUM(grade_point * credit_units) as total_quality_points,
                SUM(credit_units) as total_credits
            FROM evaluationtbl 
            WHERE student_id = ? 
                AND academic_year = ? 
                AND semester = ?
                AND grade_point IS NOT NULL 
                AND credit_units IS NOT NULL
        ");
        $semGpaStmt->execute([$studentId, $currentSem['academic_year'], $currentSem['semester']]);
        $semData = $semGpaStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($semData['total_credits'] > 0) {
            $semesterGPA = number_format($semData['total_quality_points'] / $semData['total_credits'], 2);
        }
    }
    
    // Calculate Cumulative CGPA (all semesters)
    $cgpaStmt = $pdo->prepare("
        SELECT 
            SUM(grade_point * credit_units) as total_quality_points,
            SUM(credit_units) as total_credits
        FROM evaluationtbl 
        WHERE student_id = ? 
            AND grade_point IS NOT NULL 
            AND credit_units IS NOT NULL
    ");
    $cgpaStmt->execute([$studentId]);
    $cgpaData = $cgpaStmt->fetch(PDO::FETCH_ASSOC);
    
    $cgpa = 'N/A';
    if ($cgpaData['total_credits'] > 0) {
        $cgpa = number_format($cgpaData['total_quality_points'] / $cgpaData['total_credits'], 2);
    }
    
    echo json_encode([
        'success' => true,
        'courses_enrolled' => $courseData['course_count'] ?? 0,
        'semester_gpa' => $semesterGPA,
        'cgpa' => $cgpa
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
