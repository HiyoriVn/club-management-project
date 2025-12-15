<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto">
    <div class="bg-indigo-600 rounded-lg shadow-lg mb-8 p-6 sm:p-10 text-white relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-3xl font-bold mb-2">Xin chào, <?= htmlspecialchars($_SESSION['user_name']) ?>! 👋</h1>
            <p class="text-indigo-100">Chào mừng bạn trở lại hệ thống quản lý CLB. Hôm nay bạn muốn làm gì?</p>
            <div class="mt-6 flex space-x-3">
                <a href="<?= BASE_URL ?>/project" class="bg-white text-indigo-600 px-4 py-2 rounded-md font-medium hover:bg-gray-100 transition">
                    Xem Dự án
                </a>
                <a href="<?= BASE_URL ?>/announcement" class="bg-indigo-500 bg-opacity-50 text-white px-4 py-2 rounded-md font-medium hover:bg-opacity-75 transition">
                    Đọc Thông báo
                </a>
            </div>
        </div>
        <div class="absolute right-0 top-0 h-full w-1/3 bg-white opacity-10 transform skew-x-12 translate-x-10"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <ion-icon name="people" class="text-3xl text-blue-500"></ion-icon>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Tổng thành viên</dt>
                        <dd class="text-2xl font-bold text-gray-900"><?= $total_users ?? 0 ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <ion-icon name="briefcase" class="text-3xl text-green-500"></ion-icon>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Dự án đang chạy</dt>
                        <dd class="text-2xl font-bold text-gray-900"><?= $active_projects ?? 0 ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <ion-icon name="list" class="text-3xl text-yellow-500"></ion-icon>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Công việc của tôi</dt>
                        <dd class="text-2xl font-bold text-gray-900"><?= $my_tasks_count ?? 0 ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <ion-icon name="wallet" class="text-3xl text-purple-500"></ion-icon>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Quỹ CLB (Ước tính)</dt>
                        <dd class="text-lg font-bold text-gray-900"><?= number_format($budget_balance ?? 0) ?> đ</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Công việc cần làm</h3>
                    <a href="<?= BASE_URL ?>/project" class="text-sm text-indigo-600 hover:text-indigo-800">Đi tới Dự án &rarr;</a>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if (empty($my_recent_tasks)): ?>
                        <div class="p-6 text-center text-gray-500">Bạn không có công việc nào cần làm ngay. Tuyệt vời!</div>
                    <?php else: ?>
                        <?php foreach ($my_recent_tasks as $task): ?>
                            <div class="p-4 hover:bg-gray-50 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="mr-3">
                                        <span class="block w-2 h-2 rounded-full" style="background-color: <?= $task['color'] ?? '#ccc' ?>"></span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></h4>
                                        <p class="text-xs text-gray-500">Hạn chót: <?= $task['due_date'] ? date('d/m', strtotime($task['due_date'])) : 'Không' ?></p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    <?= $task['status'] ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div class="space-y-8">

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Thông báo mới</h3>
                </div>
                <div class="p-4">
                    <?php if (empty($recent_announcements)): ?>
                        <p class="text-gray-500 text-sm">Không có thông báo mới.</p>
                    <?php else: ?>
                        <ul class="space-y-4">
                            <?php foreach ($recent_announcements as $news): ?>
                                <li>
                                    <a href="<?= BASE_URL ?>/announcement/view/<?= $news['id'] ?>" class="group block">
                                        <h4 class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition">
                                            <?= htmlspecialchars($news['title']) ?>
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1"><?= date('d/m/Y', strtotime($news['created_at'])) ?></p>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <a href="<?= BASE_URL ?>/announcement" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Xem tất cả</a>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Phím tắt</h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="<?= BASE_URL ?>/project/create" class="flex flex-col items-center justify-center p-3 border rounded hover:bg-gray-50 text-center">
                        <ion-icon name="add-circle" class="text-2xl text-green-500 mb-1"></ion-icon>
                        <span class="text-xs text-gray-700">Tạo Dự án</span>
                    </a>
                    <a href="<?= BASE_URL ?>/file" class="flex flex-col items-center justify-center p-3 border rounded hover:bg-gray-50 text-center">
                        <ion-icon name="cloud-upload" class="text-2xl text-blue-500 mb-1"></ion-icon>
                        <span class="text-xs text-gray-700">Upload File</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>