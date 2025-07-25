<?php
require_once '../includes/init.php';

header('Content-Type: application/json');

try {
    $call = new Call();
    
    // Remove expired calls
    $removedCount = $call->removeExpired();
    
    echo json_encode([
        'success' => true,
        'removed_count' => $removedCount,
        'message' => "Removed {$removedCount} expired calls",
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to cleanup expired calls',
        'error' => $e->getMessage()
    ]);
}
