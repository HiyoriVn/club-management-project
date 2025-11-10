<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="bg-white shadow rounded-lg overflow-hidden max-w-3xl mx-auto">

    <form action="<?php echo BASE_URL; ?>/project/store" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="p-6">

            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Tên Dự án: <sup>*</sup>
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
                <textarea name="description" id="description" rows="5"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Ngày bắt đầu:
                    </label>
                    <input type="date" name="start_date" id="start_date"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        value="<?php echo htmlspecialchars($data['start_date'] ?? ''); ?>">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Ngày kết thúc:
                    </label>
                    <input type="date" name="end_date" id="end_date"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        value="<?php echo htmlspecialchars($data['end_date'] ?? ''); ?>">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-5">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Trạng thái:
                    </label>
                    <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="planning" selected>Đang lên kế hoạch</option>
                        <option value="in_progress">Đang thực hiện</option>
                        <option value="completed">Đã hoàn thành</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div>
                    <label for="leader_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Project Manager:
                    </label>
                    <select name="leader_id" id="leader_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Không có</option>
                        <?php foreach ($data['all_users'] as $user) : ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['NAME']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Thuộc Ban:
                    </label>
                    <select name="department_id" id="department_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Không thuộc Ban nào</option>
                        <?php foreach ($data['all_departments'] as $dep) : ?>
                            <option value="<?php echo $dep['id']; ?>"><?php echo htmlspecialchars($dep['NAME']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
            <a href="<?php echo BASE_URL; ?>/project"
                class="btn btn-danger">
                Hủy bỏ
            </a>
            <button type="submit"
                class="btn btn-success">
                Tạo Dự án
            </button>
        </div>

    </form>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>