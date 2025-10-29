<?php
// app/Controllers/FileController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\File;
use App\Models\Department; // Cần cho dropdown Ban

class FileController extends Controller
{

    private $fileModel;
    private $departmentModel;

    public function __construct()
    {
        // Ít nhất phải là Member mới thấy file
        $this->requireRole(['admin', 'subadmin', 'member']);

        // Nạp Model
        require_once ROOT_PATH . '/app/Models/File.php';
        require_once ROOT_PATH . '/app/Models/Department.php';

        $this->fileModel = new File();
        $this->departmentModel = new Department();
    }

    /**
     * (READ) Hiển thị trang danh sách Files
     */
    public function index()
    {
        $files = $this->fileModel->findAll();
        $departments = $this->departmentModel->findAll(); // Lấy list Ban cho form upload

        $data = [
            'title' => 'Quản lý Tài liệu',
            'files' => $files,
            'all_departments' => $departments, // Cho form upload
            'upload_err' => '' // Lưu lỗi upload (nếu có)
        ];

        // Kiểm tra xem có lỗi upload từ session không (sẽ làm ở hàm upload)
        if (isset($_SESSION['upload_error'])) {
            $data['upload_err'] = $_SESSION['upload_error'];
            unset($_SESSION['upload_error']); // Xóa lỗi sau khi hiển thị
        }

        $this->view('files/index', $data);
    }

    /**
     * (CREATE) Xử lý Upload file (POST)
     */
    public function upload()
    {
        // Chỉ admin/subadmin mới được upload
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_FILES['fileToUpload'])) {
            $this->redirect(BASE_URL . '/file');
        }

        $target_dir = UPLOAD_PATH; // Lấy từ config.php
        $department_id = $_POST['department_id'];
        $uploadedFile = $_FILES['fileToUpload'];

        // Lấy tên file gốc
        $original_filename = basename($uploadedFile["name"]);
        // Tạo tên file duy nhất (để tránh trùng lặp) bằng cách thêm timestamp
        $timestamp = time();
        $unique_filename = $timestamp . "_" . $original_filename;
        $target_file = $target_dir . $unique_filename;

        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // --- Các bước kiểm tra file (Có thể thêm nhiều kiểm tra hơn) ---
        // Kiểm tra file có thực sự được upload
        if ($uploadedFile["error"] !== UPLOAD_ERR_OK) {
            $_SESSION['upload_error'] = "Lỗi khi upload file. Mã lỗi: " . $uploadedFile["error"];
            $uploadOk = 0;
        }
        // Kiểm tra kích thước file (ví dụ: giới hạn 50MB)
        elseif ($uploadedFile["size"] > 50 * 1024 * 1024) {
            $_SESSION['upload_error'] = "File quá lớn (Tối đa 50MB).";
            $uploadOk = 0;
        }
        // (Có thể thêm kiểm tra loại file nếu muốn)
        // $allowed_types = ['jpg', 'png', 'pdf', 'docx'];
        // if (!in_array($fileType, $allowed_types)) { ... }

        // --- Xử lý upload ---
        if ($uploadOk == 1) {
            // Di chuyển file từ thư mục tạm vào thư mục uploads
            if (move_uploaded_file($uploadedFile["tmp_name"], $target_file)) {
                // Upload thành công, lưu vào CSDL
                $data = [
                    'file_name' => $original_filename, // Lưu tên gốc
                    'file_path' => $unique_filename,   // Lưu tên duy nhất trên server
                    'department_id' => $department_id
                ];
                if ($this->fileModel->create($data)) {
                    // Thành công
                } else {
                    $_SESSION['upload_error'] = "Lưu thông tin file vào CSDL thất bại.";
                    unlink($target_file); // Xóa file đã upload nếu lỗi CSDL
                }
            } else {
                $_SESSION['upload_error'] = "Có lỗi khi di chuyển file đã upload.";
            }
        }

        // Quay lại trang danh sách (nếu có lỗi, nó sẽ hiển thị)
        $this->redirect(BASE_URL . '/file');
    }

    /**
     * (DELETE) Xử lý Xóa file (POST)
     */
    public function destroy($id)
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/file');
        }

        // 1. Lấy thông tin file từ CSDL
        $file = $this->fileModel->findById($id);
        if (!$file) {
            $this->redirect(BASE_URL . '/file');
        }

        // 2. Xóa file vật lý trên server
        $file_path_on_server = UPLOAD_PATH . $file['file_path'];
        if (file_exists($file_path_on_server)) {
            unlink($file_path_on_server); // Hàm xóa file của PHP
        }

        // 3. Xóa thông tin file trong CSDL
        $this->fileModel->delete($id);

        $this->redirect(BASE_URL . '/file');
    }
}
