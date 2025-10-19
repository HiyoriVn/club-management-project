<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="member-list" style="max-width: 900px; margin: 20px auto;">
    <h1><?php echo $data['title']; ?> (<?php echo count($data['users']); ?>)</h1>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">ID</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tên</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Email</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Vai trò Hệ thống</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['users'] as $user) : ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $user['id']; ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($user['NAME']); ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($user['system_role']); ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <a href="<?php echo BASE_URL; ?>/member/manage/<?php echo $user['id']; ?>"
                            style="text-decoration: none; padding: 5px 10px; background: #007bff; color: white; border-radius: 3px;">
                            Phân quyền
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>