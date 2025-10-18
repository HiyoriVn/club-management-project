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
    </style>
</head>

<body>

    <nav class="navbar" style="display: flex; justify-content: space-between; align-items: center;">

        <div>
            <a href="<?php echo BASE_URL; ?>">Trang Chủ</a>
            <a href="<?php echo BASE_URL; ?>/dashboard">Dashboard</a>
            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
                <a href="<?php echo BASE_URL; ?>/department">Quản lý Ban</a>
            <?php endif; ?>
        </div>

        <div>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <span style="color: #fff; margin-right: 15px;">
                    Xin chào, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                    (<?php echo htmlspecialchars($_SESSION['user_role']); ?>)
                </span>
                <a href="<?php echo BASE_URL; ?>/auth/logout">Đăng Xuất</a>

            <?php else : ?>
                <a href="<?php echo BASE_URL; ?>/auth/login">Đăng Nhập</a>
                <a href="<?php echo BASE_URL; ?>/auth/register">Đăng Ký</a>
            <?php endif; ?>
        </div>

    </nav>

    <div class="container">