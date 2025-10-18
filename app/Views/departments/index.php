<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="department-list" style="max-width: 900px; margin: 20px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1><?php echo $data['title']; ?> (<?php echo count($data['departments']); ?>)</h1>
        <a href="<?php echo BASE_URL; ?>/department/create" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            + Thêm Ban mới
        </a>
    </div>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">ID</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tên Ban</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Mô tả</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Ban cha (ID)</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['departments'])) : ?>
                <tr>
                    <td colspan="5" style="padding: 10px; text-align: center;">Chưa có Ban nào.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($data['departments'] as $dep) : ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $dep['id']; ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($dep['NAME']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($dep['description']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $dep['parent_id'] ?? 'N/A'; ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <a href="<?php echo BASE_URL; ?>/department/edit/<?php echo $dep['id']; ?>">Sửa</a>

                            <a href="#" style="color: red;">Xóa</a>
                        </td>
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