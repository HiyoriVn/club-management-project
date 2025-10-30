<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User; // "use" Model User vừa tạo

class AuthController extends Controller
{

    private $userModel;

    public function __construct()
    {
        // Code cũ (nạp User Model) giữ nguyên
        require_once ROOT_PATH . '/app/Models/User.php';
        $this->userModel = new User();
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

            // 1. "Làm sạch" dữ liệu đầu vào (phòng chống XSS)
            // $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

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
                    // Đăng ký thành công, chuyển hướng về trang đăng nhập
                    // (Sau này ta sẽ làm thêm Flash Message thông báo "Đăng ký thành công")
                    $this->redirect(BASE_URL . '/auth/login');
                } else {
                    die('Có lỗi xảy ra, vui lòng thử lại');
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
                    // TẠO SESSION (Rất quan trọng)
                    $this->create_user_session($loggedInUser);

                    // Chuyển hướng về trang chủ
                    $this->redirect(BASE_URL);
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
        // Hủy các biến session
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);

        // Hủy toàn bộ session
        session_destroy();

        // Chuyển hướng về trang chủ
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
}
