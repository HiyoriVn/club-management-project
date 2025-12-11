<?php
// app/Core/Router.php
namespace App\Core;

class Router
{
    protected $currentController = 'HomeController';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        // 1. Xử lý Controller
        if (isset($url[0])) {
            // Chuyển 'dashboard' -> 'DashboardController'
            $controllerName = ucwords($url[0]) . 'Controller';

            // Kiểm tra class có tồn tại không (Autoload sẽ tự tìm file)
            $controllerClass = 'App\\Controllers\\' . $controllerName;

            if (class_exists($controllerClass)) {
                $this->currentController = $controllerName;
                unset($url[0]);
            }
        }

        // Khởi tạo Controller
        // Dùng namespace đầy đủ: App\Controllers\HomeController
        $controllerClass = 'App\\Controllers\\' . $this->currentController;

        // Kiểm tra lần cuối để tránh lỗi nếu HomeController không tồn tại
        if (!class_exists($controllerClass)) {
            // Có thể xử lý lỗi 404 ở đây
            die("Lỗi: Không tìm thấy Controller '$controllerClass'.");
        }

        $this->currentController = new $controllerClass();

        // 2. Xử lý Method
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        // 3. Xử lý Params
        $this->params = $url ? array_values($url) : [];

        // 4. Gọi hàm
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    protected function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
