<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/announcement" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại bảng tin
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-900">Viết thông báo mới</h3>
        </div>

        <form action="<?= BASE_URL ?>/announcement/create" method="POST" class="p-6 space-y-6">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tiêu đề thông báo <span class="text-red-500">*</span></label>
                <input type="text" name="title" required placeholder="Nhập tiêu đề..."
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-lg py-3">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Gửi tới</label>
                <select name="target_department_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Toàn thể CLB</option>
                    <?php if (!empty($departments)): ?>
                        <optgroup label="Ban chuyên môn">
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>">Ban: <?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                </select>
                <p class="mt-1 text-xs text-gray-500">Nếu chọn Ban, chỉ thành viên trong Ban mới thấy thông báo này.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nội dung chi tiết <span class="text-red-500">*</span></label>
                <input id="content" type="hidden" name="content">
                <trix-editor input="content" class="min-h-[200px] border-gray-300 rounded-md"></trix-editor>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <ion-icon name="paper-plane-outline" class="mr-2 text-lg"></ion-icon>
                    Đăng ngay
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>