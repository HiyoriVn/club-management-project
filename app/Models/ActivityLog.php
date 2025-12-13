<?php

namespace App\Models;

use App\Core\Database;

class ActivityLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ghi log
     */
    public function create($userId, $action, $details = null)
    {
        $sql = "INSERT INTO activity_logs (user_id, action, details) VALUES (:uid, :action, :details)";

        $this->db->query($sql);
        $this->db->bind(':uid', $userId); // Có thể NULL nếu là system action
        $this->db->bind(':action', $action);
        $this->db->bind(':details', $details);

        return $this->db->execute();
    }

    /**
     * Lấy log (Phân trang)
     */
    public function getLogs($limit = 50, $offset = 0)
    {
        $sql = "SELECT l.*, u.name as user_name 
                FROM activity_logs l
                LEFT JOIN users u ON l.user_id = u.id
                ORDER BY l.created_at DESC
                LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, \PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    /**
     * Helper function toàn cục (đã dùng ở AuthController)
     * Có thể giữ lại function log_activity() trong helpers file, gọi model này
     */
}