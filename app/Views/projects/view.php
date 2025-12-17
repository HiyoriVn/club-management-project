<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="<?= BASE_URL ?>/project" class="text-gray-500 hover:text-gray-700 flex items-center mb-2">
                <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Danh sách dự án
            </a>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <?= htmlspecialchars($project['name']) ?>
            </h1>
        </div>

        <div class="flex space-x-3">
            <?php if ($is_member): ?>
                <a href="<?= BASE_URL ?>/task?project_id=<?= $project['id'] ?>" class="btn btn-primary shadow-lg">
                    <ion-icon name="clipboard-outline" class="mr-1 text-lg"></ion-icon>
                    Vào bảng công việc
                </a>
            <?php endif; ?>

            <?php if (in_array($_SESSION['user_role'], ['admin', 'subadmin']) || $_SESSION['user_id'] == $project['leader_id']): ?>

                <a href="<?= BASE_URL ?>/project/members/<?= $project['id'] ?>" class="btn btn-white border border-gray-300 text-gray-700 hover:bg-gray-50" title="Quản lý thành viên">
                    <ion-icon name="people-outline" class="mr-1 text-lg"></ion-icon> <span class="hidden sm:inline">TV</span>
                </a>

                <a href="<?= BASE_URL ?>/project/edit/<?= $project['id'] ?>" class="btn btn-white border border-gray-300 text-blue-600 hover:bg-blue-50" title="Chỉnh sửa thông tin">
                    <ion-icon name="create-outline" class="text-lg"></ion-icon>
                </a>
                    <a href="<?= BASE_URL ?>/project/delete/<?= $project['id'] ?>"
                        onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này vĩnh viễn?')"
                        class="btn btn-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300" title="Xóa dự án">
                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                    </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                <h3 class="font-bold text-gray-900 mb-4">Thông tin chung</h3>
                <div class="space-y-3 text-sm">
                    <div><span class="text-gray-500">Trạng thái:</span> <span class="ml-2 px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800"><?= $project['status'] ?></span></div>
                    <div><span class="text-gray-500">Leader:</span> <span class="ml-2 font-medium"><?= htmlspecialchars($project['leader_name'] ?? 'N/A') ?></span></div>
                    <div><span class="text-gray-500">Deadline:</span> <span class="ml-2"><?= $project['end_date'] ? date('d/m/Y', strtotime($project['end_date'])) : '∞' ?></span></div>
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-gray-500 mb-2">Mô tả:</p>
                        <p class="text-gray-700 whitespace-pre-line"><?= htmlspecialchars($project['description']) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                <h3 class="font-bold text-gray-900 mb-4">Thành viên (<?= count($members) ?>)</h3>
                <div class="flex -space-x-2 overflow-hidden">
                    <?php foreach (array_slice($members, 0, 8) as $m): ?>
                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center ring-2 ring-white text-xs font-bold" title="<?= $m['name'] ?>">
                            <?= substr($m['name'], 0, 1) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">Danh sách công việc</h3>
                    <span class="text-xs text-gray-500">Tổng: <?= count($tasks) ?></span>
                </div>

                <ul class="divide-y divide-gray-100">
                    <?php if (empty($tasks)): ?>
                        <li class="px-6 py-8 text-center text-gray-500 italic">Chưa có công việc nào.</li>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                            <li class="px-6 py-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></p>
                                        <div class="flex items-center mt-1 space-x-3 text-xs text-gray-500">
                                            <span><ion-icon name="person-outline" class="align-middle"></ion-icon> <?= htmlspecialchars($task['assignee_name'] ?? 'Unassigned') ?></span>
                                            <span><ion-icon name="calendar-outline" class="align-middle"></ion-icon> <?= $task['due_date'] ? date('d/m', strtotime($task['due_date'])) : '--' ?></span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                        <?= $task['status'] == 'completed' ? 'bg-green-100 text-green-700' : ($task['status'] == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') ?>">
                                        <?= $task['status'] ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>