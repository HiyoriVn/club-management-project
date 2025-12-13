<?php

namespace App\Models;

use App\Core\Database;

class Project
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy danh sách Dự án (type='project')
     * Có thể lọc theo status
     */
    public function getAllProjects($limit = 10, $status = null)
    {
        return $this->getByType('project', $limit, $status);
    }

    /**
     * Lấy danh sách Sự kiện (type='event')
     */
    public function getAllEvents($limit = 10, $status = null)
    {
        return $this->getByType('event', $limit, $status);
    }

    /**
     * Hàm nội bộ lấy theo type
     */
    private function getByType($type, $limit, $status)
    {
        $sql = "SELECT p.*, u.name as leader_name, d.name as department_name 
                FROM projects p
                LEFT JOIN users u ON p.leader_id = u.id
                LEFT JOIN departments d ON p.department_id = d.id
                WHERE p.type = :type";

        if ($status) {
            $sql .= " AND p.status = :status";
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit";

        $this->db->query($sql);
        $this->db->bind(':type', $type);
        if ($status) {
            $this->db->bind(':status', $status);
        }
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    /**
     * Lấy chi tiết 1 Project/Event
     */
    public function findById($id)
    {
        $sql = "SELECT p.*, u.name as leader_name, d.name as department_name 
                FROM projects p
                LEFT JOIN users u ON p.leader_id = u.id
                LEFT JOIN departments d ON p.department_id = d.id
                WHERE p.id = :id";

        $this->db->query($sql);
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    /**
     * Tạo mới Project/Event
     */
    public function create($data)
    {
        $sql = "INSERT INTO projects (name, description, type, start_date, end_date, status, leader_id, department_id) 
                VALUES (:name, :description, :type, :start_date, :end_date, :status, :leader_id, :department_id)";

        $this->db->query($sql);

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':type', $data['type'] ?? 'project');
        $this->db->bind(':start_date', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':end_date', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':status', $data['status'] ?? 'planning');
        $this->db->bind(':leader_id', !empty($data['leader_id']) ? $data['leader_id'] : null);
        $this->db->bind(':department_id', !empty($data['department_id']) ? $data['department_id'] : null);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật
     */
    public function update($id, $data)
    {
        $sql = "UPDATE projects SET 
                name = :name, 
                description = :description, 
                start_date = :start_date, 
                end_date = :end_date, 
                status = :status, 
                leader_id = :leader_id, 
                department_id = :department_id
                WHERE id = :id";

        $this->db->query($sql);

        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':start_date', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':end_date', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':status', $data['status'] ?? 'planning');
        $this->db->bind(':leader_id', !empty($data['leader_id']) ? $data['leader_id'] : null);
        $this->db->bind(':department_id', !empty($data['department_id']) ? $data['department_id'] : null);

        return $this->db->execute();
    }

    /**
     * Xóa dự án (Cần cẩn thận, có thể thêm ràng buộc xóa cascade ở DB rồi)
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM projects WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // --- MEMBER MANAGEMENT (project_members) ---

    /**
     * Thêm thành viên vào dự án
     */
    public function addMember($projectId, $userId, $role = 'Member')
    {
        // Kiểm tra tồn tại
        if ($this->checkMember($projectId, $userId)) {
            return false;
        }

        $this->db->query("INSERT INTO project_members (project_id, user_id, role) VALUES (:pid, :uid, :role)");
        $this->db->bind(':pid', $projectId);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':role', $role);

        return $this->db->execute();
    }

    /**
     * Xóa thành viên khỏi dự án
     */
    public function removeMember($projectId, $userId)
    {
        $this->db->query("DELETE FROM project_members WHERE project_id = :pid AND user_id = :uid");
        $this->db->bind(':pid', $projectId);
        $this->db->bind(':uid', $userId);
        return $this->db->execute();
    }

    /**
     * Lấy danh sách thành viên của dự án
     */
    public function getMembers($projectId)
    {
        $sql = "SELECT pm.*, u.name, u.email 
                FROM project_members pm
                JOIN users u ON pm.user_id = u.id
                WHERE pm.project_id = :pid
                ORDER BY pm.role ASC, u.name ASC";

        $this->db->query($sql);
        $this->db->bind(':pid', $projectId);
        return $this->db->resultSet();
    }

    /**
     * Kiểm tra user có trong dự án chưa
     */
    public function checkMember($projectId, $userId)
    {
        $this->db->query("SELECT id FROM project_members WHERE project_id = :pid AND user_id = :uid");
        $this->db->bind(':pid', $projectId);
        $this->db->bind(':uid', $userId);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    /**
     * Lấy các dự án mà user tham gia (cho trang My Tasks/My Projects)
     */
    public function getProjectsByUser($userId)
    {
        $sql = "SELECT p.*, pm.role as my_role
                FROM projects p
                JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = :uid
                ORDER BY p.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':uid', $userId);
        return $this->db->resultSet();
    }
}
