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
 * Hiển thị thông báo flash (nếu có)
 * Sửa lại: Thay vì echo HTML, hàm này sẽ echo một <script>
 * để gọi hàm JS showToast() (sẽ được định nghĩa ở footer.php)
 */
function display_flash_message()
{
    // Kiểm tra xem có thông báo không
    if (isset($_SESSION['flash_message'])) {

        // Lấy thông tin
        $type = $_SESSION['flash_message']['type']; // 'success', 'error', 'info'
        $message = $_SESSION['flash_message']['message'];

        // Xóa thông báo khỏi session
        unset($_SESSION['flash_message']);

        // In ra một đoạn script nhỏ
        // addslashes() để đảm bảo chuỗi JS không bị lỗi nếu có dấu '
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('" . addslashes($message) . "', '" . addslashes($type) . "');
            });
        </script>";
    }
}

/**
 * Hàm tiện ích để ghi log hoạt động
* @param string $action Mã hành động (vd: 'user_login', 'department_created')
* @param string $details Chi tiết
*/

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
/**
 * Lọc (Sanitize) HTML để chống lỗi XSS
 * @param string $dirty_html HTML bẩn từ Trix editor
 * @return string HTML sạch đã được lọc
 */
function purify_html($dirty_html)
{
    // 1. Đường dẫn đến file autoload của HTMLPurifier
    $purifier_path = ROOT_PATH . '/app/Libs/htmlpurifier-4.15.0/library/HTMLPurifier.autoload.php';

    if (!file_exists($purifier_path)) {
        // Nếu không tìm thấy, trả về lỗi thay vì làm sập trang
        return '<p style="color:red; font-weight:bold;">Lỗi: Không tìm thấy thư viện HTMLPurifier. Hãy kiểm tra lại đường dẫn.</p>';
    }

    // Sửa lại dòng này:
    require_once $purifier_path;

    // 2. Cấu hình
    $config = \HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', 'p,b,strong,i,em,u,ul,ol,li,br,a[href]'); // Chỉ cho phép các thẻ này
    $config->set('HTML.TargetBlank', true); // Tự động thêm target="_blank" cho link

    // 3. Lọc
    $purifier = new \HTMLPurifier($config);
    $clean_html = $purifier->purify($dirty_html);

    return $clean_html;
}