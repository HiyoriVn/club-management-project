<?php
// app/Helpers/SessionHelper.php

/**
 * Đặt một thông báo flash (sẽ hiển thị ở lần tải trang sau)
 * @param string $type Loại thông báo (vd: 'success', 'error', 'info')
 * @param string $message Nội dung
 */
function set_flash_message($type, $message)
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Hiển thị thông báo flash (nếu có) và xóa nó khỏi Session
 */
function display_flash_message()
{
    // Kiểm tra xem có thông báo không
    if (isset($_SESSION['flash_message'])) {

        // Lấy thông tin
        $type = $_SESSION['flash_message']['type']; // 'success' hoặc 'error'
        $message = $_SESSION['flash_message']['message'];

        // Xóa thông báo khỏi session để nó không hiện lại
        unset($_SESSION['flash_message']);

        // In ra HTML
        echo '<div class="flash-message ' . htmlspecialchars($type) . '">';
        echo htmlspecialchars($message);
        echo '</div>';
    }

    /**
     * Hàm tiện ích để ghi log hoạt động
     * @param string $action Mã hành động (vd: 'user_login', 'department_created')
     * @param string $details Chi tiết
     */
}

function log_activity($action, $details)
{
    // 1. Nạp Model (nếu chưa nạp)
    if (!class_exists('App\Models\ActivityLog')) {
        require_once ROOT_PATH . '/app/Models/ActivityLog.php';
    }

    // 2. Lấy user_id từ session (nếu đã đăng nhập)
    $user_id = null;
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }

    // 3. Tạo đối tượng và ghi log
    try {
        $logModel = new \App\Models\ActivityLog();
        $logModel->create($user_id, $action, $details);
    } catch (\Exception $e) {
        // (Nếu ghi log thất bại thì cũng không làm "chết" ứng dụng)
        // Có thể ghi lỗi này vào file log riêng nếu cần
        error_log('Failed to write activity log: ' . $e->getMessage());
    }
}
