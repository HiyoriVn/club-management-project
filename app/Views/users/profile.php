<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="bg-white shadow rounded-lg overflow-hidden max-w-4xl mx-auto">

    <form action="<?php echo BASE_URL; ?>/profile/update" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="grid grid-cols-1 md:grid-cols-3">

            <div class="md:col-span-1 p-6 border-r border-gray-200">
                <h4 class="text-lg font-medium text-gray-900">Thông tin tài khoản</h4>
                <p class="mt-1 text-sm text-gray-500">Thông tin cơ bản, không thể thay đổi.</p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Họ và Tên:</label>
                        <input type="text" value="<?php echo htmlspecialchars($data['name']); ?>" disabled
                            class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email:</label>
                        <input type="email" value="<?php echo htmlspecialchars($data['email']); ?>" disabled
                            class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Vai trò CLB:</label>
                        <input type="text" value="<?php echo htmlspecialchars($data['system_role']); ?>" disabled
                            class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 p-6">
                <h4 class="text-lg font-medium text-gray-900">Thông tin hồ sơ</h4>
                <p class="mt-1 text-sm text-gray-500">Cập nhật thông tin cá nhân của bạn.</p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700">Mã số Sinh viên:</label>
                        <input type="text" name="student_id" id="student_id"
                            value="<?php echo htmlspecialchars($data['student_id'] ?? ''); ?>"
                            class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Số điện thoại:</label>
                        <input type="tel" name="phone" id="phone"
                            value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>"
                            class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700">Ngày sinh:</label>
                            <input type="date" name="dob" id="dob"
                                value="<?php echo htmlspecialchars($data['dob'] ?? ''); ?>"
                                class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">Giới tính:</label>
                            <select name="gender" id="gender" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                <option value="other" <?php echo ($data['gender'] == 'other') ? 'selected' : ''; ?>>Khác</option>
                                <option value="male" <?php echo ($data['gender'] == 'male') ? 'selected' : ''; ?>>Nam</option>
                                <option value="female" <?php echo ($data['gender'] == 'female') ? 'selected' : ''; ?>>Nữ</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Địa chỉ:</label>
                        <input type="text" name="address" id="address"
                            value="<?php echo htmlspecialchars($data['address'] ?? ''); ?>"
                            class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Giới thiệu (Bio):</label>
                        <textarea name="bio" id="bio" rows="4"
                            class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"><?php echo htmlspecialchars($data['bio'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-right">
            <button type="submit"
                class="btn btn-success">
                Lưu thay đổi
            </button>
        </div>
    </form>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>