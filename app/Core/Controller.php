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
        // Luôn lấy dữ liệu thông báo nếu đã đăng nhập
        if (isset($_SESSION['user_id'])) {
            // Nạp Model (nếu chưa nạp)
            if (!class_exists('App\Models\Notification')) {
                require_once ROOT_PATH . '/app/Models/Notification.php';
            }
            $notificationModel = new \App\Models\Notification();
            $data['unread_notifications_count'] = $notificationModel->countUnreadForUser($_SESSION['user_id']);
            $data['latest_unread_notifications'] = $notificationModel->getUnreadForUser($_SESSION['user_id'], 5);
        } else {
            // Nếu chưa đăng nhập, gán giá trị mặc định
            $data['unread_notifications_count'] = 0;
            $data['latest_unread_notifications'] = [];
        }
        // Biến mảng $data thành các biến riêng lẻ
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
    /* --- CÁC HÀM BẢO VỆ (HELPER) MỚI --- */

    /**
     * Chốt chặn: Yêu cầu người dùng phải đăng nhập.
     * Nếu chưa đăng nhập, đẩy về trang login.
     */
    protected function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            // (Chúng ta sẽ làm thêm Flash Message sau)
            // echo 'Bạn phải đăng nhập để xem trang này';
            $this->redirect(BASE_URL . '/auth/login');
        }
    }

    /**
     * Chốt chặn: Yêu cầu người dùng phải là khách (chưa đăng nhập).
     * Nếu đã đăng nhập, đẩy về trang dashboard.
     */
    protected function requireGuest()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect(BASE_URL . '/dashboard');
        }
    }

    /**
     * Hàm chuyển hướng (Helper)
     * (Chúng ta copy nó từ AuthController ra đây để dùng chung)
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
    /**
     * Chốt chặn: Yêu cầu người dùng phải có vai trò (role) cụ thể.
     * @param array $roles Mảng các vai trò được phép (vd: ['admin', 'subadmin'])
     */
    protected function requireRole($roles = [])
    {
        // 1. Kiểm tra xem đã đăng nhập chưa
        if (!isset($_SESSION['user_id'])) {
            $this->redirect(BASE_URL . '/auth/login');
        }

        // 2. Kiểm tra xem có vai trò (role) trong session không
        if (!isset($_SESSION['user_role'])) {
            // (Lỗi hi hữu) Nếu có user_id mà ko có user_role -> bắt đăng xuất
            $this->redirect(BASE_URL . '/auth/logout');
        }

        // 3. Kiểm tra vai trò (role) có nằm trong danh sách được phép
        if (!in_array($_SESSION['user_role'], $roles)) {
            // Nếu không được phép, báo lỗi 
            // (Tạm thời đẩy về dashboard, sau này ta làm trang 403 Forbidden)
            echo "Bạn không có quyền truy cập trang này!";
            echo '<br><a href="' . BASE_URL . '/dashboard">Quay về Dashboard</a>';
            exit;
        }
    }
}
