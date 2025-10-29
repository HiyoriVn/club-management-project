<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="project-list" style="max-width: 1200px; margin: 20px auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1><?php echo $data['title']; ?> (<?php echo count($data['projects']); ?>)</h1>
        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
            <a href="<?php echo BASE_URL; ?>/project/create" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                + Tạo Dự án mới
            </a>
        <?php endif; ?>
    </div>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tên Dự án</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Trạng thái</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Leader</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Thuộc Ban</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Ngày bắt đầu</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['projects'])) : ?>
                <tr>
                    <td colspan="6" style="padding: 10px; text-align: center;">Chưa có Dự án nào.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($data['projects'] as $project) : ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <a href="<?php echo BASE_URL; ?>/project/tasks/<?php echo $project['id']; ?>" style="font-weight: bold; text-decoration: none; color: #0056b3;">
                                <?php echo htmlspecialchars($project['NAME']); ?>
                            </a>
                            <p style="font-size: 0.9em; color: #555;"><?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>...</p>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($project['STATUS']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($project['leader_name'] ?? 'N/A'); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($project['department_name'] ?? 'N/A'); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $project['start_date'] ? date('d/m/Y', strtotime($project['start_date'])) : 'N/A'; ?></td>

                        <td style="padding: 10px; border: 1px solid #ddd; width: 120px;">
                            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
                                <a href="<?php echo BASE_URL; ?>/project/manage/<?php echo $project['id']; ?>"
                                    style="background: #17a2b8; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; display: block; margin-bottom: 5px; text-align: center;">
                                    Quản lý TV
                                </a>
                                <a href="<?php echo BASE_URL; ?>/project/edit/<?php echo $project['id']; ?>" style="color: blue; display: block; margin-bottom: 5px;">Sửa</a>

                                <form action="<?php echo BASE_URL; ?>/project/destroy/<?php echo $project['id']; ?>" method="POST" style="margin: 0;">
                                    <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;"
                                        onclick="return confirm('Bạn có chắc muốn xóa Dự án [<?php echo htmlspecialchars(addslashes($project['NAME'])); ?>]?');">
                                        Xóa
                                    </button>
                                </form>
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