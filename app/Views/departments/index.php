<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-xl font-bold text-gray-800 flex items-center">
        <ion-icon name="git-network-outline" class="mr-2 text-indigo-600"></ion-icon>
        Danh sách Ban Chuyên môn
    </h2>

    <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <a href="<?= BASE_URL ?>/department/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
            <ion-icon name="add-circle-outline" class="mr-2 text-lg"></ion-icon>
            Thêm Ban mới
        </a>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($departments)): ?>
        <div class="col-span-full text-center py-10 bg-white rounded-lg shadow">
            <ion-icon name="albums-outline" class="text-4xl text-gray-300 mb-2"></ion-icon>
            <p class="text-gray-500">Chưa có ban chuyên môn nào.</p>
        </div>
    <?php else: ?>
        <?php foreach ($departments as $dept): ?>
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-300 overflow-hidden flex flex-col h-full border border-gray-100">
                <div class="p-6 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-bold">
                            <?= strtoupper(substr($dept['name'], 0, 1)) ?>
                        </div>
                        <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <div class="relative group">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <ion-icon name="ellipsis-vertical" class="text-xl"></ion-icon>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block border border-gray-100">
                                    <a href="<?= BASE_URL ?>/department/edit/<?= $dept['id'] ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Chỉnh sửa</a>
                                    <a href="<?= BASE_URL ?>/department/delete/<?= $dept['id'] ?>" onclick="return confirm('Xóa ban này sẽ xóa toàn bộ liên kết thành viên. Tiếp tục?')" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Xóa ban</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-2"><?= htmlspecialchars($dept['name']) ?></h3>
                    <p class="text-sm text-gray-500 line-clamp-3">
                        <?= !empty($dept['description']) ? nl2br(htmlspecialchars($dept['description'])) : 'Chưa có mô tả.' ?>
                    </p>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-xs font-medium text-gray-500">
                        <ion-icon name="people-outline" class="align-middle mr-1"></ion-icon>
                        <?= isset($dept['member_count']) ? $dept['member_count'] : '0' ?> thành viên
                    </span>
                    <a href="<?= BASE_URL ?>/department/members/<?= $dept['id'] ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center">
                        Quản lý thành viên <ion-icon name="arrow-forward-outline" class="ml-1"></ion-icon>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>