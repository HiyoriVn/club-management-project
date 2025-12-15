<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            <ion-icon name="settings-outline" class="mr-2 text-indigo-600"></ion-icon>
            Cài đặt hệ thống
        </h2>
        <p class="text-sm text-gray-500 mt-1">Quản lý cấu hình chung và bảo mật tài khoản.</p>
    </div>

    <div class="grid grid-cols-1 gap-6">

        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Đổi mật khẩu</h3>
            </div>
            <div class="p-6">
                <form action="<?= BASE_URL ?>/auth/update_password" method="POST">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mật khẩu mới</label>
                            <input type="password" name="new_password" required minlength="6"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" required minlength="6"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Cấu hình chung (Admin)</h3>
                </div>
                <div class="p-6">
                    <form action="<?= BASE_URL ?>/setting/update" method="POST">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tên Câu lạc bộ</label>
                                <input type="text" name="club_name" value="<?= htmlspecialchars($settings['club_name'] ?? 'CLB Hiyorivn') ?>"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email liên hệ hệ thống</label>
                                <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? 'admin@hiyorivn.com') ?>"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="maintenance_mode" name="maintenance_mode" type="checkbox" value="1" <?= (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == 1) ? 'checked' : '' ?>
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="maintenance_mode" class="font-medium text-gray-700">Chế độ bảo trì</label>
                                    <p class="text-gray-500">Khi bật, chỉ Admin mới có thể truy cập hệ thống.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="btn btn-success">
                                Lưu cấu hình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>