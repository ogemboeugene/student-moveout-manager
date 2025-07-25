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
            
            // Include port in DSN if it exists
            $dsn = "mysql:host={$dbConfig['host']}";
            if (!empty($dbConfig['port'])) {
                $dsn .= ";port={$dbConfig['port']}";
            }
            $dsn .= ";dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            
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
            
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Configuration error: " . $e->getMessage());
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
