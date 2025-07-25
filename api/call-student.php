<?php
require_once '../includes/init.php';

header('Content-Type: application/json');

// Require teacher or dean role
if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$user = Auth::getUser();
if (!in_array($user['role'], ['teacher', 'dean'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate CSRF token
    if (!Auth::validateCSRFToken($input['csrf_token'] ?? '')) {
        throw new Exception('Invalid security token');
    }
    
    $studentId = (int)($input['student_id'] ?? 0);
    
    if (!$studentId) {
        throw new Exception('Invalid student ID');
    }
    
    // Check if student exists
    $student = new Student();
    $studentRecord = $student->getById($studentId);
    
    if (!$studentRecord) {
        throw new Exception('Student not found');
    }
    
    // Check if student is already called
    $call = new Call();
    if ($call->isStudentCalled($studentId)) {
        throw new Exception('Student is already called');
    }
    
    // Create the call
    if ($call->create($studentId, $user['id'])) {
        echo json_encode([
            'success' => true,
            'message' => 'Student called successfully',
            'student' => [
                'id' => $studentRecord['id'],
                'name' => $studentRecord['name'],
                'grade' => $studentRecord['grade']
            ]
        ]);
    } else {
        throw new Exception('Failed to create call');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
