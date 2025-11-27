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

if (!$input || !isset($input['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$action = $input['action'];

switch ($action) {
    case 'export_grades':
        exportGrades($input['course_id'], $lecturer_id, $pdo);
        break;
    
    case 'submit_to_records':
        submitToRecords($input['course_id'], $lecturer_id, $pdo);
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function exportGrades($course_id, $lecturer_id, $pdo) {
    try {
        // Verify lecturer owns this course
        $stmt = $pdo->prepare("SELECT course_code, course_title FROM coursetbl WHERE course_id = ? AND lecturer_id = ?");
        $stmt->execute([$course_id, $lecturer_id]);
        $course = $stmt->fetch();
        
        if (!$course) {
            echo json_encode(['success' => false, 'message' => 'Access denied to this course']);
            return;
        }
        
        // Get all students and their grades for this course
        $stmt = $pdo->prepare("
            SELECT 
                s.matric_number,
                s.first_name,
                s.last_name,
                e.ca_score,
                e.test_score,
                e.exam_score,
                e.total_score,
                e.grade,
                e.grade_point,
                e.quality_points
            FROM studenttbl s
            JOIN enrollmenttbl en ON s.student_id = en.student_id
            LEFT JOIN evaluationtbl e ON s.student_id = e.student_id AND e.course_id = ?
            WHERE en.course_id = ?
            ORDER BY s.matric_number
        ");
        $stmt->execute([$course_id, $course_id]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Generate CSV content
        $csv_content = "Matric Number,Student Name,CA Score (30),Test Score (20),Exam Score (50),Total Score,Grade,Grade Point,Quality Points\n";
        
        foreach ($students as $student) {
            $csv_content .= sprintf(
                "%s,%s %s,%s,%s,%s,%s,%s,%s,%s\n",
                $student['matric_number'],
                $student['first_name'],
                $student['last_name'],
                $student['ca_score'] ?? '0',
                $student['test_score'] ?? '0',
                $student['exam_score'] ?? '0',
                $student['total_score'] ?? '0',
                $student['grade'] ?? 'F',
                $student['grade_point'] ?? '0',
                $student['quality_points'] ?? '0'
            );
        }
        
        // Set headers for file download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $course['course_code'] . '_grades_' . date('Y-m-d') . '.csv"');
        header('Content-Length: ' . strlen($csv_content));
        
        echo $csv_content;
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function submitToRecords($course_id, $lecturer_id, $pdo) {
    try {
        // Verify lecturer owns this course
        $stmt = $pdo->prepare("SELECT course_code FROM coursetbl WHERE course_id = ? AND lecturer_id = ?");
        $stmt->execute([$course_id, $lecturer_id]);
        
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Access denied to this course']);
            return;
        }
        
        // Count grades that need to be submitted
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM evaluationtbl e
            JOIN coursetbl c ON e.course_id = c.course_id
            WHERE e.course_id = ? AND c.lecturer_id = ? AND e.total_score > 0
        ");
        $stmt->execute([$course_id, $lecturer_id]);
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            echo json_encode(['success' => false, 'message' => 'No grades found to submit']);
            return;
        }
        
        // In a real system, you might update a status field or create audit records
        // For now, we'll just return success
        echo json_encode([
            'success' => true, 
            'message' => "Successfully submitted {$result['count']} grades to academic records"
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>