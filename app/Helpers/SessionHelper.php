<?php

use App\Models\ActivityLog;

// 1. Khởi tạo session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================================================================
 * PHẦN 1: FLASH MESSAGE & SESSION
 * Dùng cho hiển thị thông báo (Alert)
 * ========================================================================= */

/**
 * Gán thông báo flash
 * @param string $type Loại thông báo: 'success', 'error', 'warning', 'info'
 * @param string $message Nội dung thông báo
 */
if (!function_exists('set_flash_message')) {
    function set_flash_message($type, $message)
    {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

/**
 * Lấy thông báo flash hiện tại và XÓA ngay sau khi lấy
 * Dùng trong header.php để hiển thị Alert
 * @return array|null Trả về mảng ['type', 'message'] hoặc null
 */
if (!function_exists('get_flash_message')) {
    function get_flash_message()
    {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']); // Xóa ngay để không hiện lại khi F5
            return $message;
        }
        return null;
    }
}

/**
 * [TƯƠNG THÍCH NGƯỢC]
 * Nếu code cũ của bạn có chỗ nào gọi display_flash_message() mà chưa kịp sửa,
 * hàm này sẽ giúp không bị lỗi, nhưng khuyên dùng get_flash_message() ở View hơn.
 */
if (!function_exists('display_flash_message')) {
    function display_flash_message()
    {
        // Tạm thời bỏ trống hoặc chuyển hướng sang logic mới nếu cần
        // Vì header.php mới đã tự động gọi get_flash_message() rồi.
    }
}

/* =========================================================================
 * PHẦN 2: USER AUTH HELPERS
 * Tiện ích check đăng nhập nhanh trong View
 * ========================================================================= */

/**
 * Kiểm tra user đã đăng nhập chưa
 */
if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return isset($_SESSION['user_id']);
    }
}

/**
 * Lấy thông tin user hiện tại
 * @param string $key Key cần lấy (id, name, email, role...), để trống lấy hết
 */
if (!function_exists('current_user')) {
    function current_user($key = null)
    {
        if (!is_logged_in()) return null;

        if ($key) {
            return $_SESSION['user_' . $key] ?? null;
        }
        return $_SESSION;
    }
}

/* =========================================================================
 * PHẦN 3: SYSTEM UTILITIES
 * Log, HTML Purifier (Đã chỉnh sửa để dùng Composer Autoload)
 * ========================================================================= */

/**
 * Ghi log hoạt động
 */
if (!function_exists('log_activity')) {
    function log_activity($action, $details)
    {
        // Kiểm tra login để lấy user_id
        $user_id = $_SESSION['user_id'] ?? null;

        try {
            // Giả sử Model ActivityLog đã được Autoload qua Composer
            // Nếu chưa, namespace App\Models\ActivityLog sẽ tự tìm file
            $logModel = new ActivityLog();
            $logModel->create($user_id, $action, $details);
        } catch (\Exception $e) {
            // Ghi log lỗi vào file hệ thống nếu DB lỗi
            error_log('Failed to write activity log: ' . $e->getMessage());
        }
    }
}

/**
 * Lọc HTML bẩn (Anti-XSS) dùng thư viện ezyang/htmlpurifier qua Composer
 */
if (!function_exists('purify_html')) {
    function purify_html($dirty_html)
    {
        // Không cần require thủ công file library nữa vì đã có composer autoload

        // Cấu hình
        $config = HTMLPurifier_Config::createDefault();

        // Chỉ cho phép các thẻ an toàn (tránh script, iframe...)
        $config->set('HTML.Allowed', 'p,b,strong,i,em,u,ul,ol,li,br,a[href,target],img[src|width|height|alt]');
        $config->set('HTML.TargetBlank', true); // Link tự mở tab mới

        // Nếu muốn cho phép style cơ bản (màu sắc) thì bỏ comment dòng dưới:
        // $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,text-decoration,color,background-color,text-align');

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($dirty_html);
    }
}

/**
 * Gửi thông báo (Notification)
 * Lưu ý: Cần đảm bảo Model Notification và bảng notifications tồn tại
 */
if (!function_exists('sendNotification')) {
    function sendNotification($user_id, $title, $message)
    {
        // Kiểm tra class tồn tại để tránh lỗi fatal nếu chưa có Model
        if (class_exists('App\Models\Notification')) {
            $notificationModel = new \App\Models\Notification();
            $notificationModel->create($user_id, $title, $message);
        }
    }
}
