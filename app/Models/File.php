<?php

namespace App\Models;

use App\Core\Database;

class File
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [])
    {
        // Sửa query: Lấy f.type
        $sql = "SELECT f.*, u.name as uploader_name, d.name as department_name, p.name as project_name 
                FROM files f
                LEFT JOIN users u ON f.uploaded_by = u.id
                LEFT JOIN departments d ON f.department_id = d.id
                LEFT JOIN projects p ON f.project_id = p.id
                WHERE 1=1";

        if (!empty($filters['department_id'])) {
            $sql .= " AND f.department_id = :dept_id";
        }

        $sql .= " ORDER BY f.uploaded_at DESC";

        $this->db->query($sql);

        if (!empty($filters['department_id'])) {
            $this->db->bind(':dept_id', $filters['department_id']);
        }

        return $this->db->resultSet();
    }

    public function create($data)
    {
        // SỬA: Đổi 'file_type' -> 'type' và XÓA 'file_size'
        $sql = "INSERT INTO files (file_name, file_path, type, uploaded_by, department_id, project_id) 
                VALUES (:name, :path, :type, :uploaded_by, :dept_id, :project_id)";

        $this->db->query($sql);

        $this->db->bind(':name', $data['file_name']);
        $this->db->bind(':path', $data['file_path']);
        $this->db->bind(':type', $data['type']); // Khớp với tên cột trong DB
        $this->db->bind(':uploaded_by', $data['uploaded_by']);
        $this->db->bind(':dept_id', !empty($data['department_id']) ? $data['department_id'] : null);
        $this->db->bind(':project_id', !empty($data['project_id']) ? $data['project_id'] : null);

        return $this->db->execute();
    }

    public function findById($id)
    {
        $this->db->query("SELECT * FROM files WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM files WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
