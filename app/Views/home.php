<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg p-8 text-center">

        <h1 class="text-3xl font-bold text-gray-900">
            <?php echo $data['title']; ?>
        </h1>
        <p class="mt-3 text-lg text-gray-600">
            <?php echo $data['description']; ?>
        </p>

        <div class="mt-8">
            <?php if ($data['isLoggedIn']) : ?>
                <a href="<?php echo BASE_URL; ?>/dashboard"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    Đi tới Bảng điều khiển
                </a>
            <?php else : ?>
                <a href="<?php echo BASE_URL; ?>/auth/login"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    Đăng nhập ngay
                </a>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>