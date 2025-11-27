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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $course_id = $input['course_id'] ?? null;
    $grades = $input['grades'] ?? [];
    
    if (!$course_id || empty($grades)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    try {
        // Verify that the course belongs to this lecturer
        $stmt = $pdo->prepare("SELECT course_id FROM coursetbl WHERE course_id = ? AND lecturer_id = ?");
        $stmt->execute([$course_id, $lecturer_id]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access to course']);
            exit();
        }
        
        $pdo->beginTransaction();
        
        $saved_count = 0;
        
        foreach ($grades as $grade_data) {
            $student_id = $grade_data['student_id'] ?? null;
            $test_score = $grade_data['test_score'] ?? 0;
            $assignment_score = $grade_data['assignment_score'] ?? 0;
            $exam_score = $grade_data['exam_score'] ?? 0;
            $result_id = $grade_data['result_id'] ?? null;
            
            if (!$student_id) continue;
            
            // Calculate total score
            $total_score = ($test_score * 0.3) + ($assignment_score * 0.3) + ($exam_score * 0.4);
            
            // Calculate grade
            function calculateGrade($total_score) {
                if ($total_score >= 90) return 'A+';
                if ($total_score >= 80) return 'A';
                if ($total_score >= 70) return 'B';
                if ($total_score >= 60) return 'C';
                if ($total_score >= 50) return 'D';
                return 'F';
            }
            
            $grade = calculateGrade($total_score);
            
            if ($result_id) {
                // Update existing record
                $stmt = $pdo->prepare("
                    UPDATE resulttbl 
                    SET test_score = ?, assignment_score = ?, exam_score = ?, total_score = ?, grade = ?, updated_at = NOW()
                    WHERE result_id = ? AND student_id = ? AND course_id = ?
                ");
                $stmt->execute([$test_score, $assignment_score, $exam_score, $total_score, $grade, $result_id, $student_id, $course_id]);
            } else {
                // Insert new record
                $stmt = $pdo->prepare("
                    INSERT INTO resulttbl (student_id, course_id, test_score, assignment_score, exam_score, total_score, grade, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$student_id, $course_id, $test_score, $assignment_score, $exam_score, $total_score, $grade]);
            }
            
            $saved_count++;
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => "Successfully saved grades for {$saved_count} students",
            'saved_count' => $saved_count
        ]);
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>