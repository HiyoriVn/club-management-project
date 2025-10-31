<?php
// app/Controllers/ProjectController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\Department;

class ProjectController extends Controller
{

    private $projectModel;
    private $userModel;
    private $departmentModel;

    public function __construct()
    {
        // Ít nhất phải là Member mới thấy danh sách Dự án
        $this->requireRole(['admin', 'subadmin', 'member']);

        // Nạp cả 3 Model
        require_once ROOT_PATH . '/app/Models/Project.php';
        require_once ROOT_PATH . '/app/Models/User.php';
        require_once ROOT_PATH . '/app/Models/Department.php';

        $this->projectModel = new Project();
        $this->userModel = new User();
        $this->departmentModel = new Department();
    }

    /**
     * (READ) Hiển thị trang danh sách tất cả Dự án
     */
    public function index()
    {
        $projects = $this->projectModel->findAll();
        $data = [
            'title' => 'Quản lý Dự án',
            'projects' => $projects
        ];
        $this->view('projects/index', $data);
    }

    /**
     * (CREATE) Hiển thị form tạo Dự án (GET)
     */
    public function create()
    {
        // Chỉ admin/subadmin mới được tạo
        $this->requireRole(['admin', 'subadmin']);

        $data = [
            'title' => 'Tạo Dự án mới',
            // Dữ liệu cho Form
            'name' => '',
            'description' => '',
            'start_date' => '',
            'end_date' => '',
            'leader_id' => null,
            'department_id' => null,
            'status' => 'planning', // Mặc định
            'name_err' => '',

            // Dữ liệu cho Dropdowns
            'all_users' => $this->userModel->getAllUsers(), // Lấy list user
            'all_departments' => $this->departmentModel->findAll() // Lấy list Ban
        ];

        $this->view('projects/create', $data);
    }

    /**
     * (CREATE) Xử lý lưu Dự án (POST)
     */
    public function store()
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/project');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $data = [
            'title' => 'Tạo Dự án mới',
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'leader_id' => $_POST['leader_id'],
            'department_id' => $_POST['department_id'],
            'status' => $_POST['status'],
            'name_err' => '',

            'all_users' => $this->userModel->getAllUsers(),
            'all_departments' => $this->departmentModel->findAll()
        ];

        // Validate
        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập Tên Dự án';
        }

        if (empty($data['name_err'])) {
            if ($this->projectModel->create($data)) {
                \set_flash_message('success', 'Tạo dự án [' . htmlspecialchars($data['name']) . '] thành công!');
                \log_activity('project_created', 'Đã tạo dự án mới: [' . $data['name'] . '].');
                $this->redirect(BASE_URL . '/project');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể tạo dự án.');
                $this->redirect(BASE_URL . '/project/create');
            }
        } else {
            // Lỗi, tải lại view create
            $this->view('projects/create', $data);
        }
    }

    /**
     * (UPDATE) Hiển thị form Sửa Dự án (GET)
     */
    public function edit($id)
    {
        $this->requireRole(['admin', 'subadmin']);

        $project = $this->projectModel->findById($id);
        if (!$project) {
            $this->redirect(BASE_URL . '/project');
        }

        $data = [
            'title' => 'Chỉnh sửa Dự án',
            'id' => $id,
            'name' => $project['NAME'], // Dùng NAME (viết hoa) từ CSDL
            'description' => $project['description'],
            'start_date' => $project['start_date'],
            'end_date' => $project['end_date'],
            'leader_id' => $project['leader_id'],
            'department_id' => $project['department_id'],
            'status' => $project['STATUS'],
            'name_err' => '',

            'all_users' => $this->userModel->getAllUsers(),
            'all_departments' => $this->departmentModel->findAll()
        ];

        $this->view('projects/edit', $data);
    }

    /**
     * (UPDATE) Xử lý cập nhật Dự án (POST)
     */
    public function update($id)
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/project');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $data = [
            'title' => 'Chỉnh sửa Dự án',
            'id' => $id,
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'leader_id' => $_POST['leader_id'],
            'department_id' => $_POST['department_id'],
            'status' => $_POST['status'],
            'name_err' => '',

            'all_users' => $this->userModel->getAllUsers(),
            'all_departments' => $this->departmentModel->findAll()
        ];

        // Validate
        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập Tên Dự án';
        }

        if (empty($data['name_err'])) {
            if ($this->projectModel->update($id, $data)) {
                \log_activity('project_updated', 'Đã cập nhật dự án: [' . $data['name'] . '] (ID: ' . $id . ').');
                \set_flash_message('success', 'Cập nhật dự án [' . htmlspecialchars($data['name']) . '] thành công!');
                $this->redirect(BASE_URL . '/project');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể cập nhật.');
                $this->redirect(BASE_URL . '/project/edit/' . $id);
            }
        } else {
            $this->view('projects/edit', $data);
        }
    }

    /**
     * (DELETE) Xử lý Xóa Dự án (POST)
     */
    public function destroy($id)
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/project');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $project = $this->projectModel->findById($id); // Lấy tên
        if (!$project) {
            \set_flash_message('error', 'Không tìm thấy dự án.');
            $this->redirect(BASE_URL . '/project');
        }

        if ($this->projectModel->delete($id)) {
            \log_activity('project_deleted', 'Đã xóa dự án: [' . $project['NAME'] . '] (ID: ' . $id . ').');
            \set_flash_message('success', 'Đã xóa dự án [' . htmlspecialchars($project['NAME']) . '] thành công!');
            $this->redirect(BASE_URL . '/project');
        } else {
            \set_flash_message('error', 'Có lỗi CSDL khi xóa.');
            $this->redirect(BASE_URL . '/project');
        }
    }

    /**
     * (READ) Hiển thị trang Quản lý Thành viên cho 1 Dự án (GET)
     */
    public function manage($project_id)
    {
        // Chỉ admin/subadmin mới được quản lý
        $this->requireRole(['admin', 'subadmin']);

        // 1. Lấy thông tin dự án
        $project = $this->projectModel->findById($project_id);
        if (!$project) {
            $this->redirect(BASE_URL . '/project');
        }

        // 2. Lấy danh sách (chỉ 'member' trở lên) để thêm vào
        $all_available_users = $this->userModel->getAllUsers(); // (Tạm thời lấy hết, sau này có thể lọc)

        $data = [
            'title' => 'Quản lý thành viên: ' . $project['NAME'],
            'project' => $project,
            // Lấy các thành viên dự án này đang có
            'current_members' => $this->projectModel->getMembers($project_id),
            // Lấy tất cả user để làm dropdown
            'all_users' => $all_available_users
        ];

        $this->view('projects/manage', $data);
    }

    /**
     * (CREATE) Xử lý thêm thành viên vào dự án (POST)
     */
    public function addMember($project_id)
    {
        $this->requireRole(['admin', 'subadmin']);
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/project');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $user_id = $_POST['user_id'];
        $role = $_POST['role'];

        // Validate đơn giản
        if (empty($user_id) || empty($role)) {
            \set_flash_message('error', 'Vui lòng chọn thành viên và vai trò.');
            $this->redirect(BASE_URL . '/project/manage/' . $project_id);
        }

        if ($this->projectModel->addMember($project_id, $user_id, $role)) {
            \log_activity('project_member_added', 'Đã thêm UserID: ' . $user_id . ' vào ProjectID: ' . $project_id . ' với vai trò [' . $role . '].');
            \set_flash_message('success', 'Thêm thành viên vào dự án thành công!');
        } else {
            \set_flash_message('error', 'Thêm thất bại. Thành viên này có thể đã ở trong dự án.');
        }

        $this->redirect(BASE_URL . '/project/manage/' . $project_id);
    }

    /**
     * (DELETE) Xử lý xóa thành viên khỏi dự án (POST)
     */
    public function removeMember($project_id, $assignment_id)
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/project');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $this->projectModel->removeMember($assignment_id);
        \log_activity('project_member_removed', 'Đã xóa thành viên (AssignmentID: ' . $assignment_id . ') khỏi ProjectID: ' . $project_id . '.');
        \set_flash_message('success', 'Xóa thành viên khỏi dự án thành công!');
        $this->redirect(BASE_URL . '/project/manage/' . $project_id);
    }

    /**
     * (READ) Hiển thị trang Quản lý Tasks (Kanban) cho 1 Dự án (GET)
     */
    public function tasks($project_id)
    {
        // Chỉ admin/subadmin mới được quản lý
        $this->requireRole(['admin', 'subadmin']);

        // 1. Lấy thông tin dự án
        $project = $this->projectModel->findById($project_id);
        if (!$project) {
            $this->redirect(BASE_URL . '/project');
        }

        // 2. Lấy tất cả tasks của dự án
        $all_tasks = $this->projectModel->getTasks($project_id);

        // 3. Phân loại tasks vào 3 cột
        $tasks_by_status = [
            'todo' => [],
            'in_progress' => [],
            'done' => []
        ];
        foreach ($all_tasks as $task) {
            $tasks_by_status[$task['STATUS']][] = $task;
        }

        $data = [
            'title' => 'Tasks: ' . $project['NAME'],
            'project' => $project,
            'tasks_by_status' => $tasks_by_status, // Dữ liệu 3 cột
            'all_members' => $this->projectModel->getMembers($project_id) // Dùng cho dropdown "Gán cho"
        ];

        $this->view('projects/tasks', $data);
    }

    /**
     * (CREATE) Xử lý tạo Task mới (POST)
     */
    public function storeTask($project_id)
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/project');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $data = [
            'project_id' => $project_id,
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'assigned_to' => $_POST['assigned_to'],
            'due_date' => $_POST['due_date']
        ];

        // Validate
        if (!empty($data['title'])) {
            $this->projectModel->createTask($data);
            \log_activity('project_task_created', 'Đã tạo task mới: [' . $data['title'] . '] cho ProjectID: ' . $project_id . '.');
            \set_flash_message('success', 'Tạo task [' . htmlspecialchars($data['title']) . '] thành công!');
        } else {
            \set_flash_message('error', 'Tiêu đề Task là bắt buộc.');
        }

        $this->redirect(BASE_URL . '/project/tasks/' . $project_id);
    }

    /**
     * (UPDATE) Xử lý di chuyển Task (đổi status) (POST)
     */
    public function moveTask($project_id, $task_id)
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/project');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $new_status = $_POST['new_status'];
        $this->projectModel->updateTaskStatus($task_id, $new_status);
        \log_activity('project_task_moved', 'Đã chuyển TaskID: ' . $task_id . ' (ProjectID: ' . $project_id . ') sang trạng thái [' . $new_status . '].');
        \set_flash_message('info', 'Đã di chuyển task.');
        $this->redirect(BASE_URL . '/project/tasks/' . $project_id);
    }

    /**
     * (DELETE) Xử lý xóa Task (POST)
     */
    public function deleteTask($project_id, $task_id)
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/project');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $this->projectModel->deleteTask($task_id);
        \log_activity('project_task_deleted', 'Đã xóa TaskID: ' . $task_id . ' (ProjectID: ' . $project_id . ').');
        \set_flash_message('success', 'Đã xóa task.');
        $this->redirect(BASE_URL . '/project/tasks/' . $project_id);
    }
}
