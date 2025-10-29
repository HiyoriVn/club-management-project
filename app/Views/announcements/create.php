<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="announcement-create" style="max-width: 700px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">

    <h2><?php echo $data['title']; ?></h2>

    <form action="<?php echo BASE_URL; ?>/announcement/store" method="POST">

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="form_title">Tiêu đề: <sup>*</sup></label>
            <input type="text" name="form_title" id="form_title" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['title_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['form_title']); ?>">
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['title_err']; ?></span>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="content">Nội dung:</label>
            <textarea name="content" id="content" rows="10" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;"><?php echo htmlspecialchars($data['content']); ?></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="target">Gửi tới:</label>
            <select name="target" id="target" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; background: #fff;">
                <option value="public">-- Thông báo Chung (Guest cũng thấy) --</option>
                <option value="internal" selected>-- Thông báo Nội bộ CLB (Chỉ Member) --</option>

                <optgroup label="Chỉ gửi cho Ban Cụ thể:">
                    <?php foreach ($data['all_departments'] as $dep) : ?>
                        <option value="<?php echo $dep['id']; ?>">
                            Ban: <?php echo htmlspecialchars($dep['NAME']); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </div>

        <div class="form-row">
            <input type="submit" value="Đăng" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            <a href="<?php echo BASE_URL; ?>/announcement" style="color: #6c757d; text-decoration: none; margin-left: 15px;">Hủy bỏ</a>
        </div>

    </form>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>