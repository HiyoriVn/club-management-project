<?php
// app/Controllers/MemberController.php

namespace App\Controllers;

use App\Core\Controller;
// Nạp cả 3 Model
use App\Models\User;
use App\Models\Department;
use App\Models\DepartmentRole;

class MemberController extends Controller
{

    private $userModel;
    private $departmentModel;
    private $roleModel;

    public function __construct()
    {
        // Yêu cầu admin hoặc subadmin
        $this->requireRole(['admin', 'subadmin']);

        // Nạp cả 3 Model
        require_once ROOT_PATH . '/app/Models/User.php';
        require_once ROOT_PATH . '/app/Models/Department.php';
        require_once ROOT_PATH . '/app/Models/DepartmentRole.php';

        $this->userModel = new User();
        $this->departmentModel = new Department();
        $this->roleModel = new DepartmentRole();
    }

    /**
     * (READ) Hiển thị trang danh sách tất cả Thành viên
     */
    public function index()
    {
        $users = $this->userModel->getAllUsers();
        $data = [
            'title' => 'Quản lý Thành viên',
            'users' => $users
        ];
        $this->view('members/index', $data);
    }

    /**
     * (READ) Hiển thị trang phân quyền cho 1 user
     */
    public function manage($user_id)
    {
        $user = $this->userModel->findById($user_id);
        if (!$user) {
            $this->redirect(BASE_URL . '/member');
        }

        $data = [
            'title' => 'Phân quyền cho: ' . $user['NAME'],
            'user' => $user,
            // Lấy các vai trò user này đang có
            'current_roles' => $this->userModel->getRolesForUser($user_id),
            // Lấy tất cả Ban/Vai trò để làm dropdown
            'all_departments' => $this->departmentModel->findAll(),
            'all_roles' => $this->roleModel->findAll()
        ];

        $this->view('members/manage', $data);
    }

    /**
     * (CREATE) Xử lý gán vai trò mới (POST)
     */
    public function assign($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/member');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $data = [
            'user_id' => $user_id,
            'department_id' => $_POST['department_id'],
            'role_id' => $_POST['role_id']
        ];

        // Validate đơn giản
        if (empty($data['department_id']) || empty($data['role_id'])) {
            set_flash_message('error', 'Vui lòng chọn Ban và Vai trò.');
            $this->redirect(BASE_URL . '/member/manage/' . $user_id);
        }

        // Gán vai trò
        if (!$this->userModel->assignRole($data)) {
            set_flash_message('error', 'Gán thất bại. Có thể vai trò này đã tồn tại.');
        } else {
            set_flash_message('success', 'Gán vai trò thành công!');
        }

        // Dù thành công hay thất bại, quay lại trang manage
        $this->redirect(BASE_URL . '/member/manage/' . $user_id);
    }

    /**
     * (DELETE) Xử lý thu hồi vai trò (POST)
     */
    public function revoke($user_id, $assignment_id)
    {
        // Dùng $user_id chỉ để redirect
        // Dùng $assignment_id (ID của bảng user_department_roles) để xóa
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/member');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $this->userModel->revokeRole($assignment_id);
        set_flash_message('success', 'Thu hồi vai trò thành công!');
        $this->redirect(BASE_URL . '/member/manage/' . $user_id);
    }
    
    /**
     * (UPDATE) Cập nhật System Role (thay cho promote/demote) (POST)
     */
    public function updateSystemRole($user_id)
    {
        // Chỉ "Super Admin" mới được quyền thay đổi vai trò hệ thống
        $this->requireRole(['admin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/member');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $new_role = $_POST['system_role']; // Lấy từ dropdown

        // --- Logic an toàn (Rất quan trọng) ---
        $user_to_change = $this->userModel->findById($user_id);

        // 1. Không cho admin tự thay đổi vai trò của chính mình
        if ($user_to_change['id'] == $_SESSION['user_id']) {
            set_flash_message('error', 'Không thể thay đổi vai trò của chính mình.');
            $this->redirect(BASE_URL . '/member');
        }
        // 2. Không cho admin này giáng cấp/thay đổi 1 admin khác
        if ($user_to_change['system_role'] == 'admin') {
            set_flash_message('error', 'Không thể thay đổi vai trò của Admin khác.');
            $this->redirect(BASE_URL . '/member');
        }
        // --- Hết logic an toàn ---
        set_flash_message('success', 'Cập nhật vai trò hệ thống cho ' . htmlspecialchars($user_to_change['NAME']) . ' thành công!');
        $this->redirect(BASE_URL . '/member');
    }
}
