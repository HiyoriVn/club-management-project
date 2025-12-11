<?php
// app/Controllers/DepartmentController.php (Unified)

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Department;
use App\Models\DepartmentRole;

class DepartmentController extends Controller
{
    private $departmentModel;
    private $roleModel;

    public function __construct()
    {
        $this->requireRole(['admin', 'subadmin']);
        $this->departmentModel = new Department();
        $this->roleModel = new DepartmentRole();
    }

    // ==================== QUẢN LÝ BAN ====================

    /**
     * Trang tổng quan: Departments + Roles (2 tabs)
     */
    public function index()
    {
        $departments = $this->departmentModel->findAll();
        $roles = $this->roleModel->findAll();

        $data = [
            'title' => 'Quản lý Cơ cấu Tổ chức',
            'departments' => $departments,
            'roles' => $roles,
            'active_tab' => $_GET['tab'] ?? 'departments' // departments hoặc roles
        ];

        $this->view('departments/index_unified', $data);
    }

    /**
     * Tạo Ban mới
     */
    public function createDepartment()
    {
        $allDepartments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Tạo Ban mới',
            'departments' => $allDepartments,
            'name' => '',
            'description' => '',
            'parent_id' => null,
            'name_err' => ''
        ];

        $this->view('departments/create_department', $data);
    }

    /**
     * Lưu Ban mới
     */
    public function storeDepartment()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/department');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/department');
            exit;
        }

        $allDepartments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Tạo Ban mới',
            'departments' => $allDepartments,
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'parent_id' => $_POST['parent_id'],
            'name_err' => ''
        ];

        // Validate
        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập tên Ban';
        } elseif ($this->departmentModel->findByName($data['name'])) {
            $data['name_err'] = 'Tên Ban này đã tồn tại';
        }

        if (empty($data['name_err'])) {
            if ($this->departmentModel->create($data)) {
                \log_activity('department_created', 'Đã tạo Ban mới: [' . $data['name'] . ']');
                \set_flash_message('success', 'Tạo Ban thành công!');
                $this->redirect(BASE_URL . '/department');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể tạo Ban.');
                $this->view('departments/create_department', $data);
            }
        } else {
            $this->view('departments/create_department', $data);
        }
    }

    /**
     * Sửa Ban
     */
    public function editDepartment($id)
    {
        $department = $this->departmentModel->findById($id);
        if (!$department) {
            $this->redirect(BASE_URL . '/department');
        }

        $allDepartments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Chỉnh sửa Ban',
            'departments' => $allDepartments,
            'id' => $id,
            'name' => $department['NAME'],
            'description' => $department['description'],
            'parent_id' => $department['parent_id'],
            'name_err' => ''
        ];

        $this->view('departments/edit_department', $data);
    }

    /**
     * Cập nhật Ban
     */
    public function updateDepartment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/department');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/department');
            exit;
        }

        $allDepartments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Chỉnh sửa Ban',
            'departments' => $allDepartments,
            'id' => $id,
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'parent_id' => $_POST['parent_id'],
            'name_err' => ''
        ];

        // Validate
        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập tên Ban';
        } else {
            $existing = $this->departmentModel->findByName($data['name']);
            if ($existing && $existing['id'] != $id) {
                $data['name_err'] = 'Tên Ban này đã tồn tại';
            }
        }

        if (empty($data['name_err'])) {
            if ($this->departmentModel->update($id, $data)) {
                \log_activity('department_updated', 'Đã cập nhật Ban ID ' . $id);
                \set_flash_message('success', 'Cập nhật Ban thành công!');
                $this->redirect(BASE_URL . '/department');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể cập nhật.');
                $this->view('departments/edit_department', $data);
            }
        } else {
            $this->view('departments/edit_department', $data);
        }
    }

    /**
     * Xóa Ban
     */
    public function destroyDepartment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/department');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/department');
            exit;
        }

        $department = $this->departmentModel->findById($id);
        if (!$department) {
            \set_flash_message('error', 'Không tìm thấy Ban để xóa.');
            $this->redirect(BASE_URL . '/department');
        }

        if ($this->departmentModel->delete($id)) {
            \log_activity('department_deleted', 'Đã xóa Ban: [' . $department['NAME'] . '] (ID: ' . $id . ')');
            \set_flash_message('success', 'Đã xóa Ban thành công!');
        } else {
            \set_flash_message('error', 'Có lỗi CSDL, không thể xóa Ban.');
        }

        $this->redirect(BASE_URL . '/department');
    }

    // ==================== QUẢN LÝ VAI TRÒ ====================

    /**
     * Tạo Vai trò mới
     */
    public function createRole()
    {
        $data = [
            'title' => 'Tạo Vai trò mới',
            'name' => '',
            'name_err' => ''
        ];

        $this->view('departments/create_role', $data);
    }

    /**
     * Lưu Vai trò mới
     */
    public function storeRole()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/department?tab=roles');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/department?tab=roles');
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
                \log_activity('role_created', 'Đã tạo vai trò mới: [' . $data['name'] . ']');
                \set_flash_message('success', 'Tạo vai trò thành công!');
                $this->redirect(BASE_URL . '/department?tab=roles');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL.');
                $this->view('departments/create_role', $data);
            }
        } else {
            $this->view('departments/create_role', $data);
        }
    }

    /**
     * Sửa Vai trò
     */
    public function editRole($id)
    {
        $role = $this->roleModel->findById($id);
        if (!$role) {
            $this->redirect(BASE_URL . '/department?tab=roles');
        }

        $data = [
            'title' => 'Chỉnh sửa Vai trò',
            'id' => $id,
            'name' => $role['NAME'],
            'name_err' => ''
        ];

        $this->view('departments/edit_role', $data);
    }

    /**
     * Cập nhật Vai trò
     */
    public function updateRole($id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/department?tab=roles');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/department?tab=roles');
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
                \log_activity('role_updated', 'Đã cập nhật vai trò ID ' . $id);
                \set_flash_message('success', 'Cập nhật vai trò thành công!');
                $this->redirect(BASE_URL . '/department?tab=roles');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL.');
                $this->view('departments/edit_role', $data);
            }
        } else {
            $this->view('departments/edit_role', $data);
        }
    }

    /**
     * Xóa Vai trò
     */
    public function destroyRole($id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/department?tab=roles');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/department?tab=roles');
            exit;
        }

        $role = $this->roleModel->findById($id);
        if (!$role) {
            \set_flash_message('error', 'Không tìm thấy vai trò.');
            $this->redirect(BASE_URL . '/department?tab=roles');
        }

        if ($this->roleModel->delete($id)) {
            \log_activity('role_deleted', 'Đã xóa vai trò: [' . $role['NAME'] . '] (ID: ' . $id . ')');
            \set_flash_message('success', 'Đã xóa vai trò thành công!');
        } else {
            \set_flash_message('error', 'Có lỗi CSDL.');
        }

        $this->redirect(BASE_URL . '/department?tab=roles');
    }
}
