<?php
// app/Models/Project.php

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
     * Lấy tất cả Dự án
     * JOIN với users (Leader) và departments (Ban)
     */
    public function findAll()
    {
        // Dùng LEFT JOIN phòng khi leader_id hoặc department_id là NULL
        // Dùng tên cột NAME (viết hoa) cho 2 bảng kia
        $this->db->query("SELECT 
                            p.*, 
                            u.NAME as leader_name, 
                            d.NAME as department_name 
                        FROM projects p
                        LEFT JOIN users u ON p.leader_id = u.id
                        LEFT JOIN departments d ON p.department_id = d.id
                        ORDER BY p.created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Tìm 1 Dự án bằng ID
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM projects WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Tạo Dự án mới
     * @param array $data
     * @return boolean
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO projects (NAME, description, start_date, end_date, leader_id, department_id, STATUS) 
                         VALUES (:name, :description, :start_date, :end_date, :leader_id, :department_id, :status)");

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_date', empty($data['start_date']) ? null : $data['start_date']);
        $this->db->bind(':end_date', empty($data['end_date']) ? null : $data['end_date']);
        $this->db->bind(':leader_id', empty($data['leader_id']) ? null : $data['leader_id']);
        $this->db->bind(':department_id', empty($data['department_id']) ? null : $data['department_id']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    /**
     * Cập nhật Dự án
     */
    public function update($id, $data)
    {
        $this->db->query("UPDATE projects SET 
                            NAME = :name, 
                            description = :description, 
                            start_date = :start_date, 
                            end_date = :end_date, 
                            leader_id = :leader_id, 
                            department_id = :department_id, 
                            STATUS = :status
                         WHERE id = :id");

        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_date', empty($data['start_date']) ? null : $data['start_date']);
        $this->db->bind(':end_date', empty($data['end_date']) ? null : $data['end_date']);
        $this->db->bind(':leader_id', empty($data['leader_id']) ? null : $data['leader_id']);
        $this->db->bind(':department_id', empty($data['department_id']) ? null : $data['department_id']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    /**
     * Xóa Dự án
     */
    public function delete($id)
    {
        // Nhờ CSDL (ON DELETE CASCADE), khi xóa project, 
        // mọi project_members và tasks cũng bị xóa theo.
        $this->db->query("DELETE FROM projects WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Lấy danh sách thành viên của 1 dự án
     * @param int $project_id
     * @return array
     */
    public function getMembers($project_id)
    {
        // JOIN với bảng users để lấy Tên
        $this->db->query("SELECT 
                            pm.id as assignment_id, 
                            pm.role as project_role,
                            u.id as user_id, 
                            u.NAME
                        FROM project_members pm
                        JOIN users u ON pm.user_id = u.id
                        WHERE pm.project_id = :project_id
                        ORDER BY u.NAME ASC");

        $this->db->bind(':project_id', $project_id);
        return $this->db->resultSet();
    }

    /**
     * Kiểm tra 1 user đã là thành viên dự án chưa
     * @param int $project_id
     * @param int $user_id
     * @return boolean
     */
    public function isMember($project_id, $user_id)
    {
        $this->db->query("SELECT id FROM project_members WHERE project_id = :project_id AND user_id = :user_id");
        $this->db->bind(':project_id', $project_id);
        $this->db->bind(':user_id', $user_id);

        $this->db->single();
        return ($this->db->rowCount() > 0);
    }

    /**
     * Thêm thành viên vào dự án
     * @param int $project_id
     * @param int $user_id
     * @param string $role (vd: 'member', 'leader')
     * @return boolean
     */
    public function addMember($project_id, $user_id, $role)
    {
        // Kiểm tra trùng lặp trước khi INSERT
        if ($this->isMember($project_id, $user_id)) {
            return false; // Đã là thành viên
        }

        $this->db->query("INSERT INTO project_members (project_id, user_id, role) 
                         VALUES (:project_id, :user_id, :role)");

        $this->db->bind(':project_id', $project_id);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':role', $role);

        return $this->db->execute();
    }

    /**
     * Xóa thành viên khỏi dự án
     * @param int $assignment_id ID của dòng trong bảng project_members
     * @return boolean
     */
    public function removeMember($assignment_id)
    {
        $this->db->query("DELETE FROM project_members WHERE id = :assignment_id");
        $this->db->bind(':assignment_id', $assignment_id);
        return $this->db->execute();
    }

    /**
     * Lấy tất cả Tasks của 1 Dự án
     * @param int $project_id
     * @return array
     */
    public function getTasks($project_id)
    {
        // JOIN với user (người được gán)
        $this->db->query("SELECT 
                            t.*, 
                            u.NAME as assigned_user_name
                        FROM tasks t
                        LEFT JOIN users u ON t.assigned_to = u.id
                        WHERE t.project_id = :project_id
                        ORDER BY t.created_at ASC");

        $this->db->bind(':project_id', $project_id);
        return $this->db->resultSet();
    }

    /**
     * Tạo một Task mới
     * @param array $data
     * @return boolean
     */
    public function createTask($data)
    {
        $this->db->query("INSERT INTO tasks (project_id, title, description, assigned_to, due_date, status) 
                         VALUES (:project_id, :title, :description, :assigned_to, :due_date, 'todo')");

        $this->db->bind(':project_id', $data['project_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':assigned_to', empty($data['assigned_to']) ? null : $data['assigned_to']);
        $this->db->bind(':due_date', empty($data['due_date']) ? null : $data['due_date']);

        return $this->db->execute();
    }

    /**
     * Cập nhật Trạng thái (Status) của Task
     * @param int $task_id
     * @param string $new_status ('todo', 'in_progress', 'done')
     * @return boolean
     */
    public function updateTaskStatus($task_id, $new_status)
    {
        // Kiểm tra status hợp lệ
        if (!in_array($new_status, ['todo', 'in_progress', 'done'])) {
            return false;
        }

        $this->db->query("UPDATE tasks SET status = :status WHERE id = :id");
        $this->db->bind(':status', $new_status);
        $this->db->bind(':id', $task_id);

        return $this->db->execute();
    }

    /**
     * Xóa một Task
     * @param int $task_id
     * @return boolean
     */
    public function deleteTask($task_id)
    {
        $this->db->query("DELETE FROM tasks WHERE id = :id");
        $this->db->bind(':id', $task_id);
        return $this->db->execute();
    }
}
