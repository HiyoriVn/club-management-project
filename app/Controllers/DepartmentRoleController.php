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
                $this->redirect(BASE_URL . '/departmentrole');
            } else {
                die('Có lỗi CSDL xảy ra.');
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
                $this->redirect(BASE_URL . '/departmentrole');
            } else {
                die('Có lỗi CSDL xảy ra.');
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

        if (!$this->roleModel->findById($id)) {
            $this->redirect(BASE_URL . '/departmentrole');
        }

        // (Chúng ta sẽ cần 1 bước kiểm tra nâng cao: 
        // "Vai trò này có đang được ai sử dụng không?"
        // Nhưng tạm thời ta sẽ bỏ qua để làm cho nhanh)

        if ($this->roleModel->delete($id)) {
            $this->redirect(BASE_URL . '/departmentrole');
        } else {
            die('Có lỗi CSDL xảy ra.');
        }
    }
}
