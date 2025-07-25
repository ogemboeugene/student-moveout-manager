<?php
require_once '../includes/init.php';

header('Content-Type: application/json');

try {
    $call = new Call();
    
    // Clean up expired calls first
    $call->removeExpired();
    
    // Get active calls
    $activeCalls = $call->getActiveCalls();
    
    echo json_encode([
        'success' => true,
        'students' => $activeCalls,
        'count' => count($activeCalls),
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch called students',
        'error' => $e->getMessage()
    ]);
}
