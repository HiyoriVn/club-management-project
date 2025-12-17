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
        $this->deptModel = new Department();
        $this->logModel = new ActivityLog();
    }

    // 1. DANH SÁCH DỰ ÁN
    public function index()
    {
        $projects = $this->projectModel->getAll();

        $this->view('projects/index', [
            'projects' => $projects,
            'title' => 'Danh sách Dự án'
        ]);
    }
    // 2. CHI TIẾT DỰ ÁN
    public function detail($id)
    {
        $project = $this->projectModel->findById($id);

        if (!$project) {
            $this->redirect(BASE_URL . '/project');
        }

        $members = $this->projectModel->getProjectMembers($id);
        $tasks = $this->projectModel->getTasks($id); // Lấy cả task xong và chưa xong

        // Kiểm tra xem User hiện tại có phải là thành viên dự án không
        $isMember = in_array($_SESSION['user_role'], ['admin', 'subadmin']) || $this->projectModel->isMember($id, $_SESSION['user_id']);

        // Admin hệ thống luôn có quyền như thành viên
        if (in_array($_SESSION['user_role'], ['admin', 'subadmin'])) {
            $isMember = true;
        }

        $this->view('projects/view', [
            'project' => $project,
            'members' => $members,
            'tasks' => $tasks,
            'is_member' => $isMember, // Biến này dùng để ẩn/hiện nút "Vào làm việc"
            'title' => $project['name']
        ]);
    }

    // 3. TẠO DỰ ÁN (Chỉ Admin/Subadmin)
    public function create()
    {
        // 1. Check quyền: Chỉ Admin/Subadmin mới được vào
        if (in_array($_SESSION['user_role'], ['member'])) {
            if (function_exists('set_flash_message')) \set_flash_message('error', 'Bạn không có quyền tạo dự án.');
            $this->redirect(BASE_URL . '/project');
        }

        // 2. Xử lý khi Submit form
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => $_POST['description'],
                'type' => $_POST['type'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'status' => 'planning',
                'leader_id' => !empty($_POST['leader_id']) ? $_POST['leader_id'] : null,
                'department_id' => !empty($_POST['department_id']) ? $_POST['department_id'] : null
            ];

            // Gọi Model để tạo dự án
            $newId = $this->projectModel->create($data);

            if ($newId) {
                if (function_exists('set_flash_message')) \set_flash_message('success', 'Tạo dự án thành công.');
                $this->redirect(BASE_URL . '/project');
            } else {
                if (function_exists('set_flash_message')) \set_flash_message('error', 'Lỗi hệ thống.');
            }
        }

        // 3. Chuẩn bị dữ liệu cho View (SỬA Ở ĐÂY)
        // Lấy danh sách Leader (Chỉ Admin/Subadmin)
        $leaders = $this->userModel->getLeadersOnly();

        // Lấy danh sách Ban
        $depts = $this->deptModel->getAll();

        // Truyền biến $leaders sang View
        $this->view('projects/create', [
            'leaders' => $leaders,
            'departments' => $depts,
            'title' => 'Tạo Dự án mới'
        ]);
    }

    // 4. CHỈNH SỬA DỰ ÁN
    public function edit($id)
    {
        $project = $this->projectModel->findById($id);
        if (!$project) $this->redirect(BASE_URL . '/project');

        if (!in_array($_SESSION['user_role'], ['admin', 'subadmin']) && $_SESSION['user_id'] != $project['leader_id']) {
            if (function_exists('set_flash_message')) \set_flash_message('error', 'Bạn không có quyền sửa dự án này.');
            $this->redirect(BASE_URL . '/project/detail/' . $id);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => $_POST['description'],
                'status' => $_POST['status'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'leader_id' => !empty($_POST['leader_id']) ? $_POST['leader_id'] : null,
                'department_id' => !empty($_POST['department_id']) ? $_POST['department_id'] : null
            ];

            if ($this->projectModel->update($id, $data)) {
                // Nếu đổi Leader, đảm bảo Leader mới có trong nhóm
                if (!empty($data['leader_id'])) {
                    $this->projectModel->addMember($id, $data['leader_id'], 'Leader');
                }

                $this->logModel->create($_SESSION['user_id'], 'project_update', "Cập nhật dự án ID: $id");
                if (function_exists('set_flash_message')) \set_flash_message('success', 'Cập nhật thành công.');
                $this->redirect(BASE_URL . '/project/detail/' . $id);
            } else {
                if (function_exists('set_flash_message')) \set_flash_message('error', 'Lỗi cập nhật.');
            }
        }

        $usersData = $this->userModel->getAllUsers();
        $users = isset($usersData['users']) ? $usersData['users'] : $usersData;
        $depts = $this->deptModel->getAll();

        $this->view('projects/edit', [
            'project' => $project,
            'users' => $users,
            'departments' => $depts,
            'title' => 'Sửa dự án: ' . $project['name']
        ]);
    }

    // 5. QUẢN LÝ THÀNH VIÊN DỰ ÁN
    public function members($id)
    {
        $project = $this->projectModel->findById($id);
        if (!$project) $this->redirect(BASE_URL . '/project');

        // Quyền: Admin, Subadmin HOẶC Leader
        $canManage = in_array($_SESSION['user_role'], ['admin', 'subadmin']) || $_SESSION['user_id'] == $project['leader_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$canManage) {
                if (function_exists('set_flash_message')) \set_flash_message('error', 'Bạn không có quyền quản lý thành viên.');
                $this->redirect(BASE_URL . '/project/detail/' . $id);
            }

            $action = $_POST['action'];
            $userId = $_POST['user_id'];

            if ($action == 'add') {
                $role = $_POST['role'] ?? 'Member';
                if ($this->projectModel->addMember($id, $userId, $role)) {
                    if (function_exists('set_flash_message')) \set_flash_message('success', 'Đã thêm thành viên.');
                } else {
                    if (function_exists('set_flash_message')) \set_flash_message('error', 'Thành viên đã tồn tại.');
                }
            } elseif ($action == 'remove') {
                $this->projectModel->removeMember($id, $userId);
                if (function_exists('set_flash_message')) \set_flash_message('success', 'Đã xóa thành viên khỏi dự án.');
            }

            $this->redirect(BASE_URL . '/project/members/' . $id);
        }

        // Lấy danh sách thành viên hiện tại
        $currentMembers = $this->projectModel->getProjectMembers($id);

        // Lấy danh sách user để chọn thêm mới (trừ những người đã tham gia)
        $usersData = $this->userModel->getAllUsers();
        $allUsers = isset($usersData['users']) ? $usersData['users'] : $usersData;

        $currentIds = array_column($currentMembers, 'user_id');
        $availableUsers = array_filter($allUsers, function ($u) use ($currentIds) {
            return !in_array($u['id'], $currentIds);
        });

        $this->view('projects/members', [
            'project' => $project,
            'members' => $currentMembers,
            'available_users' => $availableUsers,
            'can_manage' => $canManage, // Truyền biến này xuống view để ẩn/hiện nút xóa
            'title' => 'Thành viên: ' . $project['name']
        ]);
    }

    // 6. XÓA DỰ ÁN
    public function delete($id)
    {
        if (!in_array($_SESSION['user_role'], ['admin', 'subadmin'])) {
            if (function_exists('set_flash_message')) \set_flash_message('error', 'Bạn không có quyền xóa dự án.');
            $this->redirect(BASE_URL . '/project');
        }

        if ($this->projectModel->delete($id)) {
            $this->logModel->create($_SESSION['user_id'], 'project_delete', "Xóa dự án ID: $id");
            if (function_exists('set_flash_message')) \set_flash_message('success', 'Đã xóa dự án.');
        } else {
            if (function_exists('set_flash_message')) \set_flash_message('error', 'Lỗi khi xóa.');
        }

        $this->redirect(BASE_URL . '/project');
    }
}
