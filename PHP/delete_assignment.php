<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturer_id = $_SESSION['lecturer_id'];
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $assignment_id = intval($input['assignment_id']);
    
    if (empty($assignment_id)) {
        echo json_encode(['success' => false, 'message' => 'Assignment ID is required.']);
        exit();
    }
    
    try {
        // Verify assignment belongs to this lecturer
        $stmt = $pdo->prepare("SELECT assignment_id FROM assignmenttbl WHERE assignment_id = ? AND lecturer_id = ?");
        $stmt->execute([$assignment_id, $lecturer_id]);
        
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this assignment.']);
            exit();
        }
        
        // Delete assignment
        $stmt = $pdo->prepare("DELETE FROM assignmenttbl WHERE assignment_id = ? AND lecturer_id = ?");
        $stmt->execute([$assignment_id, $lecturer_id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Assignment deleted successfully!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Assignment not found or already deleted.'
            ]);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete assignment: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
?>