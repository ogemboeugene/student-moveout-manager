<?php
/**
 * Database Configuration
 */

return [
    // 'database' => [
    //     'host' => 'localhost',
    //     'username' => 'root',
    //     'password' => '',
    //     'database' => 'moveout_manager',
    //     'charset' => 'utf8mb4'
    // ]
    'database' => [
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT'),
        'username' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
        'database' => getenv('DB_DATABASE'),
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
