<?php
session_start();
require_once '../config/db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

// Validate required fields
$required_fields = ['student_id', 'course_id', 'ca_score', 'test_score', 'exam_score', 'credit_units'];
foreach ($required_fields as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit();
    }
}

$student_id = $input['student_id'];
$course_id = $input['course_id'];
$ca_score = floatval($input['ca_score']);
$test_score = floatval($input['test_score']);
$exam_score = floatval($input['exam_score']);
$credit_units = intval($input['credit_units']);
$eval_id = $input['eval_id'] ?? null;

// Validate score ranges
if ($ca_score < 0 || $ca_score > 30) {
    echo json_encode(['success' => false, 'message' => 'CA score must be between 0 and 30']);
    exit();
}
if ($test_score < 0 || $test_score > 20) {
    echo json_encode(['success' => false, 'message' => 'Test score must be between 0 and 20']);
    exit();
}
if ($exam_score < 0 || $exam_score > 50) {
    echo json_encode(['success' => false, 'message' => 'Exam score must be between 0 and 50']);
    exit();
}

// Verify lecturer owns this course
try {
    $stmt = $pdo->prepare("SELECT course_id FROM coursetbl WHERE course_id = ? AND lecturer_id = ?");
    $stmt->execute([$course_id, $lecturer_id]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied to this course']);
        exit();
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

// Calculate total score
$total_score = $ca_score + $test_score + $exam_score;

// Calculate grade letter using Excel formula logic
function calculateGradeLetter($total_score) {
    if ($total_score < 40) return 'F';
    if ($total_score < 45) return 'E';
    if ($total_score < 50) return 'D';
    if ($total_score < 60) return 'C';
    if ($total_score < 70) return 'B';
    return 'A';
}

// Calculate grade point using Excel formula logic
function calculateGradePoint($total_score) {
    if ($total_score < 40) return 0;
    if ($total_score < 45) return 1;
    if ($total_score < 50) return 2;
    if ($total_score < 60) return 3;
    if ($total_score < 70) return 4;
    return 5;
}

$grade_letter = calculateGradeLetter($total_score);
$grade_point = calculateGradePoint($total_score);
$quality_points = $grade_point * $credit_units;

// Get current academic year and semester
$current_year = date('Y');
$academic_year = $current_year . '/' . ($current_year + 1);
$current_month = date('n');
$semester = ($current_month >= 1 && $current_month <= 6) ? 'Second' : 'First';

try {
    $pdo->beginTransaction();
    
    if ($eval_id) {
        // Update existing record
        $stmt = $pdo->prepare("
            UPDATE evaluationtbl 
            SET ca_score = ?, test_score = ?, exam_score = ?, total_score = ?, 
                grade = ?, grade_point = ?, quality_points = ?, 
                entered_by = ?, entered_at = NOW()
            WHERE eval_id = ? AND student_id = ? AND course_id = ?
        ");
        $stmt->execute([
            $ca_score, $test_score, $exam_score, $total_score,
            $grade_letter, $grade_point, $quality_points,
            $lecturer_id, $eval_id, $student_id, $course_id
        ]);
    } else {
        // Insert new record
        $stmt = $pdo->prepare("
            INSERT INTO evaluationtbl 
            (student_id, course_id, lecturer_id, academic_year, semester, 
             ca_score, test_score, exam_score, total_score, grade, grade_point, 
             credit_units, quality_points, entered_by, entered_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $student_id, $course_id, $lecturer_id, $academic_year, $semester,
            $ca_score, $test_score, $exam_score, $total_score, $grade_letter, 
            $grade_point, $credit_units, $quality_points, $lecturer_id
        ]);
    }
    
    // Also update/insert into resulttbl for compatibility
    $stmt = $pdo->prepare("
        INSERT INTO resulttbl 
        (student_id, course_id, ca_score, test_score, exam_score, total_score, 
         grade_letter, academic_year, semester, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
        ca_score = VALUES(ca_score),
        test_score = VALUES(test_score),
        exam_score = VALUES(exam_score),
        total_score = VALUES(total_score),
        grade_letter = VALUES(grade_letter),
        created_at = NOW()
    ");
    $stmt->execute([
        $student_id, $course_id, $ca_score, $test_score, $exam_score, 
        $total_score, $grade_letter, $academic_year, $semester
    ]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Grade saved successfully',
        'data' => [
            'total_score' => $total_score,
            'grade_letter' => $grade_letter,
            'grade_point' => $grade_point,
            'quality_points' => $quality_points
        ]
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save grade: ' . $e->getMessage()]);
}
?>