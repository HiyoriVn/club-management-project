<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/user" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại danh sách
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-32"></div>
        <div class="px-6 py-4 relative flex items-end">
            <div class="-mt-16 border-4 border-white rounded-full overflow-hidden bg-white shadow-lg">
                <div class="h-24 w-24 flex items-center justify-center bg-gray-200 text-gray-500 text-3xl font-bold">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
            </div>
            <div class="ml-4 mb-1 flex-1">
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($user['name']) ?></h2>
                        <p class="text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <?php if ($_SESSION['user_role'] == 'admin'): ?>
                        <div class="flex space-x-3">
                            <a href="<?= BASE_URL ?>/user/edit/<?= $user['id'] ?>" class="btn btn-sm btn-secondary-outline">
                                <ion-icon name="create-outline" class="mr-1"></ion-icon> Sửa
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Thông tin tài khoản</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex justify-between">
                        <span class="text-gray-500">Vai trò:</span>
                        <span class="font-medium bg-gray-100 px-2 py-0.5 rounded text-gray-800"><?= ucfirst($user['system_role']) ?></span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-500">Trạng thái:</span>
                        <span class="font-medium text-green-600">Đang hoạt động</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-500">Ngày tham gia:</span>
                        <span class="font-medium"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Hồ sơ cá nhân</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 uppercase">Số điện thoại</label>
                        <p class="font-medium text-gray-900 mt-1"><?= !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Chưa cập nhật' ?></p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 uppercase">Giới tính</label>
                        <p class="font-medium text-gray-900 mt-1"><?= ucfirst($user['gender'] ?? 'Khác') ?></p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 uppercase">Ngày sinh</label>
                        <p class="font-medium text-gray-900 mt-1"><?= !empty($user['dob']) ? date('d/m/Y', strtotime($user['dob'])) : 'Chưa cập nhật' ?></p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs text-gray-500 uppercase">Địa chỉ</label>
                        <p class="font-medium text-gray-900 mt-1"><?= !empty($user['address']) ? htmlspecialchars($user['address']) : 'Chưa cập nhật' ?></p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs text-gray-500 uppercase">Giới thiệu (Bio)</label>
                        <p class="font-medium text-gray-900 mt-1 bg-gray-50 p-3 rounded text-sm">
                            <?= !empty($user['bio']) ? nl2br(htmlspecialchars($user['bio'])) : 'Không có thông tin giới thiệu.' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>