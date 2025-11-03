<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="space-y-6">

    <div class="bg-white shadow rounded-lg p-6">
        <p class="text-lg text-gray-700">
            Chào mừng <strong><?php echo htmlspecialchars($data['user_name']); ?></strong> đã quay trở lại!
        </p>
        <p class="text-gray-600">
            Bạn đang đăng nhập với vai trò:
            <span class="font-medium text-indigo-600"><?php echo htmlspecialchars($data['user_role']); ?></span>
        </p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Nội dung Bảng điều khiển</h2>
        <p class="text-gray-700">Đây là khu vực chỉ dành cho thành viên đã đăng nhập.</p>
    </div>


    <?php if ($data['user_role'] == 'admin' || $data['user_role'] == 'subadmin') : ?>

        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-yellow-900 mb-2">
                Khu vực Quản trị
            </h3>
            <p class="text-yellow-800 mb-4">
                Vì bạn là 'admin' hoặc 'subadmin', bạn sẽ thấy được mục này.
            </p>

            <div class="flex flex-wrap gap-3">
                <a href="#" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                    Quản lý các Ban (Departments)
                </a>
                <a href="#" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                    Quản lý Thành viên
                </a>
                <a href="#" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                    Tạo Hoạt động mới
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>


<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>