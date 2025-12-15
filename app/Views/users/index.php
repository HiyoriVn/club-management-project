<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
    <form method="GET" action="<?= BASE_URL ?>/user" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
            <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <ion-icon name="search-outline" class="text-gray-400"></ion-icon>
                </div>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                    placeholder="Tên hoặc email...">
            </div>
        </div>

        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
            <select name="role" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="">-- Tất cả --</option>
                <option value="member" <?= ($role_filter == 'member') ? 'selected' : ''; ?>>Member</option>
                <option value="subadmin" <?= ($role_filter == 'subadmin') ? 'selected' : ''; ?>>Sub-Admin</option>
                <option value="admin" <?= ($role_filter == 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm">
            Lọc dữ liệu
        </button>

        <a href="<?= BASE_URL ?>/user" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
            Reset
        </a>
    </form>
</div>

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-gray-800 flex items-center">
        <ion-icon name="people-outline" class="mr-2 text-indigo-600"></ion-icon>
        Danh sách thành viên
    </h2>

    <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <a href="<?= BASE_URL ?>/user/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none">
            <ion-icon name="add-circle-outline" class="mr-2 text-lg"></ion-icon>
            Thêm mới
        </a>
    <?php endif; ?>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thông tin</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tham gia</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Hành động</span></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                        <ion-icon name="search-outline" class="text-4xl mb-2 text-gray-300"></ion-icon>
                        <p>Không tìm thấy thành viên nào phù hợp.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <span class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                        <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($u['name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($u['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $roleColors = [
                                'admin' => 'bg-red-100 text-red-800',
                                'subadmin' => 'bg-yellow-100 text-yellow-800',
                                'member' => 'bg-green-100 text-green-800'
                            ];
                            $roleClass = $roleColors[$u['system_role']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $roleClass ?>">
                                <?= ucfirst($u['system_role']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?= BASE_URL ?>/user/profile/<?= $u['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Xem hồ sơ">
                                <ion-icon name="eye-outline" class="text-lg align-middle"></ion-icon>
                            </a>

                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                                <a href="<?= BASE_URL ?>/user/edit/<?= $u['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Sửa">
                                    <ion-icon name="create-outline" class="text-lg align-middle"></ion-icon>
                                </a>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="<?= BASE_URL ?>/user/delete/<?= $u['id'] ?>"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa thành viên này? Hành động này không thể hoàn tác!');"
                                        class="text-red-600 hover:text-red-900" title="Xóa">
                                        <ion-icon name="trash-outline" class="text-lg align-middle"></ion-icon>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4 flex justify-between items-center">
    <div class="text-sm text-gray-700">
        Hiển thị trang <strong><?= $page ?></strong>
    </div>
    <div class="space-x-2">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>" class="px-3 py-1 border rounded hover:bg-gray-100 text-sm">Trước</a>
        <?php endif; ?>

        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>" class="px-3 py-1 border rounded hover:bg-gray-100 text-sm">Sau</a>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>