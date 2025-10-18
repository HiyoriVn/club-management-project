<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<h1><?php echo $data['title']; ?></h1>
<p><?php echo $data['description']; ?></p>

<hr>

<?php if ($data['isLoggedIn']) : ?>
    <p>Bạn có thể đi đến trang quản lý của mình.</p>
    <a href="<?php echo BASE_URL; ?>/dashboard" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
        Đi tới Bảng điều khiển
    </a>
<?php else : ?>
    <p>Nếu bạn là thành viên, hãy đăng nhập để truy cập hệ thống.</p>
    <a href="<?php echo BASE_URL; ?>/auth/login" style="background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
        Đăng nhập ngay
    </a>
<?php endif; ?>


<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>