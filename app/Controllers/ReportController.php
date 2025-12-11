<?php
// app/Controllers/ReportController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\Department;

class ReportController extends Controller
{
    private $userModel;
    private $eventModel;
    private $projectModel;
    private $transactionModel;
    private $departmentModel;

    public function __construct()
    {
        $this->requireRole(['admin', 'subadmin']);

        $this->userModel = new User();
        $this->eventModel = new Event();
        $this->projectModel = new Project();
        $this->transactionModel = new Transaction();
        $this->departmentModel = new Department();
    }

    /**
     * Trang tổng quan báo cáo
     */
    public function index()
    {
        // Lấy filter từ URL
        $period = $_GET['period'] ?? 'month'; // month, quarter, year
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');

        // Thống kê tổng quan
        $stats = $this->getOverviewStats();

        // Biểu đồ theo thời gian
        $charts = $this->getChartData($period, $start_date, $end_date);

        $data = [
            'title' => 'Báo cáo & Thống kê',
            'stats' => $stats,
            'charts' => $charts,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        $this->view('reports/index', $data);
    }

    /**
     * Lấy thống kê tổng quan
     */
    private function getOverviewStats()
    {
        $db = \App\Core\Database::getInstance();

        // Tổng số thành viên
        $db->query("SELECT COUNT(*) as total FROM users WHERE system_role != 'guest'");
        $total_users = $db->single()['total'];

        // Tổng số sự kiện
        $db->query("SELECT COUNT(*) as total FROM events");
        $total_events = $db->single()['total'];

        // Tổng số dự án
        $db->query("SELECT COUNT(*) as total FROM projects");
        $total_projects = $db->single()['total'];

        // Tổng số Ban
        $db->query("SELECT COUNT(*) as total FROM departments");
        $total_departments = $db->single()['total'];

        // Quỹ hiện tại
        $totals = $this->transactionModel->getTotals();
        $balance = $totals->balance;

        // Sự kiện sắp tới (trong 30 ngày)
        $db->query("SELECT COUNT(*) as total FROM events WHERE start_time >= NOW() AND start_time <= DATE_ADD(NOW(), INTERVAL 30 DAY)");
        $upcoming_events = $db->single()['total'];

        // Dự án đang thực hiện
        $db->query("SELECT COUNT(*) as total FROM projects WHERE STATUS = 'in_progress'");
        $active_projects = $db->single()['total'];

        // Thống kê theo vai trò
        $db->query("SELECT system_role, COUNT(*) as count FROM users GROUP BY system_role");
        $users_by_role = $db->resultSet();

        return [
            'total_users' => $total_users,
            'total_events' => $total_events,
            'total_projects' => $total_projects,
            'total_departments' => $total_departments,
            'balance' => $balance,
            'upcoming_events' => $upcoming_events,
            'active_projects' => $active_projects,
            'users_by_role' => $users_by_role
        ];
    }

    /**
     * Lấy dữ liệu biểu đồ
     */
    private function getChartData($period, $start_date, $end_date)
    {
        $db = \App\Core\Database::getInstance();

        // Biểu đồ sự kiện theo tháng (6 tháng gần nhất)
        $db->query("SELECT 
                        DATE_FORMAT(start_time, '%Y-%m') as month,
                        COUNT(*) as count
                    FROM events
                    WHERE start_time >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY DATE_FORMAT(start_time, '%Y-%m')
                    ORDER BY month ASC");
        $events_by_month = $db->resultSet();

        // Biểu đồ tài chính (thu/chi) theo tháng
        $db->query("SELECT 
                        DATE_FORMAT(DATE, '%Y-%m') as month,
                        SUM(CASE WHEN TYPE = 'income' THEN amount ELSE 0 END) as income,
                        SUM(CASE WHEN TYPE = 'expense' THEN amount ELSE 0 END) as expense
                    FROM transactions
                    WHERE DATE >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY DATE_FORMAT(DATE, '%Y-%m')
                    ORDER BY month ASC");
        $finance_by_month = $db->resultSet();

        // Top 5 Ban có nhiều thành viên nhất
        $db->query("SELECT 
                        d.NAME as department_name,
                        COUNT(DISTINCT udr.user_id) as member_count
                    FROM departments d
                    LEFT JOIN user_department_roles udr ON d.id = udr.department_id
                    GROUP BY d.id, d.NAME
                    ORDER BY member_count DESC
                    LIMIT 5");
        $top_departments = $db->resultSet();

        return [
            'events_by_month' => $events_by_month,
            'finance_by_month' => $finance_by_month,
            'top_departments' => $top_departments
        ];
    }

    /**
     * Export báo cáo ra Excel (CSV)
     */
    public function export()
    {
        $type = $_GET['type'] ?? 'overview'; // overview, users, events, finance

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="report_' . $type . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

        switch ($type) {
            case 'users':
                $this->exportUsers($output);
                break;
            case 'events':
                $this->exportEvents($output);
                break;
            case 'finance':
                $this->exportFinance($output);
                break;
            default:
                $this->exportOverview($output);
        }

        fclose($output);
        exit;
    }

    private function exportUsers($output)
    {
        fputcsv($output, ['ID', 'Tên', 'Email', 'Vai trò', 'Ngày tạo']);

        $db = \App\Core\Database::getInstance();
        $db->query("SELECT id, NAME, email, system_role, created_at FROM users ORDER BY id");
        $users = $db->resultSet();

        foreach ($users as $user) {
            fputcsv($output, [
                $user['id'],
                $user['NAME'],
                $user['email'],
                $user['system_role'],
                $user['created_at']
            ]);
        }
    }

    private function exportEvents($output)
    {
        fputcsv($output, ['ID', 'Tiêu đề', 'Thời gian', 'Địa điểm', 'Số người đăng ký']);

        $db = \App\Core\Database::getInstance();
        $db->query("SELECT 
                        e.id,
                        e.title,
                        e.start_time,
                        e.location,
                        COUNT(ep.user_id) as participant_count
                    FROM events e
                    LEFT JOIN event_participants ep ON e.id = ep.event_id
                    GROUP BY e.id
                    ORDER BY e.start_time DESC");
        $events = $db->resultSet();

        foreach ($events as $event) {
            fputcsv($output, [
                $event['id'],
                $event['title'],
                $event['start_time'],
                $event['location'],
                $event['participant_count']
            ]);
        }
    }

    private function exportFinance($output)
    {
        fputcsv($output, ['Ngày', 'Loại', 'Số tiền', 'Mô tả']);

        $db = \App\Core\Database::getInstance();
        $db->query("SELECT DATE, TYPE, amount, description FROM transactions ORDER BY DATE DESC");
        $transactions = $db->resultSet();

        foreach ($transactions as $tx) {
            fputcsv($output, [
                $tx['DATE'],
                $tx['TYPE'] == 'income' ? 'Thu' : 'Chi',
                $tx['amount'],
                $tx['description']
            ]);
        }
    }

    private function exportOverview($output)
    {
        $stats = $this->getOverviewStats();

        fputcsv($output, ['Chỉ số', 'Giá trị']);
        fputcsv($output, ['Tổng thành viên', $stats['total_users']]);
        fputcsv($output, ['Tổng sự kiện', $stats['total_events']]);
        fputcsv($output, ['Tổng dự án', $stats['total_projects']]);
        fputcsv($output, ['Tổng Ban', $stats['total_departments']]);
        fputcsv($output, ['Số dư quỹ', number_format($stats['balance'], 0, ',', '.')]);
    }
}
