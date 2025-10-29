<?php
// app/Models/Notification.php

namespace App\Models;

use App\Core\Database;

class Notification
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy các thông báo CHƯA ĐỌC của 1 user
     * @param int $user_id
     * @param int $limit Giới hạn số lượng (vd: 5 cái mới nhất)
     * @return array
     */
    public function getUnreadForUser($user_id, $limit = 5)
    {
        $this->db->query("SELECT * FROM notifications 
                         WHERE user_id = :user_id AND is_read = 0 
                         ORDER BY created_at DESC 
                         LIMIT :limit");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT); // Quan trọng: bind kiểu INT
        return $this->db->resultSet();
    }

    /**
     * Đếm số thông báo CHƯA ĐỌC của 1 user
     * @param int $user_id
     * @return int
     */
    public function countUnreadForUser($user_id)
    {
        $this->db->query("SELECT COUNT(*) as count FROM notifications 
                         WHERE user_id = :user_id AND is_read = 0");
        $this->db->bind(':user_id', $user_id);
        $row = $this->db->single();
        return $row ? (int)$row['count'] : 0;
    }

    /**
     * Tạo một thông báo mới
     * @param int $user_id Người nhận
     * @param string $title Tiêu đề
     * @param string $message Nội dung
     * @return boolean
     */
    public function create($user_id, $title, $message)
    {
        $this->db->query("INSERT INTO notifications (user_id, title, message) 
                         VALUES (:user_id, :title, :message)");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':title', $title);
        $this->db->bind(':message', $message);
        return $this->db->execute();
    }

    /**
     * Đánh dấu một thông báo là ĐÃ ĐỌC
     * @param int $notification_id
     * @param int $user_id (Để đảm bảo đúng người)
     * @return boolean
     */
    public function markAsRead($notification_id, $user_id)
    {
        $this->db->query("UPDATE notifications SET is_read = 1 
                         WHERE id = :notification_id AND user_id = :user_id");
        $this->db->bind(':notification_id', $notification_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    /**
     * Đánh dấu TẤT CẢ thông báo là ĐÃ ĐỌC
     * @param int $user_id
     * @return boolean
     */
    public function markAllAsRead($user_id)
    {
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }
}
