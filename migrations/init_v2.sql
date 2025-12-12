-- ============================================
-- DATABASE: club_management (REFACTORED V2)
-- Chuẩn hóa: 3NF, BCNF
-- Mục tiêu: Tối giản, Bảo mật, Dễ vận hành
-- ============================================

DROP DATABASE IF EXISTS club_management;
CREATE DATABASE club_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE club_management;

-- ============================================
-- 1. USERS - Tài khoản đăng nhập
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Họ tên đầy đủ',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email đăng nhập',
    password VARCHAR(255) NOT NULL COMMENT 'Password đã hash (bcrypt)',
    
    -- Phân quyền hệ thống
    system_role ENUM('admin', 'subadmin', 'member') NOT NULL DEFAULT 'member' 
        COMMENT 'Vai trò hệ thống: admin=quản trị, subadmin=phó, member=thành viên',
    
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Trạng thái tài khoản (soft delete)',
    
    -- Reset password
    password_reset_token VARCHAR(100) NULL COMMENT 'Token reset mật khẩu',
    password_reset_expires DATETIME NULL COMMENT 'Thời hạn token',
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_users_email (email),
    INDEX idx_users_role (system_role),
    INDEX idx_users_active (is_active)
) ENGINE=InnoDB COMMENT='Tài khoản người dùng';

-- ============================================
-- 2. USER_PROFILES - Hồ sơ chi tiết
-- ============================================
CREATE TABLE user_profiles (
    user_id INT PRIMARY KEY COMMENT 'FK tới users.id',
    
    -- Thông tin cá nhân (Không cần mã sinh viên/học sinh)
    phone VARCHAR(15) NULL COMMENT 'Số điện thoại',
    gender ENUM('male', 'female', 'other') DEFAULT 'other' COMMENT 'Giới tính',
    dob DATE NULL COMMENT 'Ngày sinh (Date of Birth)',
    address VARCHAR(255) NULL COMMENT 'Địa chỉ',
    bio TEXT NULL COMMENT 'Giới thiệu bản thân',
    
    -- Avatar (future feature)
    avatar VARCHAR(255) NULL COMMENT 'URL ảnh đại diện',
    
    -- Timestamp
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Thông tin chi tiết người dùng';

-- ============================================
-- 3. DEPARTMENTS - Phòng ban/Cơ cấu tổ chức
-- ============================================
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Tên ban (VD: Ban Truyền thông)',
    description TEXT NULL COMMENT 'Mô tả ban',
    -- Không cần ban cha con (hierarchy) trong phiên bản này
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Cơ cấu tổ chức (Ban)';

-- ============================================
-- 4. MEMBERSHIPS - Thành viên thuộc Ban
-- ============================================
CREATE TABLE memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'ID thành viên',
    department_id INT NOT NULL COMMENT 'ID ban',
    
    -- Vai trò trong ban
    department_role ENUM('Head', 'Deputy', 'Member') NOT NULL DEFAULT 'Member'
        COMMENT 'Vai trò: Trưởng ban, Phó, Thành viên',
    
    -- Không cần thời gian tham gia ban.
    
    -- Foreign Keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    
    -- Unique constraint (1 người chỉ có 1 vai trò trong 1 ban)
    UNIQUE KEY unique_membership (user_id, department_id),
    
    -- Indexes
    INDEX idx_memberships_user (user_id),
    INDEX idx_memberships_dept (department_id)
) ENGINE=InnoDB COMMENT='Thành viên - Ban';

-- ============================================
-- 5. PROJECTS - Dự án & Sự kiện
-- ============================================
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL COMMENT 'Tên dự án/sự kiện',
    description TEXT NULL COMMENT 'Mô tả chi tiết',
    
    -- Phân loại (Project hoặc Event)
    type ENUM('project', 'event') NOT NULL DEFAULT 'project' 
        COMMENT 'Loại: project=dự án, event=sự kiện',
    
    -- Thành viên trong CLB đều xem được, không phân quyền xem
    
    -- Thời gian
    start_date DATETIME NULL COMMENT 'Ngày bắt đầu',
    end_date DATETIME NULL COMMENT 'Ngày kết thúc',
    
    -- Trạng thái
    status ENUM('planning', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'planning'
        COMMENT 'Trạng thái: lên kế hoạch, đang thực hiện, hoàn thành, hủy',
    
    -- Người phụ trách
    leader_id INT NULL COMMENT 'ID người chịu trách nhiệm (Project Manager)',
    
    -- Thuộc ban nào
    department_id INT NULL COMMENT 'Dự án thuộc ban nào (NULL = chung)',
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_projects_type (type),
    INDEX idx_projects_status (status),
    INDEX idx_projects_leader (leader_id),
    INDEX idx_projects_dept (department_id)
) ENGINE=InnoDB COMMENT='Dự án & Sự kiện (Gộp chung)';

-- ============================================
-- 6. PROJECT_MEMBERS - Thành viên dự án
-- ============================================
CREATE TABLE project_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL COMMENT 'ID dự án',
    user_id INT NOT NULL COMMENT 'ID thành viên',
    
    -- Vai trò trong dự án
    role VARCHAR(50) DEFAULT 'Member' COMMENT 'Vai trò trong dự án (VD: Leader, Member, Designer...)',
    
    -- Timestamp
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tham gia dự án',
    
    -- Foreign Keys
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Unique constraint
    UNIQUE KEY unique_project_member (project_id, user_id),
    
    -- Indexes
    INDEX idx_project_members_project (project_id),
    INDEX idx_project_members_user (user_id)
) ENGINE=InnoDB COMMENT='Thành viên - Dự án';

-- ============================================
-- 7. TASKS - Công việc trong dự án
-- ============================================
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL COMMENT 'Thuộc dự án nào',
    title VARCHAR(150) NOT NULL COMMENT 'Tiêu đề công việc',
    description TEXT NULL COMMENT 'Mô tả chi tiết (HTML từ Trix Editor)',
    
    -- Trạng thái (Kanban board)
    status ENUM('backlog', 'todo', 'in_progress', 'done') NOT NULL DEFAULT 'backlog'
        COMMENT 'Trạng thái Kanban: backlog=chờ, todo=cần làm, in_progress=đang làm, done=xong',
    
    -- Thời gian
    start_date DATETIME NULL COMMENT 'Ngày bắt đầu',
    due_date DATETIME NULL COMMENT 'Ngày hết hạn (deadline)',
    
    -- Màu sắc (UI Kanban)
    color VARCHAR(7) DEFAULT '#007bff' COMMENT 'Màu thẻ Kanban (HEX color)',
    
    -- Đính kèm
    attachment_link VARCHAR(255) NULL COMMENT 'Link file/tài liệu đính kèm',
    
    -- Timestamp
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_tasks_project (project_id),
    INDEX idx_tasks_status (status),
    INDEX idx_tasks_due_date (due_date)
) ENGINE=InnoDB COMMENT='Công việc (Tasks)';

-- ============================================
-- 8. TASK_ASSIGNEES - Phân công nhiều người cho 1 task
-- ============================================
CREATE TABLE task_assignees (
    task_id INT NOT NULL COMMENT 'ID công việc',
    user_id INT NOT NULL COMMENT 'ID người được gán',
    
    -- Timestamp
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày được gán',
    
    -- Primary Key (Composite)
    PRIMARY KEY (task_id, user_id),
    
    -- Foreign Keys
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_task_assignees_user (user_id)
) ENGINE=InnoDB COMMENT='Phân công công việc (Many-to-Many)';

-- ============================================
-- 9. TRANSACTIONS - Quản lý thu chi
-- ============================================
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Loại giao dịch
    type ENUM('income', 'expense') NOT NULL COMMENT 'Loại: income=thu, expense=chi',
    
    -- Số tiền & Ngày
    amount DECIMAL(15, 2) NOT NULL COMMENT 'Số tiền (VNĐ)',
    date DATE NOT NULL DEFAULT (CURRENT_DATE) COMMENT 'Ngày giao dịch',
    
    -- Mô tả
    description VARCHAR(255) NULL COMMENT 'Mô tả giao dịch',
    
    -- Người tạo & Người duyệt
    created_by INT NULL COMMENT 'Người tạo giao dịch',
    approved_by INT NULL COMMENT 'Người phê duyệt (nếu cần)',
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_transactions_date (date),
    INDEX idx_transactions_type (type),
    INDEX idx_transactions_creator (created_by)
) ENGINE=InnoDB COMMENT='Thu chi tài chính';

-- ============================================
-- 10. ANNOUNCEMENTS - Thông báo
-- ============================================
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL COMMENT 'Tiêu đề thông báo',
    content TEXT NOT NULL COMMENT 'Nội dung (HTML)',
    
    -- Thành viên trong CLB đều xem được, không phân quyền xem
    
    -- Gửi tới ban cụ thể (NULL = gửi chung)
    target_department_id INT NULL COMMENT 'Gửi tới ban cụ thể (NULL = toàn CLB)',
    
    -- Người đăng
    posted_by INT NULL COMMENT 'Người đăng thông báo',
    
    -- Timestamp
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (target_department_id) REFERENCES departments(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_announcements_dept (target_department_id)
) ENGINE=InnoDB COMMENT='Thông báo';

-- ============================================
-- 11. FILES - Tài liệu & File upload
-- ============================================
CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(150) NOT NULL COMMENT 'Tên file gốc',
    file_path VARCHAR(255) NOT NULL COMMENT 'Đường dẫn lưu trên server',
    type VARCHAR(20) NULL COMMENT 'Loại file (MIME type)',
    
    -- Người upload
    uploaded_by INT NULL COMMENT 'Người upload',
    
    -- Liên kết với Project (nếu file thuộc dự án)
    project_id INT NULL COMMENT 'Thuộc dự án nào (NULL = file chung)',
    
    -- Liên kết với Department (nếu file thuộc ban)
    department_id INT NULL COMMENT 'Thuộc ban nào (NULL = file công khai)',
    
    -- Timestamp
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_files_uploader (uploaded_by),
    INDEX idx_files_project (project_id),
    INDEX idx_files_dept (department_id)
) ENGINE=InnoDB COMMENT='Tài liệu & Files';

-- ============================================
-- 12. ACTIVITY_LOGS - Nhật ký hoạt động
-- ============================================
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL COMMENT 'Người thực hiện (NULL = hệ thống)',
    action VARCHAR(100) NOT NULL COMMENT 'Mã hành động (VD: user_login, project_created)',
    details TEXT NULL COMMENT 'Chi tiết hành động',
    
    -- Timestamp
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_activity_logs_user (user_id),
    INDEX idx_activity_logs_action (action),
    INDEX idx_activity_logs_date (created_at)
) ENGINE=InnoDB COMMENT='Nhật ký hoạt động hệ thống';

-- ============================================
-- Sample Data
-- ============================================

-- Admin mặc định (Password: admin123)
INSERT INTO users (name, email, password, system_role) VALUES
('Admin', 'admin@clb.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Profile cho admin
INSERT INTO user_profiles (user_id, phone, gender) VALUES
(1, '0123456789', 'male');

-- ============================================
-- KẾT THÚC SCHEMA
-- ============================================