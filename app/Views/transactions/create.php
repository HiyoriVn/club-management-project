<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="bg-white shadow rounded-lg overflow-hidden max-w-3xl mx-auto">

    <form action="<?php echo BASE_URL; ?>/transaction/store" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="p-6">

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Loại Giao dịch: <sup>*</sup>
                </label>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input id="type_expense" name="type" type="radio" value="expense"
                            class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                            <?php echo ($data['type'] == 'expense') ? 'checked' : ''; ?>>
                        <label for="type_expense" class="ml-3 block text-sm font-medium text-red-700">
                            Khoản CHI
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="type_income" name="type" type="radio" value="income"
                            class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300"
                            <?php echo ($data['type'] == 'income') ? 'checked' : ''; ?>>
                        <label for="type_income" class="ml-3 block text-sm font-medium text-green-700">
                            Khoản THU
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                    Số tiền: <sup>*</sup>
                </label>
                <input type="number" name="amount" id="amount"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm <?php echo !empty($data['amount_err']) ? 'border-red-500' : ''; ?>"
                    value="<?php echo htmlspecialchars($data['amount'] ?? ''); ?>">
                <span class="text-red-600 text-sm"><?php echo $data['amount_err']; ?></span>
            </div>

            <div class="mb-5">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                    Ngày: <sup>*</sup>
                </label>
                <input type="date" name="date" id="date"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm <?php echo !empty($data['date_err']) ? 'border-red-500' : ''; ?>"
                    value="<?php echo htmlspecialchars($data['date'] ?? ''); ?>">
                <span class="text-red-600 text-sm"><?php echo $data['date_err']; ?></span>
            </div>

            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Mô tả: <sup>*</sup>
                </label>
                <textarea name="description" id="description" rows="4"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm <?php echo !empty($data['description_err']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                <span class="text-red-600 text-sm"><?php echo $data['description_err']; ?></span>
            </div>

        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
            <a href="<?php echo BASE_URL; ?>/transaction"
                class="text-sm font-medium text-gray-700 hover:text-gray-900">
                Hủy bỏ
            </a>
            <button type="submit"
                class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
                Lưu lại
            </button>
        </div>

    </form>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>