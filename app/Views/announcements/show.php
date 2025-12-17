<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-5">
        <a href="<?= BASE_URL ?>/announcement" class="text-gray-500 hover:text-gray-700 flex items-center transition-colors">
            <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Quay lại bảng tin
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">

        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <div class="flex items-center space-x-3 mb-4">
                <?php if (empty($announcement['target_department_id'])): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                        <ion-icon name="globe-outline" class="mr-1"></ion-icon> Toàn CLB
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                        <ion-icon name="people-outline" class="mr-1"></ion-icon>
                        Ban: <?= htmlspecialchars($announcement['department_name'] ?? 'Không xác định') ?>
                    </span>
                <?php endif; ?>

                <span class="text-gray-300">|</span>

                <span class="text-gray-500 text-sm flex items-center">
                    <ion-icon name="calendar-outline" class="mr-1"></ion-icon>
                    <?= date('d/m/Y H:i', strtotime($announcement['created_at'])) ?>
                </span>
            </div>

            <h1 class="text-3xl font-extrabold text-gray-900 leading-tight">
                <?= htmlspecialchars($announcement['title']) ?>
            </h1>

            <div class="mt-6 flex items-center">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                    <?= strtoupper(substr($announcement['author_name'] ?? 'A', 0, 2)) ?>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">
                        <?= htmlspecialchars($announcement['author_name'] ?? 'Người dùng đã xóa') ?>
                    </p>
                    <p class="text-xs text-gray-500">Người đăng</p>
                </div>
            </div>
        </div>

        <div class="px-8 py-8">
            <div class="prose prose-indigo max-w-none text-gray-800">
                <?php
                // Giải mã HTML entities để hiển thị đúng định dạng văn bản
                echo html_entity_decode($announcement['content']);
                ?>
            </div>
        </div>

        <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_id'] == $announcement['posted_by']): ?>
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>/announcement/edit/<?= $announcement['id'] ?>" class="btn btn-sm btn-secondary-outline">
                    <ion-icon name="create-outline" class="mr-1"></ion-icon> Chỉnh sửa
                </a>

                <a href="<?= BASE_URL ?>/announcement/delete/<?= $announcement['id'] ?>"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này không? Hành động này không thể hoàn tác.')"
                    class="btn btn-sm btn-danger">
                    <ion-icon name="trash-outline" class="mr-1"></ion-icon> Xóa bài
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>