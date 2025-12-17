<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Department;
use App\Models\Membership;
use App\Models\User;
use App\Models\ActivityLog;

class DepartmentController extends Controller
{
    private $deptModel;
    private $membershipModel;
    private $userModel;
    private $logModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->deptModel = new Department();
        $this->membershipModel = new Membership();
        $this->userModel = new User();
        $this->logModel = new ActivityLog();
    }

    public function index()
    {
        $departments = $this->deptModel->getAll();
        $counts = $this->deptModel->getMemberCounts();

        $countMap = [];
        foreach ($counts as $c) $countMap[$c['id']] = $c['member_count'];

        foreach ($departments as &$d) {
            $d['member_count'] = $countMap[$d['id']] ?? 0;
        }

        $this->view('departments/index', [
            'departments' => $departments,
            'title' => 'Cơ cấu tổ chức'
        ]);
    }

    public function create()
    {
        if ($_SESSION['user_role'] !== 'admin') $this->redirect(BASE_URL . '/department');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => $_POST['description']
            ];

            if ($this->deptModel->create($data)) {
                $this->logModel->create($_SESSION['user_id'], 'dept_create', 'Tạo ban: ' . $data['name']);
                \set_flash_message('success', 'Tạo ban thành công.');
                $this->redirect(BASE_URL . '/department');
            } else {
                \set_flash_message('error', 'Lỗi khi tạo ban.');
            }
        }
        $this->view('departments/create', ['title' => 'Thêm Ban mới']);
    }

    public function edit($id)
    {
        if ($_SESSION['user_role'] !== 'admin') $this->redirect(BASE_URL . '/department');

        $dept = $this->deptModel->findById($id);
        if (!$dept) $this->redirect(BASE_URL . '/department');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => $_POST['description']
            ];
            $this->deptModel->update($id, $data);
            $this->logModel->create($_SESSION['user_id'], 'dept_update', 'Cập nhật ban ID: ' . $id);
            \set_flash_message('success', 'Cập nhật thành công.');
            $this->redirect(BASE_URL . '/department');
        }

        $this->view('departments/edit', ['department' => $dept, 'title' => 'Sửa Ban']);
    }

    public function delete($id)
    {
        if ($_SESSION['user_role'] !== 'admin') $this->redirect(BASE_URL . '/department');

        $this->deptModel->delete($id);
        $this->logModel->create($_SESSION['user_id'], 'dept_delete', 'Xóa ban ID: ' . $id);
        \set_flash_message('success', 'Đã xóa ban.');
        $this->redirect(BASE_URL . '/department');
    }

    public function members($id)
    {
        $dept = $this->deptModel->findById($id);
        if (!$dept) $this->redirect(BASE_URL . '/department');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_SESSION['user_role'] !== 'admin') {
                \set_flash_message('error', 'Chỉ Admin mới được điều chỉnh nhân sự.');
                $this->redirect(BASE_URL . '/department/members/' . $id);
            }

            $action = $_POST['action'];

            if ($action == 'add') {
                $userId = $_POST['user_id'];
                $role = $_POST['role'];
                if ($this->membershipModel->addMember($userId, $id, $role)) {
                    \set_flash_message('success', 'Đã thêm thành viên.');
                } else {
                    \set_flash_message('error', 'Thành viên đã tồn tại trong ban này.');
                }
            } elseif ($action == 'remove') {
                $membershipId = $_POST['membership_id']; // Đã khớp với View mới
                $this->membershipModel->removeMember($membershipId);
                \set_flash_message('success', 'Đã xóa thành viên khỏi ban.');
            }
            // SỬA: Thêm logic cập nhật vai trò
            elseif ($action == 'update_role') {
                $membershipId = $_POST['membership_id'];
                $role = $_POST['role'];
                $this->membershipModel->updateRole($membershipId, $role);
                \set_flash_message('success', 'Đã cập nhật vai trò.');
            }

            $this->redirect(BASE_URL . '/department/members/' . $id);
        }

        $members = $this->membershipModel->getMembersByDepartment($id);
        $allUsers = $this->userModel->getAllUsers();

        $existingUserIds = array_column($members, 'user_id');
        $availableUsers = array_filter($allUsers['users'], function ($u) use ($existingUserIds) {
            return !in_array($u['id'], $existingUserIds);
        });

        $this->view('departments/members', [
            'department' => $dept,
            'members' => $members,
            'available_users' => $availableUsers,
            'title' => 'Thành viên: ' . $dept['name']
        ]);
    }
}
