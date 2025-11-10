<?php
// app/Views/auth/reset.php
// Nạp header MỚI (Đã bao gồm flash message)
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="flex items-center justify-center min-h-full py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-lg shadow-lg">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Đặt Lại Mật Khẩu Mới
            </h2>
        </div>

        <form class="mt-8 space-y-6" action="<?php echo BASE_URL; ?>/auth/update_password" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($data['token']); ?>">

            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="password" class="sr-only"> Mật khẩu mới </label>
                    <input id="password" name="password" type="password" required
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                        placeholder="Mật khẩu mới">
                </div>
                <div>
                    <label for="confirm_password" class="sr-only"> Xác nhận mật khẩu mới </label>
                    <input id="confirm_password" name="confirm_password" type="password" required
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                        placeholder="Xác nhận mật khẩu mới">
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-full">
                    Cập Nhật Mật Khẩu
                </button>
            </div>
        </form>
    </div>
</div>


<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>