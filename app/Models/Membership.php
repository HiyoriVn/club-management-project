<?php

namespace App\Models;

use App\Core\Database;

class Membership
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Thêm thành viên vào ban
     * @param int $userId
     * @param int $departmentId
     * @param string $role ('Head', 'Deputy', 'Member')
     */
    public function addMember($userId, $departmentId, $role = 'Member')
    {
        // Kiểm tra xem đã tồn tại chưa (tránh lỗi duplicate key)
        if ($this->checkMembership($userId, $departmentId)) {
            return false;
        }

        $this->db->query("INSERT INTO memberships (user_id, department_id, department_role) 
                          VALUES (:user_id, :department_id, :role)");

        $this->db->bind(':user_id', $userId);
        $this->db->bind(':department_id', $departmentId);
        $this->db->bind(':role', $role);

        return $this->db->execute();
    }

    /**
     * Cập nhật vai trò thành viên trong ban
     */
    public function updateRole($membershipId, $newRole)
    {
        $allowedRoles = ['Head', 'Deputy', 'Member'];
        if (!in_array($newRole, $allowedRoles)) {
            return false;
        }

        $this->db->query("UPDATE memberships SET department_role = :role WHERE id = :id");
        $this->db->bind(':role', $newRole);
        $this->db->bind(':id', $membershipId);

        return $this->db->execute();
    }

    /**
     * Xóa thành viên khỏi ban
     */
    public function removeMember($membershipId)
    {
        $this->db->query("DELETE FROM memberships WHERE id = :id");
        $this->db->bind(':id', $membershipId);
        return $this->db->execute();
    }

    /**
     * Lấy danh sách thành viên của một ban
     * (Kèm thông tin user)
     */
    public function getMembersByDepartment($departmentId)
    {
        $sql = "SELECT m.id as membership_id, m.department_role, m.user_id,
                       u.name as user_name, u.email, u.system_role
                FROM memberships m
                JOIN users u ON m.user_id = u.id
                WHERE m.department_id = :dept_id
                ORDER BY FIELD(m.department_role, 'Head', 'Deputy', 'Member'), u.name ASC";

        $this->db->query($sql);
        $this->db->bind(':dept_id', $departmentId);

        return $this->db->resultSet();
    }

    /**
     * Lấy các ban mà user tham gia
     */
    public function getDepartmentsByUser($userId)
    {
        $sql = "SELECT m.id as membership_id, m.department_role, 
                       d.id as department_id, d.name as department_name
                FROM memberships m
                JOIN departments d ON m.department_id = d.id
                WHERE m.user_id = :user_id";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);

        return $this->db->resultSet();
    }

    /**
     * Kiểm tra user có trong ban chưa
     */
    public function checkMembership($userId, $departmentId)
    {
        $this->db->query("SELECT id FROM memberships WHERE user_id = :uid AND department_id = :did");
        $this->db->bind(':uid', $userId);
        $this->db->bind(':did', $departmentId);

        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    /**
     * Tìm membership theo ID
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM memberships WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}
