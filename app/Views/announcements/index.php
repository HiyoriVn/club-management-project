<?php
// Nạp header, đây là file layout chung
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="announcement-list" style="max-width: 800px; margin: 20px auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1><?php echo $data['title']; ?></h1>

        <?php
        // PHÂN QUYỀN: Chỉ Admin hoặc Subadmin mới thấy nút "Đăng Thông báo"
        if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) :
        ?>
            <a href="<?php echo BASE_URL; ?>/announcement/create" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                + Đăng Thông báo
            </a>
        <?php endif; // Hết phần kiểm tra vai trò 
        ?>
    </div>
    <?php
    // Kiểm tra xem mảng $data['announcements'] (từ Controller) có rỗng không
    if (empty($data['announcements'])) :
    ?>
        <p style="text-align: center; color: #6c757d;">Chưa có thông báo nào.</p>
    <?php
    // Ngược lại, nếu có dữ liệu
    else :
    ?>
        <?php
        // Bắt đầu vòng lặp, lặp qua mỗi thông báo ($item)
        foreach ($data['announcements'] as $item) :
        ?>
            <div class="card" style="border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">

                <div class="card-header" style="background: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0;"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <span style="font-size: 0.9em; color: #6c757d;">
                            Đăng bởi <strong><?php echo htmlspecialchars($item['author_name']); ?></strong>
                            vào <?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?>
                        </span>
                    </div>

                    <?php
                    $tag_text = 'Lỗi';
                    $tag_bg = '#f8f9fa';
                    $tag_border = '#ddd';

                    // 1. Ưu tiên cao nhất: Gửi cho Ban cụ thể
                    if ($item['target_department_id'] !== NULL) {
                        $tag_text = 'Ban: ' . htmlspecialchars($item['department_name']);
                        $tag_bg = '#fffbe6'; // Vàng nhạt
                        $tag_border = '#ffe58f';
                    }
                    // 2. Gửi Chung (Public)
                    elseif ($item['visibility'] == 'public') {
                        $tag_text = 'Thông báo Chung';
                        $tag_bg = '#f0f9eb'; // Xanh lá nhạt
                        $tag_border = '#d9f0c5';
                    }
                    // 3. Gửi Nội bộ (Internal)
                    else {
                        $tag_text = 'Nội bộ CLB';
                        $tag_bg = '#e6f7ff'; // Xanh dương nhạt
                        $tag_border = '#b3e0ff';
                    }
                    ?>
                    <span style="font-weight: bold; font-size: 0.9em; padding: 5px 10px; border-radius: 5px; background: <?php echo $tag_bg; ?>; border: 1px solid <?php echo $tag_border; ?>;">
                        <?php echo $tag_text; ?>
                    </span>
                </div>

                <div class="card-body" style="padding: 15px;">
                    <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                </div>

                <?php
                // PHÂN QUYỀN: Chỉ Admin/Subadmin mới thấy Sửa/Xóa
                if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) :
                ?>
                    <div class="card-footer" style="background: #f8f9fa; padding: 10px 15px; border-top: 1px solid #ddd; text-align: right;">
                        <a href="<?php echo BASE_URL; ?>/announcement/edit/<?php echo $item['id']; ?>" style="color: blue; text-decoration: none; margin-right: 15px;">Sửa</a>

                        <form action="<?php echo BASE_URL; ?>/announcement/destroy/<?php echo $item['id']; ?>" method="POST" style="display: inline-block; margin: 0;">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;"
                                onclick="return confirm('Bạn có chắc muốn xóa thông báo này?');">
                                Xóa
                            </button>
                        </form>
                    </div>
                <?php endif; // Hết phần kiểm tra vai trò 
                ?>
            </div>
        <?php endforeach; // Hết vòng lặp 
        ?>
    <?php endif; // Hết kiểm tra mảng rỗng 
    ?>

</div>

<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>