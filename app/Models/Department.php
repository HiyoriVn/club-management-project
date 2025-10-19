<?php
// app/Models/Department.php

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
     * Lấy tất cả các Ban (Departments)
     */
    public function findAll()
    {
        // (Tạm thời ta lấy đơn giản, sau này sẽ join để lấy tên ban cha)
        $this->db->query("SELECT * FROM departments ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Tìm Ban bằng Tên (phân biệt hoa thường)
     * @param string $name
     * @return mixed Trả về mảng data nếu tìm thấy, false nếu không
     */
    public function findByName($name)
    {
        // Dùng BINARY để tìm kiếm chính xác (phân biệt hoa/thường)
        $this->db->query("SELECT * FROM departments WHERE BINARY NAME = :name");
        $this->db->bind(':name', $name);

        $row = $this->db->single();

        return ($this->db->rowCount() > 0) ? $row : false;
    }

    /**
     * Thêm một Ban mới vào CSDL
     * @param array $data (chứa name, description, parent_id)
     * @return boolean True nếu thành công, False nếu thất bại
     */
    public function create($data)
    {
        // Chuẩn bị câu lệnh SQL
        // Chú ý: Dùng đúng tên cột trong CSDL (NAME viết hoa)
        $this->db->query("INSERT INTO departments (NAME, description, parent_id) 
                         VALUES (:name, :description, :parent_id)");

        // Bind các giá trị
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);

        // Xử lý parent_id (nếu người dùng không chọn, nó phải là NULL)
        $this->db->bind(':parent_id', empty($data['parent_id']) ? null : $data['parent_id']);

        // Thực thi
        return $this->db->execute();
    }
    /**
     * Tìm một Ban bằng ID
     * @param int $id
     * @return mixed Trả về mảng data nếu tìm thấy, false nếu không
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM departments WHERE id = :id");
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return ($this->db->rowCount() > 0) ? $row : false;
    }

    /**
     * Cập nhật thông tin Ban
     * @param int $id ID của Ban cần sửa
     * @param array $data Dữ liệu mới (name, description, parent_id)
     * @return boolean True nếu thành công, False nếu thất bại
     */
    public function update($id, $data)
    {
        // Chú ý: Dùng đúng tên cột trong CSDL (NAME viết hoa)
        $this->db->query("UPDATE departments SET 
                            NAME = :name, 
                            description = :description, 
                            parent_id = :parent_id 
                         WHERE id = :id");

        // Bind các giá trị
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':parent_id', empty($data['parent_id']) ? null : $data['parent_id']);

        // Thực thi
        return $this->db->execute();
    }
    /**
     * Xóa một Ban khỏi CSDL
     * @param int $id ID của Ban cần xóa
     * @return boolean True nếu thành công, False nếu thất bại
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM departments WHERE id = :id");
        $this->db->bind(':id', $id);

        // Thực thi
        return $this->db->execute();
    }
}
