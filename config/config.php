<?php

// 1. Cấu hình Database
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'club_management');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// 2. Cấu hình đường dẫn
define('BASE_URL', $_ENV['APP_URL'] ?? 'http://localhost/club-management-project/public');

// Đường dẫn thư mục gốc của project (ví dụ: C:/xampp/htdocs/club-management-project)
define('ROOT_PATH', dirname(__DIR__)); 

// 3. Cấu hình Upload
define('UPLOAD_PATH', ROOT_PATH . '/uploads/'); 
define('UPLOAD_URL', BASE_URL . '/../uploads/'); 