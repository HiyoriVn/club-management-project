<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-3xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/user" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại danh sách
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Chỉnh sửa thành viên</h3>
                <p class="mt-1 text-sm text-gray-500">ID: <?= $user['id'] ?></p>
            </div>
            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                <a href="<?= BASE_URL ?>/user/delete/<?= $user['id'] ?>" onclick="return confirm('Xóa thành viên này?')" class="text-red-600 hover:text-red-800 text-sm font-medium">
                    <ion-icon name="trash-outline" class="align-middle"></ion-icon> Xóa tài khoản
                </a>
            <?php endif; ?>
        </div>

        <form action="<?= BASE_URL ?>/user/update/<?= $user['id'] ?>" method="POST" class="px-4 py-5 sm:p-6 space-y-8">

            <div>
                <h4 class="text-base font-medium text-indigo-600 mb-4 border-b pb-2">Cài đặt tài khoản</h4>
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Email (Login)</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Vai trò hệ thống</label>
                        <select name="system_role" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            <?= ($user['id'] == $_SESSION['user_id']) ? 'disabled' : '' ?>>
                            <option value="member" <?= ($user['system_role'] == 'member') ? 'selected' : '' ?>>Member</option>
                            <option value="subadmin" <?= ($user['system_role'] == 'subadmin') ? 'selected' : '' ?>>Sub-Admin</option>
                            <option value="admin" <?= ($user['system_role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                            <input type="hidden" name="system_role" value="<?= $user['system_role'] ?>">
                            <p class="mt-1 text-xs text-gray-500">Bạn không thể tự thay đổi vai trò của chính mình.</p>
                        <?php endif; ?>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Đổi mật khẩu mới</label>
                        <input type="password" name="new_password" placeholder="Để trống nếu không muốn thay đổi"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50">
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-base font-medium text-indigo-600 mb-4 border-b pb-2">Thông tin hồ sơ</h4>
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Giới tính</label>
                        <select name="gender" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="male" <?= ($user['gender'] == 'male') ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= ($user['gender'] == 'female') ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= ($user['gender'] == 'other') ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Ngày sinh</label>
                        <input type="date" name="dob" value="<?= $user['dob'] ?? '' ?>"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea name="bio" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-5 border-t">
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <ion-icon name="save-outline" class="mr-2 text-lg"></ion-icon>
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>