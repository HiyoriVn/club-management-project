<?php

namespace App\Core;

class Router
{
    protected $currentControllerName = 'AuthController'; // ✅ Lưu tên class (string)
    protected $currentController;  // ✅ Lưu object instance
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        // 1. Xử lý Controller
        if (isset($url[0])) {
            $controllerName = ucwords($url[0]) . 'Controller';
            $controllerClass = 'App\\Controllers\\' . $controllerName;

            if (class_exists($controllerClass)) {
                $this->currentControllerName = $controllerName;
                unset($url[0]);
            }
        }

        $controllerClass = 'App\\Controllers\\' . $this->currentControllerName;

        // Fallback về AuthController nếu không tìm thấy
        if (!class_exists($controllerClass)) {
            $this->currentControllerName = 'AuthController';
            $controllerClass = 'App\\Controllers\\AuthController';
        }

        // ✅ Tạo instance của controller
        $this->currentController = new $controllerClass();

        // 2. Xử lý Method
        if (isset($url[1])) {
            $requestedMethod = $url[1];

            // ✅ FIX: Kiểm tra bằng tên class (string) thay vì object
            if (
                $this->currentControllerName === 'AuthController' &&
                $_SERVER['REQUEST_METHOD'] === 'POST'
            ) {

                // Map URL method sang method xử lý POST
                switch ($requestedMethod) {
                    case 'login':
                        $requestedMethod = 'processLogin';
                        break;
                    case 'forgot':
                        $requestedMethod = 'send_reset';
                        break;
                    case 'reset':
                        $requestedMethod = 'update_password';
                        break;
                }
            }

            // Kiểm tra method có tồn tại không
            if (method_exists($this->currentController, $requestedMethod)) {
                $this->currentMethod = $requestedMethod;
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
