<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <ion-icon name="briefcase-outline" class="mr-2 text-indigo-600"></ion-icon>
                Danh sách Dự án
            </h2>
            <p class="text-sm text-gray-500 mt-1">Quản lý các đầu việc và tiến độ hoạt động.</p>
        </div>

        <?php if (in_array($_SESSION['user_role'], ['admin', 'subadmin'])): ?>
            <div class="mt-4 md:mt-0">
                <a href="<?= BASE_URL ?>/project/create" class="btn btn-primary shadow-sm">
                    <ion-icon name="add-outline" class="mr-1 text-lg"></ion-icon> Tạo mới
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <ul class="divide-y divide-gray-200">
            <?php if (empty($projects)): ?>
                <li class="px-6 py-12 text-center text-gray-500 flex flex-col items-center">
                    <div class="bg-gray-100 p-4 rounded-full mb-3">
                        <ion-icon name="folder-open-outline" class="text-3xl text-gray-400"></ion-icon>
                    </div>
                    <p>Chưa có dự án nào được tạo.</p>
                </li>
            <?php else: ?>
                <?php foreach ($projects as $item): ?>
                    <?php
                    // Kiểm tra nếu là thành viên thì hiện thêm nút tắt vào Task
                    $isJoined = in_array($item['id'], $joined_projects ?? []) || in_array($_SESSION['user_role'], ['admin', 'subadmin']);
                    ?>

                    <li class="group hover:bg-gray-50 transition duration-150 ease-in-out">
                        <div class="px-6 py-5 flex items-center justify-between">

                            <a href="<?= BASE_URL ?>/project/detail/<?= $item['id'] ?>" class="flex items-center min-w-0 flex-1 mr-4 group-hover:text-indigo-600 transition-colors">
                                <div class="flex-shrink-0 h-12 w-12 rounded-lg flex items-center justify-center <?= $item['type'] == 'event' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' ?>">
                                    <ion-icon name="<?= $item['type'] == 'event' ? 'calendar' : 'briefcase' ?>" class="text-2xl"></ion-icon>
                                </div>

                                <div class="ml-4 flex-1">
                                    <div class="flex items-center">
                                        <h3 class="text-lg font-bold text-gray-900 truncate mr-2">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </h3>

                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $item['type'] == 'event' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                            <?= $item['type'] == 'event' ? 'Sự kiện' : 'Dự án' ?>
                                        </span>

                                        <?php
                                        $stt = $item['status'];
                                        $sttClass = $stt == 'planning' ? 'bg-yellow-100 text-yellow-800' : ($stt == 'in_progress' ? 'bg-blue-100 text-blue-800' : ($stt == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'));
                                        $sttLabel = $stt == 'planning' ? 'Lên kế hoạch' : ($stt == 'in_progress' ? 'Đang chạy' : ($stt == 'completed' ? 'Hoàn thành' : 'Đã hủy'));
                                        ?>
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $sttClass ?>">
                                            <?= $sttLabel ?>
                                        </span>
                                    </div>

                                    <div class="mt-1 flex items-center text-sm text-gray-500 space-x-4">
                                        <div class="flex items-center" title="Leader">
                                            <ion-icon name="person-circle-outline" class="mr-1 text-gray-400"></ion-icon>
                                            <?= htmlspecialchars($item['leader_name'] ?? 'Chưa có') ?>
                                        </div>
                                        <div class="flex items-center">
                                            <ion-icon name="people-outline" class="mr-1 text-gray-400"></ion-icon>
                                            <?= $item['member_count'] ?? 0 ?> thành viên
                                        </div>
                                        <div class="flex items-center hidden sm:flex">
                                            <ion-icon name="time-outline" class="mr-1 text-gray-400"></ion-icon>
                                            <?= $item['end_date'] ? date('d/m/Y', strtotime($item['end_date'])) : '∞' ?>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <div class="flex items-center space-x-2">

                                <?php if ($isJoined): ?>
                                    <a href="<?= BASE_URL ?>/task?project_id=<?= $item['id'] ?>" class="hidden sm:inline-flex items-center px-3 py-1.5 border border-indigo-600 text-xs font-medium rounded text-indigo-600 bg-white hover:bg-indigo-50 transition-colors" title="Vào bảng công việc">
                                        <ion-icon name="clipboard-outline" class="mr-1"></ion-icon> Vào bảng
                                        công việc
                                    </a>
                                <?php endif; ?>

                                <?php if (in_array($_SESSION['user_role'], ['admin', 'subadmin'])): ?>
                                    <a href="<?= BASE_URL ?>/project/delete/<?= $item['id'] ?>"
                                        onclick="return confirm('CẢNH BÁO: Xóa dự án sẽ xóa toàn bộ công việc và thành viên liên quan.\nBạn có chắc chắn không?')"
                                        class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors" title="Xóa dự án">
                                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                    </a>
                                <?php endif; ?>

                                <a href="<?= BASE_URL ?>/project/detail/<?= $item['id'] ?>" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition-colors">
                                    <ion-icon name="chevron-forward-outline" class="text-lg"></ion-icon>
                                    Chi tiết
                                </a>
                            </div>

                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>