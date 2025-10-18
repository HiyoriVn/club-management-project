<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<h1><?php echo $data['title']; ?></h1>
<p>Chào mừng <strong><?php echo htmlspecialchars($data['user_name']); ?></strong> đã quay trở lại!</p>
<p>Bạn đang đăng nhập với vai trò: <strong><?php echo htmlspecialchars($data['user_role']); ?></strong></p>

<hr>

<h2>Nội dung Bảng điều khiển</h2>
<p>Đây là khu vực chỉ dành cho thành viên đã đăng nhập.</p>

<?php if ($data['user_role'] == 'admin' || $data['user_role'] == 'subadmin') : ?>
    <div style="background: #fffbe6; border: 1px solid #ffe58f; padding: 15px; border-radius: 5px;">
        <h3 style="margin-top: 0;">Khu vực Quản trị</h3>
        <p>Vì bạn là 'admin' hoặc 'subadmin', bạn sẽ thấy được mục này.</p>
        <ul>
            <li><a href="#">Quản lý các Ban (Departments)</a></li>
            <li><a href="#">Quản lý Thành viên</a></li>
            <li><a href="#">Tạo Hoạt động mới</a></li>
        </ul>
    </div>
<?php endif; ?>


<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>