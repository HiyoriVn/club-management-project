<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<!-- Tab Navigation -->
<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8">
        <a href="<?php echo BASE_URL; ?>/department?tab=departments"
            class="<?php echo ($data['active_tab'] == 'departments') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <ion-icon name="git-network-outline" class="inline-block h-5 w-5 -mt-1 mr-2"></ion-icon>
            Danh sách Ban
        </a>
        <a href="<?php echo BASE_URL; ?>/department?tab=roles"
            class="<?php echo ($data['active_tab'] == 'roles') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
            <ion-icon name="key-outline" class="inline-block h-5 w-5 -mt-1 mr-2"></ion-icon>
            Danh sách Vai trò
        </a>
    </nav>
</div>

<?php if ($data['active_tab'] == 'departments'): ?>
    <!-- ========== TAB 1: DEPARTMENTS ========== -->

    <div class="flex justify-end mb-5">
        <a href="<?php echo BASE_URL; ?>/department/createDepartment" class="btn btn-primary">
            <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
            Thêm Ban mới
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Danh sách Ban (<?php echo count($data['departments']); ?>)
            </h3>
        </div>
        <div class="border-t border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">STT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên Ban</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ban cha</th>
                        <th class="relative px-6 py-3">
                            <span class="sr-only">Hành động</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($data['departments'])): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Chưa có Ban nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $stt = 1; ?>
                        <?php foreach ($data['departments'] as $dep): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $stt; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($dep['NAME']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo htmlspecialchars(substr($dep['description'], 0, 100)); ?>...
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo $dep['parent_id'] ?? 'N/A'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    <a href="<?php echo BASE_URL; ?>/department/editDepartment/<?php echo $dep['id']; ?>"
                                        class="btn-action btn-warning">Sửa</a>

                                    <form action="<?php echo BASE_URL; ?>/department/destroyDepartment/<?php echo $dep['id']; ?>"
                                        method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit" class="btn-action btn-danger"
                                            onclick="return confirm('Bạn có chắc muốn xóa Ban này?');">
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

<?php else: ?>
    <!-- ========== TAB 2: ROLES ========== -->

    <div class="flex justify-end mb-5">
        <a href="<?php echo BASE_URL; ?>/department/createRole" class="btn btn-primary">
            <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
            Thêm Vai trò mới
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Danh sách Vai trò (<?php echo count($data['roles']); ?>)
            </h3>
        </div>
        <div class="border-t border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">STT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên Vai trò</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày tạo</th>
                        <th class="relative px-6 py-3">
                            <span class="sr-only">Hành động</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($data['roles'])): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Chưa có Vai trò nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $stt = 1; ?>
                        <?php foreach ($data['roles'] as $role): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $stt; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($role['NAME']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo date('d/m/Y', strtotime($role['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    <a href="<?php echo BASE_URL; ?>/department/editRole/<?php echo $role['id']; ?>"
                                        class="btn-action btn-warning">Sửa</a>

                                    <form action="<?php echo BASE_URL; ?>/department/destroyRole/<?php echo $role['id']; ?>"
                                        method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit" class="btn-action btn-danger"
                                            onclick="return confirm('Bạn có chắc muốn xóa Vai trò này?');">
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
<?php endif; ?>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>