<?php require_once 'app/Views/layout/auth_header.php'; ?>

<div class="max-w-md w-full space-y-8 glass-card p-10 rounded-2xl shadow-2xl relative z-10">
    <div class="text-center">
        <h2 class="mt-4 text-3xl font-extrabold text-gray-900 tracking-tight">
            Đặt lại mật khẩu
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Tạo mật khẩu mới cho tài khoản của bạn
        </p>
    </div>

    <form class="mt-8 space-y-6" action="<?= BASE_URL ?>/auth/reset" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

        <div class="rounded-md shadow-sm -space-y-px">
            <div>
                <label for="password" class="sr-only">Mật khẩu mới</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <ion-icon name="lock-closed-outline" class="text-gray-400 text-lg"></ion-icon>
                    </div>
                    <input id="password" name="password" type="password" required
                        class="appearance-none rounded-t-md relative block w-full px-10 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Mật khẩu mới">
                </div>
            </div>
            <div>
                <label for="confirm_password" class="sr-only">Xác nhận mật khẩu</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <ion-icon name="checkmark-circle-outline" class="text-gray-400 text-lg"></ion-icon>
                    </div>
                    <input id="confirm_password" name="confirm_password" type="password" required
                        class="appearance-none rounded-b-md relative block w-full px-10 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Nhập lại mật khẩu mới">
                </div>
            </div>
        </div>

        <div>
            <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-md">
                Đổi mật khẩu
            </button>
        </div>
    </form>
</div>

<?php require_once 'app/Views/layout/auth_footer.php'; ?>