<?php
// Simple test file to check PHP functionality
echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Test config file loading
try {
    $config = require_once __DIR__ . '/config/config.php';
    echo "Config loaded successfully!<br>";
    echo "Database: " . $config['database']['database'] . "<br>";
} catch (Exception $e) {
    echo "Config error: " . $e->getMessage() . "<br>";
}

// Test database connection
try {
    require_once __DIR__ . '/includes/Database.php';
    $db = Database::getInstance();
    echo "Database connection successful!<br>";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

echo "Test completed.";
?>
