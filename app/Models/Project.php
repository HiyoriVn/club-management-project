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
     * Lấy danh sách dự án (cho trang Index)
     * - Kèm tên Leader
     * - Kèm số lượng thành viên (đếm từ project_members)
     */
    public function getAll()
    {
        $sql = "SELECT p.*, 
                       u.name as leader_name,
                       (SELECT COUNT(*) FROM project_members pm WHERE pm.project_id = p.id) as member_count
                FROM projects p
                LEFT JOIN users u ON p.leader_id = u.id
                ORDER BY p.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getProjectIdsByUser($userId)
    {
        // Dùng :uid1 và :uid2 thay vì dùng :uid hai lần
        $sql = "SELECT DISTINCT p.id 
                FROM projects p 
                LEFT JOIN project_members pm ON p.id = pm.project_id 
                WHERE pm.user_id = :uid1 OR p.leader_id = :uid2";

        $this->db->query($sql);
        $this->db->bind(':uid1', $userId);
        $this->db->bind(':uid2', $userId);

        $result = $this->db->resultSet();
        return array_column($result, 'id');
    }

    /**
     * Lấy chi tiết 1 dự án
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

    public function create($data)
    {
        $sql = "INSERT INTO projects (name, description, type, start_date, end_date, status, leader_id, department_id) 
                VALUES (:name, :description, :type, :start_date, :end_date, :status, :leader_id, :dept_id)";

        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':start_date', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':end_date', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':status', $data['status'] ?? 'planning');
        $this->db->bind(':leader_id', !empty($data['leader_id']) ? $data['leader_id'] : null);
        $this->db->bind(':dept_id', !empty($data['department_id']) ? $data['department_id'] : null);

        if ($this->db->execute()) {
            $projectId = $this->db->lastInsertId();

            // YÊU CẦU: Leader mặc định là thành viên dự án
            if (!empty($data['leader_id'])) {
                // Gọi hàm addMember nội bộ
                $this->addMember($projectId, $data['leader_id'], 'Leader');
            }
            return $projectId;
        }
        return false;
    }

    public function getTasks($projectId)
    {
        $sql = "SELECT t.*, GROUP_CONCAT(u.name SEPARATOR ', ') as assignee_name 
                FROM tasks t
                LEFT JOIN task_assignees ta ON t.id = ta.task_id
                LEFT JOIN users u ON ta.user_id = u.id
                WHERE t.project_id = :pid
                GROUP BY t.id
                ORDER BY t.created_at DESC";

        $this->db->query($sql);
        $this->db->bind(':pid', $projectId);
        return $this->db->resultSet();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE projects SET 
                name = :name, description = :description, status = :status, 
                start_date = :start_date, end_date = :end_date, 
                leader_id = :leader_id, department_id = :dept_id 
                WHERE id = :id";

        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':start_date', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':end_date', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':leader_id', !empty($data['leader_id']) ? $data['leader_id'] : null);
        $this->db->bind(':dept_id', !empty($data['department_id']) ? $data['department_id'] : null);

        return $this->db->execute();
    }

    public function delete($id)
    {
        // 1. Xóa thành viên dự án
        $this->db->query("DELETE FROM project_members WHERE project_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        // 2. Xóa các task (nếu không set cascade trong DB)
        $this->db->query("DELETE FROM tasks WHERE project_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        // 3. Xóa dự án
        $this->db->query("DELETE FROM projects WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    /**
     * Kiểm tra User có quyền truy cập dự án không
     * (Là Leader HOẶC có tên trong danh sách thành viên)
     */
    public function isMember($projectId, $userId)
    {
        $sql = "SELECT 1 FROM projects p 
                LEFT JOIN project_members pm ON p.id = pm.project_id 
                WHERE p.id = :pid AND (pm.user_id = :uid1 OR p.leader_id = :uid2)";

        $this->db->query($sql);
        $this->db->bind(':pid', $projectId);
        $this->db->bind(':uid1', $userId);
        $this->db->bind(':uid2', $userId);

        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    /**
     * Lấy danh sách thành viên của dự án (để hiển thị)
     */
    public function getProjectMembers($projectId)
    {
        $sql = "SELECT pm.id as membership_id, pm.role as project_role, pm.joined_at,
                       u.id as user_id, u.name, u.email 
                FROM project_members pm
                JOIN users u ON pm.user_id = u.id
                WHERE pm.project_id = :pid
                ORDER BY pm.role ASC, u.name ASC";

        $this->db->query($sql);
        $this->db->bind(':pid', $projectId);
        return $this->db->resultSet();
    }

    public function addMember($projectId, $userId, $role = 'Member')
    {
        // Kiểm tra đã tồn tại chưa
        $this->db->query("SELECT id FROM project_members WHERE project_id = :pid AND user_id = :uid");
        $this->db->bind(':pid', $projectId);
        $this->db->bind(':uid', $userId);
        $this->db->single();

        if ($this->db->rowCount() > 0) return false;

        $sql = "INSERT INTO project_members (project_id, user_id, role) VALUES (:pid, :uid, :role)";
        $this->db->query($sql);
        $this->db->bind(':pid', $projectId);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':role', $role);
        return $this->db->execute();
    }

    public function removeMember($projectId, $userId)
    {
        $sql = "DELETE FROM project_members WHERE project_id = :pid AND user_id = :uid";
        $this->db->query($sql);
        $this->db->bind(':pid', $projectId);
        $this->db->bind(':uid', $userId);
        return $this->db->execute();
    }
}
