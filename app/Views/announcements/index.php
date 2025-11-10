<?php
// Nạp header MỚI (đã có layout sidebar)
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="flex justify-end mb-5">
    <?php
    // PHÂN QUYỀN: Chỉ Admin hoặc Subadmin mới thấy nút
    if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) :
    ?>
        <a href="<?php echo BASE_URL; ?>/announcement/create"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
            Đăng Thông báo
        </a>
    <?php endif; ?>
</div>

<div class="space-y-6">

    <?php if (empty($data['announcements'])) : ?>

        <div class="bg-white shadow rounded-lg p-6 text-center">
            <p class="text-gray-500">Chưa có thông báo nào.</p>
        </div>

    <?php else : ?>

        <?php foreach ($data['announcements'] as $item) : ?>

            <div class="bg-white shadow rounded-lg overflow-hidden">

                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">

                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Đăng bởi <strong><?php echo htmlspecialchars($item['author_name']); ?></strong>
                                vào <?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?>
                            </p>
                        </div>

                        <div class="flex-shrink-0">
                            <?php
                            $tag_text = 'Lỗi';
                            $tag_class = 'bg-gray-100 text-gray-800'; // Mặc định

                            if ($item['target_department_id'] !== NULL) {
                                $tag_text = 'Ban: ' . htmlspecialchars($item['department_name']);
                                $tag_class = 'bg-yellow-100 text-yellow-800'; // Tag Ban
                            } elseif ($item['visibility'] == 'public') {
                                $tag_text = 'Thông báo Chung';
                                $tag_class = 'bg-green-100 text-green-800'; // Tag Public
                            } else {
                                $tag_text = 'Nội bộ CLB';
                                $tag_class = 'bg-blue-100 text-blue-800'; // Tag Internal
                            }
                            ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold <?php echo $tag_class; ?>">
                                <?php echo $tag_text; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-5 sm:p-6">
                    <div class="prose max-w-none text-gray-700">
                        <?php echo $item['content']; ?>
                    </div>
                </div>

                <?php
                // Card Footer: Chứa nút Sửa/Xóa (chỉ Admin thấy)
                if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) :
                ?>
                    <div class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200 text-right space-x-3">
                        <a href="<?php echo BASE_URL; ?>/announcement/edit/<?php echo $item['id']; ?>"
                            class="inline-flex justify-center items-center w-16 h-8 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600">
                            Sửa
                        </a>

                        <form action="<?php echo BASE_URL; ?>/announcement/destroy/<?php echo $item['id']; ?>" method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit"
                                class="inline-flex justify-center items-center w-16 h-8 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700"
                                onclick="return confirm('Bạn có chắc muốn xóa thông báo này?');">
                                Xóa
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div> <?php endforeach; ?>
    <?php endif; ?>

</div> <?php
        // Nạp footer MỚI
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
        ?>