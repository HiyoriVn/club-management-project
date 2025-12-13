<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Models\Task;

class DashboardController extends Controller
{
    private $userModel;
    private $projectModel;
    private $transactionModel;
    private $logModel;
    private $taskModel;

    public function __construct()
    {
        $this->requireLogin();

        $this->userModel = new User();
        $this->projectModel = new Project();
        $this->transactionModel = new Transaction();
        $this->logModel = new ActivityLog();
        $this->taskModel = new Task();
    }

    public function index()
    {
        // 1. Thống kê Thành viên
        // getAllUsers trả về array ['users' => ..., 'total' => ...]
        $userStats = $this->userModel->getAllUsers('', '', 1, 1);
        $totalUsers = $userStats['total'];

        // 2. Thống kê Dự án & Sự kiện
        // Hàm getAllProjects trả về array list, ta count số phần tử
        $projects = $this->projectModel->getAllProjects(1000);
        $events = $this->projectModel->getAllEvents(1000);
        $totalProjects = count($projects);
        $totalEvents = count($events);

        // 3. Thống kê Tài chính
        $totals = $this->transactionModel->getTotals();
        $income = 0;
        $expense = 0;
        foreach ($totals as $t) {
            if ($t['type'] == 'income') $income = $t['total'];
            if ($t['type'] == 'expense') $expense = $t['total'];
        }
        $balance = $income - $expense;

        // 4. Hoạt động gần đây (Lấy 5 logs mới nhất)
        $recentActivities = $this->logModel->getLogs(5, 0);

        // 5. Công việc của tôi (Lấy 5 task sắp đến hạn)
        $myTasks = $this->taskModel->getTasksByUser($_SESSION['user_id']);
        // Cắt lấy 5 phần tử đầu
        $myRecentTasks = array_slice($myTasks, 0, 5);

        $data = [
            'total_users' => $totalUsers,
            'total_projects' => $totalProjects,
            'total_events' => $totalEvents,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'recent_activities' => $recentActivities,
            'my_tasks' => $myRecentTasks,
            'title' => 'Dashboard'
        ];

        $this->view('dashboard/index', $data);
    }
}
