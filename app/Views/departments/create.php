<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="department-create" style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">

    <h2><?php echo $data['title']; ?></h2>

    <form action="<?php echo BASE_URL; ?>/department/store" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group" style="margin-bottom: 15px;">
            <label for="name">Tên Ban: <sup>*</sup></label>
            <input type="text" name="name" id="name" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['name_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['name']); ?>">
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['name_err']; ?></span>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="description">Mô tả:</label>
            <textarea name="description" id="description" rows="4" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;"><?php echo htmlspecialchars($data['description']); ?></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="parent_id">Trực thuộc (Ban cha):</label>
            <select name="parent_id" id="parent_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; background: #fff;">
                <option value="">-- (Không có Ban cha - Cấp cao nhất) --</option>

                <?php foreach ($data['departments'] as $dep) : ?>
                    <option value="<?php echo $dep['id']; ?>" <?php echo ($data['parent_id'] == $dep['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dep['NAME']); ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-row" style="display: flex; justify-content: space-between; align-items: center;">
            <input type="submit" value="Lưu lại" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            <a href="<?php echo BASE_URL; ?>/department" style="color: #6c757d; text-decoration: none;">Hủy bỏ</a>
        </div>

    </form>
</div>

<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>