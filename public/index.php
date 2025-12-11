<?php


// 1. Cấu hình Session 
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => true, // Đặt true nếu chạy HTTPS
    'httponly' => true, // Chống XSS đọc cookie
    'samesite' => 'Lax'
]);
session_start();

// Đảm bảo CSRF token luôn tồn tại (Logic cũ của bạn, giữ lại)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. Nạp Composer Autoload (Thay thế cho spl_autoload_register thủ công)
// Dòng này sẽ nạp luôn cả SessionHelper đã khai báo trong composer.json
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 3. Nạp biến môi trường .env
try {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
} catch (\Exception $e) {
    die('Lỗi cấu hình: Không tìm thấy file .env hoặc cấu hình sai.');
}

// 4. Nạp Config (Sử dụng biến từ .env)
require_once dirname(__DIR__) . '/config/config.php';

// 5. Cấu hình hiển thị lỗi dựa trên môi trường (Thay thế ini_set cứng nhắc)
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'local') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0); // Tắt hiển thị lỗi trên production
    error_reporting(0);
}

// 6. Khởi chạy App
use App\Core\Router;

try {
    $init = new Router();
} catch (Exception $e) {
    // Xử lý lỗi toàn cục đẹp mắt hơn
    if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'local') {
        echo "<div style='background:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; margin:20px; font-family:sans-serif;'>";
        echo "<h3>Lỗi Hệ Thống (Local):</h3>";
        echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        echo "</div>";
    } else {
        // Ghi log lỗi vào file
        error_log($e->getMessage());
        // Hiển thị thông báo thân thiện cho user
        echo "<h1>Đã xảy ra lỗi hệ thống. Vui lòng quay lại sau.</h1>";
    }
}
