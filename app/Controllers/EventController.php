<?php
// app/Controllers/EventController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;

class EventController extends Controller
{

    private $eventModel;

    public function __construct()
    {
        $this->eventModel = new Event();
    }

    /**
     * (READ) Hiển thị trang danh sách tất cả các Sự kiện
     * Ai cũng (miễn là đã đăng nhập) xem được
     */
    public function index()
    {
        // 1. Xác định vai trò
        $user_role = 'guest'; // Mặc định
        $my_registrations = []; // Mảng đăng ký

        if (isset($_SESSION['user_id'])) {
            $user_role = $_SESSION['user_role'];
            // Lấy danh sách đăng ký (nếu đã đăng nhập)
            $my_registrations = $this->eventModel->findMyRegistrations($_SESSION['user_id']);
        }

        // 2. Lấy Feed Sự kiện dựa trên vai trò
        $events = $this->eventModel->getFeed($user_role);

        $data = [
            'title' => 'Danh sách Sự kiện',
            'events' => $events,
            'my_registrations' => $my_registrations
        ];

        $this->view('events/index', $data);
    }

    /**
     * (CREATE) Hiển thị form tạo Sự kiện mới (GET)
     */
    public function create()
    {
        // "Chốt chặn" cấp 2: Phải là admin/subadmin
        $this->requireRole(['admin', 'subadmin']);

        $data = [
            'title' => 'Tạo Sự kiện mới',
            'form_title' => '',
            'description' => '',
            'start_time' => '',
            'end_time' => '',
            'location' => '',
            'title_err' => '',
            'start_time_err' => ''
        ];

        // Đổi tên biến 'title' để tránh trùng lặp
        // Em sẽ thấy $data['form_title'] thay vì $data['title'] cho form
        $this->view('events/create', $data);
    }

    /**
     * (CREATE) Xử lý lưu Sự kiện mới (POST)
     */
    public function store()
    {
        // "Chốt chặn" cấp 2: Phải là admin/subadmin
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/event');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $data = [
            'title' => 'Tạo Sự kiện mới',
            'form_title' => trim($_POST['form_title']),
            'description' => trim($_POST['description']),
            'start_time' => trim($_POST['start_time']),
            'end_time' => trim($_POST['end_time']),
            'location' => trim($_POST['location']),
            'visibility' => $_POST['visibility'],
            'title_err' => '',
            'start_time_err' => ''
        ];

        // Validate
        if (empty($data['form_title'])) {
            $data['title_err'] = 'Vui lòng nhập Tiêu đề Sự kiện';
        }
        if (empty($data['start_time'])) {
            $data['start_time_err'] = 'Vui lòng nhập Thời gian bắt đầu';
        }

        if (empty($data['title_err']) && empty($data['start_time_err'])) {
            // Đổi tên 'form_title' thành 'title' để Model hiểu
            $data['title'] = $data['form_title'];

            if ($this->eventModel->create($data)) {
                \log_activity('event_created', 'Đã tạo sự kiện: [' . $data['title'] . '].');
                \set_flash_message('success', 'Tạo sự kiện [' . htmlspecialchars($data['title']) . '] thành công!');
                $this->redirect(BASE_URL . '/event');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể tạo sự kiện.');
                $this->redirect(BASE_URL . '/event/create');
            }
        } else {
            // Có lỗi, tải lại view create với data lỗi
            $this->view('events/create', $data);
        }
    }

    /**
     * (UPDATE) Hiển thị form Sửa Sự kiện (GET)
     */
    public function edit($id)
    {
        // "Chốt chặn" cấp 2: Phải là admin/subadmin
        $this->requireRole(['admin', 'subadmin']);

        // 1. Lấy thông tin của Sự kiện cần sửa
        $event = $this->eventModel->findById($id);
        if (!$event) {
            $this->redirect(BASE_URL . '/event');
        }

        // (Kiểm tra quyền nâng cao: Có phải chính người tạo mới được sửa?)
        // (Tạm thời bỏ qua, admin/subadmin được sửa hết)

        $data = [
            'title' => 'Chỉnh sửa Sự kiện',
            'id' => $id,
            'form_title' => $event['title'],
            'description' => $event['description'],
            'start_time' => date('Y-m-d\TH:i', strtotime($event['start_time'])),
            'end_time' => $event['end_time'] ? date('Y-m-d\TH:i', strtotime($event['end_time'])) : '',
            'location' => $event['location'],
            'visibility' => $event['visibility'],
            'title_err' => '',
            'start_time_err' => ''
        ];

        $this->view('events/edit', $data);
    }

    /**
     * (UPDATE) Xử lý cập nhật Sự kiện (POST)
     */
    public function update($id)
    {
        // "Chốt chặn" cấp 2: Phải là admin/subadmin
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/event');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $data = [
            'title' => 'Chỉnh sửa Sự kiện',
            'id' => $id,
            'form_title' => trim($_POST['form_title']),
            'description' => trim($_POST['description']),
            'start_time' => trim($_POST['start_time']),
            'end_time' => trim($_POST['end_time']),
            'location' => trim($_POST['location']),
            'visibility' => $_POST['visibility'],
            'title_err' => '',
            'start_time_err' => ''
        ];

        // Validate
        if (empty($data['form_title'])) {
            $data['title_err'] = 'Vui lòng nhập Tiêu đề Sự kiện';
        }
        if (empty($data['start_time'])) {
            $data['start_time_err'] = 'Vui lòng nhập Thời gian bắt đầu';
        }

        if (empty($data['title_err']) && empty($data['start_time_err'])) {
            $data['title'] = $data['form_title'];

            if ($this->eventModel->update($id, $data)) {
                \log_activity('event_updated', 'Đã cập nhật sự kiện: [' . $data['title'] . '] (ID: ' . $id . ').');
                \set_flash_message('success', 'Cập nhật sự kiện [' . htmlspecialchars($data['title']) . '] thành công!');
                $this->redirect(BASE_URL . '/event');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể cập nhật.');
                $this->redirect(BASE_URL . '/event/edit/' . $id);
            }
        } else {
            // Có lỗi, tải lại view edit với data lỗi
            $this->view('events/edit', $data);
        }
    }

    /**
     * (DELETE) Xử lý Xóa Sự kiện (POST)
     */
    public function destroy($id)
    {
        // "Chốt chặn" cấp 2: Phải là admin/subadmin
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/event');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        $event = $this->eventModel->findById($id); // Lấy tên trước khi xóa
        if (!$event) {
            \set_flash_message('error', 'Không tìm thấy sự kiện để xóa.');
            $this->redirect(BASE_URL . '/event');
        }

        // Tiến hành Xóa
        if ($this->eventModel->delete($id)) {
            \log_activity('event_deleted', 'Đã xóa sự kiện: [' . $event['title'] . '] (ID: ' . $id . ').');
            \set_flash_message('success', 'Đã xóa sự kiện [' . htmlspecialchars($event['title']) . '] thành công!');
            $this->redirect(BASE_URL . '/event');
        } else {
            \set_flash_message('error', 'Có lỗi CSDL, không thể xóa.');
            $this->redirect(BASE_URL . '/event');
        }
    }

    /**
     * Xử lý Đăng ký / Hủy đăng ký (HTTP POST)
     */
    public function toggleRegistration($event_id)
    {
        // 1. Phải đăng nhập
        $this->requireLogin();

        // 2. Phải là POST
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/event');
        }

        // 3. Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        // 4. Lấy thông tin
        $user_id = $_SESSION['user_id'];

        // 5. Kiểm tra sự kiện có thật không
        $event = $this->eventModel->findById($event_id);
        if (!$event) {
            $this->redirect(BASE_URL . '/event');
        }

        // 6. Logic "Toggle"
        $is_registered = $this->eventModel->isUserRegistered($event_id, $user_id);

        if ($is_registered) {
            \log_activity('event_unregistered', 'Đã hủy đăng ký sự kiện: [' . $event['title'] . '].');
            $this->eventModel->unregisterParticipant($event_id, $user_id);
            \set_flash_message('info', 'Đã hủy đăng ký sự kiện.');
        } else {
            \log_activity('event_registered', 'Đã đăng ký sự kiện: [' . $event['title'] . '].');
            $this->eventModel->registerParticipant($event_id, $user_id);
            \set_flash_message('success', 'Đăng ký sự kiện thành công!');
        }

        // 7. Quay lại trang danh sách
        $this->redirect(BASE_URL . '/event');
    }

    /**
     * (READ) Hiển thị trang Điểm danh cho 1 Sự kiện (GET)
     */
    public function attendance($event_id)
    {
        // "Chốt chặn" cấp 2: Phải là admin/subadmin
        $this->requireRole(['admin', 'subadmin']);

        // 1. Lấy thông tin sự kiện
        $event = $this->eventModel->findById($event_id);
        if (!$event) {
            $this->redirect(BASE_URL . '/event');
        }

        // 2. Lấy danh sách người tham gia
        $participants = $this->eventModel->getParticipants($event_id);

        $data = [
            'title' => 'Điểm danh: ' . $event['title'],
            'event' => $event,
            'participants' => $participants
        ];

        $this->view('events/attendance', $data);
    }

    /**
     * (UPDATE) Xử lý Check-in (POST)
     */
    public function checkin($event_id, $attendance_id)
    {
        // "Chốt chặn" cấp 2
        $this->requireRole(['admin', 'subadmin']);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/event');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        // Cập nhật status
        $this->eventModel->checkInParticipant($attendance_id);
        \set_flash_message('success', 'Check-in thành công!');
        $this->redirect(BASE_URL . '/event/attendance/' . $event_id);
    }

    /**
     * (UPDATE) Xử lý Hoàn tác Check-in (POST)
     */
    public function undocheckin($event_id, $attendance_id)
    {
        // "Chốt chặn" cấp 2
        $this->requireRole(['admin', 'subadmin']);
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/event');
        }

        // Kiểm tra CSRF Token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL);
            exit;
        }

        // Cập nhật status
        $this->eventModel->undoCheckIn($attendance_id);
        \set_flash_message('info', 'Đã hoàn tác check-in.');
        $this->redirect(BASE_URL . '/event/attendance/' . $event_id);
    }
}
