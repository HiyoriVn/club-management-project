<?php

namespace App\Models;

use App\Core\Database;

class Department
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy tất cả phòng ban
     */
    public function getAll()
    {
        $this->db->query("SELECT * FROM departments ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Lấy thông tin 1 phòng ban theo ID
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM departments WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Tạo phòng ban mới
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO departments (name, description) VALUES (:name, :description)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Cập nhật phòng ban
     */
    public function update($id, $data)
    {
        $this->db->query("UPDATE departments SET name = :name, description = :description WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Xóa phòng ban
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM departments WHERE id = :id");
        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Đếm số lượng thành viên trong mỗi ban (Dùng cho Dashboard/List)
     */
    public function getMemberCounts()
    {
        $sql = "SELECT d.id, d.name, COUNT(m.id) as member_count 
                FROM departments d 
                LEFT JOIN memberships m ON d.id = m.department_id 
                GROUP BY d.id, d.name";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
}
