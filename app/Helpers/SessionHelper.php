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
        // (Chúng ta sẽ thêm CSS cho class 'flash-message' ở Bước 3)
        echo '<div class="flash-message ' . htmlspecialchars($type) . '">';
        echo htmlspecialchars($message);
        echo '</div>';
    }
}