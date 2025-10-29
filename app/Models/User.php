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
    /**
     * Lấy 1 user bằng ID
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return ($this->db->rowCount() > 0) ? $row : false;
    }

    /**
     * Lấy tất cả user trong hệ thống
     */
    public function getAllUsers()
    {
        // Lấy các cột cần thiết, dùng NAME viết hoa
        $this->db->query("SELECT id, NAME, email, system_role FROM users ORDER BY NAME");
        return $this->db->resultSet();
    }

    /**
     * Lấy các vai trò (trong các Ban) của 1 user cụ thể
     */
    public function getRolesForUser($user_id)
    {
        // Dùng JOIN để lấy tên Ban và tên Vai trò
        // Dùng NAME viết hoa cho các bảng
        $this->db->query("SELECT 
                            udr.id as assignment_id, 
                            d.NAME as department_name, 
                            dr.NAME as role_name
                        FROM user_department_roles udr
                        JOIN departments d ON udr.department_id = d.id
                        JOIN department_roles dr ON udr.role_id = dr.id
                        WHERE udr.user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    /**
     * Gán 1 vai trò mới cho user
     */
    public function assignRole($data)
    {
        // 'assigned_by' là admin đang thực hiện
        $this->db->query("INSERT INTO user_department_roles (user_id, department_id, role_id, assigned_by) 
                         VALUES (:user_id, :department_id, :role_id, :assigned_by)");

        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':department_id', $data['department_id']);
        $this->db->bind(':role_id', $data['role_id']);
        $this->db->bind(':assigned_by', $_SESSION['user_id']); // Lấy ID của admin đang đăng nhập

        // Dùng try-catch để "bẫy" lỗi Duplicate Key (Mã lỗi 23000)
        try {
            return $this->db->execute();
        } catch (\PDOException $e) {
            // Nếu lỗi là do "Duplicate key" (mã 23000)
            if ($e->getCode() == 23000) {
                // Chỉ đơn giản là trả về false, không làm "chết" chương trình
                return false;
            } else {
                // Nếu là lỗi khác, thì mới báo
                die('Lỗi CSDL khác: ' . $e->getMessage());
            }
        }
    }

    /**
     * Thu hồi 1 vai trò (xóa 1 dòng trong user_department_roles)
     */
    public function revokeRole($assignment_id)
    {
        $this->db->query("DELETE FROM user_department_roles WHERE id = :assignment_id");
        $this->db->bind(':assignment_id', $assignment_id);
        return $this->db->execute();
    }

    /**
     * Lấy danh sách ID các Ban mà user là thành viên
     * @param int $user_id
     * @return array Mảng các ID, vd: [1, 3]
     */
    public function getDepartmentIds($user_id)
    {
        $this->db->query("SELECT DISTINCT department_id FROM user_department_roles WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);

        // 1. Lấy kết quả bằng hàm public
        // Kết quả sẽ là: [ ['department_id' => 1], ['department_id' => 3] ]
        $resultSet = $this->db->resultSet();

        // 2. Dùng array_column() để trích xuất thành mảng [ 1, 3 ]
        $results = array_column($resultSet, 'department_id');

        return $results ? $results : [];
    }

    /**
     * Cập nhật system_role cho 1 user (Promote/Demote)
     * @param int $user_id ID của user cần đổi
     * @param string $new_role Vai trò mới (vd: 'member', 'guest')
     * @return boolean
     */
    public function setSystemRole($user_id, $new_role)
    {
        // Kiểm tra xem $new_role có hợp lệ không (an toàn)
        $allowed_roles = ['guest', 'member', 'subadmin', 'admin'];
        if (!in_array($new_role, $allowed_roles)) {
            return false;
        }

        $this->db->query("UPDATE users SET system_role = :new_role WHERE id = :user_id");
        $this->db->bind(':new_role', $new_role);
        $this->db->bind(':user_id', $user_id);

        return $this->db->execute();
    }
}
