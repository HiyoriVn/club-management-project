<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="login-container" style="max-width: 500px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
    <h2><?php echo $data['title']; ?></h2>
    <p>Vui lòng điền thông tin để đăng nhập</p>

    <form action="<?php echo BASE_URL; ?>/auth/processLogin" method="POST">

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="email">Email: <sup>*</sup></label>
            <input type="email" name="email" id="email" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['email_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['email']); ?>">
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['email_err']; ?></span>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="password">Mật khẩu: <sup>*</sup></label>
            <input type="password" name="password" id="password" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['password_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['password']); ?>">
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['password_err']; ?></span>
        </div>

        <div class="form-row" style="display: flex; justify-content: space-between; align-items: center;">
            <input type="submit" value="Đăng Nhập" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">

            <a href="<?php echo BASE_URL; ?>/auth/register" style="color: #007bff; text-decoration: none;">Chưa có tài khoản? Đăng ký</a>
        </div>

    </form>
</div>

<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>