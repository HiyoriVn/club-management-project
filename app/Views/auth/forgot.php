<?php
// app/Views/auth/forgot.php
// Nạp header MỚI (Đã bao gồm flash message)
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="flex items-center justify-center min-h-full py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-lg shadow-lg">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Quên Mật Khẩu
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Nhập email của bạn, chúng tôi sẽ gửi một link đặt lại mật khẩu.
            </p>
        </div>

        <form class="mt-8 space-y-6" action="<?php echo BASE_URL; ?>/auth/send_reset" method="POST">

            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only"> Email </label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                        placeholder="Địa chỉ email">
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-full">
                    Gửi Link Đặt Lại
                </button>
            </div>
        </form>

        <div class="text-sm text-center">
            <a href="<?php echo BASE_URL; ?>/auth/login" class="font-medium text-blue-600 hover:text-blue-500">
                Quay lại Đăng nhập
            </a>
        </div>
    </div>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>