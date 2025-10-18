CREATE DATABASE IF NOT EXISTS club_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; USE
    club_management;
-- 1. user
CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    NAME VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    PASSWORD VARCHAR(255) NOT NULL,
    system_role ENUM('guest', 'member', 'subadmin', 'admin') NOT NULL DEFAULT 'guest',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- 2. user_profiles
CREATE TABLE user_profiles(
    user_id INT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE,
    phone VARCHAR(20),
    gender ENUM('male', 'female', 'other'),
    dob DATE,
    address VARCHAR(255),
    join_date DATE,
    avatar VARCHAR(255),
    bio TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- 3. departments
CREATE TABLE departments(
    id INT AUTO_INCREMENT PRIMARY KEY,
    NAME VARCHAR(255) NOT NULL,
    parent_id INT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(parent_id) REFERENCES departments(id) ON DELETE SET NULL
);
-- 4. department_roles
CREATE TABLE department_roles(
    id INT AUTO_INCREMENT PRIMARY KEY,
    NAME VARCHAR(255) NOT NULL,
    permissions JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- 5. user_department_roles
CREATE TABLE user_department_roles(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    department_id INT NOT NULL,
    role_id INT NOT NULL,
    assigned_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(department_id) REFERENCES departments(id) ON DELETE CASCADE,
    FOREIGN KEY(role_id) REFERENCES department_roles(id) ON DELETE CASCADE,
    FOREIGN KEY(assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_role(
        user_id,
        department_id,
        role_id
    )
);
-- 6. events
CREATE TABLE EVENTS(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    location VARCHAR(255),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE RESTRICT
);
-- 7. event_participants
CREATE TABLE event_participants(
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM(
        'participant',
        'organizer',
        'volunteer'
    ) NOT NULL DEFAULT 'participant',
    STATUS ENUM
        (
            'registered',
            'checked_in',
            'cancelled'
        ) NOT NULL DEFAULT 'registered',
        registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(event_id) REFERENCES EVENTS(id) ON DELETE CASCADE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_participation(event_id, user_id)
);
-- 8. announcements
CREATE TABLE announcements(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    posted_by INT NOT NULL,
    target_department_id INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(posted_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY(target_department_id) REFERENCES departments(id) ON DELETE SET NULL
);
-- 9. files
CREATE TABLE files(
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_by INT NOT NULL,
    department_id INT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(uploaded_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY(department_id) REFERENCES departments(id) ON DELETE SET NULL
);
-- 10. user_points
CREATE TABLE user_points(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    SOURCE ENUM('event', 'project', 'bonus', 'penalty') NOT NULL,
    points INT NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- 11. projects
CREATE TABLE projects(
    id INT AUTO_INCREMENT PRIMARY KEY,
    NAME VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    leader_id INT,
    department_id INT,
    STATUS ENUM
        (
            'planning',
            'in_progress',
            'completed',
            'cancelled'
        ) NOT NULL DEFAULT 'planning',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(leader_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY(department_id) REFERENCES departments(id) ON DELETE SET NULL
);
-- 12. project_members
CREATE TABLE project_members(
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('leader', 'member', 'collaborator') NOT NULL DEFAULT 'member',
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_member(project_id, user_id)
);
-- 13. tasks
CREATE TABLE tasks(
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    assigned_to INT,
    STATUS ENUM
        ('todo', 'in_progress', 'done') NOT NULL DEFAULT 'todo',
        due_date DATE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY(assigned_to) REFERENCES users(id) ON DELETE SET NULL
);
-- 14. transactions
CREATE TABLE transactions(
    id INT AUTO_INCREMENT PRIMARY KEY,
    TYPE ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    DATE DATE NOT NULL,
    created_by INT NOT NULL,
    approved_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY(approved_by) REFERENCES users(id) ON DELETE SET NULL
);
-- 15. forms
CREATE TABLE forms(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    TYPE ENUM(
        'event_register',
        'leave_request',
        'custom'
    ) NOT NULL DEFAULT 'custom',
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE RESTRICT
);
-- 16. form_submissions
CREATE TABLE form_submissions(
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    user_id INT NOT NULL,
    STATUS ENUM
        ('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
        response JSON,
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(form_id) REFERENCES forms(id) ON DELETE CASCADE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- 17. notifications
CREATE TABLE notifications(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- 18. activity_logs
CREATE TABLE activity_logs(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    ACTION VARCHAR(255) NOT NULL,
    details TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);