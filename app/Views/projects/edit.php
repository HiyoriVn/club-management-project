<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="project-edit" style="max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">

    <h2><?php echo $data['title']; ?>: <?php echo htmlspecialchars($data['name']); ?></h2>

    <form action="<?php echo BASE_URL; ?>/project/update/<?php echo $data['id']; ?>" method="POST">

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="name">Tên Dự án: <sup>*</sup></label>
            <input type="text" name="name" id="name" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['name_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['name']); ?>">
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['name_err']; ?></span>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="description">Mô tả:</label>
            <textarea name="description" id="description" rows="5" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;"><?php echo htmlspecialchars($data['description']); ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="start_date">Ngày bắt đầu:</label>
                <input type="date" name="start_date" id="start_date" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;"
                    value="<?php echo htmlspecialchars($data['start_date']); ?>">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="end_date">Ngày kết thúc:</label>
                <input type="date" name="end_date" id="end_date" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;"
                    value="<?php echo htmlspecialchars($data['end_date']); ?>">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="status">Trạng thái:</label>
                <select name="status" id="status" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; background: #fff;">
                    <option value="planning" <?php echo ($data['status'] == 'planning') ? 'selected' : ''; ?>>Đang lên kế hoạch</option>
                    <option value="in_progress" <?php echo ($data['status'] == 'in_progress') ? 'selected' : ''; ?>>Đang thực hiện</option>
                    <option value="completed" <?php echo ($data['status'] == 'completed') ? 'selected' : ''; ?>>Đã hoàn thành</option>
                    <option value="cancelled" <?php echo ($data['status'] == 'cancelled') ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="leader_id">Leader (Trưởng dự án):</label>
                <select name="leader_id" id="leader_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; background: #fff;">
                    <option value="">-- (Không có) --</option>
                    <?php foreach ($data['all_users'] as $user) : ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo ($data['leader_id'] == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['NAME']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="department_id">Thuộc Ban:</label>
                <select name="department_id" id="department_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; background: #fff;">
                    <option value="">-- (Không thuộc Ban nào) --</option>
                    <?php foreach ($data['all_departments'] as $dep) : ?>
                        <option value="<?php echo $dep['id']; ?>" <?php echo ($data['department_id'] == $dep['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dep['NAME']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <input type="submit" value="Cập nhật" style="background: #ffc107; color: black; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            <a href="<?php echo BASE_URL; ?>/project" style="color: #6c757d; text-decoration: none; margin-left: 15px;">Hủy bỏ</a>
        </div>

    </form>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>