<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="max-w-md mx-auto mt-10">
    <div class="bg-white shadow rounded-lg overflow-hidden">

        <form action="<?php echo BASE_URL; ?>/auth/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900"><?php echo $data['title']; ?></h2>
                <p class="mt-1 text-sm text-gray-600">Vui lòng điền thông tin để tạo tài khoản</p>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên của bạn: <sup>*</sup></label>
                    <input type="text" name="name" id="name"
                        class="block w-full rounded-md border-gray-300 shadow-sm <?php echo !empty($data['name_err']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>">
                    <span class="text-red-600 text-sm"><?php echo $data['name_err']; ?></span>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email: <sup>*</sup></label>
                    <input type="email" name="email" id="email"
                        class="block w-full rounded-md border-gray-300 shadow-sm <?php echo !empty($data['email_err']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                    <span class="text-red-600 text-sm"><?php echo $data['email_err']; ?></span>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu: <sup>*</sup></label>
                    <input type="password" name="password" id="password"
                        class="block w-full rounded-md border-gray-300 shadow-sm <?php echo !empty($data['password_err']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo htmlspecialchars($data['password'] ?? ''); ?>">
                    <span class="text-red-600 text-sm"><?php echo $data['password_err']; ?></span>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu: <sup>*</sup></label>
                    <input type="password" name="confirm_password" id="confirm_password"
                        class="block w-full rounded-md border-gray-300 shadow-sm <?php echo !empty($data['confirm_password_err']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo htmlspecialchars($data['confirm_password'] ?? ''); ?>">
                    <span class="text-red-600 text-sm"><?php echo $data['confirm_password_err']; ?></span>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <a href="<?php echo BASE_URL; ?>/auth/login" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    Đã có tài khoản?
                </a>
                <button type="submit"
                    class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    Đăng Ký
                </button>
            </div>
        </form>

    </div>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>