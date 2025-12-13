<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\ActivityLog;

class SettingController extends Controller
{
    private $userModel;
    private $logModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->userModel = new User();
        $this->logModel = new ActivityLog();
    }

    /**
     * Trang cài đặt chung (Hiện tại chỉ có đổi mật khẩu)
     */
    public function index()
    {
        $data = [
            'title' => 'Cài đặt tài khoản',
            'user' => $this->userModel->getProfile($_SESSION['user_id'])
        ];

        $this->view('settings/index', $data);
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function change_password()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            // 1. Kiểm tra mật khẩu hiện tại
            // Chúng ta cần hàm login để check pass, hoặc viết hàm verifyPassword mới
            // Tận dụng hàm login: lấy email từ session để check
            $user = $this->userModel->login($_SESSION['user_email'], $currentPassword);

            if (!$user) {
                \set_flash_message('error', 'Mật khẩu hiện tại không đúng.');
                $this->redirect(BASE_URL . '/setting');
                return;
            }

            // 2. Validate mật khẩu mới
            if (strlen($newPassword) < 6) {
                \set_flash_message('error', 'Mật khẩu mới phải từ 6 ký tự.');
                $this->redirect(BASE_URL . '/setting');
                return;
            }

            if ($newPassword !== $confirmPassword) {
                \set_flash_message('error', 'Xác nhận mật khẩu không khớp.');
                $this->redirect(BASE_URL . '/setting');
                return;
            }

            // 3. Cập nhật
            if ($this->userModel->changePassword($_SESSION['user_id'], $newPassword)) {
                $this->logModel->create($_SESSION['user_id'], 'password_change', 'Đã tự đổi mật khẩu.');
                \set_flash_message('success', 'Đổi mật khẩu thành công!');
            } else {
                \set_flash_message('error', 'Lỗi hệ thống.');
            }

            $this->redirect(BASE_URL . '/setting');
        } else {
            $this->redirect(BASE_URL . '/setting');
        }
    }
}
