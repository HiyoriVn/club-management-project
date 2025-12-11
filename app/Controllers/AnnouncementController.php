<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Department;

class AnnouncementController extends Controller
{
    private $announcementModel;
    private $userModel;
    private $departmentModel;

    public function __construct()
    {
        $this->announcementModel = new Announcement();
        $this->userModel = new User();
        $this->departmentModel = new Department();
    }

    /**
     * (READ) Hiển thị trang danh sách thông báo
     * Logic phân quyền:
     * - Admin/Subadmin: Thấy TẤT CẢ.
     * - Member/Guest: Chỉ thấy Toàn CLB + Ban của mình.
     */
    public function index()
    {

        // 1. Xác định vai trò
        $user_role = 'guest'; // Mặc định là khách
        $my_department_ids = [];

        if (isset($_SESSION['user_id'])) {
            // Nếu đã đăng nhập
            $user_role = $_SESSION['user_role'];
            // Lấy Ban của user (chỉ khi không phải guest)
            if ($user_role != 'guest') {
                $my_department_ids = $this->userModel->getDepartmentIds($_SESSION['user_id']);
            }
        }

        // 2. Lấy thông báo (sẽ sửa Model ở bước sau)
        $announcements = $this->announcementModel->getFeed($user_role, $my_department_ids);

        $data = [
            'title' => 'Thông báo',
            'announcements' => $announcements
        ];

        $this->view('announcements/index', $data);
    }

    /**
     * (CREATE) Hiển thị form tạo thông báo (GET)
     */
    public function create()
    {
        $this->requireRole(['admin', 'subadmin']);

        // Lấy danh sách Ban để làm dropdown
        $departments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Đăng thông báo mới',
            'form_title' => '',
            'content' => '',
            'target_department_id' => null,
            'all_departments' => $departments,
            'title_err' => ''
        ];

        $this->view('announcements/create', $data);
    }

    /**
     * (CREATE) Xử lý lưu thông báo (POST)
     */
    public function store()
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/announcement');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
        }

        $departments = $this->departmentModel->findAll();

        // 1. Lấy target từ form
        $target = $_POST['target'];

        // 2. Giải mã target
        $visibility = 'internal';
        $target_department_id = null;

        if ($target == 'public') {
            $visibility = 'public';
        } elseif ($target == 'internal') {
            $visibility = 'internal';
        } else {
            // Nếu là số (ID của Ban)
            $target_department_id = (int)$target;
            // $visibility giữ nguyên là 'internal'
        }

        $data = [
            'title' => 'Đăng thông báo mới',
            'form_title' => trim($_POST['form_title']),
            'content' => trim($_POST['content']),

            // Dữ liệu đã giải mã
            'target_department_id' => $target_department_id,
            'visibility' => $visibility,

            'all_departments' => $departments,
            'title_err' => ''
        ];

        // Validate
        if (empty($data['form_title'])) {
            $data['title_err'] = 'Vui lòng nhập Tiêu đề';
        }

        if (empty($data['title_err'])) {
            $data['title'] = $data['form_title'];

            if ($this->announcementModel->create($data)) {
                \set_flash_message('success', 'Đăng thông báo [' . $data['title'] . '] thành công!');
                $this->redirect(BASE_URL . '/announcement');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL xảy ra, không thể đăng thông báo.');
                $this->redirect(BASE_URL . '/announcement/create'); // Quay lại form
            }
        } else {
            // Lỗi, tải lại view create
            $this->view('announcements/create', $data);
        }
    }

    /**
     * (UPDATE) Hiển thị form Sửa thông báo (GET)
     */
    public function edit($id)
    {
        $this->requireRole(['admin', 'subadmin']);

        $announcement = $this->announcementModel->findById($id);
        if (!$announcement) {
            $this->redirect(BASE_URL . '/announcement');
        }

        // (Kiểm tra quyền: Chỉ admin hoặc chính người đăng mới được sửa)
        // (Tạm thời bỏ qua, admin/subadmin sửa hết)

        $departments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Chỉnh sửa Thông báo',
            'id' => $id,
            'form_title' => $announcement['title'], // Lấy title cũ
            'content' => $announcement['content'], // Lấy content cũ

            // PHẦN QUAN TRỌNG: Lấy cả 2 giá trị này từ CSDL
            'target_department_id' => $announcement['target_department_id'],
            'visibility' => $announcement['visibility'], // Truyền 'visibility' ra View

            'all_departments' => $departments,
            'title_err' => ''
        ];

        $this->view('announcements/edit', $data);
    }

    /**
     * (UPDATE) Xử lý cập nhật thông báo (POST)
     */
    public function update($id)
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/announcement');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
        }

        $departments = $this->departmentModel->findAll();

        // 1. Lấy target từ form
        $target = $_POST['target'];

        // 2. Giải mã target
        $visibility = 'internal'; // Mặc định
        $target_department_id = null;

        if ($target == 'public') {
            $visibility = 'public';
        } elseif ($target == 'internal') {
            $visibility = 'internal';
        } else {
            // Nếu là số (ID của Ban)
            $target_department_id = (int)$target;
            // $visibility giữ nguyên là 'internal'
        }

        $data = [
            'title' => 'Chỉnh sửa Thông báo',
            'id' => $id,
            'form_title' => trim($_POST['form_title']),
            'content' => trim($_POST['content']),

            'target_department_id' => $target_department_id,
            'visibility' => $visibility,

            'all_departments' => $departments,
            'title_err' => ''
        ];

        // Validate
        if (empty($data['form_title'])) {
            $data['title_err'] = 'Vui lòng nhập Tiêu đề';
        }

        if (empty($data['title_err'])) {
            $data['title'] = $data['form_title'];

            if ($this->announcementModel->update($id, $data)) {
                \set_flash_message('success', 'Cập nhật thông báo [' . $data['title'] . '] thành công!');
                $this->redirect(BASE_URL . '/announcement');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể cập nhật.');
                $this->redirect(BASE_URL . '/announcement/edit/' . $id); // Quay lại form
            }
        } else {
            $this->view('events/edit', $data);
        }
    }

    /**
     * (DELETE) Xử lý Xóa thông báo (POST)
     */
    public function destroy($id)
    {
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/announcement');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
        }

        if (!$this->announcementModel->findById($id)) {
            \set_flash_message('error', 'Không tìm thấy thông báo để xóa.');
            $this->redirect(BASE_URL . '/announcement');
        }

        if ($this->announcementModel->delete($id)) {
            \set_flash_message('success', 'Xóa thông báo thành công!');
            $this->redirect(BASE_URL . '/announcement');
        } else {
            \set_flash_message('error', 'Có lỗi CSDL, không thể xóa thông báo.');
            $this->redirect(BASE_URL . '/announcement');
        }
    }
}
