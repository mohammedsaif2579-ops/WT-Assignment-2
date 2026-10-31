-- Database schema for the Campusly student administration portal.
-- students stores student details; student_files stores uploads linked by student_id.
CREATE DATABASE IF NOT EXISTS campusly_college
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE campusly_college;

-- Main table containing one row for each student.
CREATE TABLE IF NOT EXISTS students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    roll_number VARCHAR(40) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL,
    course VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Upload metadata table; each file belongs to one student.
CREATE TABLE IF NOT EXISTS student_files (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    file_category ENUM('profile', 'document') NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Cascade metadata deletion when its student record is deleted.
    CONSTRAINT fk_student_files_student
        FOREIGN KEY (student_id)
        REFERENCES students(id)
        ON DELETE CASCADE
);
