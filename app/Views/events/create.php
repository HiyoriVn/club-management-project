<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="bg-white shadow rounded-lg overflow-hidden max-w-3xl mx-auto">

    <form action="<?php echo BASE_URL; ?>/event/store" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="p-6">
            <div class="mb-5">
                <label for="form_title" class="block text-sm font-medium text-gray-700 mb-1">
                    Tiêu đề Sự kiện: <sup>*</sup>
                </label>
                <input type="text" name="form_title" id="form_title"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm <?php echo !empty($data['title_err']) ? 'border-red-500' : ''; ?>"
                    value="<?php echo htmlspecialchars($data['form_title'] ?? ''); ?>">
                <span class="text-red-600 text-sm"><?php echo $data['title_err']; ?></span>
            </div>

            <div class="mb-5">
                <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">
                    Loại sự kiện:
                </label>
                <select name="visibility" id="visibility"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="internal" selected>Nội bộ (Chỉ Member thấy)</option>
                    <option value="public">Công khai (Guest cũng thấy)</option>
                </select>
            </div>

            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Mô tả:
                </label>
                <textarea name="description" id="description" rows="5"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">
                        Thời gian bắt đầu: <sup>*</sup>
                    </label>
                    <input type="datetime-local" name="start_time" id="start_time"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm <?php echo !empty($data['start_time_err']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo htmlspecialchars($data['start_time'] ?? ''); ?>">
                    <span class="text-red-600 text-sm"><?php echo $data['start_time_err']; ?></span>
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">
                        Thời gian kết thúc: (Tùy chọn)
                    </label>
                    <input type="datetime-local" name="end_time" id="end_time"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        value="<?php echo htmlspecialchars($data['end_time'] ?? ''); ?>">
                </div>
            </div>

            <div class="mb-5">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                    Địa điểm:
                </label>
                <input type="text" name="location" id="location"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    value="<?php echo htmlspecialchars($data['location'] ?? ''); ?>">
            </div>

        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">

            <a href="<?php echo BASE_URL; ?>/event"
                class="text-sm font-medium text-gray-700 hover:text-gray-900">
                Hủy bỏ
            </a>

            <button type="submit"
                class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
                Tạo Sự kiện
            </button>
        </div>

    </form>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>