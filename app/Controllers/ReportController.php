<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActivityLog;

class ReportController extends Controller
{
    private $logModel;

    public function __construct()
    {
        $this->requireLogin();
        // Chỉ admin mới được xem log
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect(BASE_URL);
        }
        $this->logModel = new ActivityLog();
    }

    public function activity_logs()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $logs = $this->logModel->getLogs($limit, $offset);

        $this->view('reports/activity_logs', [
            'logs' => $logs,
            'page' => $page,
            'title' => 'Nhật ký hoạt động'
        ]);
    }
}