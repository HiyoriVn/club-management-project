<?php
// app/Controllers/DashboardController.php

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{

    /**
     * Hàm khởi tạo (construct) sẽ chạy ĐẦU TIÊN
     * trước khi bất kỳ method nào (như index) được gọi.
     */
    public function __construct()
    {
        // Chỉ member trở lên mới có Dashboard
        $this->requireRole(['admin', 'subadmin', 'member']);
    }

    /**
     * Trang Dashboard chính
     */
    public function index()
    {
        // Lấy thông tin từ Session để truyền ra View
        $data = [
            'title' => 'Bảng điều khiển',
            'user_name' => $_SESSION['user_name'],
            'user_role' => $_SESSION['user_role']
        ];

        $this->view('dashboard/index', $data);
    }
}
