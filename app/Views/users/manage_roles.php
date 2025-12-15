<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-5 flex items-center justify-between">
        <a href="<?= BASE_URL ?>/user" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại
        </a>
        <h2 class="text-xl font-bold text-gray-800">Phân quyền: <?= htmlspecialchars($user['name']) ?></h2>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Vai trò trong các Ban</h3>
            <span class="text-sm text-gray-500">System Role: <strong class="uppercase"><?= $user['system_role'] ?></strong></span>
        </div>

        <div class="p-6">
            <?php if (empty($memberships)): ?>
                <div class="text-center py-8 bg-gray-50 rounded border border-dashed border-gray-300">
                    <p class="text-gray-500">Thành viên này chưa tham gia ban chuyên môn nào.</p>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ban chuyên môn</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vai trò hiện tại</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($memberships as $mem): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($mem['department_name']) ?>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= $mem['role'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <form action="<?= BASE_URL ?>/user/manage_roles/<?= $user['id'] ?>" method="POST" class="inline-flex items-center space-x-2">
                                        <input type="hidden" name="department_id" value="<?= $mem['department_id'] ?>">
                                        <select name="role" class="text-xs border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="Member" <?= $mem['role'] == 'Member' ? 'selected' : '' ?>>Thành viên</option>
                                            <option value="Deputy" <?= $mem['role'] == 'Deputy' ? 'selected' : '' ?>>Phó ban</option>
                                            <option value="Head" <?= $mem['role'] == 'Head' ? 'selected' : '' ?>>Trưởng ban</option>
                                        </select>
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">Lưu</button>
                                    </form>

                                    <a href="<?= BASE_URL ?>/user/remove_from_dept/<?= $user['id'] ?>?dept=<?= $mem['department_id'] ?>"
                                        onclick="return confirm('Xóa thành viên khỏi ban này?')"
                                        class="ml-3 text-red-600 hover:text-red-900 text-xs font-medium">Rời ban</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="mt-8 border-t pt-6">
                <h4 class="text-sm font-bold text-gray-700 mb-3">Thêm vào Ban khác</h4>
                <form action="<?= BASE_URL ?>/user/add_to_dept/<?= $user['id'] ?>" method="POST" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500 mb-1">Chọn Ban</label>
                        <select name="department_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- Chọn ban --</option>
                            <?php foreach ($all_departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-40">
                        <label class="block text-xs text-gray-500 mb-1">Vai trò</label>
                        <select name="role" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="Member">Thành viên</option>
                            <option value="Deputy">Phó ban</option>
                            <option value="Head">Trưởng ban</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>