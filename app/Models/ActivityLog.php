<?php
// app/Models/ActivityLog.php

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
     * Ghi một hành động mới vào CSDL
     * @param int|null $user_id ID của người thực hiện (có thể là NULL nếu là hệ thống)
     * @param string $action Mã hành động (vd: 'user_login', 'department_created')
     * @param string $details Chi tiết hành động
     * @return boolean
     */
    public function create($user_id, $action, $details)
    {
        $this->db->query("INSERT INTO activity_logs (user_id, action, details) 
                         VALUES (:user_id, :action, :details)");
        
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':action', $action);
        $this->db->bind(':details', $details);

        return $this->db->execute();
    }

    /**
     * Lấy tất cả log, JOIN với bảng user để lấy tên
     * @param int $limit Giới hạn số lượng log (vd: 100 cái mới nhất)
     * @return array
     */
    public function findAllWithUser($limit = 100)
    {
        // Dùng LEFT JOIN phòng khi user_id là NULL (ví dụ: hành động của hệ thống)
        $this->db->query("SELECT 
                            al.*, 
                            u.NAME as user_name 
                        FROM activity_logs al
                        LEFT JOIN users u ON al.user_id = u.id
                        ORDER BY al.created_at DESC
                        LIMIT :limit");
        
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}