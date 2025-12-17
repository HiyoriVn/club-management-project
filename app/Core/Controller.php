<?php

namespace App\Core;

class Controller
{
    /**
     * Load View
     */
    protected function view($view, $data = [])
    {
        // Extract data thành các biến độc lập để view dùng
        extract($data);

        // Kiểm tra file view có tồn tại không
        if (file_exists('../app/Views/' . $view . '.php')) {
            require_once '../app/Views/' . $view . '.php';
        } else {
            die("View does not exist: " . $view);
        }
    }

    /**
     * Load Model
     */
    protected function model($model)
    {
        // Require file model
        require_once '../app/Models/' . $model . '.php';

        // Instantiate model
        // Namespace chuẩn: App\Models\User
        $modelClass = 'App\\Models\\' . $model;
        return new $modelClass();
    }

    /**
     * Chuyển hướng trang
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Yêu cầu phải đăng nhập (Middleware: Auth)
     * Nếu chưa đăng nhập -> đá về trang login
     */
    protected function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Yêu cầu khách (Middleware: Guest)
     * Nếu đã đăng nhập -> đá về trang dashboard
     */
    protected function requireGuest()
    {
        if (isset($_SESSION['user_id'])) {
            // Sửa dòng này: Trỏ thẳng vào dashboard
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }
}
