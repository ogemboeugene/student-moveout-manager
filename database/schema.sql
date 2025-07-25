-- Student Move-Out Manager Database Schema
-- MySQL 8+ Compatible

SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if they exist
DROP TABLE IF EXISTS calls;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS students;

-- Create students table
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    grade VARCHAR(20) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_grade (grade),
    INDEX idx_status (status)
);

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('dean', 'teacher') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
);

-- Create calls table
CREATE TABLE calls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    teacher_id INT NOT NULL,
    called_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP GENERATED ALWAYS AS (DATE_ADD(called_at, INTERVAL 5 MINUTE)) STORED,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_teacher_id (teacher_id),
    INDEX idx_called_at (called_at),
    INDEX idx_expires_at (expires_at),
    UNIQUE KEY unique_active_call (student_id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password_hash, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dean');

-- Insert sample teacher (password: teacher123)
INSERT INTO users (username, password_hash, role) VALUES 
('teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher');

-- Insert sample students
INSERT INTO students (name, grade) VALUES 
('John Doe', 'Grade 10'),
('Jane Smith', 'Grade 11'),
('Bob Johnson', 'Grade 9'),
('Alice Brown', 'Grade 12'),
('Charlie Wilson', 'Grade 10'),
('Diana Davis', 'Grade 11'),
('Frank Miller', 'Grade 9'),
('Grace Taylor', 'Grade 12');

SET FOREIGN_KEY_CHECKS = 1;
