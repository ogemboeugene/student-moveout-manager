<?php
// Include all necessary classes
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Student.php';
require_once __DIR__ . '/Call.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Validator.php';

// Initialize authentication
Auth::init();
