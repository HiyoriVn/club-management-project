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
                <tr style="<?php echo ($user['system_role'] == 'admin' || $user['system_role'] == 'subadmin') ? 'background-color: #fffbe6;' : ''; ?>">

                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $user['id']; ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($user['NAME']); ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($user['email']); ?></td>

                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <?php if ($user['system_role'] == 'admin'): ?>
                            <strong style="color: red;">Admin (Quản trị)</strong>
                        <?php elseif ($user['system_role'] == 'subadmin'): ?>
                            <strong style="color: orange;">Sub-Admin (Quản lý)</strong>
                        <?php elseif ($user['system_role'] == 'member'): ?>
                            <span style="color: green;">Member (Thành viên)</span>
                        <?php else: ?>
                            <span style="color: #6c757d;">Guest (Khách)</span>
                        <?php endif; ?>
                    </td>

                    <td style="padding: 10px; border: 1px solid #ddd; display: flex; flex-wrap: wrap; gap: 5px;">

                        <a href="<?php echo BASE_URL; ?>/member/manage/<?php echo $user['id']; ?>"
                            style="text-decoration: none; padding: 5px 10px; background: #007bff; color: white; border-radius: 3px;">
                            Phân quyền (Ban)
                        </a>

                        <?php
                        // LOGIC MỚI: Chỉ 'admin' mới thấy form đổi System Role
                        // Và không cho admin tự sửa mình, hoặc sửa admin khác
                        if (
                            $_SESSION['user_role'] == 'admin' &&
                            $user['id'] != $_SESSION['user_id'] &&
                            $user['system_role'] != 'admin'
                        ) :
                        ?>
                            <form action="<?php echo BASE_URL; ?>/member/updateSystemRole/<?php echo $user['id']; ?>" method="POST" style="display: flex; gap: 5px; margin: 0;">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <select name="system_role" style="padding: 5px; border: 1px solid #ccc; border-radius: 3px;">
                                    <option value="guest" <?php echo ($user['system_role'] == 'guest') ? 'selected' : ''; ?>>Guest</option>
                                    <option value="member" <?php echo ($user['system_role'] == 'member') ? 'selected' : ''; ?>>Member</option>
                                    <option value="subadmin" <?php echo ($user['system_role'] == 'subadmin') ? 'selected' : ''; ?>>Sub-Admin</option>
                                </select>
                                <button type="submit" style="background: #17a2b8; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Lưu</button>
                            </form>
                        <?php endif; // Hết điều kiện 'admin' 
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>