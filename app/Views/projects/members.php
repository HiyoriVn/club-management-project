<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="<?= BASE_URL ?>/project/detail/<?= $project['id'] ?>" class="text-gray-500 hover:text-gray-700 flex items-center mb-1">
                <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại dự án
            </a>
            <h2 class="text-2xl font-bold text-gray-900">Thành viên: <?= htmlspecialchars($project['name']) ?></h2>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="md:col-span-2 bg-white shadow rounded-lg overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between">
                <h3 class="font-medium text-gray-700">Danh sách thành viên (<?= count($members) ?>)</h3>
            </div>

            <ul class="divide-y divide-gray-100">
                <?php if (empty($members)): ?>
                    <li class="px-6 py-8 text-center text-gray-500">Chưa có thành viên nào.</li>
                <?php else: ?>
                    <?php foreach ($members as $mem): ?>
                        <li class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold mr-3">
                                    <?= strtoupper(substr($mem['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($mem['name']) ?></div>
                                    <div class="text-xs text-gray-500"><?= $mem['email'] ?></div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $mem['project_role'] == 'Leader' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= $mem['project_role'] ?>
                                </span>

                                <?php if (isset($can_manage) && $can_manage && $mem['project_role'] !== 'Leader'): ?>
                                    <form action="<?= BASE_URL ?>/project/members/<?= $project['id'] ?>" method="POST" onsubmit="return confirm('Xóa thành viên này khỏi dự án?');">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="user_id" value="<?= $mem['user_id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Xóa">
                                            <ion-icon name="close-circle-outline" class="text-xl"></ion-icon>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <?php if (isset($can_manage) && $can_manage): ?>
            <div class="md:col-span-1">
                <div class="bg-white shadow rounded-lg p-6 border border-gray-200 sticky top-6">
                    <h3 class="font-bold text-gray-900 mb-4">Thêm thành viên</h3>

                    <form action="<?= BASE_URL ?>/project/members/<?= $project['id'] ?>" method="POST">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chọn User</label>
                            <select name="user_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">-- Chọn thành viên --</option>
                                <?php foreach ($available_users as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= $u['email'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Chỉ hiện user chưa tham gia.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                            <select name="role" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="Member">Thành viên (Member)</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full btn btn-primary justify-center">
                            <ion-icon name="add-outline" class="mr-1"></ion-icon> Thêm vào dự án
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>