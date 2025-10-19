<?php
// app/Models/Event.php

namespace App\Models;

use App\Core\Database;

class Event
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy tất cả các Sự kiện, JOIN với user để lấy tên người tạo
     */
    public function findAll()
    {
        // Sắp xếp sự kiện mới nhất lên đầu
        $this->db->query("SELECT 
                            events.*, 
                            users.NAME as creator_name 
                        FROM events 
                        JOIN users ON events.created_by = users.id 
                        ORDER BY events.start_time DESC");

        return $this->db->resultSet();
    }

    /**
     * Thêm một Sự kiện mới vào CSDL
     * @param array $data (title, desc, start, end, location)
     * @return boolean True nếu thành công, False nếu thất bại
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO events (title, description, start_time, end_time, location, created_by) 
                         VALUES (:title, :description, :start_time, :end_time, :location, :created_by)");

        // Bind các giá trị
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', empty($data['end_time']) ? null : $data['end_time']); // Cho phép end_time là NULL
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':created_by', $_SESSION['user_id']); // Lấy ID của admin/subadmin đang tạo

        // Thực thi
        return $this->db->execute();
    }

    // (Các hàm findById, update, delete ta sẽ thêm ở bước sau)
    /**
     * Tìm một Sự kiện bằng ID
     * @param int $id
     * @return mixed Trả về mảng data nếu tìm thấy, false nếu không
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM events WHERE id = :id");
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return ($this->db->rowCount() > 0) ? $row : false;
    }

    /**
     * Cập nhật thông tin Sự kiện
     * @param int $id ID của Sự kiện cần sửa
     * @param array $data Dữ liệu mới (title, desc, start, end, location)
     * @return boolean True nếu thành công, False nếu thất bại
     */
    public function update($id, $data)
    {
        $this->db->query("UPDATE events SET 
                            title = :title, 
                            description = :description, 
                            start_time = :start_time, 
                            end_time = :end_time,
                            location = :location
                         WHERE id = :id");

        // Bind các giá trị
        $this->db->bind(':id', $id);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', empty($data['end_time']) ? null : $data['end_time']);
        $this->db->bind(':location', $data['location']);

        // Thực thi
        return $this->db->execute();
    }

    /**
     * Xóa một Sự kiện khỏi CSDL
     * @param int $id ID của Sự kiện cần xóa
     * @return boolean True nếu thành công, False nếu thất bại
     */
    public function delete($id)
    {
        // Nhờ CSDL (ON DELETE CASCADE), khi xóa event, 
        // mọi lượt đăng ký (participants) cũng sẽ bị xóa theo.
        $this->db->query("DELETE FROM events WHERE id = :id");
        $this->db->bind(':id', $id);

        // Thực thi
        return $this->db->execute();
    }
}
