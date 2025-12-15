<?php

use App\Models\ActivityLog;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================================================================
 * PHẦN 1: FLASH MESSAGE
 * ========================================================================= */

if (!function_exists('set_flash_message')) {
    function set_flash_message($type, $message)
    {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

if (!function_exists('get_flash_message')) {
    function get_flash_message()
    {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return null;
    }
}

if (!function_exists('display_flash_message')) {
    function display_flash_message() {}
}

/* =========================================================================
 * PHẦN 2: USER HELPERS
 * ========================================================================= */

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return isset($_SESSION['user_id']);
    }
}

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
 * PHẦN 3: SYSTEM UTILITIES (Log & Purify)
 * ========================================================================= */

if (!function_exists('log_activity')) {
    function log_activity($action, $details)
    {
        $user_id = $_SESSION['user_id'] ?? null;
        try {
            // ActivityLog Model đã được Composer Autoload
            $logModel = new ActivityLog();
            $logModel->create($user_id, $action, $details);
        } catch (\Exception $e) {
            error_log('Activity Log Error: ' . $e->getMessage());
        }
    }
}

if (!function_exists('purify_html')) {
    function purify_html($dirty_html)
    {
        // Sử dụng HTMLPurifier qua Composer
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,strong,i,em,u,ul,ol,li,br,a[href,target],img[src|alt]');
        $config->set('HTML.TargetBlank', true);

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($dirty_html);
    }
}
