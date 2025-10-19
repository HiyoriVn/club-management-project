<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="event-list" style="max-width: 1000px; margin: 20px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1><?php echo $data['title']; ?></h1>

        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
            <a href="<?php echo BASE_URL; ?>/event/create" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                + Tạo Sự kiện mới
            </a>
        <?php endif; ?>
    </div>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Sự kiện</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Thời gian bắt đầu</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Thời gian kết thúc</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Địa điểm</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Người tạo</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['events'])) : ?>
                <tr>
                    <td colspan="6" style="padding: 10px; text-align: center;">Chưa có Sự kiện nào.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($data['events'] as $event) : ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                            <p style="font-size: 0.9em; color: #555;"><?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 100))); ?>...</p>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo date('d/m/Y H:i', strtotime($event['start_time'])); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $event['end_time'] ? date('d/m/Y H:i', strtotime($event['end_time'])) : 'N/A'; ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($event['location']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($event['creator_name']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <a href="#" style="color: green;">Đăng ký</a>

                            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
                                <a href="#" style="color: blue;">Sửa</a>
                                <a href="#" style="color: red;">Xóa</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>