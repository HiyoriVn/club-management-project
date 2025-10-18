<?php
// app/Controllers/HomeController.php

namespace App\Controllers;

// Nhớ "use" lớp Controller cha
use App\Core\Controller;

class HomeController extends Controller
{

    // Đây là method mặc định (index)
    public function index()
    {
        // Chuẩn bị dữ liệu để truyền ra view
        $data = [
            'title' => 'Chào mừng đến với Trang chủ CLB!',
            'description' => 'Đây là trang chủ được tải từ HomeController.'
        ];

        // Gọi hàm view() từ lớp cha (Controller)
        // và truyền vào tên view 'home' cùng mảng $data
        $this->view('home', $data);
    }

    // (Sau này ta có thể thêm các method khác, ví dụ: public function about() { ... })
}
