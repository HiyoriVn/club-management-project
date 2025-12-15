<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-4xl mx-auto">

    <div class="bg-white shadow rounded-lg mb-6 overflow-hidden">
        <div class="bg-indigo-600 h-32"></div>
        <div class="px-4 py-5 sm:px-6 relative">
            <div class="-mt-16 mb-4 flex items-end">
                <div class="h-24 w-24 rounded-full ring-4 ring-white bg-white flex items-center justify-center shadow-lg">
                    <span class="text-3xl font-bold text-indigo-600"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                </div>
                <div class="ml-4 mb-1">
                    <h3 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($user['name']) ?></h3>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>

            <div class="flex space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <?= ucfirst($user['system_role']) ?>
                </span>
                <?php if (!$user['is_active']): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Đã khóa
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($is_own_profile): ?>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                    <ion-icon name="settings-outline" class="mr-2"></ion-icon>
                    Cập nhật thông tin
                </h3>
            </div>

            <form action="<?= BASE_URL ?>/user/update_profile" method="POST" class="px-4 py-5 sm:p-6 space-y-6">

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Ngày sinh</label>
                        <input type="date" name="dob" value="<?= $user['dob'] ?? '' ?>"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Giới tính</label>
                        <select name="gender" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="male" <?= ($user['gender'] == 'male') ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= ($user['gender'] == 'female') ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= ($user['gender'] == 'other') ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Giới thiệu bản thân (Bio)</label>
                        <textarea name="bio" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <ion-icon name="save-outline" class="mr-2 text-lg"></ion-icon>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="bg-white shadow rounded-lg overflow-hidden px-4 py-5 sm:p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Số điện thoại</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Ngày sinh</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= $user['dob'] ? date('d/m/Y', strtotime($user['dob'])) : 'Chưa cập nhật' ?></dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Địa chỉ</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($user['address'] ?? 'Chưa cập nhật') ?></dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Bio</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?></dd>
                </div>
            </dl>
            <div class="mt-6 border-t pt-4">
                <a href="<?= BASE_URL ?>/user" class="text-indigo-600 hover:text-indigo-900 text-sm">Quay lại danh sách</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>