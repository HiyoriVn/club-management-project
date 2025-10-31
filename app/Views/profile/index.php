<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="profile-page" style="max-width: 800px; margin: 20px auto;">
    <h1><?php echo $data['title']; ?></h1>
    <p>Cập nhật thông tin cá nhân của bạn.</p>

    <form action="<?php echo BASE_URL; ?>/profile/update" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

            <div class="basic-info">
                <h4>Thông tin tài khoản</h4>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Họ và Tên:</label>
                    <input type="text" value="<?php echo htmlspecialchars($data['name']); ?>" disabled style="width: 100%; padding: 8px; background: #eee; border: 1px solid #ccc;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Email:</label>
                    <input type="email" value="<?php echo htmlspecialchars($data['email']); ?>" disabled style="width: 100%; padding: 8px; background: #eee; border: 1px solid #ccc;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Vai trò CLB:</label>
                    <input type="text" value="<?php echo htmlspecialchars($data['system_role']); ?>" disabled style="width: 100%; padding: 8px; background: #eee; border: 1px solid #ccc;">
                </div>

            </div>

            <div class="extended-info" style="border-left: 1px solid #ddd; padding-left: 30px;">
                <h4>Thông tin hồ sơ</h4>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="student_id">Mã số Sinh viên/Học sinh:</label>
                    <input type="text" name="student_id" id="student_id" value="<?php echo htmlspecialchars($data['student_id']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="phone">Số điện thoại:</label>
                    <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($data['phone']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="dob">Ngày sinh:</label>
                        <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($data['dob']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc;">
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="gender">Giới tính:</label>
                        <select name="gender" id="gender" style="width: 100%; padding: 8px; border: 1px solid #ccc;">
                            <option value="other" <?php echo ($data['gender'] == 'other') ? 'selected' : ''; ?>>Khác</option>
                            <option value="male" <?php echo ($data['gender'] == 'male') ? 'selected' : ''; ?>>Nam</option>
                            <option value="female" <?php echo ($data['gender'] == 'female') ? 'selected' : ''; ?>>Nữ</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="address">Địa chỉ:</label>
                    <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($data['address']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="bio">Giới thiệu (Bio):</label>
                    <textarea name="bio" id="bio" rows="4" style="width: 100%; padding: 8px; border: 1px solid #ccc;"><?php echo htmlspecialchars($data['bio']); ?></textarea>
                </div>

                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                <input type="submit" value="Lưu thay đổi" style="background: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            </div>

        </div>
    </form>
</div>

<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>