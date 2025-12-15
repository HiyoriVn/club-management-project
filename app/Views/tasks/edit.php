<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-2xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/task/kanban/<?= $task['project_id'] ?>" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Hủy bỏ
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Chỉnh sửa công việc</h3>
        </div>

        <form action="<?= BASE_URL ?>/task/edit/<?= $task['id'] ?>" method="POST" class="px-4 py-5 sm:p-6 space-y-6">

            <div>
                <label class="block text-sm font-medium text-gray-700">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                    <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <?php foreach (['backlog', 'todo', 'in_progress', 'done'] as $s): ?>
                            <option value="<?= $s ?>" <?= $task['status'] == $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Màu sắc</label>
                    <input type="color" name="color" value="<?= $task['color'] ?>" class="mt-1 block w-full h-9 border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bắt đầu</label>
                    <input type="datetime-local" name="start_date" value="<?= $task['start_date'] ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hạn chót</label>
                    <input type="datetime-local" name="due_date" value="<?= $task['due_date'] ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phân công</label>
                <div class="bg-gray-50 p-3 rounded-md border border-gray-200 h-40 overflow-y-auto grid grid-cols-2 gap-2">
                    <?php foreach ($members as $mem): ?>
                        <div class="flex items-center">
                            <input id="user_<?= $mem['user_id'] ?>" name="assignees[]" value="<?= $mem['user_id'] ?>" type="checkbox" <?= in_array($mem['user_id'], $assignee_ids) ? 'checked' : '' ?> class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="user_<?= $mem['user_id'] ?>" class="ml-2 block text-sm text-gray-900"><?= htmlspecialchars($mem['name']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?= htmlspecialchars($task['description']) ?></textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>