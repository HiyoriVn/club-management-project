# Hệ thống Quản lý Câu lạc bộ 

Nền tảng quản lý tập trung dành cho các Câu lạc bộ, hỗ trợ quản lý thành viên, dự án, tài chính và sự kiện.

## Yêu cầu hệ thống

| Thành phần | Yêu cầu tối thiểu |
| :--- | :--- | 
| **PHP** | >= 8.2 |
| **Database** | MySQL 8.0 |
| **Web Server** | Apache |
| **Công cụ** | Composer, Git |
---

## 🛠️ Hướng dẫn cài đặt trên máy cá nhân (Localhost - XAMPP)

### Bước 1: Chuẩn bị Source Code
Mở **Terminal** (hoặc Git Bash/CMD) tại thư mục `htdocs` của XAMPP (thường là `C:\xampp\htdocs`) và chạy lệnh:
1.  Tải source code về máy.
    ```bash
    git clone https://github.com/HiyoriVn/club-management-project
    ```
2.  Di chuyển vào thư mục của dụ án
    ```bash
    cd club-management-project
    ```

### Bước 2: Cài đặt Database
1.  Mở **XAMPP Control Panel**, bật **Apache** và **MySQL**.
2.  Truy cập **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3.  Bấm vào tab **Import (Nhập)**. 
4.  Chọn file `migrations/init.sql` trong thư mục code và bấm **Import** để nạp cấu trúc bảng và dữ liệu mẫu.

### Bước 3: Cài đặt thư viện (Composer)
1.  Mở Terminal (hoặc CMD/Git Bash) tại thư mục gốc của dự án.
2.  Chạy lệnh sau để tải các thư viện cần thiết:
    ```bash
    composer install
    ```
    *(Lưu ý: Bạn cần cài đặt Composer trên máy tính trước)*.

### Bước 4: Cấu hình môi trường (.env)
1.  Trong thư mục gốc, tìm file `.env.example` (hoặc `.env_example`).
2.  Đổi tên file này thành `.env`.
3.  Mở file `.env` bằng trình soạn thảo (VS Code, Notepad...) và chỉnh sửa các thông số sau:

    ```ini
    # Cấu hình Database
    DB_HOST=localhost
    DB_NAME=club_management  # Tên DB bạn đã tạo ở Bước 2
    DB_USER=root             # Mặc định XAMPP là root
    DB_PASS=                 # Mặc định XAMPP để trống

    # Cấu hình App
    # Quan trọng: Trỏ đúng vào thư mục public
    APP_URL=http://localhost/club-management-project/public
    APP_ENV=local

    # Cấu hình Email (Nếu muốn test gửi mail)
    SMTP_HOST=smtp.gmail.com
    SMTP_PORT=587
    SMTP_USERNAME=email_cua_ban@gmail.com
    SMTP_PASSWORD=mat_khau_ung_dung
    ```

### Bước 5: Chạy dự án
Truy cập trình duyệt theo đường dẫn:
[http://localhost/club-management-project/public](http://localhost/club-management-project/public)

---

## Hướng dẫn cài đặt trên aaPanel (Linux Server)

### Bước 1: Tạo Website trên aaPanel
1.  Đăng nhập aaPanel, vào mục **Website** -> **Add site**.
2.  Nhập **Domain** của bạn.
3.  Tại phần **Database**, chọn **Create** (MySQL). Lưu lại *Username* và *Password* của Database.
4.  **PHP Version**: Chọn PHP 8.0 trở lên.

### Bước 2: Upload Source Code
1.  Vào mục **Files**, truy cập vào thư mục gốc của website vừa tạo (thường là `/www/wwwroot/yourdomain.com`).
2.  Xóa các file mặc định (như `index.html`, `404.html`).
3.  Upload toàn bộ source code dự án lên và giải nén.

### Bước 3: Cài đặt Database
1.  Trên aaPanel, vào mục **Databases**, bấm vào nút **phpMyAdmin** tương ứng với database vừa tạo.
2.  Trong giao diện phpMyAdmin, chọn Database đó -> Tab **Import**.
3.  Upload file `migrations/init_v2.sql` và thực hiện Import.

### Bước 4: Cài đặt Composer
1.  Trên aaPanel, mở **Terminal**.
2.  Di chuyển vào thư mục dự án:
    ```bash
    cd /www/wwwroot/yourdomain.com
    ```
3.  Chạy lệnh cài đặt thư viện:
    ```bash
    composer install
    ```
    *(Nếu gặp lỗi permission, hãy set quyền user là `www` cho thư mục)*.

### Bước 5: Cấu hình .env
1.  Trong mục **Files** của aaPanel, tìm file `.env.example`, đổi tên thành `.env`.
2.  Chỉnh sửa nội dung file `.env`:
    * **DB_NAME, DB_USER, DB_PASS**: Điền thông tin Database đã tạo ở Bước 1.
    * **APP_URL**: Điền domain thật của bạn (VD: `https://yourdomain.com`).
    * **APP_ENV**: Đổi thành `production` (để ẩn lỗi hệ thống).

### Bước 6: Cấu hình Web Server (Quan trọng)
Dự án chạy thông qua file `index.php` trong thư mục `public`. Bạn cần trỏ Document Root vào đó.

1.  Vào mục **Website**, click vào tên domain để mở cài đặt (Site config).
2.  Chọn **Site directory**:
    * **Running directory**: Chọn `/public`.
    * Bấm **Save**.
3.  **URL Rewrite** (Nếu dùng Nginx):
    * Chuyển sang tab **URL rewrite**.
    * Chọn mẫu **Laravel 5** (hoặc copy đoạn code dưới đây) rồi Save:
        ```nginx
        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }
        ```

---

## Tài khoản Admin mặc định

Sau khi cài đặt, bạn có thể đăng nhập bằng tài khoản Admin được tạo sẵn:

* **Email**: `admin@clb.vn`
* **Mật khẩu**: `password`
---

## Lưu ý Config (Tùy chỉnh)

Nếu bạn cần tùy chỉnh sâu hơn các hằng số hệ thống mà không có trong `.env`, hãy mở file:
`config/config.php`

Tại đây bạn có thể chỉnh sửa:
* Đường dẫn upload file (`UPLOAD_PATH`).
* Timezone hệ thống.
