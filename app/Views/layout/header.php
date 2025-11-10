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
        /* Ghi đè style của Trix để nó giống input của Tailwind */
        trix-editor {
            background-color: white;
            border-radius: 0.375rem;
            /* rounded-md */
            border-color: #D1D5DB;
            /* border-gray-300 */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            /* shadow-sm */
        }

        trix-editor:focus-within {
            --tw-ring-color: #2563EB;
            /* focus:ring-blue-500 */
            border-color: #2563EB;
        }

        /* Style cho thanh công cụ */
        trix-toolbar.trix-button-group {
            border-color: #D1D5DB;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
        }
    </style>
    <style>
        /* Ẩn thanh cuộn mặc định trên Firefox */
        main.overflow-y-auto {
            scrollbar-width: thin;
            scrollbar-color: #a1a1aa #f1f5f9;
            /* thumb track */
        }

        /* Style thanh cuộn cho Chrome, Edge, và Safari (Webkit) */

        /* 1. Style thanh cuộn DỌC (của <main>) */
        main.overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }

        /* 2. Style thanh cuộn NGANG (của Bảng Kanban) */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
            /* Làm thanh cuộn ngang mỏng lại */
        }

        /* 3. Style cái thanh trượt (thumb) */
        main.overflow-y-auto::-webkit-scrollbar-thumb,
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background-color: #a1a1aa;
            /* Màu xám nhạt (zinc-400) */
            border-radius: 10px;
            border: 2px solid transparent;
            /* Đổi thành transparent */
            background-clip: content-box;
            /* Fix lỗi padding */
        }

        /* 4. Style khi hover vào thanh trượt */
        main.overflow-y-auto::-webkit-scrollbar-thumb:hover,
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background-color: #71717a;
            /* Xám đậm hơn (zinc-500) */
        }

        /* 5. Style cái rãnh (track) - làm trong suốt để "tự ẩn" */
        main.overflow-y-auto::-webkit-scrollbar-track,
        .overflow-x-auto::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>
</head>

<body class="h-full">

    <div class="flex h-full">

        <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
            <div class="flex flex-col flex-grow bg-white pt-5 shadow-lg">

                <div class="flex items-center flex-shrink-0 px-4">
                    <span class="text-2xl font-bold text-blue-600">CLB Management</span>
                </div>

                <div class="mt-5 flex-1 flex flex-col overflow-y-auto">
                    <nav class="flex-1 px-2 space-y-1 pb-4">

                        <a href="<?php echo BASE_URL; ?>/dashboard"
                            class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <ion-icon name="home-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                            Dashboard
                        </a>

                        <a href="<?php echo BASE_URL; ?>/event"
                            class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <ion-icon name="calendar-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                            Sự kiện
                        </a>

                        <a href="<?php echo BASE_URL; ?>/announcement"
                            class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <ion-icon name="megaphone-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                            Thông báo
                        </a>

                        <a href="<?php echo BASE_URL; ?>/project"
                            class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <ion-icon name="briefcase-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                            Dự án
                        </a>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] != 'guest') : ?>
                            <a href="<?php echo BASE_URL; ?>/file"
                                class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <ion-icon name="folder-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                                Tài liệu
                            </a>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
                            <div class="pt-4">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2">Quản trị</span>
                                <div class="mt-2 space-y-1">
                                    <a href="<?php echo BASE_URL; ?>/department"
                                        class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <ion-icon name="git-network-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                                        Quản lý Ban
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/member"
                                        class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <ion-icon name="people-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                                        Quản lý Thành viên
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/transaction"
                                        class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <ion-icon name="cash-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                                        Quản lý Quỹ
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') : ?>
                            <div class="pt-4">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2">Hệ thống</span>
                                <div class="mt-2 space-y-1">
                                    <a href="<?php echo BASE_URL; ?>/departmentrole"
                                        class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <ion-icon name="key-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                                        Quản lý Vai trò
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/activitylog"
                                        class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <ion-icon name="clipboard-outline" class="text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></ion-icon>
                                        Nhật ký hoạt động
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </nav>
                </div>

                <?php if (isset($_SESSION['user_id'])) : ?>
                    <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                        <a href="<?php echo BASE_URL; ?>/profile" class="flex-shrink-0 w-full group block">
                            <div class="flex items-center">
                                <div>
                                    <ion-icon name="person-circle-outline" class="h-10 w-10 text-gray-700"></ion-icon>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                                    <p class="text-xs font-medium text-gray-500 group-hover:text-gray-700">Xem hồ sơ</p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-col flex-1 md:pl-64 overflow-hidden">

            <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow-sm">
                <div class="flex-1 px-4 flex justify-between sm:px-6 md:px-8">

                    <div class="flex-1 flex">
                        <form class="w-full flex md:ml-0" action="#" method="GET">
                            <label for="search-field" class="sr-only">Tìm kiếm</label>
                            <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                                    <ion-icon name="search-outline" class="h-5 w-5"></ion-icon>
                                </div>
                                <input id="search-field" class="block w-full h-full pl-8 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-0 focus:border-transparent sm:text-sm" placeholder="Tìm kiếm..." type="search" name="search">
                            </div>
                        </form>
                    </div>

                    <div class="ml-4 flex items-center md:ml-6">

                        <button type="button" class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="sr-only">View notifications</span>
                            <ion-icon name="notifications-outline" class="h-6 w-6"></ion-icon>
                        </button>

                        <?php if (!isset($_SESSION['user_id'])) : ?>
                            <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-secondary-outline ml-4">Đăng Nhập</a>
                            <a href="<?php echo BASE_URL; ?>/auth/register" class="btn btn-primary ml-4">Đăng Ký</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/auth/logout" class="btn btn-secondary-outline ml-4">Đăng Xuất</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <main class="flex-1 overflow-y-auto">
                <div class="py-6">

                    <div class="px-4 sm:px-6 md:px-8">
                        <h1 class="text-2xl font-semibold text-gray-900">
                            <?php echo $data['title'] ?? 'Dashboard'; ?>
                        </h1>
                    </div>

                    <div class="px-4 sm:px-6 md:px-8 mt-5">

                        <?php \display_flash_message(); ?>