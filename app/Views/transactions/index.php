<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <ion-icon name="wallet-outline" class="mr-2 text-indigo-600"></ion-icon>
                Quản lý Tài chính
            </h2>
            <p class="text-sm text-gray-500 mt-1">Theo dõi dòng tiền và ngân sách CLB.</p>
        </div>

        <div class="mt-4 md:mt-0 flex space-x-3">
            <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin'): ?>
                <a href="<?= BASE_URL ?>/transaction/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                    <ion-icon name="add-circle-outline" class="mr-2 text-lg"></ion-icon>
                    Tạo phiếu mới
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($total_income) && isset($total_expense)): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Tổng Thu</dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-600">+<?= number_format($total_income) ?> đ</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Tổng Chi</dt>
                    <dd class="mt-1 text-3xl font-semibold text-red-600">-<?= number_format($total_expense) ?> đ</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Số dư hiện tại</dt>
                    <dd class="mt-1 text-3xl font-semibold text-indigo-600"><?= number_format($total_income - $total_expense) ?> đ</dd>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày GD</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền (VNĐ)</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người tạo</th>
                    <?php if ($_SESSION['user_role'] == 'admin'): ?>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Chưa có giao dịch nào được ghi nhận.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $trans): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y', strtotime($trans['date'])) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium"><?= htmlspecialchars($trans['description']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($trans['type'] == 'income'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Thu (Income)
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Chi (Expense)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold <?= $trans['type'] == 'income' ? 'text-green-600' : 'text-red-600' ?>">
                                <?= $trans['type'] == 'income' ? '+' : '-' ?><?= number_format($trans['amount']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($trans['creator_name'] ?? 'N/A') ?>
                            </td>

                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= BASE_URL ?>/transaction/edit/<?= $trans['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                                    <a href="<?= BASE_URL ?>/transaction/delete/<?= $trans['id'] ?>"
                                        onclick="return confirm('Bạn có chắc muốn xóa giao dịch này?')"
                                        class="text-red-600 hover:text-red-900">Xóa</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>