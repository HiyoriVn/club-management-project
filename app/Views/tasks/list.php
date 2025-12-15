<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <ion-icon name="list-outline" class="mr-2 text-indigo-600"></ion-icon>
                Danh sách công việc
            </h2>
            <p class="text-sm text-gray-500 mt-1">Dự án: <?= htmlspecialchars($project['name']) ?></p>
        </div>
        <div class="flex space-x-2">
            <a href="<?= BASE_URL ?>/task/kanban/<?= $project['id'] ?>" class="btn btn-secondary-outline">
                <ion-icon name="grid-outline" class="mr-2"></ion-icon> Kanban
            </a>
            <a href="<?= BASE_URL ?>/task/create/<?= $project['id'] ?>?view=list" class="btn btn-primary">
                <ion-icon name="add-outline" class="mr-2"></ion-icon> Tạo mới
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Công việc</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người thực hiện</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hạn chót</th>
                    <th class="relative px-6 py-3"><span class="sr-only">Action</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($tasks)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">Chưa có công việc nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="w-2.5 h-2.5 rounded-full mr-3" style="background-color: <?= $task['color'] ?>"></span>
                                    <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $badges = ['backlog' => 'bg-gray-100 text-gray-800', 'todo' => 'bg-yellow-100 text-yellow-800', 'in_progress' => 'bg-blue-100 text-blue-800', 'done' => 'bg-green-100 text-green-800'];
                                $labels = ['backlog' => 'Backlog', 'todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done'];
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badges[$task['status']] ?>">
                                    <?= $labels[$task['status']] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex -space-x-1 overflow-hidden">
                                    <?php foreach ($task['assignees'] as $u): ?>
                                        <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700 ring-2 ring-white" title="<?= $u['name'] ?>">
                                            <?= substr($u['name'], 0, 1) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= $task['due_date'] ? date('d/m/Y H:i', strtotime($task['due_date'])) : '-' ?>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="<?= BASE_URL ?>/task/edit/<?= $task['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                                <a href="<?= BASE_URL ?>/task/delete/<?= $task['id'] ?>" onclick="return confirm('Xóa task này?')" class="text-red-600 hover:text-red-900">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>