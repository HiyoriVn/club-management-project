<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-2xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/department" class="text-gray-500 hover:text-gray-700 flex items-center">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại danh sách
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Chỉnh sửa thông tin Ban</h3>
        </div>

        <form action="<?= BASE_URL ?>/department/edit/<?= $department['id'] ?>" method="POST" class="px-4 py-5 sm:p-6 space-y-6">

            <div>
                <label class="block text-sm font-medium text-gray-700">Tên Ban <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="<?= htmlspecialchars($department['name']) ?>" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Mô tả chức năng</label>
                <textarea name="description" rows="4"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?= htmlspecialchars($department['description']) ?></textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <ion-icon name="save-outline" class="mr-2 text-lg"></ion-icon>
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>