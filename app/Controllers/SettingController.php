<?php
// app/Controllers/SettingsController.php

namespace App\Controllers;

use App\Core\Controller;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->requireRole(['admin']);
    }

    /**
     * Trang cài đặt tổng quan
     */
    public function index()
    {
        // Đọc cấu hình từ .env hoặc database
        $settings = $this->loadSettings();

        $data = [
            'title' => 'Cài đặt Hệ thống',
            'settings' => $settings
        ];

        $this->view('settings/index', $data);
    }

    /**
     * Cập nhật cài đặt
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/settings');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/settings');
            exit;
        }

        $new_settings = [
            'club_name' => trim($_POST['club_name']),
            'club_email' => trim($_POST['club_email']),
            'club_phone' => trim($_POST['club_phone']),
            'club_address' => trim($_POST['club_address']),
            'enable_registration' => isset($_POST['enable_registration']) ? 1 : 0,
            'enable_notifications' => isset($_POST['enable_notifications']) ? 1 : 0,
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0
        ];

        if ($this->saveSettings($new_settings)) {
            \log_activity('settings_updated', 'Admin đã cập nhật cài đặt hệ thống');
            \set_flash_message('success', 'Cập nhật cài đặt thành công!');
        } else {
            \set_flash_message('error', 'Có lỗi xảy ra khi lưu cài đặt.');
        }

        $this->redirect(BASE_URL . '/settings');
    }

    /**
     * Load cài đặt từ file hoặc DB
     */
    private function loadSettings()
    {
        $db = \App\Core\Database::getInstance();

        // Kiểm tra xem bảng settings có tồn tại không
        try {
            $db->query("SELECT * FROM settings LIMIT 1");
            $settings = $db->single();

            if ($settings) {
                return $settings;
            }
        } catch (\Exception $e) {
            // Bảng chưa tồn tại, tạo mới
            $this->createSettingsTable();
        }

        // Trả về giá trị mặc định
        return [
            'club_name' => 'CLB Management',
            'club_email' => 'contact@clb.vn',
            'club_phone' => '0123456789',
            'club_address' => 'Hà Nội, Việt Nam',
            'enable_registration' => 0,
            'enable_notifications' => 1,
            'maintenance_mode' => 0
        ];
    }

    /**
     * Lưu cài đặt
     */
    private function saveSettings($settings)
    {
        $db = \App\Core\Database::getInstance();

        // Kiểm tra xem đã có record chưa
        $db->query("SELECT id FROM settings LIMIT 1");
        $existing = $db->single();

        if ($existing) {
            // Update
            $db->query("UPDATE settings SET 
                            club_name = :club_name,
                            club_email = :club_email,
                            club_phone = :club_phone,
                            club_address = :club_address,
                            enable_registration = :enable_registration,
                            enable_notifications = :enable_notifications,
                            maintenance_mode = :maintenance_mode,
                            updated_at = NOW()
                        WHERE id = :id");
            $db->bind(':id', $existing['id']);
        } else {
            // Insert
            $db->query("INSERT INTO settings (club_name, club_email, club_phone, club_address, enable_registration, enable_notifications, maintenance_mode) 
                       VALUES (:club_name, :club_email, :club_phone, :club_address, :enable_registration, :enable_notifications, :maintenance_mode)");
        }

        $db->bind(':club_name', $settings['club_name']);
        $db->bind(':club_email', $settings['club_email']);
        $db->bind(':club_phone', $settings['club_phone']);
        $db->bind(':club_address', $settings['club_address']);
        $db->bind(':enable_registration', $settings['enable_registration']);
        $db->bind(':enable_notifications', $settings['enable_notifications']);
        $db->bind(':maintenance_mode', $settings['maintenance_mode']);

        return $db->execute();
    }

    /**
     * Tạo bảng settings nếu chưa tồn tại
     */
    private function createSettingsTable()
    {
        $db = \App\Core\Database::getInstance();

        $sql = "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            club_name VARCHAR(255) NOT NULL DEFAULT 'CLB Management',
            club_email VARCHAR(255) DEFAULT NULL,
            club_phone VARCHAR(20) DEFAULT NULL,
            club_address TEXT DEFAULT NULL,
            enable_registration BOOLEAN DEFAULT FALSE,
            enable_notifications BOOLEAN DEFAULT TRUE,
            maintenance_mode BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $db->query($sql);
        $db->execute();

        // Insert giá trị mặc định
        $db->query("INSERT INTO settings (club_name) VALUES ('CLB Management')");
        $db->execute();
    }

    /**
     * Reset hệ thống (NGUY HIỂM!)
     */
    public function reset()
    {
        // Hiển thị trang xác nhận
        $data = [
            'title' => 'Reset Hệ thống'
        ];

        $this->view('settings/reset', $data);
    }

    /**
     * Xử lý reset
     */
    public function confirmReset()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/settings');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/settings');
            exit;
        }

        // Yêu cầu xác nhận bằng mật khẩu
        $password = $_POST['password'] ?? '';

        $userModel = new \App\Models\User();
        $admin = $userModel->findById($_SESSION['user_id']);

        if (!password_verify($password, $admin['PASSWORD'])) {
            \set_flash_message('error', 'Mật khẩu không chính xác!');
            $this->redirect(BASE_URL . '/settings/reset');
            return;
        }

        // Thực hiện reset (xóa dữ liệu test, giữ lại cấu trúc)
        $db = \App\Core\Database::getInstance();

        try {
            // Xóa dữ liệu (trừ admin)
            $db->query("DELETE FROM event_participants");
            $db->execute();

            $db->query("DELETE FROM events");
            $db->execute();

            $db->query("DELETE FROM project_members");
            $db->execute();

            $db->query("DELETE FROM tasks");
            $db->execute();

            $db->query("DELETE FROM projects");
            $db->execute();

            $db->query("DELETE FROM announcements");
            $db->execute();

            $db->query("DELETE FROM files");
            $db->execute();

            $db->query("DELETE FROM transactions");
            $db->execute();

            $db->query("DELETE FROM user_department_roles");
            $db->execute();

            // Chỉ giữ lại admin hiện tại
            $db->query("DELETE FROM users WHERE id != :admin_id");
            $db->bind(':admin_id', $_SESSION['user_id']);
            $db->execute();

            \log_activity('system_reset', 'Admin đã reset toàn bộ dữ liệu hệ thống');
            \set_flash_message('success', 'Đã reset hệ thống thành công! Chỉ giữ lại tài khoản admin.');
        } catch (\Exception $e) {
            \set_flash_message('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }

        $this->redirect(BASE_URL . '/settings');
    }
}
