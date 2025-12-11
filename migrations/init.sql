CREATE DATABASE IF NOT EXISTS club_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE club_management;

-- 1. USERS: Tài khoản đăng nhập (Gọn nhẹ)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,          
    email VARCHAR(100) NOT NULL UNIQUE,  
    password VARCHAR(255) NOT NULL,
    system_role ENUM('admin', 'subadmin', 'member') NOT NULL DEFAULT 'member',
    password_reset_token VARCHAR(100) NULL,
    password_reset_expires DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. USER_PROFILES: Hồ sơ chi tiết
CREATE TABLE user_profiles (
    user_id INT PRIMARY KEY,
    phone_number VARCHAR(15),
    gender ENUM('male', 'female', 'other'),
    dob DATE,
    address VARCHAR(255),
    avatar VARCHAR(255),
    bio_link VARCHAR(255),               
    generation VARCHAR(20),              
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. DEPARTMENTS: Phòng ban
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- 4. MEMBERSHIPS: Thành viên thuộc ban
CREATE TABLE memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    department_id INT NOT NULL,
    role ENUM('Head', 'Deputy', 'Member') NOT NULL DEFAULT 'Member',
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_member (user_id, department_id)
);

-- 5. PROJECTS: Dự án & Sự kiện
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    type ENUM('project', 'event') NOT NULL DEFAULT 'project',
    start_date DATETIME,
    end_date DATETIME,
    status ENUM('Planning', 'Active', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Planning',
    leader_id INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 6. PROJECT_MEMBERS: Nhân sự dự án
CREATE TABLE project_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    project_role VARCHAR(50) DEFAULT 'Member',
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_mem (project_id, user_id)
);

-- 7. TASKS: Công việc
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    status ENUM('todo', 'in_progress', 'review', 'done') NOT NULL DEFAULT 'todo',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    start_date DATETIME,
    due_date DATETIME,
    color VARCHAR(7) DEFAULT '#007bff',
    attachment_link VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- 8. TASK_ASSIGNEES: 
CREATE TABLE task_assignees (
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (task_id, user_id),      
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 9. TRANSACTIONS: Thu chi
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    description VARCHAR(255),
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 10. ANNOUNCEMENTS: Thông báo
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    posted_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 11. FILES: Tài liệu chung
CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    path VARCHAR(255) NOT NULL,
    type VARCHAR(20),
    uploaded_by INT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 12. ACTIVITY_LOGS: Nhật ký
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,                        
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);