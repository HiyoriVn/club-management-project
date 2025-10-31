<?php
// app/Controllers/DepartmentRoleController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\DepartmentRole; // "use" Model mới

class DepartmentRoleController extends Controller
{

    private $roleModel;

    public function __construct()
    {
        // Chỉ "Super Admin" mới được quản lý Vai trò
        $this->requireRole(['admin']);

        require_once ROOT_PATH . '/app/Models/DepartmentRole.php';
        $this->roleModel = new DepartmentRole();
    }

    /**
     * (READ) Hiển thị trang danh sách tất cả các Vai trò
     */
    public function index()
    {
        $roles = $this->roleModel->findAll();
        $data = [
            'title' => 'Quản lý Vai trò trong Ban',
            'roles' => $roles
        ];
        $this->view('department_roles/index', $data);
    }

    /**
     * (CREATE) Hiển thị form tạo Vai trò mới (GET)
     */
    public function create()
    {
        $data = [
            'title' => 'Tạo Vai trò mới',
            'name' => '',
            'name_err' => ''
        ];
        $this->view('department_roles/create', $data);
    }

    /**
     * (CREATE) Xử lý lưu Vai trò mới (POST)
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/departmentrole');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $data = [
            'title' => 'Tạo Vai trò mới',
            'name' => trim($_POST['name']),
            'name_err' => ''
        ];

        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập tên Vai trò';
        } elseif ($this->roleModel->findByName($data['name'])) {
            $data['name_err'] = 'Tên Vai trò này đã tồn tại';
        }

        if (empty($data['name_err'])) {
            if ($this->roleModel->create($data)) {
                \log_activity('department_role_created', 'Đã tạo vai trò mới: [' . $data['name'] . '].');
                \set_flash_message('success', 'Tạo vai trò [' . htmlspecialchars($data['name']) . '] thành công!');
                $this->redirect(BASE_URL . '/departmentrole');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể tạo vai trò.');
                $this->redirect(BASE_URL . '/departmentrole/create');
            }
        } else {
            $this->view('department_roles/create', $data);
        }
    }

    /**
     * (UPDATE) Hiển thị form Sửa Vai trò (GET)
     */
    public function edit($id)
    {
        $role = $this->roleModel->findById($id);
        if (!$role) {
            $this->redirect(BASE_URL . '/departmentrole');
        }

        $data = [
            'title' => 'Chỉnh sửa Vai trò',
            'id' => $id,
            'name' => $role['NAME'], // Dùng NAME viết hoa
            'name_err' => ''
        ];
        $this->view('department_roles/edit', $data);
    }

    /**
     * (UPDATE) Xử lý cập nhật Vai trò (POST)
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/departmentrole');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $data = [
            'title' => 'Chỉnh sửa Vai trò',
            'id' => $id,
            'name' => trim($_POST['name']),
            'name_err' => ''
        ];

        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập tên Vai trò';
        } else {
            $existing = $this->roleModel->findByName($data['name']);
            if ($existing && $existing['id'] != $id) {
                $data['name_err'] = 'Tên Vai trò này đã tồn tại';
            }
        }

        if (empty($data['name_err'])) {
            if ($this->roleModel->update($id, $data)) {
                \log_activity('department_role_updated', 'Đã cập nhật vai trò (ID: ' . $id . ') thành: [' . $data['name'] . '].');
                \set_flash_message('success', 'Cập nhật vai trò [' . htmlspecialchars($data['name']) . '] thành công!');
                $this->redirect(BASE_URL . '/departmentrole');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể cập nhật.');
                $this->redirect(BASE_URL . '/departmentrole/edit/' . $id);
            }
        } else {
            $this->view('department_roles/edit', $data);
        }
    }

    /**
     * (DELETE) Xử lý Xóa Vai trò (POST)
     */
    public function destroy($id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/departmentrole');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $role = $this->roleModel->findById($id); // Lấy tên trước khi xóa
        if (!$role) {
            \set_flash_message('error', 'Không tìm thấy vai trò để xóa.');
            $this->redirect(BASE_URL . '/departmentrole');
        }

        // (Chúng ta sẽ cần 1 bước kiểm tra nâng cao: 
        // "Vai trò này có đang được ai sử dụng không?"
        // Nhưng tạm thời ta sẽ bỏ qua để làm cho nhanh)

        if ($this->roleModel->delete($id)) {
            \log_activity('department_role_deleted', 'Đã xóa vai trò: [' . $role['NAME'] . '] (ID: ' . $id . ').');
            \set_flash_message('success', 'Đã xóa vai trò [' . htmlspecialchars($role['NAME']) . '] thành công!');
            $this->redirect(BASE_URL . '/departmentrole');
        } else {
            \set_flash_message('error', 'Có lỗi CSDL, không thể xóa vai trò.');
            $this->redirect(BASE_URL . '/departmentrole');
        }
    }
}
