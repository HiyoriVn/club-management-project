# HƯỚNG DẪN CÀI ĐẶT & SỬ DỤNG HỆ THỐNG QUẢN LÝ CÂU LẠC BỘ

## PHẦN I: YÊU CẦU HỆ THỐNG

### Yêu cầu phần mềm
- **Web Server**: Apache hoặc Nginx
- **PHP**: 8.2+
- **Database**: MySQL 5.7+
- **Composer**: 2.0+ (package manager PHP)
- **Mail Service**: SMTP server hoặc Gmail (để gửi reset password)

### Kiểm tra môi trường
```bash
# Kiểm tra PHP version
php -v

# Kiểm tra MySQL
mysql --version

# Kiểm tra Composer
composer --version
```

---

## PHẦN II: CÀI ĐẶT HỆ THỐNG

### Bước 1: Chuẩn bị môi trường (XAMPP)

Nếu dùng **XAMPP** (khuyến nghị cho Windows):
1. Tải XAMPP từ https://www.apachefriends.org/
2. Chọn version có PHP 7.4+ 
3. Cài đặt vào `C:\xampp`
4. Khởi động Control Panel → Start **Apache** và **MySQL**

### Bước 2: Tải mã nguồn

```bash
# Chuyển vào thư mục htdocs
cd C:\xampp\htdocs

# Clone hoặc sao chép project
git clone https://github.com/HiyoriVn/club-management-project
cd club-management-project
```

### Bước 3: Cài đặt dependencies

```bash
# Cài đặt package PHP qua Composer
composer install

# Tạo/kiểm tra folder uploads (cho file)
mkdir uploads
chmod 755 uploads
```

### Bước 4: Nạp database

```bash
# Mở MySQL
mysql -u root -p

# Tạo database
CREATE DATABASE club_management;
USE club_management;

# Import file SQL
source migrations/init.sql;
# hoặc nếu file trên Windows
source C:\xampp\htdocs\club-management-project\migrations\init.sql;
```

Hoặc dùng **phpMyAdmin**:
1. Mở http://localhost/phpmyadmin
2. Tạo database mới tên `club_management`
3. Chọn database → Import → chọn file `migrations/init.sql`

### Bước 5: Cấu hình file `.env` (nếu có)

Nếu project dùng `.env`, tạo file `.env` ở root:

```env
APP_URL=http://localhost/club-management-project/public
DB_HOST=localhost
DB_NAME=club_management
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4

MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM=noreply@clubmanagement.local
```

### Bước 6: Kiểm tra cài đặt

1. Truy cập: http://localhost/club-management-project/public
2. Bạn sẽ được chuyển đến trang đăng nhập
3. Nếu lỗi, xem phần **Khắc phục sự cố**

---

## PHẦN III: CẤU HÌNH HỆ THỐNG

### File cấu hình chính

**Vị trí**: `config/config.php`

```php
<?php

// 1. Cấu hình Database
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'club_management');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// 2. Cấu hình đường dẫn
define('BASE_URL', $_ENV['APP_URL'] ?? 'http://localhost/club-management-project/public');

define('ROOT_PATH', dirname(__DIR__)); 

// 3. Cấu hình Upload
define('UPLOAD_PATH', ROOT_PATH . '/uploads/'); 
define('UPLOAD_URL', BASE_URL . '/../uploads/'); 
```

### Cấu hình Apache (nếu cần)

Tạo/sửa file `public/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /club-management-project/public/
    
    # Loại bỏ /public khỏi URL
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [L]
</IfModule>
```

### Cấu hình Gmail (để gửi email)

Nếu dùng Gmail SMTP:

1. Bật 2-factor authentication trên Gmail
2. Tạo **App Password** tại: https://myaccount.google.com/apppasswords
3. Chọn "Mail" và "Windows Computer"
4. Google sẽ cấp mật khẩu 16 ký tự → sao chép vào `MAIL_PASSWORD` trong `.env`

Ví dụ:
```php
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM=noreply@clubmanagement.local
```

---

## PHẦN IV: TÀI KHOẢN & MẬT KHẨU MẶC ĐỊNH

### Tài khoản mặc định sau khi import DB

| Email | Mật khẩu | Vai trò | Ghi chú |
|-------|----------|--------|--------|
| admin@clb.vn | admin123 | Admin | Quản trị viên hệ thống |
| subadmin@clb.vn | subadmin123 | Subadmin | Quản trị viên phụ |
| member@clb.vn | member123 | Member | Thành viên bình thường |


## PHẦN V: HƯỚNG DẪN SỬ DỤNG THEO VAI TRÒ

### 1. VAI TRÒ MEMBER (Thành viên)

#### Định nghĩa
- Thành viên bình thường của CLB
- Có thể xem thông tin chung, tham gia hoạt động, xem task được giao

#### Quyền cơ bản
- ✅ Xem dashboard cá nhân
- ✅ Xem/sửa profile cá nhân
- ✅ Xem danh sách project và task
- ✅ Cập nhật trạng thái task (nếu được giao)
- ✅ Tải lên file (trong giới hạn)
- ✅ Xem thông báo

#### Quy trình sử dụng hàng ngày

**Bước 1: Đăng nhập**
```
1. Truy cập: http://localhost/club-management-project/public
2. Nhập email: member@clb.vn
3. Nhập mật khẩu: member123
4. Click "Đăng nhập"
```
**Bước 2: Xem Dashboard**
- Hiển thị: số sự kiện sắp tới, task chưa hoàn thành, thông báo mới
- Menu trái: các module chính (Announcements, Projects, Tasks, Files)


**Bước 3: Xem task**
```
1. Menu → "Dự Án"→ "Tasks" → "Kanban"
2. Xem các task ở trên bản
```

**Bước 5: Tải lên file**
```
1. Menu → "Files"
2. Click "Upload New File"
3. Chọn file
4. Nhập mô tả
5. Click "Upload"
```

**Bước 6: Xem thông báo**
```
1. Menu → "Thông báo"
2. Xem danh sách thông báo
3. Click để xem nội dung đầy đủ
```

---

### 2. VAI TRÒ SUBADMIN (Quản trị viên phụ)

#### Định nghĩa
- Quản trị viên phụ — hỗ trợ admin quản lý một phòng/ban hoặc nhóm
- Có quyền quản lý nội dung (announcement, project) trong phạm vi được phân

#### Quyền cơ bản
- ✅ Tất cả quyền của Member
- ✅ Tạo/sửa/xóa Announcement
- ✅ Tạo/sửa/xóa Project và Task
- ✅ Xem ban 

#### Quy trình sử dụng hàng ngày

**Bước 1: Đăng nhập**
```
1. Truy cập: http://localhost/club-management-project/public
2. Nhập email: subadmin@clb.vn
3. Nhập mật khẩu: subadmin123
4. Click "Đăng nhập"
```

**Bước 2: Tạo thông báo**
```
1. Menu → "Thông báo"
2. Click nút "Đăng thông báo"
3. Nhập nội dung
4. Click "Đăng ngay"
5. Thông báo gửi
```

**Bước 3: Tạo dự án**
```
1. Menu → "Dự án"
2. Click "Dự án mới"
3. Nhập:
   - Tên project
   - Mô tả
   - Ngày bắt đầu/kết thúc
   - Trạng thái
   - Leader
4. Click "Tạo"
```

**Bước 4: Tạo task**
```
1. Menu → "Dự Án"→ "Tasks" → "Kanban"
2. Nhập:
   - Tiêu đề
   - Trạng thái, màu sắc
   - Giao cho ai 
   - Ngày hạn bắt đầu, kết thúc
   - Mô tả
3. Click "Lưu công việc"
```

---

### 3. VAI TRÒ ADMIN (Quản trị viên hệ thống)

#### Định nghĩa
- Quản trị viên hệ thống toàn quyền
- Quản lý tất cả users, roles, cấu hình, dữ liệu

#### Quyền cơ bản
- ✅ Tất cả quyền của Subadmin
- ✅ Tạo/sửa/xóa User
- ✅ Gán/thay đổi Role user
- ✅ Reset mật khẩu user
- ✅ Truy cập toàn bộ Activity Log
- ✅ Xóa dữ liệu
- ✅ Quản lý upload files

#### Quy trình sử dụng hàng ngày

**Bước 1: Đăng nhập**
```
1. Truy cập: http://localhost/club-management-project/public
2. Nhập email: admin@clb.vn
3. Nhập mật khẩu: admin123
4. Click "Đăng nhập"
5. Dashboard hiển thị thống kê toàn hệ thống
```

**Bước 2: Tạo user mới**
```
1. Menu → "Quản lý người dùng"
2. Click nút "Thêm mới"
3. Nhập thông tin
4. Click "Tạo"
```

**Bước 3: Gán/thay đổi vai trò**
```
1. Menu → "Quản lý người dùng"
2. Click user → biểu tượng sửa
3. Chọn vai trò mới (Member → Subadmin hoặc Admin)
4. Click "Cập nhật"
```

**Bước 4: Xem Activity Log**
```
1. Menu → "Báo cáo & Thống kê" → "Nhật ký hệ thống"
2. Xem tất cả thay đổi trên hệ thống:
   - Ai thay đổi gì
   - Khi nào
   - IP address (để phát hiện lạ)
```


**Bước 5: Sao lưu Database**
```
Dùng phpMyAdmin
1. Truy cập: http://localhost/phpmyadmin
2. Chọn database "club_management"
3. Tab "Export" → chọn "SQL"
4. Click "Go" → lưu file .sql
```

**Bước 6: Khôi phục Database**
```
Dùng phpMyAdmin
1. Truy cập: http://localhost/phpmyadmin
2. Chọn database hoặc tạo cái mới
3. Tab "Import"
4. Chọn file .sql backup
5. Click "Import"
```
**Bước 7: Quản lý tài liệu**
```
1. Menu → "Quản lý tài liệu"
2. Xem file đã upload
3. Xóa file cũ/không cần để tiết kiệm dung lượng
```


---

## PHẦN VI: VẬN HÀNH & BẢO TRÌ

### 6.1 Hàng ngày

- **Kiểm tra hệ thống**: vào trang chủ xem có lỗi gì không
- **Kiểm tra email**: nếu dùng reset password, đảm bảo SMTP hoạt động
- **Theo dõi Activity Log**: (Admin) xem có hoạt động lạ không

### 6.2 Hàng tuần

- **Sao lưu Database**: tối thiểu 1 lần/tuần
- **Kiểm tra dung lượng uploads**: xóa file cũ/không cần
- **Cập nhật danh sách thành viên**: thêm/xóa account theo đơn đăng ký
- **Kiểm tra task chưa hoàn thành**: ghi nhận tiến độ

### 6.3 Hàng tháng

- **Audit user accounts**: xóa account người rời CLB
- **Review reports**: xem báo cáo hoạt động, tổng hợp thống kê
- **Cập nhật cấu hình** (nếu cần): điều chỉnh SESSION_TIMEOUT, MAX_UPLOAD_SIZE, v.v.
- **Kiểm tra bảo mật**: mật khẩu user, phân quyền

### 6.4 Best practices

#### Bảo mật
- ✅ Đổi mật khẩu admin hàng tháng
- ✅ Không chia sẻ tài khoản admin
- ✅ Kiểm tra Activity Log định kỳ (để phát hiện hành động lạ)
- ✅ Sao lưu trước khi thực hiện thay đổi lớn
- ✅ Sử dụng mật khẩu mạnh (ít nhất 8 ký tự, có số + chữ hoa)

#### Performance
- ✅ Giới hạn kích thước upload file (khuyến nghị 10MB)
- ✅ Xóa file cũ/duplicate để tiết kiệm ổ cứng
- ✅ Giới hạn số session active (nếu có quá nhiều user)
- ✅ Archive dữ liệu cũ (ví dụ: project, event đã kết thúc)

#### Tính sẵn sàng
- ✅ Sao lưu DB ở 2 nơi (USB + Cloud)
- ✅ Lưu file cấu hình cũ (để khôi phục nếu sai)
- ✅ Ghi lại thời gian downtime, lý do (để thống kê)

---

## PHẦN VII: KHẮC PHỤC SỰ CỐ

### Vấn đề 1: Không thể truy cập trang chủ

**Triệu chứng**: 404 Not Found hoặc Blank Page

**Nguyên nhân phổ biến**:
- Apache/MySQL chưa khởi động
- File index.php bị xóa hoặc lỗi cú pháp
- .htaccess cấu hình sai

**Giải pháp**:
```bash
# 1. Kiểm tra Apache chạy chưa
# Windows: mở XAMPP Control Panel → Start Apache

# 2. Kiểm tra file config.php
php config/config.php  # Chạy xem có error không

# 3. Xóa cache browser
# Ctrl+Shift+Delete → xóa cache

# 4. Kiểm tra .htaccess
# Mở public/.htaccess → đảm bảo RewriteEngine bật
```

### Vấn đề 2: Lỗi "Undefined property '$db'" hoặc "PDOException"

**Triệu chứng**: Database connection error

**Nguyên nhân phổ biến**:
- MySQL chưa chạy
- DB credentials sai (user, password, database name)
- Database chưa import dữ liệu

**Giải pháp**:
```bash
# 1. Kiểm tra MySQL
# XAMPP Control Panel → Start MySQL

# 2. Kiểm tra credentials
# Mở config/config.php → xem DB_HOST, DB_USER, DB_PASSWORD, DB_NAME

# 3. Kiểm tra database tồn tại
mysql -u root -p
SHOW DATABASES;
USE club_management;
SHOW TABLES;
```

### Vấn đề 3: Lỗi gửi email (Reset Password không gửi)

**Triệu chứng**: Tính năng quên mật khẩu không hoạt động

**Nguyên nhân phổ biến**:
- SMTP chưa cấu hình
- Gmail 2FA chưa bật
- App Password chưa tạo

**Giải pháp**:
- Kiểm tra cấu hình của file .env và config.php