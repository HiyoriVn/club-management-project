<?php
// app/Controllers/ActivityLogController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    private $logModel;

    public function __construct()
    {
        $this->requireRole(['admin']);
        $this->logModel = new ActivityLog();
    }

    /**
     * (READ) Hiển thị trang danh sách tất cả Log
     */
    public function index()
    {
        // Lấy 200 log mới nhất
        $logs = $this->logModel->findAllWithUser(200);

        $data = [
            'title' => 'Nhật ký Hoạt động Hệ thống',
            'logs' => $logs
        ];

        $this->view('activity_logs/index', $data);
    }
}
