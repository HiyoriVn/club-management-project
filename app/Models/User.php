<?php

namespace App\Models;

use PDO;
use App\Core\Database;
use Exception;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Tạo User mới (Kèm Profile mặc định)
     * Sử dụng Transaction để đảm bảo toàn vẹn dữ liệu
     */
    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Insert vào bảng users
            $this->db->query("INSERT INTO users (name, email, password, system_role, is_active) 
                              VALUES (:name, :email, :password, :system_role, 1)");

            $this->db->bind(':name', $data['name']);
            $this->db->bind(':email', $data['email']);
            // Password cần được hash trước khi truyền vào đây hoặc hash tại đây
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            $this->db->bind(':password', $passwordHash);
            $this->db->bind(':system_role', $data['system_role'] ?? 'member');

            if (!$this->db->execute()) {
                throw new Exception("Không thể tạo tài khoản user.");
            }

            $userId = $this->db->lastInsertId();

            // 2. Insert vào bảng user_profiles (Thông tin cơ bản)
            $this->db->query("INSERT INTO user_profiles (user_id, phone, gender, dob, address, bio) 
                              VALUES (:user_id, :phone, :gender, :dob, :address, :bio)");

            $this->db->bind(':user_id', $userId);
            $this->db->bind(':phone', $data['phone'] ?? null);
            $this->db->bind(':gender', $data['gender'] ?? 'other');
            $this->db->bind(':dob', $data['dob'] ?? null);
            $this->db->bind(':address', $data['address'] ?? null);
            $this->db->bind(':bio', $data['bio'] ?? null);

            if (!$this->db->execute()) {
                throw new Exception("Không thể tạo hồ sơ user.");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("User Create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy thông tin chi tiết User (Join users + user_profiles)
     */
    public function getProfile($id)
    {
        $sql = "SELECT u.id, u.name, u.email, u.system_role, u.is_active, u.created_at,
                       p.phone, p.gender, p.dob, p.address, p.bio, p.avatar
                FROM users u
                LEFT JOIN user_profiles p ON u.id = p.user_id
                WHERE u.id = :id AND u.is_active = 1"; // Chỉ lấy user còn active

        $this->db->query($sql);
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    /**
     * Cập nhật thông tin User (Profile + Basic Info)
     */
    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Update bảng users (nếu có thay đổi name/email/role)
            // Chỉ update các trường có trong $data
            if (isset($data['name']) || isset($data['email']) || isset($data['system_role'])) {
                $sql = "UPDATE users SET ";
                $updates = [];
                if (isset($data['name'])) $updates[] = "name = :name";
                if (isset($data['email'])) $updates[] = "email = :email";
                if (isset($data['system_role'])) $updates[] = "system_role = :role";

                $sql .= implode(", ", $updates) . " WHERE id = :id";

                $this->db->query($sql);
                $this->db->bind(':id', $id);
                if (isset($data['name'])) $this->db->bind(':name', $data['name']);
                if (isset($data['email'])) $this->db->bind(':email', $data['email']);
                if (isset($data['system_role'])) $this->db->bind(':role', $data['system_role']);

                $this->db->execute();
            }

            // 2. Update bảng user_profiles
            // Kiểm tra xem profile đã tồn tại chưa (đề phòng data cũ thiếu)
            $this->db->query("SELECT user_id FROM user_profiles WHERE user_id = :id");
            $this->db->bind(':id', $id);

            if ($this->db->rowCount() == 0) {
                // Nếu chưa có profile -> Insert
                $this->db->query("INSERT INTO user_profiles (user_id, phone, gender, dob, address, bio) 
                                  VALUES (:id, :phone, :gender, :dob, :address, :bio)");
            } else {
                // Nếu có rồi -> Update
                $this->db->query("UPDATE user_profiles SET 
                                  phone = :phone, gender = :gender, dob = :dob, 
                                  address = :address, bio = :bio 
                                  WHERE user_id = :id");
            }

            $this->db->bind(':id', $id);
            $this->db->bind(':phone', $data['phone'] ?? null);
            $this->db->bind(':gender', $data['gender'] ?? 'other');
            $this->db->bind(':dob', !empty($data['dob']) ? $data['dob'] : null);
            $this->db->bind(':address', $data['address'] ?? null);
            $this->db->bind(':bio', $data['bio'] ?? null);

            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("User Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft Delete (Xóa mềm)
     * Set is_active = 0 thay vì xóa khỏi DB
     */
    public function delete($id)
    {
        $this->db->query("UPDATE users SET is_active = 0 WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Tìm User để đăng nhập
     * Chỉ tìm user đang Active
     */
    public function login($email, $password)
    {
        $this->db->query("SELECT * FROM users WHERE email = :email AND is_active = 1");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if ($row) {
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    /**
     * Thay đổi mật khẩu
     */
    public function changePassword($id, $newPassword)
    {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = :password WHERE id = :id");
        $this->db->bind(':password', $hashed);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Tìm theo Email (Dùng cho check unique, forgot password...)
     */
    public function findByEmail($email)
    {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Tìm theo Reset Token (Password Reset)
     */
    public function findByResetToken($token)
    {
        $this->db->query("SELECT * FROM users 
                          WHERE password_reset_token = :token 
                          AND password_reset_expires > NOW()
                          AND is_active = 1");
        $this->db->bind(':token', $token);
        return $this->db->single();
    }

    /**
     * Cập nhật Reset Token
     */
    public function updateResetToken($id, $token, $expires)
    {
        $this->db->query("UPDATE users SET password_reset_token = :token, password_reset_expires = :expires WHERE id = :id");
        $this->db->bind(':token', $token);
        $this->db->bind(':expires', $expires);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Reset Password (và xóa token)
     */
    public function resetPassword($id, $password)
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = :password, password_reset_token = NULL, password_reset_expires = NULL WHERE id = :id");
        $this->db->bind(':password', $hashed);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Lấy danh sách user (Có phân trang & Tìm kiếm)
     */
    public function getAllUsers($search = '', $role = '', $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $where = ["is_active = 1"]; // Chỉ lấy user active

        if (!empty($search)) {
            $where[] = "(name LIKE :search OR email LIKE :search)";
        }
        if (!empty($role)) {
            $where[] = "system_role = :role";
        }

        $whereSql = implode(" AND ", $where);

        // Count total
        $sqlCount = "SELECT COUNT(*) as total FROM users WHERE $whereSql";
        $this->db->query($sqlCount);
        if (!empty($search)) $this->db->bind(':search', "%$search%");
        if (!empty($role)) $this->db->bind(':role', $role);

        $totalRow = $this->db->single();
        $total = $totalRow['total'];

        // Get Data
        $sqlData = "SELECT id, name, email, system_role, created_at 
                    FROM users 
                    WHERE $whereSql 
                    ORDER BY created_at DESC 
                    LIMIT :limit OFFSET :offset";

        $this->db->query($sqlData);
        if (!empty($search)) $this->db->bind(':search', "%$search%");
        if (!empty($role)) $this->db->bind(':role', $role);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return [
            'users' => $this->db->resultSet(),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }
    public function getLeadersOnly()
    {
        $sql = "SELECT id, name, email, system_role 
                FROM users 
                WHERE system_role IN ('admin', 'subadmin') 
                ORDER BY name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
}
