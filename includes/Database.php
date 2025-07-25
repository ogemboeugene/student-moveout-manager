<?php
/**
 * Database Connection Class
 */

class Database {
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct() {
        $configPath = __DIR__ . '/../config/config.php';
        if (!file_exists($configPath)) {
            throw new Exception("Configuration file not found: " . $configPath);
        }
        
        $this->config = require $configPath;
        
        if (!is_array($this->config)) {
            throw new Exception("Configuration file must return an array");
        }
        
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        try {
            // Check if database config exists
            if (!isset($this->config['database'])) {
                throw new Exception("Database configuration not found in config file");
            }
            
            $dbConfig = $this->config['database'];
            
            // Debug logging - show what environment variables are being read
            error_log("=== DATABASE CONNECTION DEBUG ===");
            error_log("DB_HOST from getenv: " . (getenv('DB_HOST') ?: 'NOT SET'));
            error_log("DB_PORT from getenv: " . (getenv('DB_PORT') ?: 'NOT SET'));
            error_log("DB_USERNAME from getenv: " . (getenv('DB_USERNAME') ?: 'NOT SET'));
            error_log("DB_PASSWORD from getenv: " . (getenv('DB_PASSWORD') ? 'SET (hidden)' : 'NOT SET'));
            error_log("DB_DATABASE from getenv: " . (getenv('DB_DATABASE') ?: 'NOT SET'));
            
            // Debug logging - show actual config values being used
            error_log("Config host: " . ($dbConfig['host'] ?: 'EMPTY'));
            error_log("Config port: " . ($dbConfig['port'] ?: 'EMPTY'));
            error_log("Config username: " . ($dbConfig['username'] ?: 'EMPTY'));
            error_log("Config password: " . ($dbConfig['password'] ? 'SET (hidden)' : 'EMPTY'));
            error_log("Config database: " . ($dbConfig['database'] ?: 'EMPTY'));
            
            // Include port in DSN if it exists
            $dsn = "mysql:host={$dbConfig['host']}";
            if (!empty($dbConfig['port'])) {
                $dsn .= ";port={$dbConfig['port']}";
            }
            $dsn .= ";dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            
            error_log("DSN: " . $dsn);
            error_log("Attempting connection...");
            
            $this->connection = new PDO($dsn, 
                $dbConfig['username'], 
                $dbConfig['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 30
                ]
            );
            
            error_log("Database connection successful!");
            error_log("=== END DATABASE DEBUG ===");
            
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            error_log("PDO Error Code: " . $e->getCode());
            error_log("=== END DATABASE DEBUG ===");
            die("Database connection failed: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("General Exception: " . $e->getMessage());
            error_log("=== END DATABASE DEBUG ===");
            die("Configuration error: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollback() {
        return $this->connection->rollback();
    }
}
