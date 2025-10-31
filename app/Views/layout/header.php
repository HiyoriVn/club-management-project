<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $data['title'] ?? 'CLB Management'; ?></title>

    <style>
        /* Thêm cái này để reset */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;

            /* --- THÊM 3 DÒNG SAU --- */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* 100% Viewport Height */
        }

        .navbar {
            background-color: #333;
            padding: 15px 30px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        .container {
            padding: 20px 30px;

            /* --- THÊM DÒNG SAU --- */
            flex-grow: 1;
            /* Bảo nó chiếm hết không gian thừa */
        }

        .footer {
            background-color: #f1f1f1;
            padding: 20px 30px;
            text-align: center;
            /* Bỏ margin-top đi cho đẹp */
            /* margin-top: 20px; */
        }

        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            font-weight: bold;
        }

        .flash-message.success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .flash-message.error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .flash-message.info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
    </style>
</head>

<body>

    <nav class="navbar" style="display: flex; justify-content: space-between; align-items: center;">

        <div>
            <a href="<?php echo BASE_URL; ?>">Trang Chủ</a>
            <a href="<?php echo BASE_URL; ?>/dashboard">Dashboard</a>
            <a href="<?php echo BASE_URL; ?>/event">Sự kiện</a>
            <a href="<?php echo BASE_URL; ?>/announcement">Thông báo</a>
            <a href="<?php echo BASE_URL; ?>/project">Dự án</a>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] != 'guest') : ?>
                <a href="<?php echo BASE_URL; ?>/file">Tài liệu</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
                <a href="<?php echo BASE_URL; ?>/department">Quản lý Ban</a>
                <a href="<?php echo BASE_URL; ?>/member">Quản lý Thành viên</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') : ?>
                <a href="<?php echo BASE_URL; ?>/departmentrole">Quản lý Vai trò</a>
                <a href="<?php echo BASE_URL; ?>/activitylog" style="color: #ffc107;">Nhật ký (Log)</a>
            <?php endif; ?>
        </div>

        <div>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <div class="notification-area" style="display: inline-block; position: relative; margin-right: 15px;">
                    <a href="#" onclick="toggleNotificationDropdown()" style="color: white; text-decoration: none; font-size: 1.2em;" title="Thông báo">
                        🔔 <?php if ($data['unread_notifications_count'] > 0): ?>
                            <span style="background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7em; position: absolute; top: -5px; right: -8px;">
                                <?php echo $data['unread_notifications_count']; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <div id="notificationDropdown" style="display: none; position: absolute; right: 0; top: 100%; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); width: 300px; z-index: 100;">
                        <div style="padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <strong>Thông báo</strong>
                            <a href="#" style="font-size: 0.8em; color: #007bff;">Đánh dấu đã đọc</a>
                        </div>
                        <ul style="list-style: none; margin: 0; padding: 0; max-height: 300px; overflow-y: auto;">
                            <?php if (empty($data['latest_unread_notifications'])): ?>
                                <li style="padding: 10px; text-align: center; color: #6c757d;">Không có thông báo mới.</li>
                            <?php else: ?>
                                <?php foreach ($data['latest_unread_notifications'] as $noti): ?>
                                    <li style="padding: 10px; border-bottom: 1px solid #eee;">
                                        <strong style="display: block; font-size: 0.9em;"><?php echo htmlspecialchars($noti['title']); ?></strong>
                                        <span style="font-size: 0.85em; color: #555;"><?php echo htmlspecialchars($noti['message']); ?></span>
                                        <small style="display: block; text-align: right; color: #999; font-size: 0.75em;"><?php echo date('d/m H:i', strtotime($noti['created_at'])); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        <div style="text-align: center; padding: 10px; border-top: 1px solid #eee;">
                            <a href="#" style="font-size: 0.9em;">Xem tất cả</a>
                        </div>
                    </div>
                </div>

                <span style="color: #fff; margin-right: 15px;">
                    Xin chào, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                    (<?php echo htmlspecialchars($_SESSION['user_role']); ?>)
                </span>
                <a href="<?php echo BASE_URL; ?>/profile" style="color: #ffc107;">Hồ sơ</a>
                <a href="<?php echo BASE_URL; ?>/auth/logout">Đăng Xuất</a>

            <?php else : ?>
                <a href="<?php echo BASE_URL; ?>/auth/login">Đăng Nhập</a>
                <a href="<?php echo BASE_URL; ?>/auth/register">Đăng Ký</a>
            <?php endif; ?>
        </div>

    </nav> <?php \display_flash_message(); ?>

    <div class="container">