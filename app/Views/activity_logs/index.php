<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="log-list" style="max-width: 1000px; margin: 20px auto;">
    <h1><?php echo $data['title']; ?></h1>
    <p>Hiển thị 200 hành động mới nhất.</p>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd; font-size: 0.9em;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Thời gian</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Người thực hiện</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Hành động</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['logs'])) : ?>
                <tr>
                    <td colspan="4" style="padding: 10px; text-align: center;">Chưa có hoạt động nào được ghi lại.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($data['logs'] as $log) : ?>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; width: 150px;"><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                        <td style="padding: 8px; border: 1px solid #ddd; width: 120px;">
                            <?php echo htmlspecialchars($log['user_name'] ?? 'Hệ thống'); ?>
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd; width: 150px;">
                            <code style="background: #eee; padding: 2px 5px; border-radius: 3px;"><?php echo htmlspecialchars($log['ACTION']); ?></code>
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($log['details']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>