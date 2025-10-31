<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="role-list" style="max-width: 700px; margin: 20px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1><?php echo $data['title']; ?> (<?php echo count($data['roles']); ?>)</h1>
        <a href="<?php echo BASE_URL; ?>/departmentrole/create" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            + Thêm Vai trò mới
        </a>
    </div>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">ID</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tên Vai trò</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Ngày tạo</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['roles'])) : ?>
                <tr>
                    <td colspan="4" style="padding: 10px; text-align: center;">Chưa có Vai trò nào.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($data['roles'] as $role) : ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $role['id']; ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($role['NAME']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo date('d/m/Y', strtotime($role['created_at'])); ?></td>

                        <td style="padding: 10px; border: 1px solid #ddd; display: flex; gap: 10px;">
                            <a href="<?php echo BASE_URL; ?>/departmentrole/edit/<?php echo $role['id']; ?>"
                                style="text-decoration: none; padding: 5px 10px; background: #ffc107; color: black; border-radius: 3px;">
                                Sửa
                            </a>

                            <form action="<?php echo BASE_URL; ?>/departmentrole/destroy/<?php echo $role['id']; ?>" method="POST" style="margin: 0;">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <button type="submit"
                                    style="padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer;"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa Vai trò [<?php echo htmlspecialchars(addslashes($role['NAME'])); ?>]?');">
                                    Xóa
                                </button>
                            </form>
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