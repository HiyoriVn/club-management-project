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
        // Code cũ (nạp User Model) giữ nguyên
        // require_once ROOT_PATH . '/app/Models/User.php';
        $this->userModel = new User();
        $this->logModel = new ActivityLog();
    }

    /**
     * HÀM MỚI: Xử lý khi truy cập /auth
     * Tự động chuyển hướng đến trang login
     */
    public function index()
    {
        $this->redirect(BASE_URL . '/auth/login');
    }

    /**
     * Hiển thị form đăng ký (HTTP GET)
     */
    public function register()
    {
        $this->requireGuest();

        // Dữ liệu mặc định cho form
        $data = [
            'title' => 'Đăng Ký Tài Khoản',
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'name_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        // Tải view và truyền $data
        $this->view('auth/register', $data);
    }

    /**
     * Xử lý dữ liệu từ form đăng ký (HTTP POST)
     */
    public function store()
    {
        $this->requireGuest();

        // Chỉ xử lý nếu request là POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Kiểm tra CSRF Token
            if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
                \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
                $this->redirect(BASE_URL . '/auth/register'); // Quay lại form đăng ký
                exit;
            }

            $data = [
                'title' => 'Đăng Ký Tài Khoản',
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // 2. Validate Dữ liệu
            // Validate Name
            if (empty($data['name'])) {
                $data['name_err'] = 'Vui lòng nhập tên';
            }

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Email không hợp lệ';
            } elseif ($this->userModel->findUserByEmail($data['email'])) {
                // Kiểm tra email đã tồn tại chưa (dùng Model)
                $data['email_err'] = 'Email này đã được sử dụng';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Vui lòng nhập mật khẩu';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            // Validate Confirm Password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Vui lòng xác nhận mật khẩu';
            } elseif ($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'Mật khẩu không khớp';
            }

            // 3. Xử lý sau khi Validate
            // Nếu không còn lỗi (các biến _err đều rỗng)
            if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {

                // MÃ HÓA MẬT KHẨU (Rất quan trọng)
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Gọi Model để đăng ký
                if ($this->userModel->register($data)) {
                    \log_activity('user_register', 'Tài khoản mới [' . $data['name'] . '] đã được đăng ký.');
                    \set_flash_message('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.');
                    $this->redirect(BASE_URL . '/auth/login');
                } else {
                    \set_flash_message('error', 'Có lỗi xảy ra, không thể đăng ký. Vui lòng thử lại.');
                    $this->redirect(BASE_URL . '/auth/register'); // Quay lại form
                }
            } else {
                // Nếu có lỗi, tải lại view và hiển thị lỗi
                $this->view('auth/register', $data);
            }
        } else {
            // Nếu không phải POST, đẩy về trang đăng ký
            $this->redirect(BASE_URL . '/auth/register');
        }
    }

    /**
     * Hiển thị form đăng nhập (HTTP GET)
     */
    public function login()
    {
        $this->requireGuest();

        // Dữ liệu mặc định cho form
        $data = [
            'title' => 'Đăng Nhập',
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        // Tải view và truyền $data
        $this->view('auth/login', $data);
    }

    /**
     * Xử lý dữ liệu từ form đăng nhập (HTTP POST)
     */
    public function processLogin()
    {
        $this->requireGuest();

        // Chỉ xử lý nếu request là POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Kiểm tra CSRF Token
            if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
                \set_flash_message('error', 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
                $this->redirect(BASE_URL . '/auth/register'); // Quay lại form đăng ký
                exit;
            }

            // Dữ liệu "thô" từ form
            $data = [
                'title' => 'Đăng Nhập',
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            // 1. Validate Dữ liệu
            if (empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            }
            if (empty($data['password'])) {
                $data['password_err'] = 'Vui lòng nhập mật khẩu';
            }

            // 2. Kiểm tra email có tồn tại không
            if (empty($data['email_err']) && empty($data['password_err'])) {
                if (!$this->userModel->findUserByEmail($data['email'])) {
                    // Email không tồn tại (chúng ta gộp lỗi chung cho bảo mật)
                    $data['email_err'] = 'Email hoặc mật khẩu không đúng';
                }
            }

            // 3. Xử lý sau khi Validate
            if (empty($data['email_err']) && empty($data['password_err'])) {
                // Email tồn tại, giờ thử đăng nhập
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    // Đăng nhập thành công!
                    $this->create_user_session($loggedInUser);
                    \log_activity('user_login', 'Người dùng [' . $loggedInUser['NAME'] . '] đã đăng nhập.');
                    \set_flash_message('success', 'Đăng nhập thành công! Chào mừng ' . htmlspecialchars($loggedInUser['NAME']) . '.');
                    $this->redirect(BASE_URL); // Chuyển về trang chủ
                } else {
                    // Mật khẩu sai
                    $data['password_err'] = 'Email hoặc mật khẩu không đúng';
                    // Tải lại view và hiển thị lỗi
                    $this->view('auth/login', $data);
                }
            } else {
                // Nếu có lỗi (email/pass rỗng), tải lại view và hiển thị lỗi
                $this->view('auth/login', $data);
            }
        } else {
            // Nếu không phải POST, đẩy về trang đăng nhập
            $this->redirect(BASE_URL . '/auth/login');
        }
    }

    /**
     * Hủy Session và Đăng xuất
     */
    public function logout()
    {
        if (isset($_SESSION['user_name'])) {
            \log_activity('user_logout', 'Người dùng [' . $_SESSION['user_name'] . '] đã đăng xuất.');
        }
        // Hủy các biến session
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);

        // Hủy toàn bộ session
        session_destroy();
        session_start();

        \set_flash_message('info', 'Bạn đã đăng xuất khỏi hệ thống.');
        $this->redirect(BASE_URL);
    }

    /* --- CÁC HÀM TIỆN ÍCH --- */

    // Hàm chuyển hướng (Helper)
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    // Hàm kiểm tra đã đăng nhập chưa
    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Hàm tạo session
    protected function create_user_session($user)
    {
        // $user là mảng data lấy từ Model
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['NAME'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['system_role']; // Lấy vai trò từ CSDL

        // --- THÊM DÒNG NÀY ---
        // Tạo một token ngẫu nhiên để chống lỗi CSRF
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    /**
     * HÀM MỚI 1: Hiển thị form "Quên mật khẩu"
     * URL: /auth/forgot
     */
    public function forgot()
    {
        // Không dùng layout, chỉ render file view đơn giản
        $this->view('auth/forgot');
    }

    /**
     * HÀM MỚI 2: Xử lý gửi link reset
     * URL: /auth/send_reset (xử lý POST từ form)
     */
    public function send_reset()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        // Anh bỏ qua check CSRF token để khớp với code login/register của em
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $userModel = $this->model('User');

            // Em PHẢI TỰ THÊM hàm findByEmail vào User.php nhé!
            $user = $this->userModel->findByEmail($email);

            // Vấn đề Bảo mật: Luôn báo thành công kể cả khi không tìm thấy email
            // để ngăn kẻ xấu dò email.
            if ($user) {
                // 1. Tạo Token
                $token = bin2hex(random_bytes(32)); // Tạo 1 token 64 ký tự
                $expires = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Hạn là 15 phút

                // 2. Lưu Token vào DB (Em phải thêm hàm này vào User.php)
                $this->userModel->updateResetToken($user['id'], $token, $expires);

                // 3. Gửi Email
                $reset_link = BASE_URL . '/auth/reset?token=' . $token;

                try {
                    $this->sendEmail($user['email'], $user['name'], $reset_link);
                } catch (Exception $e) {
                    // Nếu email lỗi, cũng không báo cho user
                    // Em có thể log lỗi này lại cho em xem
                    error_log("PHPMailer Error: {$e->getMessage()}");
                }
            }

            // 4. Luôn luôn báo thành công
            set_flash_message('success', 'Nếu email của bạn tồn tại, một link đặt lại mật khẩu đã được gửi.');
            $this->redirect(BASE_URL . '/auth/forgot');
        } else {
            $this->redirect(BASE_URL . '/auth/login');
        }
    }

    /**
     * HÀM MỚI 3: Hàm helper riêng để gửi email (dùng PHPMailer)
     * (Hàm này là private, chỉ dùng trong class này)
     */
    private function sendEmail($to_email, $to_name, $reset_link)
    {
        $mail = new PHPMailer(true); // True để bật Exceptions

        // ----- CẤU HÌNH GỬI MAIL (DÙNG GMAIL) -----
        // Cảnh báo: Em PHẢI dùng "Mật khẩu ứng dụng" (App Password)
        // của Google, không dùng mật khẩu Gmail thường.
        // Xem hướng dẫn: https://support.google.com/accounts/answer/185833

        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];     // Đọc từ .env
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME']; // Đọc từ .env
        $mail->Password   = $_ENV['SMTP_PASSWORD']; // Đọc từ .env
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $_ENV['SMTP_PORT'];     // Đọc từ .env
        $mail->CharSet    = 'UTF-8';

        // Người gửi
        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);

        // Người nhận
        $mail->addAddress($to_email, $to_name);

        // Nội dung
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
     * HÀM MỚI 4: Hiển thị form reset (khi user bấm link trong email)
     * URL: /auth/reset
     */
    public function reset()
    {
        // 1. Lấy token từ URL
        // Router của em sẽ tự động pass tham số vào, nhưng $_GET an toàn hơn
        $token = $_GET['token'] ?? '';

        // 2. Kiểm tra token (Em phải thêm hàm này vào User.php)
        $userModel = $this->model('User');
        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            // Token không tồn tại hoặc đã hết hạn
            set_flash_message('error', 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
            $this->redirect(BASE_URL . '/auth/login');
        } else {
            // Token hợp lệ, hiển thị form và truyền token sang view
            $this->view('auth/reset', ['token' => $token]);
        }
    }

    /**
     * HÀM MỚI 5: Xử lý cập nhật mật khẩu mới
     * URL: /auth/update_password (xử lý POST từ form)
     */
    public function update_password()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['token'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // 1. Kiểm tra lại token (cho chắc chắn)
            $userModel = $this->model('User');
            $user = $this->userModel->findByResetToken($token);

            if (!$user) {
                set_flash_message('error', 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
                $this->redirect(BASE_URL . '/auth/login');
                return;
            }

            // 2. Validate mật khẩu
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

            if (strlen($password) < 6) { // Yêu cầu tối thiểu 6 ký tự
                set_flash_message('error', 'Mật khẩu phải có ít nhất 6 ký tự.');
                $this->redirect(BASE_URL . '/auth/reset?token=' . $token);
                return;
            }

            // 3. Mọi thứ OK -> Cập nhật mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Hàm updatePassword này (trong Model) phải tự động xóa token
            // (Em phải thêm hàm này vào User.php)
            if ($this->userModel->updatePassword($user['id'], $hashed_password)) {
                $this->logModel->create(
                    $user['id'],
                    'password_reset',
                    'Người dùng ' . htmlspecialchars($user['name']) . ' (ID: ' . $user['id'] . ') đã đặt lại mật khẩu thành công.'
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
