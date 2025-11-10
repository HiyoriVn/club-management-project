<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="flex justify-end mb-5">
    <a href="<?php echo BASE_URL; ?>/departmentrole/create"
        class="btn btn-primary">
        <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
        Thêm Vai trò mới
    </a>
</div>

<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            <?php echo $data['title']; ?> (<?php echo count($data['roles']); ?>)
        </h3>
    </div>
    <div class="border-t border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Vai trò</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Hành động</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($data['roles'])) : ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Chưa có Vai trò nào.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php $stt = 1; ?>
                    <?php foreach ($data['roles'] as $role) : ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $stt; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($role['NAME']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo date('d/m/Y', strtotime($role['created_at'])); ?></td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <a href="<?php echo BASE_URL; ?>/departmentrole/edit/<?php echo $role['id']; ?>" class="btn-action btn-warning">Sửa</a>

                                <form action="<?php echo BASE_URL; ?>/departmentrole/destroy/<?php echo $role['id']; ?>" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit" class="btn-action btn-danger"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa Vai trò [<?php echo htmlspecialchars(addslashes($role['NAME'])); ?>]?');">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php $stt++; ?>
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