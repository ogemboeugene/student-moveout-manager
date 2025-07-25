-- Sample data insertion script
-- Run this after creating the database schema

-- Remove or update the USE statement if your database is not named 'moveout_manager'
USE moveout_manager;

-- Insert sample users with proper password hashes
-- admin123 and teacher123 passwords
INSERT INTO users (username, password_hash, role, status) VALUES 
('admin', '$2y$10$j7TDvpzxQAbP.N85T.1FI.d0rcvt5D4rCirEAbUKGSaHIkRAzPqiu', 'dean', 'active'),
('teacher1', '$2y$10$ptTIwzzAwgA/MAX1ST9Ef.7YDn366FJsfwEZQJa394Q1UnjRszl1q', 'teacher', 'active')
ON DUPLICATE KEY UPDATE 
password_hash = VALUES(password_hash);

-- Insert additional sample teachers
INSERT INTO users (username, password_hash, role, status) VALUES 
('teacher2', '$2y$10$ptTIwzzAwgA/MAX1ST9Ef.7YDn366FJsfwEZQJa394Q1UnjRszl1q', 'teacher', 'active'),
('teacher3', '$2y$10$ptTIwzzAwgA/MAX1ST9Ef.7YDn366FJsfwEZQJa394Q1UnjRszl1q', 'teacher', 'active')
ON DUPLICATE KEY UPDATE 
password_hash = VALUES(password_hash);

-- Insert additional sample students (including Kenyan names)
INSERT INTO students (name, grade) VALUES 
('Michael Anderson', 'Grade 10'),
('Sarah Williams', 'Grade 11'),
('David Martinez', 'Grade 9'),
('Emily Rodriguez', 'Grade 12'),
('James Thompson', 'Grade 10'),
('Jessica Garcia', 'Grade 11'),
('William Lopez', 'Grade 9'),
('Ashley Hernandez', 'Grade 12'),
('Christopher Young', 'Grade 10'),
('Amanda King', 'Grade 11'),
('Matthew Wright', 'Grade 9'),
('Stephanie Scott', 'Grade 12'),
('Joshua Green', 'Grade 10'),
('Nicole Adams', 'Grade 11'),
('Daniel Baker', 'Grade 9'),
('Brian Otieno', 'Form 1'),
('Faith Wanjiku', 'Form 2'),
('Kevin Mwangi', 'Form 3'),
('Mercy Achieng', 'Form 4'),
('Samuel Kiptoo', 'Form 1'),
('Janet Njeri', 'Form 2'),
('Collins Ouma', 'Form 3'),
('Diana Chebet', 'Form 4'),
('George Kamau', 'Form 1'),
('Cynthia Muthoni', 'Form 2'),
('Elvis Mutua', 'Form 3'),
('Linet Atieno', 'Form 4'),
('Peter Maina', 'Form 1'),
('Sharon Wambui', 'Form 2'),
('Victor Kiplangat', 'Form 3'),
('Grace Nyambura', 'Form 4');

-- Note: All passwords are: admin123 or teacher123