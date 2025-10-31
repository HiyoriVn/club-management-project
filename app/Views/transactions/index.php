<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="transaction-list" style="max-width: 1000px; margin: 20px auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1><?php echo $data['title']; ?></h1>
        <a href="<?php echo BASE_URL; ?>/transaction/create" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            + Thêm Giao dịch
        </a>
    </div>

    <div class="summary-boxes" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div style="background: #e6ffed; border: 1px solid green; padding: 15px; border-radius: 5px;">
            <h3 style="margin-top: 0;">Tổng Thu</h3>
            <strong style="color: green; font-size: 1.5em;"><?php echo number_format($data['totals']->income, 0, ',', '.'); ?> đ</strong>
        </div>
        <div style="background: #ffebee; border: 1px solid red; padding: 15px; border-radius: 5px;">
            <h3 style="margin-top: 0;">Tổng Chi</h3>
            <strong style="color: red; font-size: 1.5em;"><?php echo number_format($data['totals']->expense, 0, ',', '.'); ?> đ</strong>
        </div>
        <div style="background: #eef7ff; border: 1px solid blue; padding: 15px; border-radius: 5px;">
            <h3 style="margin-top: 0;">Số Dư Hiện Tại</h3>
            <strong style="color: blue; font-size: 1.5em;"><?php echo number_format($data['totals']->balance, 0, ',', '.'); ?> đ</strong>
        </div>
    </div>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Ngày</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Mô tả</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Số tiền</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Người tạo</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['transactions'])) : ?>
                <tr>
                    <td colspan="5" style="padding: 10px; text-align: center;">Chưa có giao dịch nào.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($data['transactions'] as $tx) : ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo date('d/m/Y', strtotime($tx['date'])); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($tx['description']); ?></td>

                        <?php if ($tx['type'] == 'income') : ?>
                            <td style="padding: 10px; border: 1px solid #ddd; color: green; font-weight: bold;">
                                + <?php echo number_format($tx['amount'], 0, ',', '.'); ?> đ
                            </td>
                        <?php else : ?>
                            <td style="padding: 10px; border: 1px solid #ddd; color: red; font-weight: bold;">
                                - <?php echo number_format($tx['amount'], 0, ',', '.'); ?> đ
                            </td>
                        <?php endif; ?>

                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($tx['creator_name']); ?></td>

                        <td style="padding: 10px; border: 1px solid #ddd; display: flex; gap: 10px;">
                            <a href="<?php echo BASE_URL; ?>/transaction/edit/<?php echo $tx['id']; ?>"
                                style="text-decoration: none; padding: 5px 10px; background: #ffc107; color: black; border-radius: 3px;">
                                Sửa
                            </a>

                            <form action="<?php echo BASE_URL; ?>/transaction/destroy/<?php echo $tx['id']; ?>" method="POST"
                                style="margin: 0;">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <button type="submit"
                                    style="padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer;"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa Giao dịch [<?php echo htmlspecialchars(addslashes($tx['description'])); ?>]?');">
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