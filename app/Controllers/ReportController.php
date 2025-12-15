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

        // Kiểm tra quyền: Chỉ Admin mới được truy cập báo cáo
        // (Nếu muốn cho Sub-admin xem thì sửa thành in_array($_SESSION['user_role'], ['admin', 'subadmin']))
        if ($_SESSION['user_role'] !== 'admin') {
            // Dùng set_flash_message nếu function này có sẵn (đã được nạp qua helper)
            if (function_exists('set_flash_message')) {
                set_flash_message('error', 'Bạn không có quyền truy cập trang này.');
            }
            $this->redirect(BASE_URL . '/dashboard');
        }

        $this->logModel = new ActivityLog();
    }

    /**
     * Trang tổng quan Báo cáo
     * URL: /report
     */
    public function index()
    {
        // Hiển thị view reports/index.php (Dashboard các loại báo cáo)
        $this->view('reports/index', [
            'title' => 'Trung tâm Báo cáo'
        ]);
    }

    /**
     * Xem nhật ký hoạt động
     * URL: /report/activity_logs
     */
    public function activity_logs()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Đảm bảo page >= 1
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
