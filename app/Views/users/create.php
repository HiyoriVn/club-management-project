<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-2xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/user" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại danh sách
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Thêm thành viên mới</h3>
            <p class="mt-1 text-sm text-gray-500">Tạo tài khoản đăng nhập và hồ sơ cơ bản.</p>
        </div>

        <form action="<?= BASE_URL ?>/user/store" method="POST" class="px-4 py-5 sm:p-6 space-y-6">

            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <?php if (!empty($name_err)): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $name_err ?></p>
                    <?php endif; ?>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Email đăng nhập <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <?php if (!empty($email_err)): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $email_err ?></p>
                    <?php endif; ?>
                </div>

                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <?php if (!empty($password_err)): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $password_err ?></p>
                    <?php endif; ?>
                </div>

                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Vai trò hệ thống</label>
                    <select name="system_role" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="member">Thành viên (Member)</option>
                        <option value="subadmin">Phó quản trị (Sub-Admin)</option>
                        <option value="admin">Quản trị viên (Admin)</option>
                    </select>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Thông tin bổ sung (Tùy chọn)</h4>
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                        <input type="text" name="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Giới tính</label>
                        <select name="gender" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="other">Khác</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-5">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <ion-icon name="checkmark-circle-outline" class="mr-2 text-lg"></ion-icon>
                    Tạo người dùng
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>