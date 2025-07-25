<?php
/**
 * Validation Helper Class
 */

class Validator {
    public static function validateRequired($value, $fieldName) {
        if (empty(trim($value))) {
            return "{$fieldName} is required.";
        }
        return null;
    }

    public static function validateLength($value, $fieldName, $min = 1, $max = null) {
        $length = strlen(trim($value));
        
        if ($length < $min) {
            return "{$fieldName} must be at least {$min} characters long.";
        }
        
        if ($max && $length > $max) {
            return "{$fieldName} must not exceed {$max} characters.";
        }
        
        return null;
    }

    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }
        return null;
    }

    public static function validatePassword($password) {
        $config = require __DIR__ . '/../config/config.php';
        $minLength = $config['security']['password_min_length'];
        
        if (strlen($password) < $minLength) {
            return "Password must be at least {$minLength} characters long.";
        }
        
        return null;
    }

    public static function validateRole($role) {
        $validRoles = ['dean', 'teacher'];
        if (!in_array($role, $validRoles)) {
            return "Invalid role selected.";
        }
        return null;
    }

    public static function validateStatus($status) {
        $validStatuses = ['active', 'inactive'];
        if (!in_array($status, $validStatuses)) {
            return "Invalid status selected.";
        }
        return null;
    }

    public static function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function validateStudentData($name, $grade) {
        $errors = [];
        
        if ($error = self::validateRequired($name, 'Student name')) {
            $errors[] = $error;
        } else if ($error = self::validateLength($name, 'Student name', 2, 100)) {
            $errors[] = $error;
        }
        
        if ($error = self::validateRequired($grade, 'Grade')) {
            $errors[] = $error;
        } else if ($error = self::validateLength($grade, 'Grade', 1, 20)) {
            $errors[] = $error;
        }
        
        return $errors;
    }

    public static function validateUserData($username, $role, $status = 'active', $password = null) {
        $errors = [];
        
        if ($error = self::validateRequired($username, 'Username')) {
            $errors[] = $error;
        } else if ($error = self::validateLength($username, 'Username', 3, 50)) {
            $errors[] = $error;
        }
        
        if ($error = self::validateRole($role)) {
            $errors[] = $error;
        }
        
        if ($error = self::validateStatus($status)) {
            $errors[] = $error;
        }
        
        if ($password !== null && ($error = self::validatePassword($password))) {
            $errors[] = $error;
        }
        
        return $errors;
    }
}
