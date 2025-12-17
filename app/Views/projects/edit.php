<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Chỉnh sửa: <?= htmlspecialchars($project['name']) ?></h2>
        <a href="<?= BASE_URL ?>/project/detail/<?= $project['id'] ?>" class="text-gray-500 hover:text-gray-700 text-sm">Hủy bỏ</a>
    </div>

    <form action="<?= BASE_URL ?>/project/edit/<?= $project['id'] ?>" method="POST" class="p-6 space-y-6">

        <div>
            <label class="block text-sm font-medium text-gray-700">Tên Dự án / Sự kiện</label>
            <input type="text" name="name" value="<?= htmlspecialchars($project['name']) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
            <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="planning" <?= $project['status'] == 'planning' ? 'selected' : '' ?>>Lên kế hoạch</option>
                <option value="in_progress" <?= $project['status'] == 'in_progress' ? 'selected' : '' ?>>Đang thực hiện</option>
                <option value="completed" <?= $project['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                <option value="cancelled" <?= $project['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Mô tả chi tiết</label>
            <textarea name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"><?= htmlspecialchars($project['description']) ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                <input type="date" name="start_date" value="<?= $project['start_date'] ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
                <input type="date" name="end_date" value="<?= $project['end_date'] ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Người phụ trách (Leader)</label>
                <select name="leader_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Chọn Leader --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($u['id'] == $project['leader_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ban chủ trì</label>
                <select name="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Không thuộc ban nào --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($d['id'] == $project['department_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full btn btn-primary justify-center py-3">
                <ion-icon name="checkmark-circle-outline" class="mr-2 text-lg"></ion-icon> Cập nhật thay đổi
            </button>
        </div>
    </form>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>