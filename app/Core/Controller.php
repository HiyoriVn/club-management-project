<?php
// app/Core/Controller.php
namespace App\Core;

class Controller
{
    /**
     * Tải View
     * @param string $view Tên view (vd: 'home' hoặc 'auth/login')
     * @param array $data Dữ liệu truyền vào view
     */
    public function view($view, $data = [])
    {
        // 1. Logic lấy thông báo (Notifications) đã được tách ra
        // Chúng ta sẽ gọi nó ở file layout (header.php) thay vì ở đây.
        // Điều này giúp Controller gọn nhẹ và không phụ thuộc vào Model Notification.

        // 2. Extract data để dùng trong view
        extract($data);

        // 3. Kiểm tra file view
        $viewPath = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Ném lỗi để Developer biết thiếu file view nào
            throw new \Exception("View '$view' not found at: $viewPath");
        }
    }

    // --- CÁC HÀM HELPER  ---

    protected function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect(BASE_URL . '/auth/login');
        }
    }

    protected function requireGuest()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect(BASE_URL . '/dashboard');
        }
    }
    /**
     * Chốt chặn: Yêu cầu người dùng phải có vai trò (role) cụ thể.
     * @param array $roles Mảng các vai trò được phép (vd: ['admin', 'subadmin'])
     */
    protected function requireRole($roles = [])
    {
        $this->requireLogin();

        if (!isset($_SESSION['user_role'])) {
            $this->redirect(BASE_URL . '/auth/logout');
        }

        if (!in_array($_SESSION['user_role'], $roles)) {
            // Có thể nâng cấp thành trang 403 Access Denied sau này
            echo "<div style='padding:20px; text-align:center; color:red;'>
                    <h1>403 Forbidden</h1>
                    <p>Bạn không có quyền truy cập trang này.</p>
                    <a href='" . BASE_URL . "'>Quay về trang chủ</a>
                  </div>";
            exit;
        }
    }

    /**
     * Hàm chuyển hướng
     * (Chúng ta copy nó từ AuthController ra đây để dùng chung)
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}
