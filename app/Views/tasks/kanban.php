<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="h-[calc(100vh-140px)] flex flex-col">
    <div class="flex justify-between items-center mb-4 px-4 sm:px-0">
        <div>
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <ion-icon name="grid-outline" class="mr-2 text-indigo-600"></ion-icon>
                Kanban: <?= htmlspecialchars($project['name']) ?>
            </h2>
            <a href="<?= BASE_URL ?>/project/detail/<?= $project['id'] ?>" class="text-xs text-gray-500 hover:text-indigo-600">&larr; Quay lại dự án</a>
        </div>
        <div class="flex space-x-2">
            <a href="<?= BASE_URL ?>/task/list/<?= $project['id'] ?>" class="btn btn-sm btn-secondary-outline">
                <ion-icon name="list-outline" class="mr-1"></ion-icon> List
            </a>
            <a href="<?= BASE_URL ?>/task/create/<?= $project['id'] ?>?view=kanban" class="btn btn-sm btn-primary">
                <ion-icon name="add-outline" class="mr-1"></ion-icon> Thêm Task
            </a>
        </div>
    </div>

    <div class="flex-1 overflow-x-auto overflow-y-hidden pb-4">
        <div class="flex h-full space-x-4 px-4 sm:px-0 min-w-max">
            <?php
            $columns = [
                'backlog' => ['label' => 'Backlog', 'color' => 'border-gray-300', 'bg' => 'bg-gray-50'],
                'todo' => ['label' => 'To Do', 'color' => 'border-yellow-400', 'bg' => 'bg-yellow-50'],
                'in_progress' => ['label' => 'In Progress', 'color' => 'border-blue-400', 'bg' => 'bg-blue-50'],
                'done' => ['label' => 'Done', 'color' => 'border-green-400', 'bg' => 'bg-green-50']
            ];
            ?>

            <?php foreach ($columns as $status => $col): ?>
                <div class="w-80 flex flex-col h-full bg-gray-100 rounded-lg shadow-sm border-t-4 <?= $col['color'] ?>">
                    <div class="p-3 font-bold text-gray-700 flex justify-between items-center bg-white rounded-t-sm">
                        <?= $col['label'] ?>
                        <span class="bg-gray-200 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                            <?= count($tasks[$status] ?? []) ?>
                        </span>
                    </div>

                    <div class="flex-1 p-2 overflow-y-auto space-y-2 kanban-col" data-status="<?= $status ?>">
                        <?php if (!empty($tasks[$status])): ?>
                            <?php foreach ($tasks[$status] as $task): ?>
                                <div class="bg-white p-3 rounded shadow-sm cursor-move hover:shadow-md transition-shadow border-l-4"
                                    style="border-left-color: <?= $task['color'] ?>;"
                                    data-id="<?= $task['id'] ?>">
                                    <div class="flex justify-between items-start">
                                        <h4 class="text-sm font-medium text-gray-900 mb-1 leading-tight">
                                            <a href="<?= BASE_URL ?>/task/edit/<?= $task['id'] ?>" class="hover:text-indigo-600 block"><?= htmlspecialchars($task['title']) ?></a>
                                        </h4>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <div class="flex -space-x-1 overflow-hidden">
                                            <?php foreach (array_slice($task['assignees'], 0, 3) as $u): ?>
                                                <div class="h-6 w-6 rounded-full bg-gray-300 flex items-center justify-center text-xs ring-2 ring-white" title="<?= $u['name'] ?>">
                                                    <?= substr($u['name'], 0, 1) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <?php if ($task['due_date']): ?>
                                            <span class="text-xs <?= (strtotime($task['due_date']) < time()) ? 'text-red-500 font-bold' : 'text-gray-400' ?>">
                                                <?= date('d/m', strtotime($task['due_date'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const columns = document.querySelectorAll('.kanban-col');
        columns.forEach(col => {
            new Sortable(col, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'opacity-50',
                onEnd: function(evt) {
                    const taskId = evt.item.getAttribute('data-id');
                    const newStatus = evt.to.getAttribute('data-status');

                    // Call API
                    fetch('<?= BASE_URL ?>/task/update_status/' + taskId, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert('Lỗi cập nhật!');
                                location.reload();
                            }
                        });
                }
            });
        });
    });
</script>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>