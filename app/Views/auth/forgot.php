<?php require_once 'app/Views/layout/auth_header.php'; ?>

<div class="max-w-md w-full space-y-8 glass-card p-10 rounded-2xl shadow-2xl relative z-10">
    <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
            <ion-icon name="key-outline" class="text-2xl text-indigo-600"></ion-icon>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Quên mật khẩu?
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Đừng lo lắng! Nhập email của bạn và chúng tôi sẽ gửi hướng dẫn khôi phục.
        </p>
    </div>

    <form class="mt-8 space-y-6" action="<?= BASE_URL ?>/auth/forgot" method="POST">
        <div class="rounded-md shadow-sm -space-y-px">
            <div>
                <label for="email" class="sr-only">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <ion-icon name="mail-outline" class="text-gray-400 text-lg"></ion-icon>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required
                        class="appearance-none rounded-md relative block w-full px-10 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all duration-300"
                        placeholder="Nhập địa chỉ Email đã đăng ký">
                </div>
            </div>
        </div>

        <div class="flex flex-col space-y-3">
            <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-md">
                Gửi liên kết khôi phục
            </button>

            <a href="<?= BASE_URL ?>/auth/login" class="flex justify-center items-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                <ion-icon name="arrow-back-outline" class="mr-2"></ion-icon>
                Quay lại đăng nhập
            </a>
        </div>
    </form>
</div>

<?php require_once 'app/Views/layout/auth_footer.php'; ?>