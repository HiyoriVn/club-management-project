<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Announcement;
use App\Models\Department;
use App\Models\Membership;

class AnnouncementController extends Controller
{
    private $annModel;
    private $deptModel;
    private $memModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->annModel = new Announcement();
        $this->deptModel = new Department();
        $this->memModel = new Membership();
    }

    public function index()
    {
        $myDepts = $this->memModel->getDepartmentsByUser($_SESSION['user_id']);
        $deptIds = array_column($myDepts, 'department_id');

        if ($_SESSION['user_role'] === 'admin') {
            $announcements = $this->annModel->getForUser(null);
        } else {
            $announcements = $this->annModel->getForUser($_SESSION['user_id'], $deptIds);
        }

        $this->view('announcements/index', [
            'announcements' => $announcements,
            'title' => 'Thông báo'
        ]);
    }

    public function show($id)
    {
        // Gọi hàm findById đã sửa ở Bước 1
        $news = $this->annModel->findById($id);

        if (!$news) {
            // Nếu không tìm thấy bài viết, báo lỗi và quay về danh sách
            if (function_exists('set_flash_message')) {
                \set_flash_message('error', 'Thông báo không tồn tại.');
            }
            $this->redirect(BASE_URL . '/announcement');
        }

        // Gọi view 'announcements/show' thay vì 'view'
        $this->view('announcements/show', [
            'announcement' => $news,
            'title' => $news['title']
        ]);
    }

    public function create()
    {
        // Chỉ Admin/Subadmin được đăng
        if (!in_array($_SESSION['user_role'], ['admin', 'subadmin'])) {
            \set_flash_message('error', 'Bạn không có quyền đăng thông báo.');
            $this->redirect(BASE_URL . '/announcement');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = trim($_POST['title']);
            $content = $_POST['content']; // Nội dung từ editor (HTML)

            if (empty($title) || empty($content)) {
                \set_flash_message('error', 'Vui lòng nhập tiêu đề và nội dung.');
                $this->redirect(BASE_URL . '/announcement/create');
            }

            $data = [
                'title' => $title,
                'content' => $content,
                'target_department_id' => !empty($_POST['target_department_id']) ? $_POST['target_department_id'] : null,
                'posted_by' => $_SESSION['user_id']
            ];

            if ($this->annModel->create($data)) {
                \set_flash_message('success', 'Đã đăng thông báo thành công.');
                $this->redirect(BASE_URL . '/announcement');
            } else {
                \set_flash_message('error', 'Lỗi hệ thống, vui lòng thử lại.');
            }
        }

        $depts = $this->deptModel->getAll();
        $this->view('announcements/create', ['departments' => $depts, 'title' => 'Đăng thông báo mới']);
    }

    // Form sửa thông báo
    public function edit($id)
    {
        // Chỉ Admin hoặc chính người đăng mới được sửa
        $news = $this->annModel->findById($id);

        if (!$news) {
            $this->redirect(BASE_URL . '/announcement');
        }

        if ($_SESSION['user_role'] != 'admin' && $_SESSION['user_id'] != $news['posted_by']) {
            \set_flash_message('error', 'Bạn không có quyền sửa bài này.');
            $this->redirect(BASE_URL . '/announcement');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => trim($_POST['title']),
                'content' => $_POST['content'],
                'target_department_id' => !empty($_POST['target_department_id']) ? $_POST['target_department_id'] : null,
            ];

            $this->annModel->update($id, $data);
            \set_flash_message('success', 'Cập nhật thành công.');
            $this->redirect(BASE_URL . '/announcement');
        }

        $depts = $this->deptModel->getAll();
        $this->view('announcements/edit', [
            'announcement' => $news,
            'departments' => $depts,
            'title' => 'Chỉnh sửa thông báo'
        ]);
    }

    // Xóa thông báo
    public function delete($id)
    {
        $news = $this->annModel->findById($id);

        if ($news) {
            if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_id'] == $news['posted_by']) {
                $this->annModel->delete($id);
                \set_flash_message('success', 'Đã xóa thông báo.');
            } else {
                \set_flash_message('error', 'Bạn không có quyền xóa.');
            }
        }

        $this->redirect(BASE_URL . '/announcement');
    }
}