<?php
// Nạp header MỚI (đã có layout sidebar)
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl text-gray-700">Tổng quan về Quỹ</h2>
    <a href="<?php echo BASE_URL; ?>/transaction/create"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
        Thêm Giao dịch
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <ion-icon name="arrow-down-outline" class="h-6 w-6 text-green-500"></ion-icon>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Tổng Thu
                        </dt>
                        <dd class="text-2xl font-bold text-green-600">
                            <?php echo number_format($data['totals']->income, 0, ',', '.'); ?> đ
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <ion-icon name="arrow-up-outline" class="h-6 w-6 text-red-500"></ion-icon>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Tổng Chi
                        </dt>
                        <dd class="text-2xl font-bold text-red-600">
                            <?php echo number_format($data['totals']->expense, 0, ',', '.'); ?> đ
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <ion-icon name="wallet-outline" class="h-6 w-6 text-blue-500"></ion-icon>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Số Dư Hiện Tại
                        </dt>
                        <dd class="text-2xl font-bold text-blue-600">
                            <?php echo number_format($data['totals']->balance, 0, ',', '.'); ?> đ
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Chi tiết Giao dịch
        </h3>
    </div>
    <div class="border-t border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người tạo</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Hành động</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($data['transactions'])) : ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Chưa có giao dịch nào.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($data['transactions'] as $tx) : ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo date('d/m/Y', strtotime($tx['date'])); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($tx['description']); ?></td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if ($tx['type'] == 'income') : ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        + <?php echo number_format($tx['amount'], 0, ',', '.'); ?> đ
                                    </span>
                                <?php else : ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        - <?php echo number_format($tx['amount'], 0, ',', '.'); ?> đ
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($tx['creator_name']); ?></td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <a href="<?php echo BASE_URL; ?>/transaction/edit/<?php echo $tx['id']; ?>" class="text-yellow-600 hover:text-yellow-900">Sửa</a>

                                <form action="<?php echo BASE_URL; ?>/transaction/destroy/<?php echo $tx['id']; ?>" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900"
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
</div>


<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>