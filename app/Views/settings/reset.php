<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-2xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/settings" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại Cài đặt
        </a>
    </div>

    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <ion-icon name="alert-circle" class="h-5 w-5 text-red-500"></ion-icon>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    Đây là khu vực nguy hiểm. Các hành động tại đây không thể hoàn tác.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-red-100">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 text-red-600 flex items-center">
                <ion-icon name="nuclear-outline" class="mr-2"></ion-icon> Reset Dữ liệu Hệ thống
            </h3>
            <p class="mt-1 text-sm text-gray-500">Xóa toàn bộ dữ liệu mẫu, reset về trạng thái ban đầu.</p>
        </div>

        <form action="<?= BASE_URL ?>/settings/reset_execute" method="POST" class="px-4 py-5 sm:p-6" onsubmit="return confirm('CẢNH BÁO CUỐI CÙNG: Bạn có chắc chắn muốn xóa toàn bộ dữ liệu không?');">

            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="confirm_reset" name="confirm_reset" type="checkbox" required class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="confirm_reset" class="font-medium text-gray-700">Tôi hiểu rằng hành động này sẽ xóa vĩnh viễn dữ liệu.</label>
                        <p class="text-gray-500">Bao gồm: Dự án, Tasks, Giao dịch, Thông báo (Giữ lại tài khoản Admin).</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="backup_first" name="backup_first" type="checkbox" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="backup_first" class="font-medium text-gray-700">Tạo bản sao lưu trước khi reset (Khuyên dùng)</label>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nhập mật khẩu Admin để xác nhận</label>
                <input type="password" name="admin_password" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm max-w-xs">
            </div>

            <div class="mt-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 w-full sm:w-auto">
                    Xác nhận Reset Hệ thống
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>