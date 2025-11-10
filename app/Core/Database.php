<?php
// app/Core/Database.php

// Sử dụng namespace để tổ chức code
namespace App\Core;

// Import lớp PDO (có sẵn của PHP)
use PDO;
use PDOException;

class Database
{
    // 1. Khai báo các thuộc tính của CSDL
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $name = DB_NAME;
    private $charset = DB_CHARSET;

    // 2. Khai báo các thuộc tính của class
    private static $instance = null; // Biến static để lưu trữ 1 instance duy nhất
    private $pdo; // Biến chứa kết nối PDO
    private $stmt; // Biến chứa câu lệnh (statement)

    // 3. Hàm khởi tạo (construct) là private
    // Ngăn không cho tạo đối tượng bằng "new Database()" từ bên ngoài
    private function __construct()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->name . ';charset=' . $this->charset;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Bật chế độ báo lỗi
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Trả về dữ liệu dạng mảng associative
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // Tạo kết nối PDO
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Nếu kết nối thất bại, "chết" và báo lỗi
            die('Kết nối CSDL thất bại: ' . $e->getMessage());
        }
    }

    // 4. Phương thức "getInstance" (public, static)
    // Đây là cách duy nhất để lấy được đối tượng Database
    public static function getInstance()
    {
        // Nếu $instance chưa được tạo...
        if (self::$instance == null) {
            // ...thì tạo mới
            self::$instance = new Database();
        }
        // Trả về instance (mới tạo hoặc đã có từ trước)
        return self::$instance;
    }

    // 5. Phương thức "query" (để chuẩn bị câu lệnh SQL)
    // Ví dụ: $db->query("SELECT * FROM users WHERE id = :id");
    public function query($sql)
    {
        $this->stmt = $this->pdo->prepare($sql);
    }

    // 6. Phương thức "bind" (để gán giá trị an toàn, chống SQL Injection)
    // Ví dụ: $db->bind(':id', 1);
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // 7. Phương thức "execute" (để thực thi câu lệnh)
    public function execute()
    {
        return $this->stmt->execute();
    }

    // 8. Lấy nhiều dòng (SELECT *)
    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 9. Lấy một dòng (SELECT ... LIMIT 1)
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 10. Lấy số dòng bị ảnh hưởng (INSERT, UPDATE, DELETE)
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
    /**
     * 11. Lấy ID của dòng vừa INSERT
     * (HÀM MỚI ĐỂ SỬA LỖI FATAL)
     */
    public function lastInsertId()
    {
        // Gọi hàm lastInsertId() gốc của PDO
        return $this->pdo->lastInsertId();
    }
}
