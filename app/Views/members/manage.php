<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="manage-roles" style="max-width: 900px; margin: 20px auto; display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

    <div class="assign-form" style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
        <h3>Gán vai trò mới</h3>
        <h4>Cho: <?php echo htmlspecialchars($data['user']['NAME']); ?></h4>

        <form action="<?php echo BASE_URL; ?>/member/assign/<?php echo $data['user']['id']; ?>" method="POST">

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="department_id">1. Chọn Ban:</label>
                <select name="department_id" id="department_id" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="">-- Chọn một Ban --</option>
                    <?php foreach ($data['all_departments'] as $dep): ?>
                        <option value="<?php echo $dep['id']; ?>"><?php echo htmlspecialchars($dep['NAME']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="role_id">2. Chọn Vai trò:</label>
                <select name="role_id" id="role_id" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="">-- Chọn một Vai trò --</option>
                    <?php foreach ($data['all_roles'] as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['NAME']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="submit" value="Gán" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%;">
        </form>
    </div>

    <div class="current-roles">
        <h3>Các vai trò hiện tại (<?php echo count($data['current_roles']); ?>)</h3>

        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
            <thead style="background-color: #f4f4f4;">
                <tr>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tên Ban</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tên Vai trò</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['current_roles'])) : ?>
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: center;">Thành viên này chưa được gán vai trò nào.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($data['current_roles'] as $role) : ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($role['department_name']); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($role['role_name']); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <form action="<?php echo BASE_URL; ?>/member/revoke/<?php echo $data['user']['id']; ?>/<?php echo $role['assignment_id']; ?>" method="POST" style="margin: 0;">
                                    <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;"
                                        onclick="return confirm('Bạn có chắc muốn thu hồi vai trò [<?php echo htmlspecialchars($role['role_name']); ?>] tại [<?php echo htmlspecialchars($role['department_name']); ?>]?');">
                                        Thu hồi
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>