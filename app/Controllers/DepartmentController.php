<?php
// app/Controllers/DepartmentController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Department; // "use" Model Department

class DepartmentController extends Controller
{

    private $departmentModel;

    public function __construct()
    {
        // Áp dụng "chốt chặn": Chỉ admin hoặc subadmin mới được vào
        $this->requireRole(['admin', 'subadmin']);

        // Nạp model
        // (Tạm thời nạp thủ công, sau này ta nâng cấp Autoloader)
        require_once ROOT_PATH . '/app/Models/Department.php';
        $this->departmentModel = new Department();
    }

    /**
     * Hiển thị trang danh sách tất cả các Ban
     */
    public function index()
    {
        // Gọi Model để lấy tất cả departments
        $departments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Quản lý các Ban',
            'departments' => $departments
        ];

        $this->view('departments/index', $data);
    }
    /**
     * Hiển thị form tạo Ban mới (HTTP GET)
     */
    public function create()
    {
        // Lấy tất cả các Ban để làm dropdown "Ban cha"
        $allDepartments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Tạo Ban mới',
            'departments' => $allDepartments, // Dùng cho dropdown
            'name' => '',
            'description' => '',
            'parent_id' => null,
            'name_err' => ''
        ];

        $this->view('departments/create', $data);
    }

    /**
     * Lưu trữ Ban mới vào CSDL (HTTP POST)
     */
    public function store()
    {
        // Chỉ xử lý nếu request là POST
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/department');
        }

        // 1. Lấy dữ liệu "thô" từ form
        $allDepartments = $this->departmentModel->findAll(); // Lấy lại list cho dropdown
        $data = [
            'title' => 'Tạo Ban mới',
            'departments' => $allDepartments,
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'parent_id' => $_POST['parent_id'],
            'name_err' => ''
        ];

        // 2. Validate Dữ liệu
        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập tên Ban';
        } elseif ($this->departmentModel->findByName($data['name'])) {
            // Kiểm tra tên Ban đã tồn tại chưa
            $data['name_err'] = 'Tên Ban này đã tồn tại';
        }

        // 3. Xử lý sau khi Validate
        if (empty($data['name_err'])) {
            // Validation thành công
            if ($this->departmentModel->create($data)) {
                // Tạo thành công
                // (Sau này ta sẽ làm Flash Message "Tạo thành công")
                $this->redirect(BASE_URL . '/department');
            } else {
                // Lỗi CSDL
                die('Có lỗi xảy ra khi thêm vào CSDL. Vui lòng thử lại.');
            }
        } else {
            // Validation thất bại, hiển thị lại form với lỗi
            $this->view('departments/create', $data);
        }
    }
    /**
     * Hiển thị form Sửa Ban (HTTP GET)
     * @param int $id ID từ URL (vd: /department/edit/1)
     */
    public function edit($id)
    {
        // 1. Lấy thông tin của Ban cần sửa
        $department = $this->departmentModel->findById($id);

        // Nếu không tìm thấy Ban, đẩy về trang danh sách
        if (!$department) {
            $this->redirect(BASE_URL . '/department');
        }

        // 2. Lấy tất cả các Ban khác để làm dropdown "Ban cha"
        $allDepartments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Chỉnh sửa Ban',
            'departments' => $allDepartments, // Dùng cho dropdown

            // Dữ liệu của Ban cần sửa
            'id' => $id,
            'name' => $department['NAME'], // Lấy NAME (viết hoa) từ CSDL
            'description' => $department['description'],
            'parent_id' => $department['parent_id'],

            // Lỗi (nếu có)
            'name_err' => ''
        ];

        $this->view('departments/edit', $data);
    }

    /**
     * Xử lý cập nhật Ban (HTTP POST)
     * @param int $id ID từ URL (vd: /department/update/1)
     */
    public function update($id)
    {
        // Chỉ xử lý nếu request là POST
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/department');
        }

        // 1. Lấy dữ liệu "thô" từ form
        $allDepartments = $this->departmentModel->findAll(); // Lấy lại list cho dropdown
        $data = [
            'title' => 'Chỉnh sửa Ban',
            'departments' => $allDepartments,
            'id' => $id,
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'parent_id' => $_POST['parent_id'],
            'name_err' => ''
        ];

        // 2. Validate Dữ liệu
        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập tên Ban';
        } else {
            // Kiểm tra tên Ban đã tồn tại VÀ không phải là chính nó
            $existing = $this->departmentModel->findByName($data['name']);
            if ($existing && $existing['id'] != $id) {
                $data['name_err'] = 'Tên Ban này đã tồn tại';
            }
        }

        // 3. Xử lý sau khi Validate
        if (empty($data['name_err'])) {
            // Validation thành công
            if ($this->departmentModel->update($id, $data)) {
                // Cập nhật thành công
                $this->redirect(BASE_URL . '/department');
            } else {
                // Lỗi CSDL
                die('Có lỗi xảy ra khi cập nhật CSDL. Vui lòng thử lại.');
            }
        } else {
            // Validation thất bại, hiển thị lại form (edit) với lỗi
            $this->view('departments/edit', $data);
        }
    }
    
    /**
     * Xử lý Xóa Ban (HTTP POST)
     * @param int $id ID từ URL (vd: /department/destroy/1)
     */
    public function destroy($id)
    {
        // Chỉ xử lý nếu request là POST
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/department');
        }

        // 1. Tìm xem Ban này có tồn tại không
        $department = $this->departmentModel->findById($id);
        if (!$department) {
            // (Sau này ta sẽ làm flash message "Không tìm thấy Ban")
            $this->redirect(BASE_URL . '/department');
        }

        // 2. (Nâng cao - Tùy chọn) Kiểm tra xem Ban này có Ban con không
        // Nếu có Ban con, không cho xóa (để an toàn)
        // $this->db->query("SELECT COUNT(*) as count FROM departments WHERE parent_id = :id");
        // ... (Logic này ta có thể thêm sau)

        // 3. Tiến hành Xóa
        if ($this->departmentModel->delete($id)) {
            // Xóa thành công
            $this->redirect(BASE_URL . '/department');
        } else {
            // Lỗi CSDL
            die('Có lỗi xảy ra khi xóa CSDL. Vui lòng thử lại.');
        }
    }
}
