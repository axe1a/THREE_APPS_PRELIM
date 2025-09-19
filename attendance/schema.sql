-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') NOT NULL,
    course_id INT,
    year_level INT,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Attendances table
CREATE TABLE IF NOT EXISTS attendances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    time_in TIME NOT NULL,
    status ENUM('on time', 'late') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

ALTER TABLE attendances ADD UNIQUE KEY unique_attendance (user_id, date);
ALTER TABLE users MODIFY course_id INT NULL;
ALTER TABLE users MODIFY year_level INT NULL;

-- Excuse Letters table
CREATE TABLE IF NOT EXISTS excuse_letters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    reason TEXT NOT NULL,
    date_of_absence DATE NOT NULL,
    supporting_document VARCHAR(255) NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_comment TEXT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
);