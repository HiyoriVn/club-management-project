<?php
// app/Models/DepartmentRole.php

namespace App\Models;

use App\Core\Database;

class DepartmentRole
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy tất cả các Vai trò
     */
    public function findAll()
    {
        // Chú ý: Dùng đúng tên cột (NAME viết hoa)
        $this->db->query("SELECT id, NAME, created_at FROM department_roles ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Tìm Vai trò bằng Tên (phân biệt hoa thường)
     */
    public function findByName($name)
    {
        $this->db->query("SELECT * FROM department_roles WHERE BINARY NAME = :name");
        $this->db->bind(':name', $name);
        $row = $this->db->single();
        return ($this->db->rowCount() > 0) ? $row : false;
    }

    /**
     * Tìm Vai trò bằng ID
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM department_roles WHERE id = :id");
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return ($this->db->rowCount() > 0) ? $row : false;
    }

    /**
     * Thêm một Vai trò mới
     * (Tạm thời bỏ qua cột `permissions` JSON)
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO department_roles (NAME) VALUES (:name)");
        $this->db->bind(':name', $data['name']);
        return $this->db->execute();
    }

    /**
     * Cập nhật Vai trò
     */
    public function update($id, $data)
    {
        $this->db->query("UPDATE department_roles SET NAME = :name WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        return $this->db->execute();
    }

    /**
     * Xóa Vai trò
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM department_roles WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
