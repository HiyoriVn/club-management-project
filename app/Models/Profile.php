<?php
// app/Models/Profile.php

namespace App\Models;

use App\Core\Database;

class Profile
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Tìm hồ sơ (profile) của 1 user bằng user_id
     * @param int $user_id
     * @return mixed Trả về mảng data nếu tìm thấy, false nếu không
     */
    public function findByUserId($user_id)
    {
        $this->db->query("SELECT * FROM user_profiles WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);

        $row = $this->db->single();
        return ($this->db->rowCount() > 0) ? $row : false;
    }

    /**
     * Tạo hoặc Cập nhật Hồ sơ
     * Dùng "INSERT ... ON DUPLICATE KEY UPDATE" để tự động
     * @param int $user_id
     * @param array $data
     * @return boolean
     */
    public function createOrUpdate($user_id, $data)
    {

        // Kỹ thuật này rất hay:
        // 1. Thử INSERT (vì user_id là PRIMARY KEY)
        // 2. Nếu "Duplicate Key" (đã có dòng user_id đó) -> thì nó tự chuyển sang UPDATE
        $this->db->query("INSERT INTO user_profiles (user_id, student_id, phone, gender, dob, address, bio) 
                         VALUES (:user_id, :student_id, :phone, :gender, :dob, :address, :bio)
                         ON DUPLICATE KEY UPDATE 
                            student_id = VALUES(student_id),
                            phone = VALUES(phone),
                            gender = VALUES(gender),
                            dob = VALUES(dob),
                            address = VALUES(address),
                            bio = VALUES(bio)");

        // Bind giá trị
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':student_id', $data['student_id']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':dob', empty($data['dob']) ? null : $data['dob']); // Cho phép NULL
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':bio', $data['bio']);

        return $this->db->execute();
    }
}
