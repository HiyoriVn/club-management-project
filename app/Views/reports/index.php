<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            <ion-icon name="bar-chart-outline" class="mr-2 text-indigo-600"></ion-icon>
            Trung tâm Báo cáo
        </h2>
        <p class="text-sm text-gray-500 mt-1">Tổng hợp số liệu thống kê và trích xuất dữ liệu hệ thống.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <a href="<?= BASE_URL ?>/report/activity_logs" class="block group">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200 hover:border-indigo-500 transition-colors h-full">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                        <ion-icon name="clipboard-outline" class="text-2xl"></ion-icon>
                    </div>
                    <ion-icon name="arrow-forward" class="text-gray-300 group-hover:text-indigo-500"></ion-icon>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Nhật ký hoạt động</h3>
                <p class="text-sm text-gray-500">Xem lịch sử đăng nhập, thao tác dữ liệu của thành viên.</p>
            </div>
        </a>

        <a href="<?= BASE_URL ?>/transaction/index" class="block group">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200 hover:border-indigo-500 transition-colors h-full">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                        <ion-icon name="cash-outline" class="text-2xl"></ion-icon>
                    </div>
                    <ion-icon name="arrow-forward" class="text-gray-300 group-hover:text-indigo-500"></ion-icon>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Báo cáo Tài chính</h3>
                <p class="text-sm text-gray-500">Thống kê thu chi, ngân sách tồn dư theo tháng/quý.</p>
            </div>
        </a>
        </div>

    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>