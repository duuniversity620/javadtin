CREATE DATABASE IF NOT EXISTS university_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE university_portal;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','teacher','student') NOT NULL DEFAULT 'student',
  student_id VARCHAR(60) NULL UNIQUE,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE works (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('assignment','quiz','homework','viva') NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  due_at DATETIME NOT NULL,
  duration_minutes INT NOT NULL DEFAULT 60,
  file_name VARCHAR(255),
  stored_name VARCHAR(255),
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  work_id INT NOT NULL,
  student_id INT NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  stored_name VARCHAR(255) NOT NULL,
  submitted_at DATETIME NOT NULL,
  FOREIGN KEY (work_id) REFERENCES works(id),
  FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE downloads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  work_id INT NOT NULL,
  user_id INT NOT NULL,
  downloaded_at DATETIME NOT NULL,
  FOREIGN KEY (work_id) REFERENCES works(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users(name,email,password_hash,role,status) VALUES
('Portal Administrator','admin@university.local','$2y$10$1QD.KT5S0Y6UUZhN0/SkJu83WD6DBFfZjFccsG4QuF.wgtP5vLrAG','admin','active');
-- Default admin password: Admin@12345
