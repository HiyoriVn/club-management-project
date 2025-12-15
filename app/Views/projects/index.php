<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <ion-icon name="briefcase-outline" class="mr-2 text-indigo-600"></ion-icon>
                Quản lý Hoạt động
            </h2>
            <p class="text-sm text-gray-500 mt-1">Danh sách Dự án và Sự kiện của CLB.</p>
        </div>

        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?= BASE_URL ?>/project/create?type=project" class="btn btn-primary">
                <ion-icon name="add-outline" class="mr-1 text-lg"></ion-icon> Dự án mới
            </a>
            <a href="<?= BASE_URL ?>/project/create?type=event" class="btn btn-secondary-outline">
                <ion-icon name="calendar-outline" class="mr-1 text-lg"></ion-icon> Sự kiện mới
            </a>
        </div>
    </div>

    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <a href="<?= BASE_URL ?>/project"
                class="<?= ($current_type == 'all' || !isset($_GET['type'])) ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Tất cả
            </a>
            <a href="<?= BASE_URL ?>/project?type=project"
                class="<?= ($current_type == 'project') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Dự án
            </a>
            <a href="<?= BASE_URL ?>/project?type=event"
                class="<?= ($current_type == 'event') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Sự kiện
            </a>
        </nav>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            <?php if (empty($projects)): ?>
                <li class="px-6 py-10 text-center text-gray-500">
                    <ion-icon name="folder-open-outline" class="text-4xl mb-2 text-gray-300"></ion-icon>
                    <p>Chưa có dữ liệu nào.</p>
                </li>
            <?php else: ?>
                <?php foreach ($projects as $item): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/project/detail/<?= $item['id'] ?>" class="block hover:bg-gray-50 transition duration-150 ease-in-out">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-md flex items-center justify-center <?= $item['type'] == 'event' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' ?>">
                                            <ion-icon name="<?= $item['type'] == 'event' ? 'calendar' : 'briefcase' ?>" class="text-xl"></ion-icon>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-indigo-600 truncate"><?= htmlspecialchars($item['name']) ?></p>
                                            <p class="text-sm text-gray-500">
                                                <?= $item['leader_id'] ? 'Leader: ' . htmlspecialchars($item['leader_name'] ?? 'N/A') : 'Chưa có Leader' ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <?php
                                        $statusColors = [
                                            'planning' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusLabel = [
                                            'planning' => 'Lên kế hoạch',
                                            'in_progress' => 'Đang chạy',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColors[$item['status']] ?? 'bg-gray-100' ?>">
                                            <?= $statusLabel[$item['status']] ?? $item['status'] ?>
                                        </span>
                                        <div class="mt-1 text-xs text-gray-500 flex items-center">
                                            <ion-icon name="time-outline" class="mr-1"></ion-icon>
                                            <?= $item['end_date'] ? date('d/m/Y', strtotime($item['end_date'])) : 'Không thời hạn' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>