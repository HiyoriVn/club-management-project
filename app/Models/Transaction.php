<?php

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
     * Lấy danh sách thu chi (có phân trang)
     */
    public function getAll($limit = 20, $offset = 0)
    {
        $sql = "SELECT t.*, u.name as creator_name, ua.name as approver_name
                FROM transactions t
                LEFT JOIN users u ON t.created_by = u.id
                LEFT JOIN users ua ON t.approved_by = ua.id
                ORDER BY t.date DESC, t.created_at DESC
                LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, \PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    /**
     * Tính tổng thu/chi (Dùng cho Dashboard)
     */
    public function getTotals()
    {
        $this->db->query("SELECT type, SUM(amount) as total FROM transactions GROUP BY type");
        return $this->db->resultSet();
    }

    public function create($data)
    {
        $sql = "INSERT INTO transactions (type, amount, date, description, created_by, approved_by) 
                VALUES (:type, :amount, :date, :description, :created_by, :approved_by)";

        $this->db->query($sql);

        $this->db->bind(':type', $data['type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':created_by', $data['created_by']);
        $this->db->bind(':approved_by', $data['approved_by'] ?? null);

        return $this->db->execute();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE transactions SET 
                type = :type, 
                amount = :amount, 
                date = :date, 
                description = :description,
                approved_by = :approved_by
                WHERE id = :id";

        $this->db->query($sql);

        $this->db->bind(':id', $id);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':approved_by', $data['approved_by'] ?? null);

        return $this->db->execute();
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM transactions WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function findById($id)
    {
        $this->db->query("SELECT * FROM transactions WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}
