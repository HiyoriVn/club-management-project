<?php
// config/config.php

// 1. Cấu hình Database
define('DB_HOST', 'localhost'); // Thường là localhost
define('DB_USER', 'root');      // User Mặc định của XAMPP/WAMP
define('DB_PASS', '');          // Password Mặc định của XAMPP/WAMP (để trống)
define('DB_NAME', 'club_management'); // Tên DB em đã tạo
define('DB_CHARSET', 'utf8mb4');

// 2. Cấu hình đường dẫn (sẽ dùng sau)
// URL gốc của website (ví dụ: http://localhost/club-management-project)
define('BASE_URL', 'http://localhost/club-management-project/public'); 

// Đường dẫn thư mục gốc của project (ví dụ: C:/xampp/htdocs/club-management-project)
define('ROOT_PATH', dirname(__DIR__)); // __DIR__ là thư mục hiện tại (config), dirname(__DIR__) là thư mục cha (project root)

// 3. Cấu hình Upload
define('UPLOAD_PATH', ROOT_PATH . '/uploads/'); // Đường dẫn thư mục vật lý
define('UPLOAD_URL', BASE_URL . '/../uploads/'); // URL để truy cập file từ trình duyệt (lùi 1 cấp từ /public)