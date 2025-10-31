<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="role-edit" style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">

    <h2><?php echo $data['title']; ?>: <?php echo htmlspecialchars($data['name']); ?></h2>

    <form action="<?php echo BASE_URL; ?>/departmentrole/update/<?php echo $data['id']; ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group" style="margin-bottom: 15px;">
            <label for="name">Tên Vai trò: <sup>*</sup></label>
            <input type="text" name="name" id="name" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['name_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['name']); ?>">
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['name_err']; ?></span>
        </div>

        <div class="form-row" style="display: flex; justify-content: space-between; align-items: center;">
            <input type="submit" value="Cập nhật" style="background: #ffc107; color: black; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            <a href="<?php echo BASE_URL; ?>/departmentrole" style="color: #6c757d; text-decoration: none;">Hủy bỏ</a>
        </div>

    </form>
</div>

<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>