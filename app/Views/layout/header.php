<?php
// Helper để active menu
function isActive($path)
{
    $uri = $_SERVER['REQUEST_URI'];
    return strpos($uri, $path) !== false ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - Club Management' : 'Club Management' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/css/custom.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">Club Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= isActive('/dashboard') ?>" href="<?= BASE_URL ?>/dashboard">
                                <i class="fas fa-tachometer-alt me-1"></i> Tổng quan
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= isActive('/project') ?>" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-project-diagram me-1"></i> Hoạt động
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/project">Tất cả Dự án & Sự kiện</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/project?type=project">Chỉ Dự án</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/project?type=event">Chỉ Sự kiện</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= isActive('/department') ?>" href="<?= BASE_URL ?>/department">
                                <i class="fas fa-users me-1"></i> Ban chuyên môn
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= isActive('/user') ?>" href="<?= BASE_URL ?>/user">
                                <i class="fas fa-user-friends me-1"></i> Thành viên
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= isActive('/transaction') ?>" href="<?= BASE_URL ?>/transaction">
                                <i class="fas fa-wallet me-1"></i> Tài chính
                            </a>
                        </li>

                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= isActive('/report') ?>" href="<?= BASE_URL ?>/report/activity_logs">
                                    <i class="fas fa-file-alt me-1"></i> Báo cáo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= isActive('/setting') ?>" href="<?= BASE_URL ?>/setting">
                                    <i class="fas fa-cog me-1"></i> Cài đặt
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['user_name'] ?? 'User' ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile"><i class="fas fa-id-card me-2"></i>Hồ sơ</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container main-content">
        <?php
        $flash = \get_flash_message();
        if ($flash):
        ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'danger' : 'success' ?> alert-dismissible fade show">
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>