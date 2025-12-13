<?php
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

    public function index()
    {
        $this->redirect(BASE_URL . '/auth/login');
    }

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

    public function processLogin()
    {
        $this->requireGuest();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/auth/login');
        }

        // CSRF Check 
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Phiên làm việc không hợp lệ. Vui lòng tải lại trang.');
            $this->redirect(BASE_URL . '/auth/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $data = [
            'title' => 'Đăng Nhập',
            'email' => $email,
            'password' => $password,
            'email_err' => '',
            'password_err' => ''
        ];

        if (empty($email)) $data['email_err'] = 'Vui lòng nhập email';
        if (empty($password)) $data['password_err'] = 'Vui lòng nhập mật khẩu';

        if (empty($data['email_err']) && empty($data['password_err'])) {
            $loggedInUser = $this->userModel->login($email, $password);

            if ($loggedInUser) {
                $this->createUserSession($loggedInUser);

                $this->logModel->create($loggedInUser['id'], 'user_login', 'Đăng nhập thành công.');

                \set_flash_message('success', 'Chào mừng trở lại, ' . htmlspecialchars($loggedInUser['name']) . '!');
                $this->redirect(BASE_URL);
            } else {
                $data['password_err'] = 'Email/Mật khẩu không đúng hoặc tài khoản bị khóa.';
                $this->view('auth/login', $data);
            }
        } else {
            $this->view('auth/login', $data);
        }
    }

    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            $this->logModel->create($_SESSION['user_id'], 'user_logout', 'Đăng xuất hệ thống.');
        }

        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);

        session_destroy();
        session_start();

        \set_flash_message('info', 'Bạn đã đăng xuất.');
        $this->redirect(BASE_URL . '/auth/login');
    }

    public function forgot()
    {
        $this->requireGuest();
        $this->view('auth/forgot', ['title' => 'Quên mật khẩu']);
    }

    public function send_reset()
    {
        $this->requireGuest();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $user = $this->userModel->findByEmail($email);

            if ($user && $user['is_active']) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $this->userModel->updateResetToken($user['id'], $token, $expires);

                $resetLink = BASE_URL . '/auth/reset?token=' . $token;

                if ($this->sendEmail($user['email'], $user['name'], $resetLink)) {
                    \set_flash_message('success', 'Đã gửi link đặt lại mật khẩu vào email của bạn.');
                } else {
                    \set_flash_message('error', 'Không thể gửi email. Vui lòng thử lại sau.');
                }
            } else {
                // Bảo mật: Không thông báo rõ ràng email có tồn tại hay không
                \set_flash_message('success', 'Nếu email tồn tại, link đặt lại mật khẩu sẽ được gửi.');
            }

            $this->redirect(BASE_URL . '/auth/forgot');
        }
    }

    public function reset()
    {
        $this->requireGuest();
        $token = $_GET['token'] ?? '';
        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            \set_flash_message('error', 'Link không hợp lệ hoặc đã hết hạn.');
            $this->redirect(BASE_URL . '/auth/login');
        }

        $this->view('auth/reset', ['token' => $token, 'title' => 'Đặt lại mật khẩu']);
    }

    public function update_password()
    {
        $this->requireGuest();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['token'];
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];

            $user = $this->userModel->findByResetToken($token);
            if (!$user) {
                \set_flash_message('error', 'Token không hợp lệ.');
                $this->redirect(BASE_URL . '/auth/login');
                return;
            }

            if ($password !== $confirm) {
                \set_flash_message('error', 'Mật khẩu xác nhận không khớp.');
                $this->redirect(BASE_URL . '/auth/reset?token=' . $token);
                return;
            }

            if (strlen($password) < 6) {
                \set_flash_message('error', 'Mật khẩu phải từ 6 ký tự.');
                $this->redirect(BASE_URL . '/auth/reset?token=' . $token);
                return;
            }

            // Cập nhật mật khẩu và xóa token (Logic nằm trong Model)
            if ($this->userModel->resetPassword($user['id'], $password)) {
                $this->logModel->create($user['id'], 'password_reset', 'Khôi phục mật khẩu thành công.');
                \set_flash_message('success', 'Mật khẩu đã được thay đổi. Vui lòng đăng nhập.');
                $this->redirect(BASE_URL . '/auth/login');
            } else {
                \set_flash_message('error', 'Lỗi hệ thống. Vui lòng thử lại.');
                $this->redirect(BASE_URL . '/auth/reset?token=' . $token);
            }
        }
    }

    // --- Helpers ---

    private function createUserSession($user)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name']; // Model mới trả về 'name', không phải 'NAME'
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['system_role'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function sendEmail($toEmail, $toName, $link)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $_ENV['SMTP_PORT'];
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = 'Đặt lại mật khẩu - CLB Management';
            $mail->Body    = "Chào $toName,<br><br>Vui lòng click vào link sau để đặt lại mật khẩu:<br><a href='$link'>$link</a><br><br>Link hết hạn sau 15 phút.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mail Error: ' . $mail->ErrorInfo);
            return false;
        }
    }
}