<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="manage-project-members" style="max-width: 900px; margin: 20px auto;">

    <div style="margin-bottom: 20px;">
        <a href="<?php echo BASE_URL; ?>/project" style="color: #007bff; text-decoration: none;">&larr; Quay lại Danh sách Dự án</a>
        <h1 style="margin-top: 10px;"><?php echo htmlspecialchars($data['title']); ?></h1>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

        <div class="add-member-form" style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            <h3>Thêm thành viên</h3>

            <form action="<?php echo BASE_URL; ?>/project/addMember/<?php echo $data['project']['id']; ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="user_id">1. Chọn Thành viên:</label>
                    <select name="user_id" id="user_id" style="width: 100%; padding: 8px; margin-top: 5px;">
                        <option value="">-- Chọn một người --</option>
                        <?php foreach ($data['all_users'] as $user): ?>
                            <?php // Chỉ cho phép thêm 'member' trở lên 
                            ?>
                            <?php if ($user['system_role'] == 'guest') continue; ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['NAME']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="role">2. Chọn Vai trò:</label>
                    <select name="role" id="role" style="width: 100%; padding: 8px; margin-top: 5px;">
                        <option value="Member" selected>Member (Thành viên)</option>
                        <option value="Leader">Leader (Trưởng dự án)</option>
                        <option value="Collaborator">Collaborator (Cộng tác viên)</option>
                    </select>
                </div>

                <input type="submit" value="Thêm vào Dự án" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%;">
            </form>
        </div>

        <div class="current-members">
            <h3>Thành viên hiện tại (<?php echo count($data['current_members']); ?>)</h3>

            <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                <thead style="background-color: #f4f4f4;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tên Thành viên</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Vai trò trong Dự án</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['current_members'])) : ?>
                        <tr>
                            <td colspan="3" style="padding: 10px; text-align: center;">Dự án này chưa có thành viên.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($data['current_members'] as $member) : ?>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($member['NAME']); ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($member['project_role']); ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <form action="<?php echo BASE_URL; ?>/project/removeMember/<?php echo $data['project']['id']; ?>/<?php echo $member['assignment_id']; ?>" method="POST" style="margin: 0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;"
                                            onclick="return confirm('Bạn có chắc muốn xóa [<?php echo htmlspecialchars(addslashes($member['NAME'])); ?>] khỏi dự án?');">
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
    </div>
</div>

<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>