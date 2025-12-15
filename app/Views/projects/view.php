<?php require_once 'app/Views/layout/header.php'; ?>

<div class="max-w-6xl mx-auto text-center py-10">
    <div class="bg-white p-10 rounded-lg shadow-lg inline-block">
        <ion-icon name="construct-outline" class="text-6xl text-indigo-500 mb-4"></ion-icon>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Quản lý Công việc</h2>
        <p class="text-gray-500 mb-6">Bạn đang xem danh sách công việc của dự án <strong><?= htmlspecialchars($project['name']) ?></strong>.</p>

        <div class="flex justify-center space-x-4">
            <a href="<?= BASE_URL ?>/task/kanban/<?= $project['id'] ?>" class="btn btn-primary px-6 py-3">
                <ion-icon name="grid-outline" class="mr-2"></ion-icon> Mở Kanban Board
            </a>
            <a href="<?= BASE_URL ?>/task/list/<?= $project['id'] ?>" class="btn btn-secondary-outline px-6 py-3">
                <ion-icon name="list-outline" class="mr-2"></ion-icon> Xem Danh sách
            </a>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>