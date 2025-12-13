<?php

namespace App\Models;

use App\Core\Database;

class Task
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy danh sách Task của một dự án
     * Có thể lọc theo status (cho cột Kanban)
     */
    public function getTasksByProject($projectId, $status = null)
    {
        $sql = "SELECT t.*, 
                       (SELECT COUNT(*) FROM task_assignees ta WHERE ta.task_id = t.id) as assignee_count
                FROM tasks t
                WHERE t.project_id = :pid";

        if ($status) {
            $sql .= " AND t.status = :status";
        }

        // Sắp xếp theo deadline hoặc ngày tạo
        $sql .= " ORDER BY t.due_date ASC, t.created_at DESC";

        $this->db->query($sql);
        $this->db->bind(':pid', $projectId);
        if ($status) {
            $this->db->bind(':status', $status);
        }

        return $this->db->resultSet();
    }

    /**
     * Lấy chi tiết 1 Task
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM tasks WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Tạo Task mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO tasks (project_id, title, description, status, start_date, due_date, color, attachment_link) 
                VALUES (:project_id, :title, :description, :status, :start_date, :due_date, :color, :attachment_link)";

        $this->db->query($sql);

        $this->db->bind(':project_id', $data['project_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'backlog');
        $this->db->bind(':start_date', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':due_date', !empty($data['due_date']) ? $data['due_date'] : null);
        $this->db->bind(':color', $data['color'] ?? '#007bff');
        $this->db->bind(':attachment_link', $data['attachment_link'] ?? null);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật Task
     */
    public function update($id, $data)
    {
        $sql = "UPDATE tasks SET 
                title = :title, 
                description = :description, 
                status = :status,
                start_date = :start_date, 
                due_date = :due_date, 
                color = :color,
                attachment_link = :attachment_link
                WHERE id = :id";

        $this->db->query($sql);

        $this->db->bind(':id', $id);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'backlog');
        $this->db->bind(':start_date', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':due_date', !empty($data['due_date']) ? $data['due_date'] : null);
        $this->db->bind(':color', $data['color'] ?? '#007bff');
        $this->db->bind(':attachment_link', $data['attachment_link'] ?? null);

        return $this->db->execute();
    }

    /**
     * Cập nhật riêng trạng thái (Dùng cho kéo thả Kanban)
     */
    public function updateStatus($id, $status)
    {
        $allowed = ['backlog', 'todo', 'in_progress', 'done'];
        if (!in_array($status, $allowed)) {
            return false;
        }

        $this->db->query("UPDATE tasks SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Xóa Task
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM tasks WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // --- ASSIGNEE MANAGEMENT (task_assignees) ---

    /**
     * Gán người dùng vào task
     */
    public function assign($taskId, $userId)
    {
        // Kiểm tra đã gán chưa
        if ($this->checkAssignee($taskId, $userId)) {
            return false;
        }

        $this->db->query("INSERT INTO task_assignees (task_id, user_id) VALUES (:tid, :uid)");
        $this->db->bind(':tid', $taskId);
        $this->db->bind(':uid', $userId);
        return $this->db->execute();
    }

    /**
     * Bỏ gán người dùng khỏi task
     */
    public function unassign($taskId, $userId)
    {
        $this->db->query("DELETE FROM task_assignees WHERE task_id = :tid AND user_id = :uid");
        $this->db->bind(':tid', $taskId);
        $this->db->bind(':uid', $userId);
        return $this->db->execute();
    }

    /**
     * Lấy danh sách người được gán cho task
     */
    public function getAssignees($taskId)
    {
        $sql = "SELECT u.id, u.name, u.email, ta.assigned_at
                FROM task_assignees ta
                JOIN users u ON ta.user_id = u.id
                WHERE ta.task_id = :tid";

        $this->db->query($sql);
        $this->db->bind(':tid', $taskId);
        return $this->db->resultSet();
    }

    /**
     * Kiểm tra user đã được gán vào task chưa
     */
    public function checkAssignee($taskId, $userId)
    {
        $this->db->query("SELECT task_id FROM task_assignees WHERE task_id = :tid AND user_id = :uid");
        $this->db->bind(':tid', $taskId);
        $this->db->bind(':uid', $userId);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    /**
     * Lấy các task được gán cho 1 user (My Tasks)
     */
    public function getTasksByUser($userId)
    {
        $sql = "SELECT t.*, p.name as project_name 
                FROM tasks t
                JOIN task_assignees ta ON t.id = ta.task_id
                JOIN projects p ON t.project_id = p.id
                WHERE ta.user_id = :uid
                ORDER BY t.due_date ASC";

        $this->db->query($sql);
        $this->db->bind(':uid', $userId);
        return $this->db->resultSet();
    }
}
