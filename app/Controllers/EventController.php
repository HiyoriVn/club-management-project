<?php
// app/Controllers/EventController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event; // "use" Model Event

class EventController extends Controller
{

    private $eventModel;

    public function __construct()
    {
        // Lần này chúng ta không "chặn" ở __construct
        // vì hàm index() (xem danh sách) cho phép mọi người vào

        // Nạp model
        require_once ROOT_PATH . '/app/Models/Event.php';
        $this->eventModel = new Event();
    }

    /**
     * (READ) Hiển thị trang danh sách tất cả các Sự kiện
     * Ai cũng (miễn là đã đăng nhập) xem được
     */
    public function index()
    {
        // "Chốt chặn" cấp 1: Chỉ cần đăng nhập
        $this->requireLogin();

        $events = $this->eventModel->findAll();

        $data = [
            'title' => 'Danh sách Sự kiện',
            'events' => $events
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

        $data = [
            'title' => 'Tạo Sự kiện mới', // Dùng cho <title> của trang
            'form_title' => trim($_POST['form_title']), // Dùng cho form
            'description' => trim($_POST['description']),
            'start_time' => trim($_POST['start_time']),
            'end_time' => trim($_POST['end_time']),
            'location' => trim($_POST['location']),
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
                $this->redirect(BASE_URL . '/event');
            } else {
                die('Có lỗi CSDL xảy ra.');
            }
        } else {
            // Có lỗi, tải lại view create với data lỗi
            $this->view('events/create', $data);
        }
    }
}
