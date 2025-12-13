<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Membership; // Để lấy thông tin ban chuyên môn nếu cần
use App\Models\ActivityLog;

class UserController extends Controller
{
    private $userModel;
    private $logModel;

    public function __construct()
    {
        // Yêu cầu đăng nhập cho toàn bộ Controller này
        $this->requireLogin();

        $this->userModel = new User();
        $this->logModel = new ActivityLog();
    }

    /**
     * Danh sách người dùng (Chỉ dành cho Admin/Subadmin)
     */
    public function index()
    {
        // Kiểm tra quyền
        if ($_SESSION['user_role'] === 'member' || $_SESSION['user_role'] === 'guest') {
            \set_flash_message('error', 'Bạn không có quyền truy cập trang này.');
            $this->redirect(BASE_URL);
        }

        // Lấy tham số filter từ URL
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;

        // Gọi Model lấy dữ liệu phân trang
        $data = $this->userModel->getAllUsers($search, $role, $page, $limit);

        // Truyền lại các tham số filter để view hiển thị
        $data['search'] = $search;
        $data['role_filter'] = $role;
        $data['title'] = 'Quản lý thành viên';

        $this->view('users/index', $data);
    }

    /**
     * Xem hồ sơ cá nhân (hoặc của người khác)
     */
    public function profile($id = null)
    {
        // Nếu không truyền ID -> Xem hồ sơ của chính mình
        if ($id === null) {
            $id = $_SESSION['user_id'];
        }

        $user = $this->userModel->getProfile($id);

        if (!$user) {
            \set_flash_message('error', 'Người dùng không tồn tại.');
            $this->redirect(BASE_URL);
        }

        $data = [
            'user' => $user,
            'is_own_profile' => ($id == $_SESSION['user_id']),
            'title' => 'Hồ sơ: ' . htmlspecialchars($user['name'])
        ];

        $this->view('users/profile', $data);
    }

    /**
     * Cập nhật hồ sơ cá nhân
     */
    public function update_profile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/user/profile');
        }

        $id = $_SESSION['user_id'];

        // Lấy dữ liệu từ form
        $updateData = [
            'name' => trim($_POST['name']),
            'phone' => trim($_POST['phone']),
            'gender' => $_POST['gender'],
            'dob' => $_POST['dob'],
            'address' => trim($_POST['address']),
            'bio' => trim($_POST['bio'])
            // Không cho phép user tự đổi email/role ở đây
        ];

        // Validate cơ bản
        if (empty($updateData['name'])) {
            \set_flash_message('error', 'Họ tên không được để trống.');
            $this->redirect(BASE_URL . '/user/profile');
            return;
        }

        // Gọi Model update
        if ($this->userModel->update($id, $updateData)) {
            // Cập nhật lại session name nếu đổi tên
            $_SESSION['user_name'] = $updateData['name'];

            $this->logModel->create($id, 'profile_update', 'Cập nhật hồ sơ cá nhân.');
            \set_flash_message('success', 'Cập nhật hồ sơ thành công.');
        } else {
            \set_flash_message('error', 'Có lỗi xảy ra khi cập nhật.');
        }

        $this->redirect(BASE_URL . '/user/profile');
    }

    /**
     * Form tạo người dùng mới (Admin only)
     */
    public function create()
    {
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect(BASE_URL);
        }

        $data = [
            'title' => 'Thêm thành viên mới',
            'name' => '',
            'email' => '',
            'password' => '',
            'role' => 'member',
            'email_err' => '',
            'password_err' => '',
            'name_err' => ''
        ];

        $this->view('users/create', $data);
    }

    /**
     * Xử lý tạo người dùng
     */
    public function store()
    {
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect(BASE_URL);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'system_role' => $_POST['system_role'] ?? 'member',
                // Thông tin profile mặc định
                'phone' => trim($_POST['phone'] ?? ''),
                'gender' => $_POST['gender'] ?? 'other',
            ];

            // Validate
            $errors = [];
            if (empty($data['name'])) $errors['name_err'] = 'Vui lòng nhập tên';
            if (empty($data['email'])) $errors['email_err'] = 'Vui lòng nhập email';
            if (empty($data['password'])) $errors['password_err'] = 'Vui lòng nhập mật khẩu';
            if (strlen($data['password']) < 6) $errors['password_err'] = 'Mật khẩu phải từ 6 ký tự';

            // Check email tồn tại
            if ($this->userModel->findByEmail($data['email'])) {
                $errors['email_err'] = 'Email này đã được sử dụng';
            }

            if (empty($errors)) {
                // Tạo user (Model tự xử lý transaction User + Profile)
                if ($this->userModel->create($data)) {
                    $this->logModel->create($_SESSION['user_id'], 'user_create', "Tạo thành viên: " . $data['email']);
                    \set_flash_message('success', 'Tạo thành viên thành công!');
                    $this->redirect(BASE_URL . '/user/index');
                } else {
                    \set_flash_message('error', 'Lỗi hệ thống khi tạo user.');
                }
            } else {
                $data = array_merge($data, $errors);
                $data['title'] = 'Thêm thành viên mới';
                $this->view('users/create', $data);
            }
        }
    }

    /**
     * Form sửa người dùng (Admin edit User)
     */
    public function edit($id)
    {
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect(BASE_URL);
        }

        $user = $this->userModel->getProfile($id);

        if (!$user) {
            \set_flash_message('error', 'User không tồn tại.');
            $this->redirect(BASE_URL . '/user/index');
        }

        $data = [
            'user' => $user,
            'title' => 'Chỉnh sửa thành viên'
        ];

        $this->view('users/edit', $data);
    }

    /**
     * Xử lý cập nhật người dùng (Admin)
     */
    public function update($id)
    {
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect(BASE_URL);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $updateData = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'system_role' => $_POST['system_role'],
                'phone' => trim($_POST['phone']),
                'gender' => $_POST['gender'],
                'dob' => $_POST['dob'],
                'address' => trim($_POST['address']),
                'bio' => trim($_POST['bio'])
            ];

            // Nếu muốn đổi mật khẩu cho user
            if (!empty($_POST['new_password'])) {
                // Logic đổi pass riêng hoặc gộp vào đây tùy nhu cầu
                $this->userModel->changePassword($id, $_POST['new_password']);
            }

            if ($this->userModel->update($id, $updateData)) {
                $this->logModel->create($_SESSION['user_id'], 'user_update', "Cập nhật user ID: $id");
                \set_flash_message('success', 'Cập nhật thành công.');
                $this->redirect(BASE_URL . '/user/index');
            } else {
                \set_flash_message('error', 'Có lỗi xảy ra.');
                $this->redirect(BASE_URL . '/user/edit/' . $id);
            }
        }
    }

    /**
     * Xóa người dùng (Soft delete)
     */
    public function delete($id)
    {
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect(BASE_URL);
        }

        // Không được xóa chính mình
        if ($id == $_SESSION['user_id']) {
            \set_flash_message('error', 'Không thể xóa tài khoản đang đăng nhập.');
            $this->redirect(BASE_URL . '/user/index');
            return;
        }

        if ($this->userModel->delete($id)) {
            $this->logModel->create($_SESSION['user_id'], 'user_delete', "Xóa user ID: $id");
            \set_flash_message('success', 'Đã xóa người dùng.');
        } else {
            \set_flash_message('error', 'Xóa thất bại.');
        }

        $this->redirect(BASE_URL . '/user/index');
    }
}
