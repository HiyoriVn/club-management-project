-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 15, 2025 lúc 10:13 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `club_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Người thực hiện (NULL = hệ thống)',
  `action` varchar(100) NOT NULL COMMENT 'Mã hành động (VD: user_login, project_created)',
  `details` text DEFAULT NULL COMMENT 'Chi tiết hành động',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Nhật ký hoạt động hệ thống';

--
-- Đang đổ dữ liệu cho bảng `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'user_login', 'Đăng nhập thành công.', '2025-12-15 15:18:07'),
(2, 1, 'user_logout', 'Đăng xuất hệ thống.', '2025-12-15 15:26:59'),
(3, 1, 'user_login', 'Đăng nhập thành công.', '2025-12-15 15:35:57'),
(4, 1, 'user_create', 'Tạo thành viên: nguyenvana@clb.edu.vn', '2025-12-15 15:37:09'),
(5, 1, 'user_create', 'Tạo thành viên: nguyenvanb@clb.edu.vn', '2025-12-15 15:37:33'),
(6, 1, 'user_create', 'Tạo thành viên: nguyenvanc@clb.edu.vn', '2025-12-15 15:38:00'),
(7, 1, 'user_create', 'Tạo thành viên: nguyenvand@clb.edu.vn', '2025-12-15 15:38:19'),
(8, 1, 'trans_create', 'Tạo giao dịch income', '2025-12-15 15:43:38'),
(9, 1, 'trans_create', 'Tạo giao dịch expense', '2025-12-15 15:44:04'),
(10, 1, 'dept_create', 'Tạo ban: Ban Chuyên Môn', '2025-12-15 15:44:16'),
(11, 1, 'dept_create', 'Tạo ban: Bạn Tài chính - Đối ngoại', '2025-12-15 15:44:28'),
(12, 1, 'dept_create', 'Tạo ban: Ban Truyền thông', '2025-12-15 15:44:38'),
(13, 1, 'project_create', 'Tạo project: Hoạt động gây quỹ', '2025-12-15 15:45:48'),
(14, 1, 'task_create', 'Tạo task ID: 1 trong dự án 1', '2025-12-15 15:47:06'),
(15, 1, 'user_logout', 'Đăng xuất hệ thống.', '2025-12-15 15:47:11'),
(16, 5, 'user_login', 'Đăng nhập thành công.', '2025-12-15 15:50:12'),
(17, 5, 'user_logout', 'Đăng xuất hệ thống.', '2025-12-15 15:56:06'),
(18, 1, 'user_login', 'Đăng nhập thành công.', '2025-12-15 15:56:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL COMMENT 'Tiêu đề thông báo',
  `content` text NOT NULL COMMENT 'Nội dung (HTML)',
  `target_department_id` int(11) DEFAULT NULL COMMENT 'Gửi tới ban cụ thể (NULL = toàn CLB)',
  `posted_by` int(11) DEFAULT NULL COMMENT 'Người đăng thông báo',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Thông báo';

--
-- Đang đổ dữ liệu cho bảng `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `target_department_id`, `posted_by`, `created_at`, `updated_at`) VALUES
(1, 'CHUNG TAY VÌ NỤ CƯỜI TRẺ EM – SỰ KIỆN GÂY QUỸ NHI ĐỒNG', '<div>Với mong muốn mang đến cho các em nhỏ có hoàn cảnh khó khăn cơ hội được học tập, vui chơi và phát triển toàn diện, chúng tôi trân trọng tổ chức <strong>Sự kiện Gây quỹ vì Quỹ Nhi đồng</strong>.</div><div>Sự kiện là nơi cộng đồng cùng chung tay sẻ chia, lan tỏa yêu thương và tạo nên những giá trị tích cực cho tương lai của trẻ em. Toàn bộ số tiền gây quỹ sẽ được sử dụng minh bạch nhằm hỗ trợ các chương trình chăm sóc, giáo dục và bảo vệ trẻ em có hoàn cảnh đặc biệt.</div><div>Chúng tôi rất mong nhận được sự quan tâm, tham gia và ủng hộ của quý cá nhân, tổ chức để cùng nhau góp phần mang lại nhiều nụ cười và hy vọng cho các em nhỏ.</div><div>Mọi sự đóng góp của quý vị đều là nguồn động viên to lớn, giúp các em có thêm niềm tin và cơ hội vươn lên trong cuộc sống.</div>', NULL, 1, '2025-12-15 16:07:03', '2025-12-15 16:07:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Tên ban (VD: Ban Truyền thông)',
  `description` text DEFAULT NULL COMMENT 'Mô tả ban',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cơ cấu tổ chức (Ban)';

--
-- Đang đổ dữ liệu cho bảng `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Ban Chuyên Môn', '', '2025-12-15 15:44:16', '2025-12-15 15:44:16'),
(2, 'Bạn Tài chính - Đối ngoại', '', '2025-12-15 15:44:28', '2025-12-15 15:44:28'),
(3, 'Ban Truyền thông', '', '2025-12-15 15:44:38', '2025-12-15 15:44:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `file_name` varchar(150) NOT NULL COMMENT 'Tên file gốc',
  `file_path` varchar(255) NOT NULL COMMENT 'Đường dẫn lưu trên server',
  `type` varchar(20) DEFAULT NULL COMMENT 'Loại file (MIME type)',
  `uploaded_by` int(11) DEFAULT NULL COMMENT 'Người upload',
  `project_id` int(11) DEFAULT NULL COMMENT 'Thuộc dự án nào (NULL = file chung)',
  `department_id` int(11) DEFAULT NULL COMMENT 'Thuộc ban nào (NULL = file công khai)',
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tài liệu & Files';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `memberships`
--

CREATE TABLE `memberships` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên',
  `department_id` int(11) NOT NULL COMMENT 'ID ban',
  `department_role` enum('Head','Deputy','Member') NOT NULL DEFAULT 'Member' COMMENT 'Vai trò: Trưởng ban, Phó, Thành viên'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Thành viên - Ban';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL COMMENT 'Tên dự án/sự kiện',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `type` enum('project','event') NOT NULL DEFAULT 'project' COMMENT 'Loại: project=dự án, event=sự kiện',
  `start_date` datetime DEFAULT NULL COMMENT 'Ngày bắt đầu',
  `end_date` datetime DEFAULT NULL COMMENT 'Ngày kết thúc',
  `status` enum('planning','in_progress','completed','cancelled') NOT NULL DEFAULT 'planning' COMMENT 'Trạng thái: lên kế hoạch, đang thực hiện, hoàn thành, hủy',
  `leader_id` int(11) DEFAULT NULL COMMENT 'ID người chịu trách nhiệm (Project Manager)',
  `department_id` int(11) DEFAULT NULL COMMENT 'Dự án thuộc ban nào (NULL = chung)',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dự án & Sự kiện (Gộp chung)';

--
-- Đang đổ dữ liệu cho bảng `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `type`, `start_date`, `end_date`, `status`, `leader_id`, `department_id`, `created_at`, `updated_at`) VALUES
(1, 'Hoạt động gây quỹ', 'Gây quỹ để ủng hộ quỹ nhi đồng', 'project', '2025-12-14 00:00:00', '2025-12-25 00:00:00', 'planning', 3, 1, '2025-12-15 15:45:48', '2025-12-15 15:45:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `project_members`
--

CREATE TABLE `project_members` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL COMMENT 'ID dự án',
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên',
  `role` varchar(50) DEFAULT 'Member' COMMENT 'Vai trò trong dự án (VD: Leader, Member, Designer...)',
  `joined_at` datetime DEFAULT current_timestamp() COMMENT 'Ngày tham gia dự án'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Thành viên - Dự án';

--
-- Đang đổ dữ liệu cho bảng `project_members`
--

INSERT INTO `project_members` (`id`, `project_id`, `user_id`, `role`, `joined_at`) VALUES
(1, 1, 1, 'Member', '2025-12-15 15:45:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL COMMENT 'Thuộc dự án nào',
  `title` varchar(150) NOT NULL COMMENT 'Tiêu đề công việc',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết (HTML từ Trix Editor)',
  `status` enum('backlog','todo','in_progress','done') NOT NULL DEFAULT 'backlog' COMMENT 'Trạng thái Kanban: backlog=chờ, todo=cần làm, in_progress=đang làm, done=xong',
  `start_date` datetime DEFAULT NULL COMMENT 'Ngày bắt đầu',
  `due_date` datetime DEFAULT NULL COMMENT 'Ngày hết hạn (deadline)',
  `color` varchar(7) DEFAULT '#007bff' COMMENT 'Màu thẻ Kanban (HEX color)',
  `attachment_link` varchar(255) DEFAULT NULL COMMENT 'Link file/tài liệu đính kèm',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Công việc (Tasks)';

--
-- Đang đổ dữ liệu cho bảng `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `start_date`, `due_date`, `color`, `attachment_link`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lên kế hoạch', '', 'backlog', '2025-12-14 00:00:00', '2025-12-15 12:00:00', '#3b82f6', NULL, '2025-12-15 15:47:06', '2025-12-15 15:47:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `task_assignees`
--

CREATE TABLE `task_assignees` (
  `task_id` int(11) NOT NULL COMMENT 'ID công việc',
  `user_id` int(11) NOT NULL COMMENT 'ID người được gán',
  `assigned_at` datetime DEFAULT current_timestamp() COMMENT 'Ngày được gán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Phân công công việc (Many-to-Many)';

--
-- Đang đổ dữ liệu cho bảng `task_assignees`
--

INSERT INTO `task_assignees` (`task_id`, `user_id`, `assigned_at`) VALUES
(1, 1, '2025-12-15 15:47:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL COMMENT 'Loại: income=thu, expense=chi',
  `amount` decimal(15,2) NOT NULL COMMENT 'Số tiền (VNĐ)',
  `date` date NOT NULL DEFAULT curdate() COMMENT 'Ngày giao dịch',
  `description` varchar(255) DEFAULT NULL COMMENT 'Mô tả giao dịch',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo giao dịch',
  `approved_by` int(11) DEFAULT NULL COMMENT 'Người phê duyệt (nếu cần)',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Thu chi tài chính';

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`id`, `type`, `amount`, `date`, `description`, `created_by`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 'income', 10000000.00, '2025-12-15', 'Thu Quỹ tháng 12', 1, 1, '2025-12-15 15:43:38', '2025-12-15 15:43:38'),
(2, 'expense', 2000000.00, '2025-12-15', 'Tiền chi cho liên hoan cuối năm', 1, 1, '2025-12-15 15:44:04', '2025-12-15 15:44:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Họ tên đầy đủ',
  `email` varchar(100) NOT NULL COMMENT 'Email đăng nhập',
  `password` varchar(255) NOT NULL COMMENT 'Password đã hash (bcrypt)',
  `system_role` enum('admin','subadmin','member') NOT NULL DEFAULT 'member' COMMENT 'Vai trò hệ thống: admin=quản trị, subadmin=phó, member=thành viên',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Trạng thái tài khoản (soft delete)',
  `password_reset_token` varchar(100) DEFAULT NULL COMMENT 'Token reset mật khẩu',
  `password_reset_expires` datetime DEFAULT NULL COMMENT 'Thời hạn token',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tài khoản người dùng';

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `system_role`, `is_active`, `password_reset_token`, `password_reset_expires`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@clb.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, NULL, '2025-12-12 19:40:42', '2025-12-12 19:40:42'),
(2, 'hiro', 'hiro9395.gg@gmail.com', '!Hirovn9395', 'admin', 1, NULL, NULL, '2025-12-15 14:56:30', '2025-12-15 14:56:30'),
(3, 'Nguyễn Văn A', 'nguyenvana@clb.edu.vn', '$2y$10$tDoWByYG7Z1wehmw.Qs3q.bAS/I6SBEytqF9JWvYN2B4UQeMX.Ooi', 'subadmin', 1, 'a4b42270f6c91319dd5a5648b68026717a62a59793672624818cf5be87660ee6', '2025-12-15 10:04:01', '2025-12-15 15:37:09', '2025-12-15 15:49:01'),
(4, 'Nguyễn Văn B', 'nguyenvanb@clb.edu.vn', '$2y$10$gjOsIStXE8TydmnjdNaT6OfCNgGPhk9sxzASwXIgKDX6dNfuqjgEy', 'subadmin', 1, NULL, NULL, '2025-12-15 15:37:33', '2025-12-15 15:37:33'),
(5, 'Nguyễn Văn C', 'nguyenvanc@clb.edu.vn', '$2y$10$DU0.YgPpjgySStG9DNiEaOzVxk02OUjRhzLCCX7nHjWAJFWGjyADe', 'member', 1, NULL, NULL, '2025-12-15 15:38:00', '2025-12-15 15:38:00'),
(6, 'Nguyễn Văn D', 'nguyenvand@clb.edu.vn', '$2y$10$CdCtAWeHwMHpO8DbdcMwDexIkTlumVI5gXweNsvNJQEzT9E7r98Tq', 'member', 1, NULL, NULL, '2025-12-15 15:38:19', '2025-12-15 15:38:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_profiles`
--

CREATE TABLE `user_profiles` (
  `user_id` int(11) NOT NULL COMMENT 'FK tới users.id',
  `phone` varchar(15) DEFAULT NULL COMMENT 'Số điện thoại',
  `gender` enum('male','female','other') DEFAULT 'other' COMMENT 'Giới tính',
  `dob` date DEFAULT NULL COMMENT 'Ngày sinh (Date of Birth)',
  `address` varchar(255) DEFAULT NULL COMMENT 'Địa chỉ',
  `bio` text DEFAULT NULL COMMENT 'Giới thiệu bản thân',
  `avatar` varchar(255) DEFAULT NULL COMMENT 'URL ảnh đại diện',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Thông tin chi tiết người dùng';

--
-- Đang đổ dữ liệu cho bảng `user_profiles`
--

INSERT INTO `user_profiles` (`user_id`, `phone`, `gender`, `dob`, `address`, `bio`, `avatar`, `updated_at`) VALUES
(1, '0123456789', 'male', NULL, NULL, NULL, NULL, '2025-12-12 19:40:42'),
(3, '', 'other', NULL, NULL, NULL, NULL, '2025-12-15 15:37:09'),
(4, '', 'other', NULL, NULL, NULL, NULL, '2025-12-15 15:37:33'),
(5, '', 'other', NULL, NULL, NULL, NULL, '2025-12-15 15:38:00'),
(6, '', 'other', NULL, NULL, NULL, NULL, '2025-12-15 15:38:19');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_logs_user` (`user_id`),
  ADD KEY `idx_activity_logs_action` (`action`),
  ADD KEY `idx_activity_logs_date` (`created_at`);

--
-- Chỉ mục cho bảng `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posted_by` (`posted_by`),
  ADD KEY `idx_announcements_dept` (`target_department_id`);

--
-- Chỉ mục cho bảng `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_files_uploader` (`uploaded_by`),
  ADD KEY `idx_files_project` (`project_id`),
  ADD KEY `idx_files_dept` (`department_id`);

--
-- Chỉ mục cho bảng `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_membership` (`user_id`,`department_id`),
  ADD KEY `idx_memberships_user` (`user_id`),
  ADD KEY `idx_memberships_dept` (`department_id`);

--
-- Chỉ mục cho bảng `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_projects_type` (`type`),
  ADD KEY `idx_projects_status` (`status`),
  ADD KEY `idx_projects_leader` (`leader_id`),
  ADD KEY `idx_projects_dept` (`department_id`);

--
-- Chỉ mục cho bảng `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project_member` (`project_id`,`user_id`),
  ADD KEY `idx_project_members_project` (`project_id`),
  ADD KEY `idx_project_members_user` (`user_id`);

--
-- Chỉ mục cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tasks_project` (`project_id`),
  ADD KEY `idx_tasks_status` (`status`),
  ADD KEY `idx_tasks_due_date` (`due_date`);

--
-- Chỉ mục cho bảng `task_assignees`
--
ALTER TABLE `task_assignees`
  ADD PRIMARY KEY (`task_id`,`user_id`),
  ADD KEY `idx_task_assignees_user` (`user_id`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_transactions_date` (`date`),
  ADD KEY `idx_transactions_type` (`type`),
  ADD KEY `idx_transactions_creator` (`created_by`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_role` (`system_role`),
  ADD KEY `idx_users_active` (`is_active`);

--
-- Chỉ mục cho bảng `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `project_members`
--
ALTER TABLE `project_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`target_department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `memberships`
--
ALTER TABLE `memberships`
  ADD CONSTRAINT `memberships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `memberships_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `project_members_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `task_assignees`
--
ALTER TABLE `task_assignees`
  ADD CONSTRAINT `task_assignees_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_assignees_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
