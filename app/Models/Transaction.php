<?php
// app/Models/Transaction.php

namespace App\Models;

use App\Core\Database;

class Transaction
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy tất cả giao dịch, JOIN với bảng user để lấy tên
     * @return array
     */
    public function findAllWithUser()
    {
        // JOIN với bảng users 2 lần: 
        // 1. (creator) để lấy tên người tạo
        // 2. (approver) để lấy tên người duyệt (nếu có)
        $this->db->query("SELECT 
                            t.id,
                            t.TYPE as type,         -- Sửa ở đây
                            t.amount,
                            t.description,
                            t.DATE as date,         -- Sửa ở đây
                            t.created_at,
                            t.approved_by,
                            creator.NAME as creator_name,
                            approver.NAME as approver_name
                        FROM transactions t
                        JOIN users creator ON t.created_by = creator.id
                        LEFT JOIN users approver ON t.approved_by = approver.id
                        ORDER BY t.DATE DESC, t.created_at DESC");

        return $this->db->resultSet();
    }

    /**
     * Tìm 1 giao dịch bằng ID
     * @param int $id
     * @return mixed
     */
    public function findById($id)
    {
        $this->db->query("SELECT * FROM transactions WHERE id = :id");
        $this->db->bind(':id', $id);

        $row = $this->db->single();
        return ($this->db->rowCount() > 0) ? $row : false;
    }

    /**
     * Tạo một giao dịch mới
     * @param array $data
     * @return boolean
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO transactions (type, amount, description, date, created_by) 
                         VALUES (:type, :amount, :description, :date, :created_by)");

        $this->db->bind(':type', $data['type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':created_by', $_SESSION['user_id']); // Tự động lấy người tạo

        return $this->db->execute();
    }

    /**
     * Cập nhật một giao dịch
     * @param int $id
     * @param array $data
     * @return boolean
     */
    public function update($id, $data)
    {
        $this->db->query("UPDATE transactions SET 
                            type = :type, 
                            amount = :amount, 
                            description = :description, 
                            date = :date
                         WHERE id = :id");

        $this->db->bind(':id', $id);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':date', $data['date']);

        return $this->db->execute();
    }

    /**
     * Xóa một giao dịch
     * @param int $id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM transactions WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Lấy tổng Thu, Chi và Số dư
     * @return object
     */
    public function getTotals()
    {
        // Tính tổng thu
        $this->db->query("SELECT SUM(amount) as total FROM transactions WHERE type = 'income'");
        $income = $this->db->single()['total'] ?? 0;

        // Tính tổng chi
        $this->db->query("SELECT SUM(amount) as total FROM transactions WHERE type = 'expense'");
        $expense = $this->db->single()['total'] ?? 0;

        // Trả về một đối tượng chứa 3 giá trị
        return (object) [
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense
        ];
    }
}
