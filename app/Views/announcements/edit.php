<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/announcement" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Hủy bỏ
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
            <h3 class="text-lg font-bold text-yellow-800">Chỉnh sửa thông báo</h3>
        </div>

        <form action="<?= BASE_URL ?>/announcement/edit/<?= $announcement['id'] ?>" method="POST" class="p-6 space-y-6">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tiêu đề</label>
                <input type="text" name="title" value="<?= htmlspecialchars($announcement['title']) ?>" required
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-lg py-3">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Gửi tới</label>
                <select name="target_department_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="" <?= empty($announcement['target_department_id']) ? 'selected' : '' ?>>-- Toàn thể CLB --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= ($announcement['target_department_id'] == $dept['id']) ? 'selected' : '' ?>>
                            Ban: <?= htmlspecialchars($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nội dung</label>
                <input id="content" type="hidden" name="content" value="<?= htmlspecialchars($announcement['content']) ?>">
                <trix-editor input="content" class="min-h-[200px] border-gray-300 rounded-md"></trix-editor>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                    <ion-icon name="save-outline" class="mr-2 text-lg"></ion-icon>
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>