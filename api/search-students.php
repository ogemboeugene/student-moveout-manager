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

try {
    $student = new Student();
    $call = new Call();
    
    $query = $_GET['q'] ?? '';
    
    if ($query) {
        $students = $student->search($query);
    } else {
        $students = $student->getAll();
    }
    
    // Add call status to each student
    foreach ($students as &$studentRecord) {
        $studentRecord['is_called'] = $call->isStudentCalled($studentRecord['id']);
    }
    
    echo json_encode([
        'success' => true,
        'students' => $students,
        'query' => $query,
        'count' => count($students)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to search students',
        'error' => $e->getMessage()
    ]);
}
