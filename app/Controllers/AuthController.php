<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller
{
    private $userModel;
    private $logModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->logModel = new ActivityLog();
    }

    /**
     * Redirect /auth tới login
     */
    public function index()
    {
        $this->redirect(BASE_URL . '/auth/login');
    }

    /**
     * ❌ ĐĂNG KÝ ĐÃ BỊ VÔ HIỆU HÓA
     * Chỉ admin có thể tạo tài khoản mới qua trang quản lý người dùng
     */
    public function register()
    {
        $this->redirect(BASE_URL . '/auth/login');
    }

    public function store()
    {
        $this->redirect(BASE_URL . '/auth/login');
    }

    /**
     * Hiển thị trang đăng nhập
     */
    public function login()
    {
        $this->requireGuest();

        $data = [
            'title' => 'Đăng Nhập',
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        $this->view('auth/login', $data);
    }

    /**
     * Xử lý đăng nhập
     */
    public function processLogin()
    {
        $this->requireGuest();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/auth/login');
        }

        // CSRF Check
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            $this->redirect(BASE_URL . '/auth/login');
            exit;
        }

        $data = [
            'title' => 'Đăng Nhập',
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password']),
            'email_err' => '',
            'password_err' => ''
        ];

        // Validate
        if (empty($data['email'])) {
            $data['email_err'] = 'Vui lòng nhập email';
        }
        if (empty($data['password'])) {
            $data['password_err'] = 'Vui lòng nhập mật khẩu';
        }

        // Kiểm tra email tồn tại
        if (empty($data['email_err']) && empty($data['password_err'])) {
            if (!$this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Email hoặc mật khẩu không đúng';
            }
        }

        // Xử lý đăng nhập
        if (empty($data['email_err']) && empty($data['password_err'])) {
            $loggedInUser = $this->userModel->login($data['email'], $data['password']);

            if ($loggedInUser) {
                // ✅ KIỂM TRA: Không cho phép guest đăng nhập (hệ thống nội bộ)
                if (!isset($loggedInUser['system_role']) || !in_array($loggedInUser['system_role'], ['member', 'subadmin', 'admin'])) {
                    \set_flash_message('error', 'Tài khoản của bạn không có quyền truy cập hệ thống. Vui lòng liên hệ quản trị viên.');
                    $this->redirect(BASE_URL . '/auth/login');
                    exit;
                }

                $this->create_user_session($loggedInUser);
                \log_activity('user_login', 'Người dùng [' . $loggedInUser['NAME'] . '] đã đăng nhập.');
                \set_flash_message('success', 'Đăng nhập thành công! Chào mừng ' . htmlspecialchars($loggedInUser['NAME']) . '.');
                $this->redirect(BASE_URL);
            } else {
                $data['password_err'] = 'Email hoặc mật khẩu không đúng';
                $this->view('auth/login', $data);
            }
        } else {
            $this->view('auth/login', $data);
        }
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        if (isset($_SESSION['user_name'])) {
            \log_activity('user_logout', 'Người dùng [' . $_SESSION['user_name'] . '] đã đăng xuất.');
        }

        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);

        session_destroy();
        session_start();

        \set_flash_message('info', 'Bạn đã đăng xuất khỏi hệ thống.');
        $this->redirect(BASE_URL . '/auth/login');
    }

    /* --- HELPER METHODS --- */

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    protected function create_user_session($user)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['NAME'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['system_role'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Form quên mật khẩu
     */
    public function forgot()
    {
        $this->view('auth/forgot');
    }

    /**
     * Gửi link reset password
     */
    public function send_reset()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $user = $this->userModel->findByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                $this->userModel->updateResetToken($user['id'], $token, $expires);

                $reset_link = BASE_URL . '/auth/reset?token=' . $token;

                try {
                    $this->sendEmail($user['email'], $user['NAME'], $reset_link);
                } catch (Exception $e) {
                    error_log("PHPMailer Error: {$e->getMessage()}");
                }
            }

            set_flash_message('success', 'Nếu email của bạn tồn tại, một link đặt lại mật khẩu đã được gửi.');
            $this->redirect(BASE_URL . '/auth/forgot');
        } else {
            $this->redirect(BASE_URL . '/auth/login');
        }
    }

    /**
     * Gửi email (PHPMailer)
     */
    private function sendEmail($to_email, $to_name, $reset_link)
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $_ENV['SMTP_PORT'];
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = 'Yêu cầu đặt lại mật khẩu cho Hệ Thống CLB';
        $mail->Body    = "Chào $to_name,<br><br>" .
            "Bạn đã yêu cầu đặt lại mật khẩu. Vui lòng bấm vào link dưới đây để tiếp tục:<br>" .
            "<a href='$reset_link'>$reset_link</a><br><br>" .
            "Link này sẽ hết hạn trong 15 phút.<br>" .
            "Nếu bạn không yêu cầu, vui lòng bỏ qua email này.<br><br>" .
            "Trân trọng.";
        $mail->AltBody = "Vui lòng truy cập $reset_link để đặt lại mật khẩu. Link hết hạn trong 15 phút.";

        $mail->send();
    }

    /**
     * Form reset password
     */
    public function reset()
    {
        $token = $_GET['token'] ?? '';
        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            set_flash_message('error', 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
            $this->redirect(BASE_URL . '/auth/login');
        } else {
            $this->view('auth/reset', ['token' => $token]);
        }
    }

    /**
     * Cập nhật mật khẩu mới
     */
    public function update_password()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['token'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            $user = $this->userModel->findByResetToken($token);

            if (!$user) {
                set_flash_message('error', 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
                $this->redirect(BASE_URL . '/auth/login');
                return;
            }

            if (empty($password) || empty($confirm_password)) {
                set_flash_message('error', 'Vui lòng nhập cả hai ô mật khẩu.');
                $this->redirect(BASE_URL . '/auth/reset?token=' . $token);
                return;
            }

            if ($password !== $confirm_password) {
                set_flash_message('error', 'Mật khẩu xác nhận không khớp.');
                $this->redirect(BASE_URL . '/auth/reset?token=' . $token);
                return;
            }

            if (strlen($password) < 6) {
                set_flash_message('error', 'Mật khẩu phải có ít nhất 6 ký tự.');
                $this->redirect(BASE_URL . '/auth/reset?token=' . $token);
                return;
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            if ($this->userModel->updatePassword($user['id'], $hashed_password)) {
                $this->logModel->create(
                    $user['id'],
                    'password_reset',
                    'Người dùng ' . htmlspecialchars($user['NAME']) . ' (ID: ' . $user['id'] . ') đã đặt lại mật khẩu thành công.'
                );
                set_flash_message('success', 'Mật khẩu của bạn đã được cập nhật! Vui lòng đăng nhập.');
                $this->redirect(BASE_URL . '/auth/login');
            } else {
                set_flash_message('error', 'Đã có lỗi xảy ra. Vui lòng thử lại.');
                $this->redirect(BASE_URL . '/auth/reset?token=' . $token);
            }
        } else {
            $this->redirect(BASE_URL . '/auth/login');
        }
    }
}
