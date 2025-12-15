<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-3xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/project" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Tạo Hoạt động mới</h3>
        </div>

        <form action="<?= BASE_URL ?>/project/create" method="POST" class="px-4 py-5 sm:p-6 space-y-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Loại hình <span class="text-red-500">*</span></label>
                <div class="flex space-x-6">
                    <div class="flex items-center">
                        <input id="type_project" name="type" type="radio" value="project" <?= ($type == 'project') ? 'checked' : '' ?> class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label for="type_project" class="ml-3 block text-sm font-medium text-gray-700">Dự án (Project)</label>
                    </div>
                    <div class="flex items-center">
                        <input id="type_event" name="type" type="radio" value="event" <?= ($type == 'event') ? 'checked' : '' ?> class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label for="type_event" class="ml-3 block text-sm font-medium text-gray-700">Sự kiện (Event)</label>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Tên hoạt động <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                    <input type="date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
                    <input type="date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Người phụ trách (Leader)</label>
                    <select name="leader_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Chọn Leader --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($u['id'] == $_SESSION['user_id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Thuộc Ban</label>
                    <select name="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Không --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="btn btn-primary">Lưu lại</button>
            </div>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>