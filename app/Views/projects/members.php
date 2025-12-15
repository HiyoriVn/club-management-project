<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-5xl mx-auto">
    <div class="mb-5 flex justify-between items-center">
        <a href="<?= BASE_URL ?>/project/detail/<?= $project['id'] ?>" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại dự án
        </a>
        <h2 class="text-xl font-bold text-gray-800">Thành viên: <?= htmlspecialchars($project['name']) ?></h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Danh sách thành viên</h3>
                    <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-bold"><?= count($members) ?></span>
                </div>

                <ul class="divide-y divide-gray-200">
                    <?php if (empty($members)): ?>
                        <li class="px-4 py-8 text-center text-gray-500">Chưa có thành viên nào.</li>
                    <?php else: ?>
                        <?php foreach ($members as $mem): ?>
                            <li class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                        <?= strtoupper(substr($mem['name'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($mem['name']) ?>
                                            <?php if ($mem['user_id'] == $project['leader_id']): ?>
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Leader</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($mem['email']) ?></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <?= htmlspecialchars($mem['role']) ?>
                                    </span>

                                    <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_id'] == $project['leader_id']): ?>
                                        <?php if ($mem['user_id'] != $project['leader_id']): ?>
                                            <form action="<?= BASE_URL ?>/project/members/<?= $project['id'] ?>" method="POST" class="inline-block" onsubmit="return confirm('Xóa thành viên này?');">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="user_id" value="<?= $mem['user_id'] ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-900 ml-2"><ion-icon name="close-circle-outline" class="text-xl"></ion-icon></button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_id'] == $project['leader_id']): ?>
            <div class="lg:col-span-1">
                <div class="bg-white shadow sm:rounded-lg overflow-hidden sticky top-6">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Thêm thành viên</h3>
                    </div>
                    <div class="p-4 bg-gray-50">
                        <form action="<?= BASE_URL ?>/project/members/<?= $project['id'] ?>" method="POST">
                            <input type="hidden" name="action" value="add">

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chọn thành viên</label>
                                <select name="user_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Chọn người dùng --</option>
                                    <?php foreach ($available_users as $u): ?>
                                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= $u['email'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                                <input type="text" name="role" value="Member" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <button type="submit" class="w-full btn btn-primary justify-center">Thêm vào dự án</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>