CREATE DATABASE IF NOT EXISTS campusly_portal;
USE campusly_portal;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    password_hash CHAR(64) NOT NULL
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    roll_number VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL,
    course VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS student_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL UNIQUE,
    file_type VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_student_files_student
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Demo account: username admin, password admin123.
INSERT INTO users (username, password_hash)
VALUES ('admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9')
ON DUPLICATE KEY UPDATE username = VALUES(username);

-- Sample student record requested for the assignment preview.
INSERT INTO students (name, roll_number, email, course, phone)
VALUES ('Mohammed Saif A', '24cs121', '123@gmail.com', 'Computer Science', '1234567890')
ON DUPLICATE KEY UPDATE
    name = VALUES(name), email = VALUES(email),
    course = VALUES(course), phone = VALUES(phone);
