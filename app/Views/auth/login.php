<?php
// Sử dụng Auth Layout thay vì layout chính
require_once ROOT_PATH . '/app/Views/layout/auth_header.php';
?>

<div class="w-full max-w-md space-y-8">
    <!-- Logo với glow effect -->
    <div class="text-center">
        <div class="mx-auto h-24 w-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-2xl logo-container">
            <ion-icon name="school-outline" class="text-6xl text-white"></ion-icon>
        </div>
        <h2 class="mt-6 text-4xl font-extrabold text-white drop-shadow-lg">
            CLB Management
        </h2>
        <p class="mt-2 text-lg text-gray-300">
            Hệ thống Quản lý Câu lạc bộ
        </p>
    </div>

    <!-- Card đăng nhập -->
    <div class="glass-card shadow-2xl rounded-2xl">
        <div class="px-8 py-10">
            <h3 class="text-2xl font-bold text-gray-900 text-center mb-8">
                Đăng nhập
            </h3>

            <form action="<?php echo BASE_URL; ?>/auth/processLogin" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Địa chỉ Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <ion-icon name="mail-outline" class="h-5 w-5 text-gray-400"></ion-icon>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo !empty($data['email_err']) ? 'border-red-500' : ''; ?>"
                            placeholder="your.email@example.com"
                            value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                    </div>
                    <?php if (!empty($data['email_err'])): ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo $data['email_err']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Mật khẩu
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <ion-icon name="lock-closed-outline" class="h-5 w-5 text-gray-400"></ion-icon>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo !empty($data['password_err']) ? 'border-red-500' : ''; ?>"
                            placeholder="••••••••"
                            value="<?php echo htmlspecialchars($data['password'] ?? ''); ?>">
                    </div>
                    <?php if (!empty($data['password_err'])): ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo $data['password_err']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Forgot Password Link -->
                <div class="flex items-center justify-end">
                    <a href="<?php echo BASE_URL; ?>/auth/forgot" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Quên mật khẩu?
                    </a>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <ion-icon name="log-in-outline" class="mr-2 h-5 w-5"></ion-icon>
                    Đăng nhập
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 rounded-b-2xl text-center">
            <p class="text-sm text-gray-600">
                © <?php echo date('Y'); ?> CLB Management System
            </p>
        </div>
    </div>

    <!-- Thông tin hỗ trợ -->
    <div class="text-center">
        <p class="text-sm text-white/80">
            Liên hệ hỗ trợ: <a href="mailto:support@clb.vn" class="font-medium underline">support@clb.vn</a>
        </p>
    </div>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/auth_footer.php';
?>