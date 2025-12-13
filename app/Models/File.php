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

    /**
     * Lấy danh sách file (có thể lọc theo Project hoặc Ban)
     */
    public function getAll($projectId = null, $departmentId = null)
    {
        $sql = "SELECT f.*, u.name as uploader_name 
                FROM files f
                LEFT JOIN users u ON f.uploaded_by = u.id
                WHERE 1=1";

        if ($projectId) {
            $sql .= " AND f.project_id = :pid";
        }
        if ($departmentId) {
            $sql .= " AND f.department_id = :did";
        }

        $sql .= " ORDER BY f.uploaded_at DESC";

        $this->db->query($sql);
        if ($projectId) $this->db->bind(':pid', $projectId);
        if ($departmentId) $this->db->bind(':did', $departmentId);

        return $this->db->resultSet();
    }

    public function create($data)
    {
        $sql = "INSERT INTO files (file_name, file_path, type, uploaded_by, project_id, department_id) 
                VALUES (:name, :path, :type, :uid, :pid, :did)";

        $this->db->query($sql);

        $this->db->bind(':name', $data['file_name']);
        $this->db->bind(':path', $data['file_path']);
        $this->db->bind(':type', $data['type'] ?? null);
        $this->db->bind(':uid', $data['uploaded_by']);
        $this->db->bind(':pid', !empty($data['project_id']) ? $data['project_id'] : null);
        $this->db->bind(':did', !empty($data['department_id']) ? $data['department_id'] : null);

        return $this->db->execute();
    }

    public function delete($id)
    {
        // Lấy thông tin file để xóa file vật lý ở Controller
        $file = $this->findById($id);
        if ($file) {
            $this->db->query("DELETE FROM files WHERE id = :id");
            $this->db->bind(':id', $id);
            if ($this->db->execute()) {
                return $file; // Trả về thông tin file đã xóa
            }
        }
        return false;
    }

    public function findById($id)
    {
        $this->db->query("SELECT * FROM files WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}
