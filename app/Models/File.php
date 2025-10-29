<?php
// app/Models/File.php

namespace App\Models;

use App\Core\Database;

class File
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy tất cả Files, JOIN với user (uploader) và department
     */
    public function findAll()
    {
        $this->db->query("SELECT 
                            f.*, 
                            u.NAME as uploader_name, 
                            d.NAME as department_name 
                        FROM files f
                        JOIN users u ON f.uploaded_by = u.id
                        LEFT JOIN departments d ON f.department_id = d.id
                        ORDER BY f.uploaded_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Tìm file bằng ID
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM files WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Lưu thông tin file vào CSDL
     * @param array $data (file_name, file_path, department_id)
     * @return boolean
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO files (file_name, file_path, uploaded_by, department_id) 
                         VALUES (:file_name, :file_path, :uploaded_by, :department_id)");

        $this->db->bind(':file_name', $data['file_name']);
        $this->db->bind(':file_path', $data['file_path']); // Đường dẫn lưu trên server
        $this->db->bind(':uploaded_by', $_SESSION['user_id']);
        $this->db->bind(':department_id', empty($data['department_id']) ? null : $data['department_id']);

        return $this->db->execute();
    }

    /**
     * Xóa file khỏi CSDL (Việc xóa file vật lý sẽ làm ở Controller)
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM files WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
