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
        // Lấy danh sách ban mà user tham gia để lọc thông báo
        $myDepts = $this->memModel->getDepartmentsByUser($_SESSION['user_id']);
        $deptIds = array_column($myDepts, 'department_id');

        // Nếu là Admin thì xem hết (truyền mảng rỗng hoặc null tùy logic model, ở đây model cần chỉnh một chút nếu admin)
        // Nhưng theo logic Model đã viết: nếu userId truyền vào, nó sẽ lọc.
        // Ta có thể quy ước: Admin gọi getAll không filter.

        if ($_SESSION['user_role'] === 'admin') {
            $announcements = $this->annModel->getForUser(null); // NULL = lấy hết (cần check lại Model)
            // *Lưu ý*: Model getForUser logic hiện tại: "if ($userId) { filter }". Nếu null thì lấy hết. OK.
        } else {
            $announcements = $this->annModel->getForUser($_SESSION['user_id'], $deptIds);
        }

        $this->view('announcements/index', [
            'announcements' => $announcements,
            'title' => 'Thông báo'
        ]);
    }

    public function create()
    {
        // Chỉ Admin hoặc Trưởng ban mới được đăng (Logic mở rộng)
        // Tạm thời để Admin và Subadmin
        if (!in_array($_SESSION['user_role'], ['admin', 'subadmin'])) {
            $this->redirect(BASE_URL . '/announcement');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => trim($_POST['title']),
                'content' => $_POST['content'], // HTML content
                'target_department_id' => !empty($_POST['target_department_id']) ? $_POST['target_department_id'] : null,
                'posted_by' => $_SESSION['user_id']
            ];

            // Xử lý XSS cho content (Dùng HTMLPurifier nếu đã cài, hoặc htmlspecialchars đơn giản)
            // Ở đây tạm thời tin tưởng người đăng (Admin)

            $this->annModel->create($data);
            \set_flash_message('success', 'Đã đăng thông báo.');
            $this->redirect(BASE_URL . '/announcement');
        }

        $depts = $this->deptModel->getAll();
        $this->view('announcements/create', ['departments' => $depts, 'title' => 'Đăng thông báo']);
    }
}