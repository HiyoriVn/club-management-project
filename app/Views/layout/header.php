<?php
// 1. Tự động lấy thông báo cho user đã đăng nhập
// (Thay thế cho logic cũ trong Controller)
$unread_notifications_count = 0;
$latest_unread_notifications = [];

if (isset($_SESSION['user_id']) && class_exists('App\Models\Notification')) {
    // Sử dụng Model trực tiếp tại View (View Composition pattern)
    $notifModel = new \App\Models\Notification();
    $unread_notifications_count = $notifModel->countUnreadForUser($_SESSION['user_id']);
    $latest_unread_notifications = $notifModel->getUnreadForUser($_SESSION['user_id'], 5);
}
?>
<!DOCTYPE html>
<html lang="vi" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'CLB Management'; ?></title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/custom.css">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <style>
        /* Trix Editor Styling */
        trix-editor {
            background-color: white;
            border-radius: 0.375rem;
            border-color: #D1D5DB;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        trix-editor:focus-within {
            --tw-ring-color: #2563EB;
            border-color: #2563EB;
        }

        /* Custom Scrollbar */
        main.overflow-y-auto {
            scrollbar-width: thin;
            scrollbar-color: #a1a1aa #f1f5f9;
        }

        main.overflow-y-auto::-webkit-scrollbar,
        .overflow-x-auto::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        main.overflow-y-auto::-webkit-scrollbar-thumb,
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background-color: #a1a1aa;
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: content-box;
        }

        main.overflow-y-auto::-webkit-scrollbar-thumb:hover,
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background-color: #71717a;
        }

        main.overflow-y-auto::-webkit-scrollbar-track,
        .overflow-x-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Sidebar Active Link */
        .nav-link-active {
            background-color: #EEF2FF;
            color: #4F46E5;
            border-left: 4px solid #4F46E5;
        }
    </style>
</head>

<body class="h-full">
    <div class="flex h-full">

        <!-- ========== SIDEBAR ========== -->
        <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
            <div class="flex flex-col flex-grow bg-white pt-5 shadow-lg border-r border-gray-200">

                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-6 mb-8">
                    <div class="h-10 w-10 bg-indigo-600 rounded-lg flex items-center justify-center mr-3">
                        <ion-icon name="school-outline" class="text-white text-2xl"></ion-icon>
                    </div>
                    <span class="text-xl font-bold text-gray-900">CLB Management</span>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 space-y-1 overflow-y-auto pb-4">

                    <a href="<?php echo BASE_URL; ?>/dashboard"
                        class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                        <ion-icon name="home-outline" class="mr-3 h-5 w-5"></ion-icon>
                        Dashboard
                    </a>

                    <a href="<?php echo BASE_URL; ?>/event"
                        class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                        <ion-icon name="calendar-outline" class="mr-3 h-5 w-5"></ion-icon>
                        Sự kiện
                    </a>

                    <a href="<?php echo BASE_URL; ?>/announcement"
                        class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                        <ion-icon name="megaphone-outline" class="mr-3 h-5 w-5"></ion-icon>
                        Thông báo
                    </a>

                    <a href="<?php echo BASE_URL; ?>/project"
                        class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                        <ion-icon name="briefcase-outline" class="mr-3 h-5 w-5"></ion-icon>
                        Dự án
                    </a>

                    <a href="<?php echo BASE_URL; ?>/file"
                        class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                        <ion-icon name="folder-outline" class="mr-3 h-5 w-5"></ion-icon>
                        Tài liệu
                    </a>

                    <!-- Admin Section -->
                    <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'subadmin'])): ?>
                        <div class="pt-6">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Quản trị
                            </p>

                            <a href="<?php echo BASE_URL; ?>/department"
                                class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                                <ion-icon name="git-network-outline" class="mr-3 h-5 w-5"></ion-icon>
                                Quản lý Ban
                            </a>

                            <a href="<?php echo BASE_URL; ?>/user"
                                class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                                <ion-icon name="people-outline" class="mr-3 h-5 w-5"></ion-icon>
                                Quản lý Người dùng
                            </a>

                            <a href="<?php echo BASE_URL; ?>/transaction"
                                class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                                <ion-icon name="cash-outline" class="mr-3 h-5 w-5"></ion-icon>
                                Quản lý Quỹ
                            </a>

                            <a href="<?php echo BASE_URL; ?>/report"
                                class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                                <ion-icon name="stats-chart-outline" class="mr-3 h-5 w-5"></ion-icon>
                                Báo cáo & Thống kê
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- System Section (Admin only) -->
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                        <div class="pt-6">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Hệ thống
                            </p>

                            <a href="<?php echo BASE_URL; ?>/activitylog"
                                class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                                <ion-icon name="clipboard-outline" class="mr-3 h-5 w-5"></ion-icon>
                                Nhật ký hoạt động
                            </a>

                            <a href="<?php echo BASE_URL; ?>/settings"
                                class="nav-link text-gray-700 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors">
                                <ion-icon name="settings-outline" class="mr-3 h-5 w-5"></ion-icon>
                                Cài đặt hệ thống
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>

                <!-- User Profile (Bottom) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex-shrink-0 border-t border-gray-200 p-4">
                        <a href="<?php echo BASE_URL; ?>/profile" class="flex items-center group">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-600 font-medium text-sm">
                                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 2)); ?>
                                </span>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">
                                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                </p>
                                <p class="text-xs text-gray-500">Xem hồ sơ</p>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========== MAIN CONTENT ========== -->
        <div class="flex flex-col flex-1 md:pl-64">

            <!-- Top Bar (Simplified) -->
            <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow-sm border-b border-gray-200">
                <div class="flex-1 px-4 flex justify-between items-center sm:px-6 md:px-8">

                    <!-- Page Title (Dynamic) -->
                    <h1 class="text-xl font-semibold text-gray-900">
                        <?php echo $data['title'] ?? 'Dashboard'; ?>
                    </h1>

                    <!-- Right Actions -->
                    <div class="ml-4 flex items-center space-x-4">

                        <!-- Notifications -->
                        <button type="button" class="relative p-2 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition-colors">
                            <ion-icon name="notifications-outline" class="h-6 w-6"></ion-icon>
                            <?php if (isset($data['unread_notifications_count']) && $data['unread_notifications_count'] > 0): ?>
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                            <?php endif; ?>
                        </button>

                        <!-- Logout -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?php echo BASE_URL; ?>/auth/logout"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <ion-icon name="log-out-outline" class="mr-2 h-4 w-4"></ion-icon>
                                Đăng xuất
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="py-6 px-4 sm:px-6 md:px-8">

                    <?php \display_flash_message(); ?>