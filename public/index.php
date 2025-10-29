<?php
// public/index.php

// 1. Khởi động session
session_start();

// Bật hiển thị lỗi (để debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Nạp file cấu hình
require_once '../config/config.php';

// 2. Đăng ký Autoloader (Trình tự động nạp lớp)
// Khi em "new App\Core\Router()", nó sẽ tự động nạp file "app/Core/Router.php"
spl_autoload_register(function ($className) {
    // $className sẽ là "App\Core\Router"
    // Đổi "\" thành "/" (chuẩn của Windows/Linux)
    // Kết quả: "App/Core/Router"
    $className = str_replace('\\', '/', $className);

    // Đường dẫn file: "../app/Core/Router.php" (loại bỏ "App/")
    // Bỏ "App/" ra khỏi đường dẫn
    $classPath = str_replace('App/', '', $className);
    $file = ROOT_PATH . '/app/' . $classPath . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// --- THÊM HÀM HELPER NÀY ---
/**
 * Hàm tiện ích để gửi thông báo
 * @param int $user_id Người nhận
 * @param string $title Tiêu đề
 * @param string $message Nội dung
 */
function sendNotification($user_id, $title, $message)
{
    // Nạp Model (nếu chưa nạp)
    if (!class_exists('App\Models\Notification')) {
        require_once ROOT_PATH . '/app/Models/Notification.php';
    }
    // Tạo đối tượng và gửi
    $notificationModel = new App\Models\Notification();
    $notificationModel->create($user_id, $title, $message);
}

// 3. (Tạm thời) Nạp lớp Database thủ công vì nó chưa theo chuẩn App\
// (Sau này ta sẽ sửa lại sau)
require_once ROOT_PATH . '/app/Core/Database.php';


// 4. Khởi động ứng dụng
// Khi "new Router()", hàm __construct() trong Router sẽ tự động chạy
// và xử lý toàn bộ logic điều hướng.
try {
    new App\Core\Router();
} catch (Exception $e) {
    echo 'Đã có lỗi xảy ra: ' . $e->getMessage();
}
