<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\Task;
use App\Models\Announcement;
use App\Models\Membership;

class DashboardController extends Controller
{
    private $userModel;
    private $projectModel;
    private $transactionModel;
    private $taskModel;
    private $announcementModel;
    private $membershipModel;

    public function __construct()
    {
        $this->requireLogin();

        $this->userModel = new User();
        $this->projectModel = new Project();
        $this->transactionModel = new Transaction();
        $this->taskModel = new Task();
        $this->announcementModel = new Announcement();
        $this->membershipModel = new Membership();
    }

    public function index()
    {
        // 1. Thống kê Thành viên
        $userStats = $this->userModel->getAllUsers('', '', 1, 1);
        $totalUsers = $userStats['total'];
        
        // 2. Thống kê Dự án
        $allWorks = $this->projectModel->getAll();
        $totalProjects = 0;

        if (!empty($allWorks)) {
            foreach ($allWorks as $work) {
                if (isset($work['type']) && $work['type'] === 'project') {
                    $totalProjects++;
                }
                elseif (isset($work['type']) && $work['type'] === 'event') {
                    $totalProjects++;
                }
            }
        }

        // 3. Thống kê Tài chính
        $income = 0;
        $expense = 0;
        $balance = 0;

        // Chỉ tính toán tài chính nếu là Admin/Subadmin
        if (in_array($_SESSION['user_role'], ['admin', 'subadmin'])) {
            $totals = $this->transactionModel->getTotals();
            if (!empty($totals)) {
                foreach ($totals as $t) {
                    if ($t['type'] == 'income') $income = $t['total'];
                    if ($t['type'] == 'expense') $expense = $t['total'];
                }
            }
            $balance = $income - $expense;
        }

        // 4. Lấy thông báo
        $myDepts = $this->membershipModel->getDepartmentsByUser($_SESSION['user_id']);
        $deptIds = array_column($myDepts, 'department_id');

        if ($_SESSION['user_role'] === 'admin') {
            // Admin xem hết (truyền null)
            $allAnnouncements = $this->announcementModel->getForUser(null);
        } else {
            // Member xem theo quyền
            $allAnnouncements = $this->announcementModel->getForUser($_SESSION['user_id'], $deptIds);
        }
        $recentAnnouncements = is_array($allAnnouncements) ? array_slice($allAnnouncements, 0, 5) : [];
        
        // 5. Công việc của tôi 
        $myTasks = [];
        if (method_exists($this->taskModel, 'getTasksByUser')) {
            $allTasks = $this->taskModel->getTasksByUser($_SESSION['user_id']);

            // Chỉ lấy các việc chưa hoàn thành (todo, in_progress)
            $pendingTasks = array_filter($allTasks, function ($t) {
                return $t['status'] !== 'completed' && $t['status'] !== 'cancelled';
            });

            
            $myTasks = $pendingTasks;
        }

        $data = [
            'total_users' => $totalUsers,
            'total_projects' => $totalProjects,
            'balance' => $balance,
            'my_tasks' => $myTasks,
            'announcements' => $recentAnnouncements,
            'title' => 'Dashboard'
        ];

        $this->view('dashboard/index', $data);
    }
}
