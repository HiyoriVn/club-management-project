<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\File;
use App\Models\ActivityLog;

class FileController extends Controller
{
    private $fileModel;
    private $logModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->fileModel = new File();
        $this->logModel = new ActivityLog();
    }

    public function index()
    {
        // Filter theo project hoặc department
        $projectId = $_GET['project_id'] ?? null;
        $deptId = $_GET['department_id'] ?? null;

        $files = $this->fileModel->getAll($projectId, $deptId);

        $this->view('files/index', [
            'files' => $files,
            'title' => 'Tài liệu & Tập tin'
        ]);
    }

    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {

                $file = $_FILES['file_upload'];
                $fileName = $file['name'];
                $fileTmp = $file['tmp_name'];
                $fileType = $file['type'];

                // Validate Extension
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];

                if (!in_array($ext, $allowed)) {
                    \set_flash_message('error', 'Loại file không được phép.');
                    $this->redirect($_SERVER['HTTP_REFERER']);
                }

                // Generate unique name
                $newName = uniqid() . '_' . $fileName;
                $destPath = UPLOAD_PATH . $newName;

                // Move file
                if (move_uploaded_file($fileTmp, $destPath)) {
                    $data = [
                        'file_name' => $fileName,
                        'file_path' => $newName, // Lưu tên file thôi, đường dẫn full config ở View
                        'type' => $fileType,
                        'uploaded_by' => $_SESSION['user_id'],
                        'project_id' => !empty($_POST['project_id']) ? $_POST['project_id'] : null,
                        'department_id' => !empty($_POST['department_id']) ? $_POST['department_id'] : null
                    ];

                    $this->fileModel->create($data);
                    $this->logModel->create($_SESSION['user_id'], 'file_upload', "Upload file: $fileName");

                    \set_flash_message('success', 'Upload thành công.');
                } else {
                    \set_flash_message('error', 'Lỗi khi lưu file.');
                }
            } else {
                \set_flash_message('error', 'Vui lòng chọn file hợp lệ.');
            }
            $this->redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/file');
        }
    }

    public function delete($id)
    {
        $file = $this->fileModel->findById($id);

        // Check quyền: Chỉ người up hoặc Admin được xóa
        if ($file && ($_SESSION['user_role'] == 'admin' || $file['uploaded_by'] == $_SESSION['user_id'])) {

            // Xóa trong DB
            if ($this->fileModel->delete($id)) {
                // Xóa file vật lý
                $filePath = UPLOAD_PATH . $file['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                \set_flash_message('success', 'Đã xóa file.');
            }
        } else {
            \set_flash_message('error', 'Không có quyền xóa.');
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/file');
    }
}