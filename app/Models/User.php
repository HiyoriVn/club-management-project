<?php
// app/Models/User.php

namespace App\Models;

// "use" Lớp Database lõi của chúng ta
use App\Core\Database;

class User
{
    private $db;

    public function __construct()
    {
        // Lấy đối tượng Database (Singleton)
        $this->db = Database::getInstance();
    }

    /**
     * Tìm người dùng bằng email
     * @param string $email
     * @return mixed (object/boolean) Trả về object user nếu tìm thấy, false nếu không
     */
    public function findUserByEmail($email)
    {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Kiểm tra xem có tìm thấy user không
        if ($this->db->rowCount() > 0) {
            return $row; // Trả về thông tin user (dạng mảng)
        } else {
            return false;
        }
    }

    /**
     * Đăng ký người dùng mới
     * @param array $data (chứa name, email, password)
     * @return boolean True nếu thành công, False nếu thất bại
     */
    public function register($data)
    {
        // Chuẩn bị câu lệnh SQL
        $this->db->query("INSERT INTO users (name, email, password, system_role) VALUES (:name, :email, :password, 'guest')");

        // Bind các giá trị
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']); // Mật khẩu đã được mã hóa từ Controller

        // Thực thi
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Đăng nhập người dùng
     * @param string $email
     * @param string $password (mật khẩu người dùng gõ)
     * @return mixed Trả về mảng thông tin user nếu thành công, false nếu thất bại
     */
    public function login($email, $password)
    {
        // 1. Tìm user bằng email
        $row = $this->findUserByEmail($email);

        // Nếu không tìm thấy email
        if ($row == false) {
            return false;
        }

        // 2. Lấy mật khẩu đã mã hóa (hashed) từ CSDL
        $hashed_password = $row['PASSWORD'];

        // 3. So sánh mật khẩu người dùng gõ với mật khẩu đã mã hóa
        // Dùng hàm password_verify() có sẵn của PHP
        if (password_verify($password, $hashed_password)) {
            // Mật khẩu khớp -> Trả về thông tin user
            return $row;
        } else {
            // Mật khẩu không khớp
            return false;
        }
    }
}
