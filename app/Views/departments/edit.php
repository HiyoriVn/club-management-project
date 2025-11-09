<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="bg-white shadow rounded-lg overflow-hidden max-w-3xl mx-auto">

    <form action="<?php echo BASE_URL; ?>/department/update/<?php echo $data['id']; ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="p-6">

            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Tên Ban: <sup>*</sup>
                </label>
                <input type="text" name="name" id="name"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm <?php echo !empty($data['name_err']) ? 'border-red-500' : ''; ?>"
                    value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>">
                <span class="text-red-600 text-sm"><?php echo $data['name_err']; ?></span>
            </div>

            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Mô tả:
                </label>
                <textarea name="description" id="description" rows="4"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
            </div>

            <div class="mb-5">
                <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Trực thuộc:
                </label>
                <select name="parent_id" id="parent_id"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Không</option>
                    <?php foreach ($data['departments'] as $dep) : ?>
                        <?php if ($dep['id'] == $data['id']) continue; // Logic cũ của bạn: không cho chọn chính nó làm cha 
                        ?>
                        <option value="<?php echo $dep['id']; ?>" <?php echo ($data['parent_id'] == $dep['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dep['NAME']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
            <a href="<?php echo BASE_URL; ?>/department"
                class="text-sm font-medium text-gray-700 hover:text-gray-900">
                Hủy bỏ
            </a>
            <button type="submit"
                class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600">
                <ion-icon name="save-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
                Cập nhật
            </button>
        </div>

    </form>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>