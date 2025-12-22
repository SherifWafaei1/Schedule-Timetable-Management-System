-- Schedule Time Table Management System Database Schema
-- MySQL 8.0+ compatible

CREATE DATABASE IF NOT EXISTS timetable_management;
USE timetable_management;

-- Users table with role-based access
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL DEFAULT 'student',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    reset_token VARCHAR(255) NULL,
    reset_token_expires TIMESTAMP NULL
);

-- Subjects/Courses table
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    description TEXT,
    credits INT DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms/Locations table
CREATE TABLE rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_code VARCHAR(20) UNIQUE NOT NULL,
    room_name VARCHAR(100) NOT NULL,
    capacity INT DEFAULT 30,
    room_type ENUM('classroom', 'lab', 'auditorium', 'meeting_room') DEFAULT 'classroom',
    location VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Time slots configuration
CREATE TABLE time_slots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slot_name VARCHAR(50) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_minutes INT GENERATED ALWAYS AS (TIME_TO_SEC(TIMEDIFF(end_time, start_time))/60) STORED,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Classes/Groups table
CREATE TABLE classes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    class_code VARCHAR(20) UNIQUE NOT NULL,
    class_name VARCHAR(100) NOT NULL,
    description TEXT,
    academic_year VARCHAR(10),
    semester VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Student-Class enrollment
CREATE TABLE student_enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, class_id)
);

-- Main timetable events
CREATE TABLE timetable_events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_type ENUM('class', 'exam', 'meeting', 'break', 'other') NOT NULL,
    subject_id INT,
    class_id INT,
    teacher_id INT NOT NULL,
    room_id INT,
    time_slot_id INT NOT NULL,
    event_date DATE NOT NULL,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurrence_pattern ENUM('daily', 'weekly', 'monthly') NULL,
    recurrence_end_date DATE NULL,
    parent_event_id INT NULL,
    is_cancelled BOOLEAN DEFAULT FALSE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (time_slot_id) REFERENCES time_slots(id) ON DELETE RESTRICT,
    FOREIGN KEY (parent_event_id) REFERENCES timetable_events(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Time conflicts tracking
CREATE TABLE time_conflicts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    conflicting_event_id INT NOT NULL,
    conflict_type ENUM('room', 'teacher', 'student_class') NOT NULL,
    conflict_description TEXT,
    detected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    resolved_by INT NULL,
    FOREIGN KEY (event_id) REFERENCES timetable_events(id) ON DELETE CASCADE,
    FOREIGN KEY (conflicting_event_id) REFERENCES timetable_events(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Audit trail for important actions
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- User sessions for security
CREATE TABLE user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Indexes for performance
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_active ON users(is_active);
CREATE INDEX idx_timetable_events_date ON timetable_events(event_date);
CREATE INDEX idx_timetable_events_teacher ON timetable_events(teacher_id);
CREATE INDEX idx_timetable_events_room ON timetable_events(room_id);
CREATE INDEX idx_timetable_events_class ON timetable_events(class_id);
CREATE INDEX idx_audit_log_user ON audit_log(user_id);
CREATE INDEX idx_audit_log_created ON audit_log(created_at);
CREATE INDEX idx_conflicts_detected ON time_conflicts(detected_at);

-- Sample data insertion
INSERT INTO users (username, email, password_hash, first_name, last_name, role) VALUES
('admin', 'admin@timetable.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'admin'),
('teacher1', 'teacher1@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Smith', 'teacher'),
('teacher2', 'teacher2@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Doe', 'teacher'),
('student1', 'student1@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice', 'Johnson', 'student'),
('student2', 'student2@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob', 'Wilson', 'student');

INSERT INTO subjects (subject_code, subject_name, description, credits) VALUES
('CS101', 'Introduction to Computer Science', 'Basic programming concepts and algorithms', 3),
('MATH201', 'Calculus II', 'Advanced calculus and differential equations', 4),
('ENG101', 'English Composition', 'Writing and communication skills', 3),
('PHY101', 'Physics I', 'Mechanics and thermodynamics', 4),
('CHEM101', 'Chemistry I', 'General chemistry principles', 3);

INSERT INTO rooms (room_code, room_name, capacity, room_type, location) VALUES
('A101', 'Computer Lab A101', 30, 'lab', 'Building A, Floor 1'),
('A102', 'Lecture Hall A102', 50, 'classroom', 'Building A, Floor 1'),
('A201', 'Physics Lab A201', 25, 'lab', 'Building A, Floor 2'),
('B101', 'Meeting Room B101', 15, 'meeting_room', 'Building B, Floor 1'),
('C101', 'Auditorium C101', 200, 'auditorium', 'Building C, Floor 1');

INSERT INTO time_slots (slot_name, start_time, end_time) VALUES
('Slot 1', '08:00:00', '09:30:00'),
('Slot 2', '09:45:00', '11:15:00'),
('Slot 3', '11:30:00', '13:00:00'),
('Slot 4', '14:00:00', '15:30:00'),
('Slot 5', '15:45:00', '17:15:00'),
('Slot 6', '17:30:00', '19:00:00');

INSERT INTO classes (class_code, class_name, academic_year, semester) VALUES
('CS1A', 'Computer Science Year 1 - Section A', '2024-2025', 'Fall'),
('MATH1A', 'Mathematics Year 1 - Section A', '2024-2025', 'Fall'),
('ENG1A', 'English Year 1 - Section A', '2024-2025', 'Fall');

INSERT INTO student_enrollments (student_id, class_id) VALUES
(4, 1), -- student1 in CS1A
(5, 2); -- student2 in MATH1A

-- Sample timetable events
INSERT INTO timetable_events (title, event_type, subject_id, class_id, teacher_id, room_id, time_slot_id, event_date, created_by) VALUES
('CS101 Lecture', 'class', 1, 1, 2, 1, 1, '2024-01-15', 1),
('MATH201 Tutorial', 'class', 2, 2, 3, 2, 2, '2024-01-15', 1),
('Faculty Meeting', 'meeting', NULL, NULL, 2, 4, 4, '2024-01-16', 1);
