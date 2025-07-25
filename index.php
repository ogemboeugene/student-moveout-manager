<?php
// Main application entry point
require_once 'includes/init.php';

// Check if user is logged in
if (Auth::isLoggedIn()) {
    $user = Auth::getUser();
    
    // Redirect based on role
    if ($user['role'] === 'dean') {
        header('Location: /admin/');
    } else {
        header('Location: /teacher/');
    }
} else {
    // Redirect to login
    header('Location: /auth/login.php');
}

exit;
