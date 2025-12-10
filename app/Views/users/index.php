<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<!-- Search Bar -->
<div class="mb-6 bg-white p-4 rounded-lg shadow">
    <form method="GET" action="<?php echo BASE_URL; ?>/user" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input
                type="text"
                name="search"
                placeholder="Tìm theo tên hoặc email..."
                value="<?php echo htmlspecialchars($data['search']); ?>"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="w-48">
            <select
                name="role"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Tất cả vai trò</option>
                <option value="member" <?php echo ($data['role_filter'] == 'member') ? 'selected' : ''; ?>>Member</option>
                <option value="subadmin" <?php echo ($data['role_filter'] == 'subadmin') ? 'selected' : ''; ?>>Sub-Admin</option>
                <option value="admin" <?php echo ($data['role_filter'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <ion-icon name="search-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
            Tìm kiếm
        </button>

        <a href="<?php echo BASE_URL; ?>/user" class="btn btn-secondary-outline">
            <ion-icon name="refresh-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
            Xóa bộ lọc
        </a>
    </form>
</div>

<!-- Action Bar -->
<div class="flex justify-between items-center mb-5">
    <div class="text-sm text-gray-600">
        Tìm thấy <strong><?php echo $data['total']; ?></strong> người dùng
    </div>

    <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <a href="<?php echo BASE_URL; ?>/user/create" class="btn btn-primary">
            <ion-icon name="person-add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
            Thêm Người dùng
        </a>
    <?php endif; ?>
</div>

<!-- User Table -->
<div class="bg-white shadow overflow-hidden rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                <th class="relative px-6 py-3">
                    <span class="sr-only">Hành động</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($data['users'])): ?>
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                        Không tìm thấy người dùng nào
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['users'] as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-medium text-sm">
                                            <?php echo strtoupper(substr($user['NAME'], 0, 2)); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($user['NAME']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $role_text = 'Member';
                            $role_class = 'bg-green-100 text-green-800';

                            switch ($user['system_role']) {
                                case 'admin':
                                    $role_text = 'Admin';
                                    $role_class = 'bg-red-100 text-red-800';
                                    break;
                                case 'subadmin':
                                    $role_text = 'Sub-Admin';
                                    $role_class = 'bg-yellow-100 text-yellow-800';
                                    break;
                            }
                            ?>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $role_class; ?>">
                                <?php echo $role_text; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="<?php echo BASE_URL; ?>/user/view/<?php echo $user['id']; ?>"
                                class="text-indigo-600 hover:text-indigo-900">
                                Xem
                            </a>
                            <a href="<?php echo BASE_URL; ?>/user/edit/<?php echo $user['id']; ?>"
                                class="text-blue-600 hover:text-blue-900">
                                Sửa
                            </a>
                            <a href="<?php echo BASE_URL; ?>/user/manage/<?php echo $user['id']; ?>"
                                class="text-green-600 hover:text-green-900">
                                Phân quyền
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($data['total'] > $data['limit']): ?>
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4 rounded-lg">
        <div class="flex-1 flex justify-between sm:hidden">
            <?php if ($data['page'] > 1): ?>
                <a href="?page=<?php echo $data['page'] - 1; ?>&search=<?php echo urlencode($data['search']); ?>&role=<?php echo urlencode($data['role_filter']); ?>"
                    class="btn btn-secondary-outline">Trước</a>
            <?php endif; ?>
            <?php if ($data['page'] * $data['limit'] < $data['total']): ?>
                <a href="?page=<?php echo $data['page'] + 1; ?>&search=<?php echo urlencode($data['search']); ?>&role=<?php echo urlencode($data['role_filter']); ?>"
                    class="btn btn-secondary-outline ml-3">Sau</a>
            <?php endif; ?>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Hiển thị <span class="font-medium"><?php echo min($data['limit'], $data['total']); ?></span>
                    trong tổng số <span class="font-medium"><?php echo $data['total']; ?></span> kết quả
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <?php
                    $total_pages = ceil($data['total'] / $data['limit']);
                    for ($i = 1; $i <= $total_pages; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($data['search']); ?>&role=<?php echo urlencode($data['role_filter']); ?>"
                            class="<?php echo ($i == $data['page']) ? 'bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>