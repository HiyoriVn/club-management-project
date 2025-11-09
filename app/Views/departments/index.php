<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="flex justify-end mb-5">
    <a href="<?php echo BASE_URL; ?>/department/create"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
        + Thêm Ban mới
    </a>
</div>

<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            <?php echo $data['title']; ?> (<?php echo count($data['departments']); ?>)
        </h3>
    </div>
    <div class="border-t border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Ban</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ban cha (ID)</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Hành động</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($data['departments'])) : ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Chưa có Ban nào.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($data['departments'] as $dep) : ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $dep['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($dep['NAME']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($dep['description']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $dep['parent_id'] ?? 'N/A'; ?></td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <a href="<?php echo BASE_URL; ?>/department/edit/<?php echo $dep['id']; ?>" class="text-yellow-600 hover:text-yellow-900">Sửa</a>

                                <form action="<?php echo BASE_URL; ?>/department/destroy/<?php echo $dep['id']; ?>" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa Ban [<?php echo htmlspecialchars(addslashes($dep['NAME'])); ?>]? Hành động này không thể hoàn tác!');">
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