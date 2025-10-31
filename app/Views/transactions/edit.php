<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="transaction-edit" style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">

    <h2><?php echo $data['title']; ?></h2>

    <form action="<?php echo BASE_URL; ?>/transaction/update/<?php echo $data['id']; ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Loại Giao dịch: <sup>*</sup></label>
            <div>
                <input type="radio" id="type_expense" name="type" value="expense" <?php echo ($data['type'] == 'expense') ? 'checked' : ''; ?>>
                <label for="type_expense" style="color: red; font-weight: bold;">Khoản CHI</label>

                <input type="radio" id="type_income" name="type" value="income" <?php echo ($data['type'] == 'income') ? 'checked' : ''; ?> style="margin-left: 20px;">
                <label for="type_income" style="color: green; font-weight: bold;">Khoản THU</label>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="amount">Số tiền: <sup>*</sup></label>
            <input type="number" name="amount" id="amount" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['amount_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['amount']); ?>">
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['amount_err']; ?></span>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="date">Ngày: <sup>*</sup></label>
            <input type="date" name="date" id="date" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['date_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"
                value="<?php echo htmlspecialchars($data['date']); ?>">
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['date_err']; ?></span>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="description">Mô tả: <sup>*</sup></label>
            <textarea name="description" id="description" rows="4" style="width: 100%; padding: 8px; border: 1px solid <?php echo !empty($data['description_err']) ? '#dc3545' : '#ccc'; ?>; border-radius: 3px;"><?php echo htmlspecialchars($data['description']); ?></textarea>
            <span style="color: #dc3545; font-size: 0.9em;"><?php echo $data['description_err']; ?></span>
        </div>

        <div class="form-row" style="display: flex; justify-content: space-between; align-items: center;">
            <input type="submit" value="Cập nhật" style="background: #ffc107; color: black; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            <a href="<?php echo BASE_URL; ?>/transaction" style="color: #6c757d; text-decoration: none;">Hủy bỏ</a>
        </div>

    </form>
</div>

<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>