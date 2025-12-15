<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-3xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/project/detail/<?= $project['id'] ?>" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Hủy bỏ
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Chỉnh sửa: <?= htmlspecialchars($project['name']) ?></h3>
        </div>

        <form action="<?= BASE_URL ?>/project/edit/<?= $project['id'] ?>" method="POST" class="px-4 py-5 sm:p-6 space-y-6">

            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Tên hoạt động</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($project['name']) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                    <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="planning" <?= $project['status'] == 'planning' ? 'selected' : '' ?>>Lên kế hoạch</option>
                        <option value="in_progress" <?= $project['status'] == 'in_progress' ? 'selected' : '' ?>>Đang thực hiện</option>
                        <option value="completed" <?= $project['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                        <option value="cancelled" <?= $project['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Loại hình</label>
                    <input type="text" disabled value="<?= ucfirst($project['type']) ?>" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm sm:text-sm text-gray-500">
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?= htmlspecialchars($project['description']) ?></textarea>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                    <input type="date" name="start_date" value="<?= $project['start_date'] ? date('Y-m-d', strtotime($project['start_date'])) : '' ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
                    <input type="date" name="end_date" value="<?= $project['end_date'] ? date('Y-m-d', strtotime($project['end_date'])) : '' ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Người phụ trách</label>
                    <select name="leader_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Chọn Leader --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($u['id'] == $project['leader_id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Thuộc Ban</label>
                    <select name="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Không --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= ($dept['id'] == $project['department_id']) ? 'selected' : '' ?>><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>