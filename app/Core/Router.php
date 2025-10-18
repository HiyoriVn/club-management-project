<?php
// app/Core/Router.php

namespace App\Core;

class Router
{
    // Các thuộc tính mặc định
    protected $currentController = 'HomeController'; // Controller mặc định
    protected $currentMethod = 'index'; // Method mặc định
    protected $params = []; // Tham số mặc định

    public function __construct()
    {
        $url = $this->getUrl();

        // --- 1. Xử lý Controller ---
        // Lấy phần tử đầu tiên của URL làm tên Controller
        // ucwords: Viết hoa chữ cái đầu (vd: home -> Home)
        if (isset($url[0])) {
            $controllerName = ucwords($url[0]) . 'Controller';
            $controllerPath = ROOT_PATH . '/app/Controllers/' . $controllerName . '.php';

            // Kiểm tra file Controller có tồn tại không
            if (file_exists($controllerPath)) {
                $this->currentController = $controllerName;
                // Xóa phần tử này khỏi mảng url
                unset($url[0]);
            }
        }

        // Nạp Controller
        require_once ROOT_PATH . '/app/Controllers/' . $this->currentController . '.php';

        // Khởi tạo Controller (thêm namespace đầy đủ)
        $controllerClassName = 'App\\Controllers\\' . $this->currentController;
        $this->currentController = new $controllerClassName();

        // --- 2. Xử lý Method ---
        if (isset($url[1])) {
            // Kiểm tra xem method có tồn tại trong Controller không
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                // Xóa phần tử này khỏi mảng url
                unset($url[1]);
            }
        }

        // --- 3. Xử lý Parameters ---
        // Các phần tử còn lại trong $url chính là params
        $this->params = $url ? array_values($url) : [];

        // --- 4. Gọi Controller, Method với Params ---
        // Gọi hàm (method) của một đối tượng (controller) với danh sách tham số (params)
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    /**
     * Lấy URL, làm sạch và tách thành mảng
     */
    protected function getUrl()
    {
        if (isset($_GET['url'])) {
            // 1. Xóa dấu / ở cuối
            $url = rtrim($_GET['url'], '/');
            // 2. Lọc các ký tự không hợp lệ
            $url = filter_var($url, FILTER_SANITIZE_URL);
            // 3. Tách chuỗi bằng dấu / thành mảng
            $url = explode('/', $url);
            return $url;
        }
        // Trả về mảng rỗng nếu không có url
        return [];
    }
}
