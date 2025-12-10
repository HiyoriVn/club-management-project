<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="max-w-4xl mx-auto">

    <!-- Warning Banner -->
    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <ion-icon name="warning-outline" class="h-5 w-5 text-yellow-400"></ion-icon>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Cảnh báo:</strong> Chỉ Admin mới có quyền thay đổi cài đặt hệ thống. Hãy cẩn thận khi cấu hình.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form action="<?php echo BASE_URL; ?>/settings/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Cài đặt Hệ thống</h3>
                <p class="mt-1 text-sm text-gray-600">Cấu hình thông tin CLB và các tính năng</p>
            </div>

            <!-- Body -->
            <div class="px-6 py-6 space-y-6">

                <!-- Club Information -->
                <div>
                    <h4 class="text-base font-medium text-gray-900 mb-4">Thông tin CLB</h4>

                    <div class="space-y-4">
                        <div>
                            <label for="club_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Tên CLB
                            </label>
                            <input type="text" name="club_name" id="club_name"
                                value="<?php echo htmlspecialchars($data['settings']['club_name']); ?>"
                                class="block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label for="club_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email liên hệ
                            </label>
                            <input type="email" name="club_email" id="club_email"
                                value="<?php echo htmlspecialchars($data['settings']['club_email']); ?>"
                                class="block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label for="club_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Số điện thoại
                            </label>
                            <input type="tel" name="club_phone" id="club_phone"
                                value="<?php echo htmlspecialchars($data['settings']['club_phone']); ?>"
                                class="block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label for="club_address" class="block text-sm font-medium text-gray-700 mb-1">
                                Địa chỉ
                            </label>
                            <textarea name="club_address" id="club_address" rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm"><?php echo htmlspecialchars($data['settings']['club_address']); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="text-base font-medium text-gray-900 mb-4">Tính năng</h4>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="enable_registration" id="enable_registration"
                                    <?php echo $data['settings']['enable_registration'] ? 'checked' : ''; ?>
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="enable_registration" class="font-medium text-gray-700">
                                    Cho phép đăng ký tài khoản
                                </label>
                                <p class="text-gray-500">Người dùng có thể tự đăng ký tài khoản (mặc định là tắt)</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="enable_notifications" id="enable_notifications"
                                    <?php echo $data['settings']['enable_notifications'] ? 'checked' : ''; ?>
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="enable_notifications" class="font-medium text-gray-700">
                                    Bật thông báo hệ thống
                                </label>
                                <p class="text-gray-500">Gửi thông báo cho người dùng về các hoạt động</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="maintenance_mode" id="maintenance_mode"
                                    <?php echo $data['settings']['maintenance_mode'] ? 'checked' : ''; ?>
                                    class="h-4 w-4 text-red-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="maintenance_mode" class="font-medium text-gray-700">
                                    Chế độ bảo trì
                                </label>
                                <p class="text-gray-500">Chặn tất cả người dùng (trừ admin) truy cập hệ thống</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="pt-6 border-t border-red-200">
                    <h4 class="text-base font-medium text-red-600 mb-4">Khu vực Nguy hiểm</h4>

                    <div class="bg-red-50 rounded-lg p-4">
                        <p class="text-sm text-red-700 mb-3">
                            <strong>Reset hệ thống:</strong> Xóa tất cả dữ liệu (sự kiện, dự án, thành viên...), chỉ giữ lại tài khoản admin hiện tại.
                        </p>
                        <a href="<?php echo BASE_URL; ?>/settings/reset"
                            class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                            <ion-icon name="trash-outline" class="mr-2 h-4 w-4"></ion-icon>
                            Reset Hệ thống
                        </a>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-4">
                <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-secondary-outline">
                    Hủy bỏ
                </a>
                <button type="submit" class="btn btn-success">
                    <ion-icon name="save-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
                    Lưu cài đặt
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>