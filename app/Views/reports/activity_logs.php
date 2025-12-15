<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <ion-icon name="clipboard-outline" class="mr-2 text-indigo-600"></ion-icon>
                Nhật ký hoạt động
            </h2>
            <p class="text-sm text-gray-500 mt-1">Theo dõi các tác vụ quan trọng đã thực hiện trên hệ thống.</p>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người thực hiện</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi tiết</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            Chưa có dữ liệu nhật ký.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center text-xs text-indigo-700 font-bold mr-2">
                                        <?= strtoupper(substr($log['user_name'] ?? 'S', 0, 1)) ?>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($log['user_name'] ?? 'System') ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= htmlspecialchars($log['details']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-between items-center">
        <div class="text-sm text-gray-700">
            Trang <strong><?= $page ?? 1 ?></strong>
        </div>
        <div class="space-x-2">
            <?php if (($page ?? 1) > 1): ?>
                <a href="?page=<?= ($page ?? 1) - 1 ?>" class="btn btn-secondary-outline text-sm">Trước</a>
            <?php endif; ?>

            <a href="?page=<?= ($page ?? 1) + 1 ?>" class="btn btn-secondary-outline text-sm">Sau</a>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>