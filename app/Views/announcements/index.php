<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <ion-icon name="newspaper-outline" class="mr-2 text-indigo-600"></ion-icon>
                Bảng tin Thông báo
            </h2>
            <p class="text-sm text-gray-500 mt-1">Cập nhật tin tức và thông báo mới nhất từ Ban chủ nhiệm.</p>
        </div>

        <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin'): ?>
            <a href="<?= BASE_URL ?>/announcement/create" class="btn btn-primary">
                <ion-icon name="create-outline" class="mr-2 text-lg"></ion-icon>
                Đăng thông báo
            </a>
        <?php endif; ?>
    </div>

    <div class="space-y-6">
        <?php if (empty($announcements)): ?>
            <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                <ion-icon name="notifications-off-outline" class="text-4xl text-gray-300 mb-3"></ion-icon>
                <p class="text-gray-500">Hiện tại chưa có thông báo nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($announcements as $news): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center space-x-2 mb-3">
                                <?php if (empty($news['target_department_id'])): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <ion-icon name="globe-outline" class="mr-1"></ion-icon> Toàn CLB
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <ion-icon name="people-outline" class="mr-1"></ion-icon> <?= htmlspecialchars($news['department_name'] ?? 'Ban chuyên môn') ?>
                                    </span>
                                <?php endif; ?>

                                <span class="text-sm text-gray-400">•</span>
                                <span class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($news['created_at'])) ?></span>
                            </div>

                            <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_id'] == $news['posted_by']): ?>
                                <div class="relative group">
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <ion-icon name="ellipsis-horizontal" class="text-xl"></ion-icon>
                                    </button>
                                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block border border-gray-100">
                                        <a href="<?= BASE_URL ?>/announcement/edit/<?= $news['id'] ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Chỉnh sửa</a>
                                        <a href="<?= BASE_URL ?>/announcement/delete/<?= $news['id'] ?>" onclick="return confirm('Bạn chắc chắn muốn xóa?')" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Xóa bài</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-indigo-600 transition-colors">
                            <a href="<?= BASE_URL ?>/announcement/view/<?= $news['id'] ?>">
                                <?= htmlspecialchars($news['title']) ?>
                            </a>
                        </h3>

                        <div class="text-gray-600 prose prose-sm max-w-none line-clamp-3">
                            <?= \purify_html(html_entity_decode($news['content'])) ?>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t pt-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                    <?= strtoupper(substr($news['author_name'] ?? 'A', 0, 2)) ?>
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-700"><?= htmlspecialchars($news['author_name'] ?? 'Admin') ?></span>
                            </div>
                            <a href="<?= BASE_URL ?>/announcement/view/<?= $news['id'] ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                Đọc tiếp <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>