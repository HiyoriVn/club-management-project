<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Tạo mới</h2>
        <a href="<?= BASE_URL ?>/project" class="text-gray-500 hover:text-gray-700 text-sm">Hủy bỏ</a>
    </div>

    <form action="<?= BASE_URL ?>/project/create" method="POST" class="p-6 space-y-6">

        <div>
            <label class="block text-sm font-medium text-gray-700">Loại hình</label>
            <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="project">Dự án (Project)</option>
                <option value="event">Sự kiện (Event)</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tên Dự án / Sự kiện <span class="text-red-500">*</span></label>
            <input type="text" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="VD: Chiến dịch Mùa Hè Xanh 2024">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Mô tả chi tiết</label>
            <textarea name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                <input type="date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ngày kết thúc (Dự kiến)</label>
                <input type="date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Người phụ trách (Leader)</label>
                <select name="leader_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value=""> Chọn Leader </option>
                    <?php foreach ($leaders as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($u['id'] == $_SESSION['user_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name']) ?> (<?= $u['system_role'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="mt-1 text-xs text-gray-500">Chỉ Admin/Subadmin mới được làm Leader.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ban chủ trì (Nếu có)</label>
                <select name="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value=""> Không thuộc ban nào </option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full btn btn-primary justify-center py-3">
                <ion-icon name="save-outline" class="mr-2 text-lg"></ion-icon> Lưu và Tạo mới
            </button>
        </div>
    </form>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>