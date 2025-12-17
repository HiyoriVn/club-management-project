<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <ion-icon name="business-outline" class="mr-2 text-indigo-600"></ion-icon>
                Cơ cấu tổ chức
            </h2>
            <p class="text-sm text-gray-500 mt-1">Quản lý các Ban và nhân sự.</p>
        </div>

        <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <a href="<?= BASE_URL ?>/department/create" class="btn btn-primary">
                <ion-icon name="add-circle-outline" class="mr-2 text-lg"></ion-icon>
                Thêm Ban mới
            </a>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($departments)): ?>
            <div class="col-span-full text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                <p class="text-gray-500">Chưa có ban nào được tạo.</p>
            </div>
        <?php else: ?>
            <?php foreach ($departments as $dept): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow relative">

                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl mr-3">
                                <ion-icon name="people"></ion-icon>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 line-clamp-1" title="<?= htmlspecialchars($dept['name']) ?>">
                                    <?= htmlspecialchars($dept['name']) ?>
                                </h3>
                                <span class="text-xs font-medium text-gray-500">
                                    <?= $dept['member_count'] ?? 0 ?> thành viên
                                </span>
                            </div>
                        </div>

                        <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <div class="relative">
                                <button onclick="toggleDropdown('dropdown-<?= $dept['id'] ?>')"
                                    class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition-colors focus:outline-none">
                                    <ion-icon name="ellipsis-vertical" class="text-xl"></ion-icon>
                                </button>

                                <div id="dropdown-<?= $dept['id'] ?>"
                                    class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 border border-gray-100 transform origin-top-right transition-all">

                                    <a href="<?= BASE_URL ?>/department/members/<?= $dept['id'] ?>"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 flex items-center">
                                        <ion-icon name="people-outline" class="mr-2"></ion-icon> Quản lý thành viên
                                    </a>

                                    <a href="<?= BASE_URL ?>/department/edit/<?= $dept['id'] ?>"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-yellow-600 flex items-center">
                                        <ion-icon name="create-outline" class="mr-2"></ion-icon> Đổi tên / Mô tả
                                    </a>

                                    <div class="border-t border-gray-100 my-1"></div>

                                    <a href="<?= BASE_URL ?>/department/delete/<?= $dept['id'] ?>"
                                        onclick="return confirm('CẢNH BÁO: Xóa ban sẽ xóa toàn bộ liên kết thành viên trong ban đó.\nBạn có chắc chắn không?')"
                                        class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                        <ion-icon name="trash-outline" class="mr-2"></ion-icon> Xóa Ban
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-500 line-clamp-3 h-16">
                            <?= !empty($dept['description']) ? nl2br(htmlspecialchars($dept['description'])) : 'Chưa có mô tả cho ban này.' ?>
                        </p>
                    </div>

                    <div class="px-6 py-3 bg-gray-50 rounded-b-lg border-t border-gray-100">
                        <a href="<?= BASE_URL ?>/department/members/<?= $dept['id'] ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center justify-center">
                            Xem chi tiết <ion-icon name="arrow-forward" class="ml-1"></ion-icon>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleDropdown(id) {
        // 1. Tìm menu đang được click
        var dropdown = document.getElementById(id);

        // 2. Đóng tất cả các menu khác đang mở (để tránh mở nhiều cái cùng lúc)
        var allDropdowns = document.getElementsByClassName('dropdown-menu');
        for (var i = 0; i < allDropdowns.length; i++) {
            if (allDropdowns[i].id !== id) {
                allDropdowns[i].classList.add('hidden');
            }
        }

        // 3. Toggle trạng thái của menu hiện tại
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }

        // Ngăn chặn sự kiện click lan ra ngoài ngay lập tức (nếu không nó sẽ bị đóng ngay bởi window.onclick)
        event.stopPropagation();
    }

    // 4. Bắt sự kiện click ra ngoài màn hình để đóng menu
    window.onclick = function(event) {
        if (!event.target.closest('.dropdown-menu') && !event.target.closest('button')) {
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (!openDropdown.classList.contains('hidden')) {
                    openDropdown.classList.add('hidden');
                }
            }
        }
    }
</script>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>