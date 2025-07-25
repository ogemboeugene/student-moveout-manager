<?php
require_once '../includes/init.php';

header('Content-Type: application/json');

try {
    $student = new Student();
    $call = new Call();
    
    // Clean up expired calls
    $call->removeExpired();
    
    $totalStudents = $student->getCount();
    $activeCalls = $call->getCallsCount();
    
    echo json_encode([
        'success' => true,
        'total_students' => $totalStudents,
        'active_calls' => $activeCalls,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch statistics',
        'error' => $e->getMessage()
    ]);
}
