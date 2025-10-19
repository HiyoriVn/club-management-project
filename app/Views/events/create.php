<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="event-create" style="max-width: 700px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">

    <h2><?php echo $data['title']; ?></h2>

    <form action="<?php echo BASE_URL; ?>/event/store" method="POST">

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="form_title">Tiêu đề Sự kiện: <sup>*</sup></label>
            <input type="text" name="form_title" id="form_title" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['title_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['form_title']); ?>">
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['title_err']; ?></span>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="description">Mô tả:</label>
            <textarea name="description" id="description" rows="5" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;"><?php echo htmlspecialchars($data['description']); ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="start_time">Thời gian bắt đầu: <sup>*</sup></label>
                <input type="datetime-local" name="start_time" id="start_time" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['start_time_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                    value="<?php echo htmlspecialchars($data['start_time']); ?>">
                <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['start_time_err']; ?></span>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="end_time">Thời gian kết thúc: (Có thể bỏ trống)</label>
                <input type="datetime-local" name="end_time" id="end_time" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;"
                    value="<?php echo htmlspecialchars($data['end_time']); ?>">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="location">Địa điểm:</label>
            <input type="text" name="location" id="location" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['location']); ?>">
        </div>

        <div class="form-row" style="display: flex; justify-content: space-between; align-items: center;">
            <input type="submit" value="Tạo Sự kiện" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            <a href="<?php echo BASE_URL; ?>/event" style="color: #6c757d; text-decoration: none; margin-left: 15px;">Hủy bỏ</a>
        </div>

    </form>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>