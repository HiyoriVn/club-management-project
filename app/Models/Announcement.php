<?php

namespace App\Models;

use App\Core\Database;

class Announcement
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy thông báo (Admin xem hết, Member chỉ xem thông báo chung hoặc của ban mình)
     */
    public function getForUser($userId = null, $departmentIds = [])
    {
        $sql = "SELECT a.*, u.name as poster_name, d.name as department_name
                FROM announcements a
                LEFT JOIN users u ON a.posted_by = u.id
                LEFT JOIN departments d ON a.target_department_id = d.id
                WHERE 1=1";

        // Nếu có user_id, lọc theo quyền xem
        if ($userId) {
            // Xem: Thông báo chung (NULL) HOẶC Thông báo gửi tới ban mà user tham gia
            $deptList = implode(',', array_map('intval', $departmentIds));

            if (!empty($deptList)) {
                $sql .= " AND (a.target_department_id IS NULL OR a.target_department_id IN ($deptList))";
            } else {
                $sql .= " AND a.target_department_id IS NULL";
            }
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT 20";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function create($data)
    {
        $sql = "INSERT INTO announcements (title, content, target_department_id, posted_by) 
                VALUES (:title, :content, :target_dept, :posted_by)";

        $this->db->query($sql);

        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':target_dept', !empty($data['target_department_id']) ? $data['target_department_id'] : null);
        $this->db->bind(':posted_by', $data['posted_by']);

        return $this->db->execute();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE announcements SET 
                title = :title, 
                content = :content, 
                target_department_id = :target_dept 
                WHERE id = :id";

        $this->db->query($sql);

        $this->db->bind(':id', $id);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':target_dept', !empty($data['target_department_id']) ? $data['target_department_id'] : null);

        return $this->db->execute();
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM announcements WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function findById($id)
    {
        $this->db->query("SELECT * FROM announcements WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}
