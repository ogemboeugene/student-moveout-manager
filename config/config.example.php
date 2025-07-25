<?php
/**
 * Database Configuration
 * Copy this file to config.php and update with your settings
 */

return [
    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'student_moveout_manager',
        'charset' => 'utf8mb4'
    ],
    
    'session' => [
        'timeout' => 3600, // 1 hour
        'name' => 'STUDENT_MOVEOUT_SESSION'
    ],
    
    'security' => [
        'csrf_token_name' => 'csrf_token',
        'password_min_length' => 6
    ],
    
    'app' => [
        'name' => 'Student Move-Out Manager',
        'version' => '1.0.0',
        'timezone' => 'America/New_York',
        'call_duration_minutes' => 5
    ]
];
