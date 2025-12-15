<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-6xl mx-auto">
    <div class="mb-6 border-b border-gray-200 pb-4">
        <div class="flex items-center justify-between">
            <div>
                <a href="<?= BASE_URL ?>/project/view/<?= $project['id'] ?>" class="text-sm text-gray-500 hover:text-gray-700 mb-1 inline-block">
                    &larr; Quay lại tổng quan dự án
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Quản lý công việc: <?= htmlspecialchars($project['name']) ?></h2>
            </div>
            <div class="flex space-x-2">
                <a href="<?= BASE_URL ?>/task/kanban/<?= $project['id'] ?>" class="btn btn-secondary-outline">
                    <ion-icon name="grid-outline" class="mr-2"></ion-icon> Kanban Board
                </a>
                <a href="<?= BASE_URL ?>/task/create/<?= $project['id'] ?>" class="btn btn-primary">
                    <ion-icon name="add-outline" class="mr-2"></ion-icon> Tạo Task mới
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow-sm border-l-4 border-gray-400">
            <div class="text-gray-500 text-xs uppercase">Backlog</div>
            <div class="text-2xl font-bold"><?= $stats['backlog'] ?? 0 ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow-sm border-l-4 border-yellow-400">
            <div class="text-gray-500 text-xs uppercase">To Do</div>
            <div class="text-2xl font-bold"><?= $stats['todo'] ?? 0 ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow-sm border-l-4 border-blue-400">
            <div class="text-gray-500 text-xs uppercase">In Progress</div>
            <div class="text-2xl font-bold"><?= $stats['in_progress'] ?? 0 ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow-sm border-l-4 border-green-400">
            <div class="text-gray-500 text-xs uppercase">Done</div>
            <div class="text-2xl font-bold"><?= $stats['done'] ?? 0 ?></div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <?php
        // Nếu có dữ liệu tasks truyền vào, hiển thị luôn table
        // Logic này thường dùng chung với app/Views/tasks/list.php
        if (empty($tasks)) {
            echo '<div class="p-8 text-center text-gray-500">Chưa có công việc nào trong dự án này.</div>';
        } else {
            include ROOT_PATH . '/app/Views/tasks/_table_rows.php'; // Hoặc viết thẳng code table vào đây
        }
        ?>
        <?php if (!empty($tasks)): ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Công việc</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người làm</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Sửa</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($tasks as $task): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: <?= $task['color'] ?>"></span>
                                    <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <?= $task['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php if (!empty($task['assignees'])): ?>
                                    <div class="flex -space-x-1 overflow-hidden">
                                        <?php foreach ($task['assignees'] as $u): ?>
                                            <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center text-xs ring-2 ring-white" title="<?= $u['name'] ?>">
                                                <?= substr($u['name'], 0, 1) ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">Chưa gán</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= $task['due_date'] ? date('d/m/Y', strtotime($task['due_date'])) : '-' ?>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="<?= BASE_URL ?>/task/edit/<?= $task['id'] ?>" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>