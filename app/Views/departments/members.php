<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-5xl mx-auto">
    <div class="mb-5 flex justify-between items-center">
        <a href="<?= BASE_URL ?>/department" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại danh sách
        </a>
        <h2 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($department['name']) ?></h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Thành viên hiện tại</h3>
                    <span class="bg-indigo-100 text-indigo-800 py-1 px-3 rounded-full text-xs font-bold"><?= count($members) ?></span>
                </div>

                <ul class="divide-y divide-gray-200">
                    <?php if (empty($members)): ?>
                        <li class="px-4 py-8 text-center text-gray-500">Chưa có thành viên nào trong ban.</li>
                    <?php else: ?>
                        <?php foreach ($members as $mem): ?>
                            <li class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                        <?= strtoupper(substr($mem['name'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($mem['name']) ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($mem['email']) ?></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <?php
                                    $roleBadge = 'bg-gray-100 text-gray-800';
                                    if ($mem['department_role'] == 'Head') $roleBadge = 'bg-red-100 text-red-800';
                                    if ($mem['department_role'] == 'Deputy') $roleBadge = 'bg-yellow-100 text-yellow-800';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $roleBadge ?>">
                                        <?= $mem['department_role'] == 'Head' ? 'Trưởng ban' : ($mem['department_role'] == 'Deputy' ? 'Phó ban' : 'Thành viên') ?>
                                    </span>

                                    <?php if ($_SESSION['user_role'] == 'admin'): ?>
                                        <form action="<?= BASE_URL ?>/department/members/<?= $department['id'] ?>" method="POST" class="inline-block">
                                            <input type="hidden" name="action" value="update_role">
                                            <input type="hidden" name="membership_id" value="<?= $mem['membership_id'] ?>">
                                            <select name="role" onchange="this.form.submit()" class="text-xs border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1">
                                                <option value="Member" <?= $mem['department_role'] == 'Member' ? 'selected' : '' ?>>Member</option>
                                                <option value="Deputy" <?= $mem['department_role'] == 'Deputy' ? 'selected' : '' ?>>Phó ban</option>
                                                <option value="Head" <?= $mem['department_role'] == 'Head' ? 'selected' : '' ?>>Trưởng ban</option>
                                            </select>
                                        </form>

                                        <form action="<?= BASE_URL ?>/department/members/<?= $department['id'] ?>" method="POST" class="inline-block" onsubmit="return confirm('Xóa thành viên này khỏi ban?');">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="membership_id" value="<?= $mem['membership_id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900 ml-2">
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
        </div>

        <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <div class="lg:col-span-1">
                <div class="bg-white shadow sm:rounded-lg overflow-hidden sticky top-6">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Thêm thành viên</h3>
                    </div>
                    <div class="p-4 bg-gray-50">
                        <form action="<?= BASE_URL ?>/department/members/<?= $department['id'] ?>" method="POST">
                            <input type="hidden" name="action" value="add">

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chọn người dùng</label>
                                <select name="user_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Chọn user --</option>
                                    <?php foreach ($available_users as $u): ?>
                                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= $u['email'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Chỉ hiện những người chưa tham gia ban này.</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò trong ban</label>
                                <select name="role" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="Member">Thành viên (Member)</option>
                                    <option value="Deputy">Phó ban (Deputy)</option>
                                    <option value="Head">Trưởng ban (Head)</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                                <ion-icon name="person-add-outline" class="mr-2 text-lg"></ion-icon>
                                Thêm vào ban
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>