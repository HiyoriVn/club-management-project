<?php
// app/Controllers/UserController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\DepartmentRole;
use App\Models\Profile;

class UserController extends Controller
{
    private $userModel;
    private $departmentModel;
    private $roleModel;
    private $profileModel;

    public function __construct()
    {
        // Chỉ admin/subadmin được quản lý user
        $this->requireRole(['admin', 'subadmin']);

        require_once ROOT_PATH . '/app/Models/User.php';
        require_once ROOT_PATH . '/app/Models/Department.php';
        require_once ROOT_PATH . '/app/Models/DepartmentRole.php';
        require_once ROOT_PATH . '/app/Models/Profile.php';

        $this->userModel = new User();
        $this->departmentModel = new Department();
        $this->roleModel = new DepartmentRole();
        $this->profileModel = new Profile();
    }

    /**
     * Danh sách người dùng (có tìm kiếm và phân trang)
     */
    public function index()
    {
        // Lấy tham số tìm kiếm
        $search = $_GET['search'] ?? '';
        $role_filter = $_GET['role'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 20;

        // Gọi model với filter
        $result = $this->userModel->searchUsers($search, $role_filter, $page, $limit);

        $data = [
            'title' => 'Quản lý Người dùng',
            'users' => $result['users'],
            'total' => $result['total'],
            'page' => $page,
            'limit' => $limit,
            'search' => $search,
            'role_filter' => $role_filter
        ];

        $this->view('users/index', $data);
    }

    /**
     * Xem chi tiết người dùng
     */
    public function viewUser($user_id)
    {
        $user = $this->userModel->findById($user_id);
        if (!$user) {
            \set_flash_message('error', 'Không tìm thấy người dùng.');
            $this->redirect(BASE_URL . '/user');
            return;
        }

        $profile = $this->profileModel->findByUserId($user_id);
        $roles = $this->userModel->getRolesForUser($user_id);

        $data = [
            'title' => 'Chi tiết: ' . $user['NAME'],
            'user' => $user,
            'profile' => $profile,
            'roles' => $roles
        ];

        $this->view('users/view', $data);
    }

    /**
     * Form tạo người dùng mới
     */
    public function create()
    {
        // Chỉ admin mới được tạo user
        $this->requireRole(['admin']);

        $data = [
            'title' => 'Tạo Người dùng mới',
            'name' => '',
            'email' => '',
            'password' => '',
            'system_role' => 'member',
            'name_err' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        $this->view('users/create', $data);
    }

    /**
     * Lưu người dùng mới
     */
    public function store()
    {
        $this->requireRole(['admin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/user');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/user');
            exit;
        }

        $data = [
            'title' => 'Tạo Người dùng mới',
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password']),
            'system_role' => $_POST['system_role'] ?? 'member',
            'name_err' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        // Validate
        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập tên';
        }

        if (empty($data['email'])) {
            $data['email_err'] = 'Vui lòng nhập email';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $data['email_err'] = 'Email không hợp lệ';
        } elseif ($this->userModel->findUserByEmail($data['email'])) {
            $data['email_err'] = 'Email này đã được sử dụng';
        }

        if (empty($data['password'])) {
            $data['password_err'] = 'Vui lòng nhập mật khẩu';
        } elseif (strlen($data['password']) < 6) {
            $data['password_err'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        // Xử lý
        if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            if ($this->userModel->createUser($data)) {
                \log_activity('user_created', 'Admin đã tạo tài khoản mới: [' . $data['name'] . '] - Email: ' . $data['email']);
                \set_flash_message('success', 'Tạo người dùng [' . htmlspecialchars($data['name']) . '] thành công!');
                $this->redirect(BASE_URL . '/user');
            } else {
                \set_flash_message('error', 'Có lỗi xảy ra, không thể tạo người dùng.');
                $this->view('users/create', $data);
            }
        } else {
            $this->view('users/create', $data);
        }
    }

    /**
     * Form chỉnh sửa người dùng
     */
    public function edit($user_id)
    {
        $user = $this->userModel->findById($user_id);
        if (!$user) {
            \set_flash_message('error', 'Không tìm thấy người dùng.');
            $this->redirect(BASE_URL . '/user');
            return;
        }

        // Không cho sửa admin khác (trừ khi là chính mình)
        if ($_SESSION['user_role'] != 'admin' && $user['system_role'] == 'admin') {
            \set_flash_message('error', 'Bạn không có quyền chỉnh sửa Admin.');
            $this->redirect(BASE_URL . '/user');
            return;
        }

        $profile = $this->profileModel->findByUserId($user_id);

        $data = [
            'title' => 'Chỉnh sửa: ' . $user['NAME'],
            'user_id' => $user_id,
            'name' => $user['NAME'],
            'email' => $user['email'],
            'system_role' => $user['system_role'],
            'student_id' => $profile['student_id'] ?? '',
            'phone' => $profile['phone'] ?? '',
            'gender' => $profile['gender'] ?? 'other',
            'dob' => $profile['dob'] ?? '',
            'address' => $profile['address'] ?? '',
            'bio' => $profile['bio'] ?? '',
            'name_err' => '',
            'email_err' => ''
        ];

        $this->view('users/edit', $data);
    }

    /**
     * Cập nhật người dùng
     */
    public function update($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/user');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/user');
            exit;
        }

        $user = $this->userModel->findById($user_id);
        if (!$user) {
            \set_flash_message('error', 'Không tìm thấy người dùng.');
            $this->redirect(BASE_URL . '/user');
            return;
        }

        $profile_data = [
            'student_id' => trim($_POST['student_id']),
            'phone' => trim($_POST['phone']),
            'gender' => $_POST['gender'],
            'dob' => $_POST['dob'],
            'address' => trim($_POST['address']),
            'bio' => trim($_POST['bio'])
        ];

        $user_data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email'])
        ];

        // Validate
        $errors = [];
        if (empty($user_data['name'])) {
            $errors['name_err'] = 'Vui lòng nhập tên';
        }

        if (empty($user_data['email'])) {
            $errors['email_err'] = 'Vui lòng nhập email';
        } elseif (!filter_var($user_data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email_err'] = 'Email không hợp lệ';
        } else {
            $existing = $this->userModel->findUserByEmail($user_data['email']);
            if ($existing && $existing['id'] != $user_id) {
                $errors['email_err'] = 'Email này đã được sử dụng';
            }
        }

        if (empty($errors)) {
            // Cập nhật users
            if ($this->userModel->updateBasicInfo($user_id, $user_data)) {
                // Cập nhật profile
                $this->profileModel->createOrUpdate($user_id, $profile_data);

                \log_activity('user_updated', 'Đã cập nhật thông tin người dùng (ID: ' . $user_id . ').');
                \set_flash_message('success', 'Cập nhật người dùng thành công!');
                $this->redirect(BASE_URL . '/user/view/' . $user_id);
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể cập nhật.');
                $this->redirect(BASE_URL . '/user/edit/' . $user_id);
            }
        } else {
            $data = array_merge($user_data, $profile_data, $errors, [
                'title' => 'Chỉnh sửa: ' . $user['NAME'],
                'user_id' => $user_id,
                'system_role' => $user['system_role']
            ]);
            $this->view('users/edit', $data);
        }
    }

    /**
     * Quản lý vai trò (Department Roles)
     */
    public function manage($user_id)
    {
        $user = $this->userModel->findById($user_id);
        if (!$user) {
            $this->redirect(BASE_URL . '/user');
        }

        $data = [
            'title' => 'Phân quyền cho: ' . $user['NAME'],
            'user' => $user,
            'current_roles' => $this->userModel->getRolesForUser($user_id),
            'all_departments' => $this->departmentModel->findAll(),
            'all_roles' => $this->roleModel->findAll()
        ];

        $this->view('users/manage', $data);
    }

    /**
     * Gán vai trò trong Ban
     */
    public function assignRole($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/user');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/user');
            exit;
        }

        $data = [
            'user_id' => $user_id,
            'department_id' => $_POST['department_id'],
            'role_id' => $_POST['role_id']
        ];

        if (empty($data['department_id']) || empty($data['role_id'])) {
            \set_flash_message('error', 'Vui lòng chọn Ban và Vai trò.');
        } elseif ($this->userModel->assignRole($data)) {
            \log_activity('user_role_assigned', 'Đã gán vai trò cho UserID: ' . $user_id);
            \set_flash_message('success', 'Gán vai trò thành công!');
        } else {
            \set_flash_message('error', 'Gán thất bại. Có thể vai trò này đã tồn tại.');
        }

        $this->redirect(BASE_URL . '/user/manage/' . $user_id);
    }

    /**
     * Thu hồi vai trò
     */
    public function revokeRole($user_id, $assignment_id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/user');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/user');
            exit;
        }

        $this->userModel->revokeRole($assignment_id);
        \log_activity('user_role_revoked', 'Đã thu hồi vai trò (AssignmentID: ' . $assignment_id . ').');
        \set_flash_message('success', 'Thu hồi vai trò thành công!');
        $this->redirect(BASE_URL . '/user/manage/' . $user_id);
    }

    /**
     * Cập nhật System Role (chỉ admin)
     */
    public function updateSystemRole($user_id)
    {
        $this->requireRole(['admin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/user');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/user');
            exit;
        }

        $new_role = $_POST['system_role'];
        $user = $this->userModel->findById($user_id);

        // Logic bảo vệ
        if ($user['id'] == $_SESSION['user_id']) {
            \set_flash_message('error', 'Không thể thay đổi vai trò của chính mình.');
            $this->redirect(BASE_URL . '/user');
        } elseif ($user['system_role'] == 'admin') {
            \set_flash_message('error', 'Không thể thay đổi vai trò của Admin khác.');
            $this->redirect(BASE_URL . '/user');
        } else {
            $this->userModel->setSystemRole($user_id, $new_role);
            \log_activity('user_system_role_updated', 'Đã cập nhật system_role cho UserID: ' . $user_id . ' thành [' . $new_role . '].');
            \set_flash_message('success', 'Cập nhật vai trò hệ thống thành công!');
            $this->redirect(BASE_URL . '/user');
        }
    }

    /**
     * Vô hiệu hóa/Kích hoạt tài khoản (soft delete)
     */
    public function toggleStatus($user_id)
    {
        $this->requireRole(['admin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/user');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/user');
            exit;
        }

        $user = $this->userModel->findById($user_id);
        if (!$user) {
            \set_flash_message('error', 'Không tìm thấy người dùng.');
            $this->redirect(BASE_URL . '/user');
            return;
        }

        // Không cho vô hiệu hóa chính mình
        if ($user['id'] == $_SESSION['user_id']) {
            \set_flash_message('error', 'Không thể vô hiệu hóa tài khoản của chính mình.');
            $this->redirect(BASE_URL . '/user');
            return;
        }

        // Toggle status (cần thêm cột is_active trong DB)
        // TODO: Implement toggleStatus in User model

        \set_flash_message('success', 'Đã cập nhật trạng thái tài khoản.');
        $this->redirect(BASE_URL . '/user');
    }
}
