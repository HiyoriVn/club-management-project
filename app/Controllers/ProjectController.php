<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\Department;
use App\Models\ActivityLog;

class ProjectController extends Controller
{
    private $projectModel;
    private $userModel;
    private $deptModel;
    private $logModel;

    public function __construct()
    {
        $this->requireLogin();

        $this->projectModel = new Project();
        $this->userModel = new User();
        $this->deptModel = new Department(); // Để chọn ban phụ trách nếu cần
        $this->logModel = new ActivityLog();
    }

    /**
     * Danh sách dự án / sự kiện
     */
    public function index()
    {
        // Filter type: 'project' (default) hoặc 'event' hoặc 'all'
        $type = $_GET['type'] ?? 'project';
        $status = $_GET['status'] ?? null;

        if ($type === 'event') {
            $dataList = $this->projectModel->getAllEvents(50, $status);
            $title = 'Danh sách Sự kiện';
        } elseif ($type === 'project') {
            $dataList = $this->projectModel->getAllProjects(50, $status);
            $title = 'Danh sách Dự án';
        } else {
            // Nếu muốn lấy cả 2, cần custom method trong Model hoặc merge
            // Tạm thời default về project nếu param sai
            $dataList = $this->projectModel->getAllProjects(50, $status);
            $title = 'Danh sách Dự án';
        }

        $data = [
            'projects' => $dataList,
            'current_type' => $type,
            'current_status' => $status,
            'title' => $title
        ];

        $this->view('projects/index', $data);
    }

    /**
     * Chi tiết dự án/sự kiện
     */
    public function detail($id)
    {
        $project = $this->projectModel->findById($id);
        if (!$project) {
            \set_flash_message('error', 'Dữ liệu không tồn tại.');
            $this->redirect(BASE_URL . '/project');
        }

        $members = $this->projectModel->getMembers($id);

        $data = [
            'project' => $project,
            'members' => $members,
            'title' => $project['name']
        ];

        // Load view 'projects/view.php'
        $this->view('projects/view', $data);
    }

    /**
     * Tạo mới
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => $_POST['description'],
                'type' => $_POST['type'] ?? 'project', // Quan trọng
                'status' => $_POST['status'] ?? 'planning',
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'leader_id' => $_POST['leader_id'] ?? null,
                'department_id' => $_POST['department_id'] ?? null
            ];

            if (empty($data['name'])) {
                \set_flash_message('error', 'Tên không được để trống.');
                $this->redirect(BASE_URL . '/project/create?type=' . $data['type']);
            }

            $id = $this->projectModel->create($data);
            if ($id) {
                // Tự động add người tạo làm thành viên (hoặc Leader)
                $this->projectModel->addMember($id, $_SESSION['user_id'], 'Member');

                $this->logModel->create($_SESSION['user_id'], 'project_create', "Tạo " . $data['type'] . ": " . $data['name']);
                \set_flash_message('success', 'Tạo thành công.');
                $this->redirect(BASE_URL . '/project/index?type=' . $data['type']);
            } else {
                \set_flash_message('error', 'Lỗi hệ thống.');
                $this->redirect(BASE_URL . '/project/create');
            }
        } else {
            // Chuẩn bị data cho form
            $users = $this->userModel->getAllUsers(); // Cho dropdown chọn Leader
            $depts = $this->deptModel->getAll();      // Cho dropdown chọn Ban
            $type = $_GET['type'] ?? 'project';

            $data = [
                'users' => $users['users'] ?? [], // getAllUsers trả về array có key 'users'
                'departments' => $depts,
                'type' => $type,
                'title' => 'Tạo ' . ($type == 'event' ? 'Sự kiện' : 'Dự án') . ' mới'
            ];

            $this->view('projects/create', $data);
        }
    }

    /**
     * Chỉnh sửa
     */
    public function edit($id)
    {
        $project = $this->projectModel->findById($id);
        if (!$project) {
            $this->redirect(BASE_URL . '/project');
        }

        // Check quyền: Chỉ Admin hoặc Leader mới được sửa (Logic mở rộng sau)
        // Hiện tại cho phép login user sửa

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => $_POST['description'],
                'status' => $_POST['status'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'leader_id' => $_POST['leader_id'],
                'department_id' => $_POST['department_id']
            ];

            if ($this->projectModel->update($id, $data)) {
                $this->logModel->create($_SESSION['user_id'], 'project_update', "Cập nhật ID: $id");
                \set_flash_message('success', 'Cập nhật thành công.');
                $this->redirect(BASE_URL . '/project/view/' . $id);
            } else {
                \set_flash_message('error', 'Lỗi cập nhật.');
                $this->redirect(BASE_URL . '/project/edit/' . $id);
            }
        } else {
            $users = $this->userModel->getAllUsers();
            $depts = $this->deptModel->getAll();

            $data = [
                'project' => $project,
                'users' => $users['users'] ?? [],
                'departments' => $depts,
                'title' => 'Chỉnh sửa: ' . $project['name']
            ];
            $this->view('projects/edit', $data);
        }
    }

    /**
     * Quản lý thành viên (Thêm/Xóa)
     */
    public function members($id)
    {
        $project = $this->projectModel->findById($id);
        if (!$project) $this->redirect(BASE_URL . '/project');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Action: add hoặc remove
            $action = $_POST['action'] ?? '';
            $userId = $_POST['user_id'] ?? 0;
            $role = $_POST['role'] ?? 'Member';

            if ($action == 'add') {
                if ($this->projectModel->addMember($id, $userId, $role)) {
                    \set_flash_message('success', 'Đã thêm thành viên.');
                } else {
                    \set_flash_message('error', 'Thành viên đã tồn tại hoặc lỗi.');
                }
            } elseif ($action == 'remove') {
                $this->projectModel->removeMember($id, $userId);
                \set_flash_message('success', 'Đã xóa thành viên.');
            }

            $this->redirect(BASE_URL . '/project/members/' . $id);
        } else {
            $currentMembers = $this->projectModel->getMembers($id);
            $allUsers = $this->userModel->getAllUsers();

            // Lọc ra những user chưa tham gia để hiển thị trong dropdown thêm mới
            $memberIds = array_column($currentMembers, 'user_id');
            $availableUsers = array_filter($allUsers['users'], function ($u) use ($memberIds) {
                return !in_array($u['id'], $memberIds);
            });

            $data = [
                'project' => $project,
                'members' => $currentMembers,
                'available_users' => $availableUsers,
                'title' => 'Thành viên: ' . $project['name']
            ];
            $this->view('projects/members', $data);
        }
    }

    /**
     * Xóa dự án (Admin only)
     */
    public function delete($id)
    {
        if ($_SESSION['user_role'] !== 'admin') {
            \set_flash_message('error', 'Bạn không có quyền xóa dự án.');
            $this->redirect(BASE_URL . '/project');
        }

        $project = $this->projectModel->findById($id);
        if ($project && $this->projectModel->delete($id)) {
            $this->logModel->create($_SESSION['user_id'], 'project_delete', "Xóa project ID: $id");
            \set_flash_message('success', 'Đã xóa dự án.');
        } else {
            \set_flash_message('error', 'Xóa thất bại.');
        }
        $this->redirect(BASE_URL . '/project');
    }
}
