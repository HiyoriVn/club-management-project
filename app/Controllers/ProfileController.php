<?php
// app/Controllers/ProfileController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User; // Cần để lấy Name, Email
use App\Models\Profile; // Cần để lấy phone, dob...

class ProfileController extends Controller
{

    private $userModel;
    private $profileModel;

    public function __construct()
    {
        // Chỉ member trở lên mới có Hồ sơ
        $this->requireRole(['admin', 'subadmin', 'member']);

        // Nạp cả 2 Model
        require_once ROOT_PATH . '/app/Models/User.php';
        require_once ROOT_PATH . '/app/Models/Profile.php';

        $this->userModel = new User();
        $this->profileModel = new Profile();
    }

    /**
     * (READ) Hiển thị trang "Hồ sơ của tôi" (chính là form Edit)
     */
    public function index()
    {
        $user_id = $_SESSION['user_id'];

        // 1. Lấy thông tin cơ bản (từ bảng users)
        $user = $this->userModel->findById($user_id);

        // 2. Lấy thông tin mở rộng (từ bảng user_profiles)
        $profile = $this->profileModel->findByUserId($user_id);

        // 3. Chuẩn bị data cho View
        $data = [
            'title' => 'Hồ sơ của tôi',
            'user_id' => $user_id,
            'name' => $user['NAME'], // Từ bảng users
            'email' => $user['email'], // Từ bảng users
            'system_role' => $user['system_role'], // Từ bảng users

            // Dữ liệu từ bảng user_profiles (có thể là rỗng/false)
            'student_id' => $profile ? $profile['student_id'] : '',
            'phone' => $profile ? $profile['phone'] : '',
            'gender' => $profile ? $profile['gender'] : 'other',
            'dob' => $profile ? $profile['dob'] : '',
            'address' => $profile ? $profile['address'] : '',
            'bio' => $profile ? $profile['bio'] : ''
        ];

        $this->view('profile/index', $data);
    }

    /**
     * (UPDATE) Xử lý cập nhật Hồ sơ (POST)
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/profile');
        }

        $user_id = $_SESSION['user_id'];

        // 1. Lấy dữ liệu "thô" từ form
        $data = [
            'student_id' => trim($_POST['student_id']),
            'phone' => trim($_POST['phone']),
            'gender' => $_POST['gender'],
            'dob' => $_POST['dob'],
            'address' => trim($_POST['address']),
            'bio' => trim($_POST['bio'])
        ];

        // (Ta có thể thêm validate cho phone, dob... nhưng tạm thời bỏ qua)

        // 2. Gọi Model (hàm "createOrUpdate" tự lo việc Insert/Update)
        if ($this->profileModel->createOrUpdate($user_id, $data)) {
            // Cập nhật thành công
            // (Nên có Flash Message "Cập nhật thành công")
            $this->redirect(BASE_URL . '/profile');
        } else {
            die('Có lỗi CSDL xảy ra.');
        }
    }
}
