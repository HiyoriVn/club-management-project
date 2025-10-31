<?php
// public/index.php

// 1. Khởi động session
session_start();

// Đảm bảo CSRF token luôn tồn tại cho MỌI PHIÊN LÀM VIỆC
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Bật hiển thị lỗi (để debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Nạp file cấu hình
require_once '../config/config.php';

// 2. Đăng ký Autoloader (Trình tự động nạp lớp)
// Khi em "new App\Core\Router()", nó sẽ tự động nạp file "app/Core/Router.php"
spl_autoload_register(function ($className) {
    $className = str_replace('\\', '/', $className);
    $classPath = str_replace('App/', '', $className);
    $file = ROOT_PATH . '/app/' . $classPath . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * Hàm tiện ích để gửi thông báo
 * @param int $user_id Người nhận
 * @param string $title Tiêu đề
 * @param string $message Nội dung
 */
function sendNotification($user_id, $title, $message)
{
    // Nạp Model
    if (!class_exists('App\Models\Notification')) {
        require_once ROOT_PATH . '/app/Models/Notification.php';
    }
    // Tạo đối tượng và gửi
    $notificationModel = new App\Models\Notification();
    $notificationModel->create($user_id, $title, $message);
}

// 3. (Tạm thời) Nạp lớp Database thủ công vì nó chưa theo chuẩn App\
require_once ROOT_PATH . '/app/Core/Database.php';

// Nạp file Helper (để dùng hàm set_flash_message và display_flash_message)
require_once ROOT_PATH . '/app/Helpers/SessionHelper.php';

// 4. Khởi động ứng dụng

try {
    new App\Core\Router();
} catch (Exception $e) {
    echo 'Đã có lỗi xảy ra: ' . $e->getMessage();
}
