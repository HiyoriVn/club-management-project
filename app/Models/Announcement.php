<?php
// app/Models/Announcement.php

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
     * Lấy thông báo (Feed) dựa trên vai trò và Ban của user
     * @param string $user_role (guest, member, subadmin, admin)
     * @param array $department_ids Mảng ID các Ban của user
     */
    public function getFeed($user_role, $department_ids)
    {

        // Admin/Subadmin thấy tất cả
        if ($user_role == 'admin' || $user_role == 'subadmin') {
            $sql = "SELECT a.*, u.NAME as author_name, d.NAME as department_name
                FROM announcements a
                JOIN users u ON a.posted_by = u.id
                LEFT JOIN departments d ON a.target_department_id = d.id
                ORDER BY a.created_at DESC";

            $this->db->query($sql);
            return $this->db->resultSet();
        }

        // Logic cho Member
        if ($user_role == 'member') {
            // Member thấy: Public + Internal (CLB) + Ban của mình

            $placeholders = !empty($department_ids) ? implode(',', $department_ids) : 'NULL';

            $sql = "SELECT a.*, u.NAME as author_name, d.NAME as department_name
                FROM announcements a
                JOIN users u ON a.posted_by = u.id
                LEFT JOIN departments d ON a.target_department_id = d.id
                WHERE 
                    -- 1. Thông báo Public (Chung)
                    (a.visibility = 'public' AND a.target_department_id IS NULL)
                    OR 
                    -- 2. Thông báo Internal (Nội bộ CLB)
                    (a.visibility = 'internal' AND a.target_department_id IS NULL)
                    OR
                    -- 3. Thông báo Ban (của mình)
                    (a.target_department_id IN ($placeholders))
                ORDER BY a.created_at DESC";

            $this->db->query($sql);
            return $this->db->resultSet();
        }

        // Logic cho Guest (và các vai trò khác nếu có)
        // Guest CHỈ thấy Public
        $sql = "SELECT a.*, u.NAME as author_name, d.NAME as department_name
            FROM announcements a
            JOIN users u ON a.posted_by = u.id
            LEFT JOIN departments d ON a.target_department_id = d.id
            WHERE 
                (a.visibility = 'public' AND a.target_department_id IS NULL)
            ORDER BY a.created_at DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Lấy TẤT CẢ thông báo (dành cho Admin)
     */
    public function findAll()
    {
        $this->db->query("SELECT 
                            a.*, 
                            u.NAME as author_name, 
                            d.NAME as department_name
                        FROM announcements a
                        JOIN users u ON a.posted_by = u.id
                        LEFT JOIN departments d ON a.target_department_id = d.id
                        ORDER BY a.created_at DESC");

        return $this->db->resultSet();
    }

    /**
     * Tìm 1 thông báo bằng ID
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM announcements WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Tạo thông báo mới
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO announcements (title, content, posted_by, target_department_id, visibility) 
                         VALUES (:title, :content, :posted_by, :target_department_id, :visibility)");

        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':posted_by', $_SESSION['user_id']);
        $this->db->bind(':target_department_id', $data['target_department_id']);

        $this->db->bind(':visibility', $data['visibility']); // 'public' hoặc 'internal'

        return $this->db->execute();
    }

    /**
     * Cập nhật thông báo
     */
    public function update($id, $data)
    {
        // THÊM "visibility = :visibility" VÀO CÂU LỆNH
        $this->db->query("UPDATE announcements SET 
                            title = :title, 
                            content = :content, 
                            target_department_id = :target_department_id,
                            visibility = :visibility 
                         WHERE id = :id");

        $this->db->bind(':id', $id);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':target_department_id', $data['target_department_id']);
        $this->db->bind(':visibility', $data['visibility']);

        return $this->db->execute();
    }

    /**
     * Xóa thông báo
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM announcements WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
