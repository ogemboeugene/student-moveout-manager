<?php
/**
 * Database Connection Test Script
 * Use this to debug database connection issues
 */

echo "<h1>Database Connection Debug Test</h1>";

echo "<h2>Environment Variables:</h2>";
echo "<pre>";
echo "DB_HOST: " . (getenv('DB_HOST') ?: 'NOT SET') . "\n";
echo "DB_PORT: " . (getenv('DB_PORT') ?: 'NOT SET') . "\n";
echo "DB_USERNAME: " . (getenv('DB_USERNAME') ?: 'NOT SET') . "\n";
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') ? 'SET (hidden for security)' : 'NOT SET') . "\n";
echo "DB_DATABASE: " . (getenv('DB_DATABASE') ?: 'NOT SET') . "\n";
echo "</pre>";

echo "<h2>Config File Test:</h2>";
$configPath = __DIR__ . '/config/config.php';
if (file_exists($configPath)) {
    echo "✅ Config file exists<br>";
    $config = require $configPath;
    if (isset($config['database'])) {
        echo "✅ Database config found<br>";
        $dbConfig = $config['database'];
        echo "<pre>";
        echo "Host: " . ($dbConfig['host'] ?: 'EMPTY') . "\n";
        echo "Port: " . ($dbConfig['port'] ?: 'EMPTY') . "\n";
        echo "Username: " . ($dbConfig['username'] ?: 'EMPTY') . "\n";
        echo "Password: " . ($dbConfig['password'] ? 'SET (hidden)' : 'EMPTY') . "\n";
        echo "Database: " . ($dbConfig['database'] ?: 'EMPTY') . "\n";
        echo "</pre>";
    } else {
        echo "❌ Database config not found in config file<br>";
    }
} else {
    echo "❌ Config file not found at: " . $configPath . "<br>";
}

echo "<h2>Database Connection Test:</h2>";
try {
    if (!isset($config) || !isset($config['database'])) {
        throw new Exception("Config not loaded or database config missing");
    }
    
    $dbConfig = $config['database'];
    
    // Build DSN
    $dsn = "mysql:host={$dbConfig['host']}";
    if (!empty($dbConfig['port'])) {
        $dsn .= ";port={$dbConfig['port']}";
    }
    $dsn .= ";dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
    
    echo "DSN: " . $dsn . "<br>";
    echo "Attempting connection...<br>";
    
    $pdo = new PDO($dsn, 
        $dbConfig['username'], 
        $dbConfig['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 30
        ]
    );
    
    echo "✅ <strong>Database connection successful!</strong><br>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT VERSION() as version, NOW() as server_time");
    $result = $stmt->fetch();
    echo "MySQL Version: " . $result['version'] . "<br>";
    echo "Server Time: " . $result['server_time'] . "<br>";
    
} catch (PDOException $e) {
    echo "❌ <strong>Database connection failed!</strong><br>";
    echo "PDO Error: " . $e->getMessage() . "<br>";
    echo "Error Code: " . $e->getCode() . "<br>";
} catch (Exception $e) {
    echo "❌ <strong>General error!</strong><br>";
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<h2>System Information:</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "PDO Available: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "\n";
echo "PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "</pre>";
?>
