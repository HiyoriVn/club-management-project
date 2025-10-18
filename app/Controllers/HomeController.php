<?php
// app/Controllers/HomeController.php

namespace App\Controllers;

// Nhớ "use" lớp Controller cha
use App\Core\Controller;

class HomeController extends Controller
{

    public function index()
    {
        // Kiểm tra xem đã đăng nhập chưa (dùng Session)
        $isLoggedIn = isset($_SESSION['user_id']);

        if ($isLoggedIn) {
            // Nếu đã đăng nhập
            $data = [
                'title' => 'Chào mừng quay trở lại!',
                'description' => 'Bạn đã đăng nhập với tên ' . htmlspecialchars($_SESSION['user_name']) . '.',
                'isLoggedIn' => true
            ];
        } else {
            // Nếu là khách
            $data = [
                'title' => 'Chào mừng đến với Trang chủ CLB!',
                'description' => 'Đây là hệ thống quản lý nội bộ của CLB.',
                'isLoggedIn' => false
            ];
        }

        $this->view('home', $data);
    }

    // (Sau này ta có thể thêm các method khác, ví dụ: public function about() { ... })
}
