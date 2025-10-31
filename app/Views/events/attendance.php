<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="attendance-page" style="max-width: 900px; margin: 20px auto;">

    <div style="margin-bottom: 20px;">
        <a href="<?php echo BASE_URL; ?>/event" style="color: #007bff; text-decoration: none;">&larr; Quay lại Danh sách Sự kiện</a>

        <h1 style="margin-top: 10px;"><?php echo htmlspecialchars($data['title']); ?></h1>
        <p>
            Tổng số người đã đăng ký: <strong><?php echo count($data['participants']); ?></strong>
        </p>
    </div>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tên Thành viên</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Email</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Trạng thái</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['participants'])) : ?>
                <tr>
                    <td colspan="4" style="padding: 10px; text-align: center;">Chưa có ai đăng ký sự kiện này.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($data['participants'] as $p) : ?>
                    <tr style="<?php echo ($p['status'] == 'checked_in') ? 'background-color: #e6ffed;' : ''; ?>">
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($p['NAME']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($p['email']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php if ($p['status'] == 'checked_in') : ?>
                                <strong style="color: green;">Đã Check-in</strong>
                            <?php elseif ($p['status'] == 'registered') : ?>
                                <span style="color: #6c757d;">Đã Đăng ký</span>
                            <?php else: ?>
                                <span style="color: red;"><?php echo htmlspecialchars($p['status']); ?></span>
                            <?php endif; ?>
                        </td>

                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <?php if ($p['status'] == 'registered') : ?>
                                <form action="<?php echo BASE_URL; ?>/event/checkin/<?php echo $data['event']['id']; ?>/<?php echo $p['attendance_id']; ?>" method="POST" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit" style="background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; padding: 5px 10px;">
                                        Check-in
                                    </button>
                                </form>
                            <?php elseif ($p['status'] == 'checked_in') : ?>
                                <form action="<?php echo BASE_URL; ?>/event/undocheckin/<?php echo $data['event']['id']; ?>/<?php echo $p['attendance_id']; ?>" method="POST" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit" style="background: #ffc107; color: black; border: none; border-radius: 3px; cursor: pointer; padding: 5px 10px;">
                                        Hoàn tác
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
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>