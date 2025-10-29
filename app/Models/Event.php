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
     * Lấy Feed Sự kiện dựa trên vai trò
     */
    public function getFeed($user_role)
    {

        $sql = "";

        // Admin/Subadmin/Member thấy Public + Internal
        if ($user_role == 'admin' || $user_role == 'subadmin' || $user_role == 'member') {
            $sql = "SELECT events.*, users.NAME as creator_name 
                FROM events 
                JOIN users ON events.created_by = users.id 
                WHERE events.visibility = 'public' OR events.visibility = 'internal'
                ORDER BY events.start_time DESC";
        }
        // Guest chỉ thấy Public
        else {
            $sql = "SELECT events.*, users.NAME as creator_name 
                FROM events 
                JOIN users ON events.created_by = users.id 
                WHERE events.visibility = 'public'
                ORDER BY events.start_time DESC";
        }

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Thêm một Sự kiện mới vào CSDL
     * @param array $data (title, desc, start, end, location)
     * @return boolean True nếu thành công, False nếu thất bại
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO events (title, description, start_time, end_time, location, created_by, visibility) 
                     VALUES (:title, :description, :start_time, :end_time, :location, :created_by, :visibility)");

        // Bind các giá trị
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', empty($data['end_time']) ? null : $data['end_time']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':created_by', $_SESSION['user_id']);
        $this->db->bind(':visibility', $data['visibility']);

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
                        location = :location,
                        visibility = :visibility 
                     WHERE id = :id");

        // Bind các giá trị
        $this->db->bind(':id', $id);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', empty($data['end_time']) ? null : $data['end_time']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':visibility', $data['visibility']);

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

    /**
     * Lấy tất cả event_id mà user hiện tại đã đăng ký
     * @param int $user_id ID của user (từ Session)
     * @return array Mảng chứa các event_id, vd: [1, 3, 5]
     */
    public function findMyRegistrations($user_id)
    {
        $this->db->query("SELECT event_id FROM event_participants WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);

        // 1. Dùng hàm resultSet() (public) để lấy mảng
        // Kết quả trả về sẽ là: [ ['event_id' => 1], ['event_id' => 3] ]
        $resultSet = $this->db->resultSet();

        // 2. Dùng hàm array_column() (của PHP) để trích xuất cột 'event_id'
        // Kết quả trả về sẽ là: [ 1, 3 ]
        $results = array_column($resultSet, 'event_id');

        return $results ? $results : [];
    }

    /**
     * Kiểm tra 1 user đã đăng ký 1 event cụ thể chưa
     * @param int $event_id
     * @param int $user_id
     * @return boolean True nếu đã đăng ký, False nếu chưa
     */
    public function isUserRegistered($event_id, $user_id)
    {
        $this->db->query("SELECT id FROM event_participants WHERE event_id = :event_id AND user_id = :user_id");
        $this->db->bind(':event_id', $event_id);
        $this->db->bind(':user_id', $user_id);

        $this->db->single();
        return ($this->db->rowCount() > 0);
    }

    /**
     * Đăng ký tham gia (INSERT)
     */
    public function registerParticipant($event_id, $user_id)
    {
        // Dùng CSDL của em, role mặc định là 'participant', status là 'registered'
        $this->db->query("INSERT INTO event_participants (event_id, user_id, role, status) 
                         VALUES (:event_id, :user_id, 'participant', 'registered')");
        $this->db->bind(':event_id', $event_id);
        $this->db->bind(':user_id', $user_id);

        return $this->db->execute();
    }

    /**
     * Hủy đăng ký tham gia (DELETE)
     */
    public function unregisterParticipant($event_id, $user_id)
    {
        $this->db->query("DELETE FROM event_participants WHERE event_id = :event_id AND user_id = :user_id");
        $this->db->bind(':event_id', $event_id);
        $this->db->bind(':user_id', $user_id);

        return $this->db->execute();
    }

    /**
     * Lấy danh sách người tham gia (đã đăng ký) của 1 sự kiện
     * @param int $event_id
     * @return array Danh sách user (lấy từ 2 bảng)
     */
    public function getParticipants($event_id)
    {
        // JOIN với bảng users để lấy Tên, Email, v.v.
        $this->db->query("SELECT 
                            ep.id as attendance_id, 
                            ep.status, 
                            ep.role as participant_role,
                            u.id as user_id, 
                            u.NAME, 
                            u.email
                        FROM event_participants ep
                        JOIN users u ON ep.user_id = u.id
                        WHERE ep.event_id = :event_id
                        ORDER BY u.NAME ASC"); // Sắp xếp theo Tên

        $this->db->bind(':event_id', $event_id);
        return $this->db->resultSet();
    }

    /**
     * "Check-in" cho một người tham gia
     * @param int $attendance_id ID của dòng trong bảng event_participants
     * @return boolean
     */
    public function checkInParticipant($attendance_id)
    {
        // Cập nhật status thành 'checked_in'
        $this->db->query("UPDATE event_participants 
                         SET status = 'checked_in' 
                         WHERE id = :attendance_id");

        $this->db->bind(':attendance_id', $attendance_id);
        return $this->db->execute();
    }

    /**
     * "Hoàn tác" Check-in (đưa về 'registered')
     * @param int $attendance_id ID của dòng trong bảng event_participants
     * @return boolean
     */
    public function undoCheckIn($attendance_id)
    {
        $this->db->query("UPDATE event_participants 
                         SET status = 'registered' 
                         WHERE id = :attendance_id");

        $this->db->bind(':attendance_id', $attendance_id);
        return $this->db->execute();
    }
}
