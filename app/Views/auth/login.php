<?php require_once 'app/Views/layout/auth_header.php'; ?>

<div class="max-w-md w-full space-y-8 glass-card p-10 rounded-2xl shadow-2xl relative z-10 animate-fade-in-up">
    <div class="logo-container mx-auto h-16 w-16 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg transform rotate-3 transition hover:rotate-6">
        <ion-icon name="school" class="text-3xl text-white"></ion-icon>
    </div>

    <div class="text-center">
        <h2 class="mt-4 text-3xl font-extrabold text-gray-900 tracking-tight">
            Chào mừng trở lại!
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Hệ thống quản lý CLB Hiyorivn
        </p>
    </div>

    <form class="mt-8 space-y-6" action="<?= BASE_URL ?>/auth/login" method="POST">
        <div class="rounded-md shadow-sm -space-y-px">
            <div>
                <label for="email" class="sr-only">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <ion-icon name="mail-outline" class="text-gray-400 text-lg"></ion-icon>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required
                        class="appearance-none rounded-none rounded-t-md relative block w-full px-10 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all duration-300 ease-in-out"
                        placeholder="Địa chỉ Email">
                </div>
            </div>
            <div>
                <label for="password" class="sr-only">Mật khẩu</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <ion-icon name="lock-closed-outline" class="text-gray-400 text-lg"></ion-icon>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="appearance-none rounded-none rounded-b-md relative block w-full px-10 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all duration-300 ease-in-out"
                        placeholder="Mật khẩu">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                <label for="remember_me" class="ml-2 block text-sm text-gray-900 cursor-pointer">
                    Ghi nhớ đăng nhập
                </label>
            </div>

            <div class="text-sm">
                <a href="<?= BASE_URL ?>/auth/forgot" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                    Quên mật khẩu?
                </a>
            </div>
        </div>

        <div>
            <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <ion-icon name="log-in-outline" class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition ease-in-out duration-150"></ion-icon>
                </span>
                Đăng nhập
            </button>
        </div>
    </form>

    <div class="mt-6 text-center text-xs text-gray-500">
        &copy; <?= date('Y') ?> Club Management System. Version 2.0
    </div>
</div>

<?php require_once 'app/Views/layout/auth_footer.php'; ?>