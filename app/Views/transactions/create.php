<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-2xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/transaction" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại danh sách
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Tạo phiếu Thu / Chi mới</h3>
        </div>

        <form action="<?= BASE_URL ?>/transaction/create" method="POST" class="px-4 py-5 sm:p-6 space-y-6">

            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Loại giao dịch <span class="text-red-500">*</span></label>
                    <select name="type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="income">Thu (Income)</option>
                        <option value="expense">Chi (Expense)</option>
                    </select>
                </div>

                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Ngày giao dịch <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Số tiền (VNĐ) <span class="text-red-500">*</span></label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">₫</span>
                    </div>
                    <input type="number" name="amount" min="0" step="1000" required placeholder="0"
                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">VNĐ</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Mô tả chi tiết <span class="text-red-500">*</span></label>
                <textarea name="description" rows="3" required placeholder="VD: Thu quỹ tháng 10, Mua nước uống sự kiện..."
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <ion-icon name="save-outline" class="mr-2 text-lg"></ion-icon>
                    Lưu giao dịch
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>