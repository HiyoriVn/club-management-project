<?php
// Nạp header MỚI (đã có layout sidebar)
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="bg-white shadow rounded-lg overflow-hidden max-w-3xl mx-auto">

    <form action="<?php echo BASE_URL; ?>/announcement/store" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="p-6">

            <div class="mb-5">
                <label for="form_title" class="block text-sm font-medium text-gray-700 mb-1">
                    Tiêu đề: <sup>*</sup>
                </label>
                <input type="text" name="form_title" id="form_title"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm <?php echo !empty($data['title_err']) ? 'border-red-500' : ''; ?>"
                    value="<?php echo htmlspecialchars($data['form_title'] ?? ''); ?>">
                <span class="text-red-600 text-sm"><?php echo $data['title_err']; ?></span>
            </div>

            <div class="mb-5">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                    Nội dung:
                </label>
                <input type="hidden" name="content" id="content"
                    value="<?php echo htmlspecialchars($data['content'] ?? ''); ?>">

                <trix-editor input="content" class="min-h-[200px]"></trix-editor>
            </div>

            <div class="mb-5">
                <label for="target" class="block text-sm font-medium text-gray-700 mb-1">
                    Gửi tới:
                </label>
                <select name="target" id="target"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="public">-- Thông báo Chung (Guest cũng thấy) --</option>
                    <option value="internal" selected>-- Thông báo Nội bộ CLB (Chỉ Member) --</option>
                    <optgroup label="Chỉ gửi cho Ban Cụ thể:">
                        <?php foreach ($data['all_departments'] as $dep) : ?>
                            <option value="<?php echo $dep['id']; ?>">
                                Ban: <?php echo htmlspecialchars($dep['NAME']); ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>

        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
            <a href="<?php echo BASE_URL; ?>/announcement"
                class="btn btn-secondary-outline">
                Hủy bỏ
            </a>
            <button type="submit"
                class="btn btn-success">
                <ion-icon name="send-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
                Đăng
            </button>
        </div>

    </form>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>