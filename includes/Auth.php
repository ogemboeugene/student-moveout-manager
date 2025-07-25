<?php
/**
 * Authentication and Session Management Class
 */

class Auth {
    private static $config = null;

    public static function init() {
        // Load config if not already loaded
        if (self::$config === null) {
            $configPath = __DIR__ . '/../config/config.php';
            if (!file_exists($configPath)) {
                throw new Exception("Configuration file not found: " . $configPath);
            }
            
            self::$config = require $configPath;
            
            if (!is_array(self::$config)) {
                throw new Exception("Configuration file must return an array");
            }
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            $sessionName = self::$config['session']['name'] ?? 'STUDENT_MOVEOUT_SESSION';
            session_name($sessionName);
            session_start();
        }
        
        // Set timezone with fallback
        $timezone = self::$config['app']['timezone'] ?? 'UTC';
        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
        }
    }

    public static function login($username, $password) {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? AND status = 'active'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Update last login
            $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            return true;
        }
        
        return false;
    }

    public static function logout() {
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    public static function isLoggedIn() {
        if (self::$config === null) {
            self::init();
        }
        
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
            return false;
        }
        
        // Check session timeout with fallback
        $timeout = self::$config['session']['timeout'] ?? 3600;
        if (time() - $_SESSION['login_time'] > $timeout) {
            self::logout();
            return false;
        }
        
        return true;
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            // Use relative path for login redirect
            $loginPath = '/student-moveout-manager/auth/login.php';
            header('Location: ' . $loginPath);
            exit;
        }
    }

    public static function requireRole($roles) {
        self::requireLogin();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!in_array($_SESSION['role'], $roles)) {
            header('HTTP/1.0 403 Forbidden');
            die('Access denied');
        }
    }

    public static function getUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
    }

    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
