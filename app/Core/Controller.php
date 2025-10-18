<?php
// app/Core/Controller.php

namespace App\Core;

class Controller
{

    /**
     * Tải một Model
     * (Chúng ta sẽ hoàn thiện hàm này sau)
     */
    public function model($model)
    {
        // Ví dụ: require_once '../app/Models/' . $model . '.php';
        // return new $model();
    }

    /**
     * Tải một View (Giao diện)
     * @param string $view Tên file view (ví dụ: 'home')
     * @param array $data Dữ liệu muốn truyền ra view (ví dụ: ['title' => 'Trang chủ'])
     */
    public function view($view, $data = [])
    {
        // Biến mảng $data thành các biến riêng lẻ
        // Ví dụ: $data['title'] sẽ thành biến $title
        extract($data);

        // Đường dẫn đến file view
        $viewPath = ROOT_PATH . '/app/Views/' . $view . '.php';

        // Kiểm tra file view có tồn tại không
        if (file_exists($viewPath)) {
            // Nếu tồn tại, nạp file view
            require_once $viewPath;
        } else {
            // Nếu không, báo lỗi
            die('View "' . $view . '" không tồn tại.');
        }
    }
}
