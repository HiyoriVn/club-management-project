<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\File;
use App\Models\Department;

class FileController extends Controller
{
    private $fileModel;
    private $deptModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->fileModel = new File();
        $this->deptModel = new Department();
    }

    public function index()
    {
        $filters = [];
        if (isset($_GET['dept_id']) && $_GET['dept_id'] != '') {
            $filters['department_id'] = $_GET['dept_id'];
        }

        $files = $this->fileModel->getAll($filters);
        $departments = $this->deptModel->getAll();

        $this->view('files/index', [
            'files' => $files,
            'departments' => $departments,
            'title' => 'Kho Tài liệu'
        ]);
    }

    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Kiểm tra xem có file được gửi lên không
            if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
                if (function_exists('set_flash_message')) \set_flash_message('error', 'Vui lòng chọn file hợp lệ.');
                $this->redirect(BASE_URL . '/file');
            }

            $file = $_FILES['file'];
            $deptId = !empty($_POST['department_id']) ? $_POST['department_id'] : null;

            // 2. Cấu hình thư mục upload (Sử dụng UPLOAD_PATH trong config)
            $uploadDir = UPLOAD_PATH;

            // Tự động tạo thư mục nếu chưa có
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // 3. Tạo tên file an toàn (timestamp_tên_gốc)
            $fileName = $file['name'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = time() . '_' . uniqid() . '.' . $fileExt;
            $destination = $uploadDir . $newFileName;

            // 4. Di chuyển file vào thư mục uploads
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // 5. Lưu thông tin vào Database
                $data = [
                    'file_name' => $fileName,      
                    'file_path' => $newFileName,  
                    'type'      => $fileExt,
                    'uploaded_by' => $_SESSION['user_id'],
                    'department_id' => $deptId,
                    'project_id' => null 
                ];

                if ($this->fileModel->create($data)) {
                    if (function_exists('set_flash_message')) \set_flash_message('success', 'Tải lên thành công: ' . $fileName);
                } else {
                    // Nếu lỗi DB thì xóa file rác
                    unlink($destination);
                    if (function_exists('set_flash_message')) \set_flash_message('error', 'Lỗi lưu dữ liệu.');
                }
            } else {
                if (function_exists('set_flash_message')) \set_flash_message('error', 'Không thể lưu file vào thư mục uploads. Kiểm tra quyền ghi (Permission).');
            }
        }
        $this->redirect(BASE_URL . '/file');
    }

    public function delete($id)
    {
        $file = $this->fileModel->findById($id);

        if ($file) {
            // Chỉ Admin hoặc người upload mới được xóa
            if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_id'] == $file['uploaded_by']) {
                // 1. Xóa file vật lý
                $filePath = UPLOAD_PATH . $file['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // 2. Xóa trong DB
                $this->fileModel->delete($id);
                if (function_exists('set_flash_message')) \set_flash_message('success', 'Đã xóa tài liệu.');
            } else {
                if (function_exists('set_flash_message')) \set_flash_message('error', 'Bạn không có quyền xóa.');
            }
        }

        $this->redirect(BASE_URL . '/file');
    }
}
