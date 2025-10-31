<?php
// Nạp header, đây là file layout chung
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="event-list" style="max-width: 1000px; margin: 20px auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1><?php echo $data['title']; ?> (<?php echo count($data['events']); ?>)</h1>

        <?php
        // PHÂN QUYỀN: Chỉ Admin hoặc Subadmin mới thấy nút "Tạo Sự kiện"
        // Biến $_SESSION['user_role'] được tạo khi đăng nhập
        if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) :
        ?>
            <a href="<?php echo BASE_URL; ?>/event/create" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                + Tạo Sự kiện mới
            </a>
        <?php endif; // Hết phần kiểm tra vai trò 
        ?>
    </div>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Sự kiện</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Thời gian bắt đầu</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Thời gian kết thúc</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Địa điểm</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Người tạo</th>

                <th style="padding: 10px; border: 1px solid #ddd; text-align: left; width: 120px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Kiểm tra xem mảng $data['events'] (từ Controller) có rỗng không
            if (empty($data['events'])) :
            ?>
                <tr>
                    <td colspan="6" style="padding: 10px; text-align: center;">Chưa có Sự kiện nào.</td>
                </tr>
            <?php
            // Ngược lại, nếu có dữ liệu
            else :
            ?>
                <?php
                // Bắt đầu vòng lặp, lặp qua mỗi sự kiện
                // $data['events'] là mảng, $event là từng phần tử
                foreach ($data['events'] as $event) :
                ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                            <p style="font-size: 0.9em; color: #555;"><?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 100))); ?>...</p>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo date('d/m/Y H:i', strtotime($event['start_time'])); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $event['end_time'] ? date('d/m/Y H:i', strtotime($event['end_time'])) : 'N/A'; ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($event['location']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($event['creator_name']); ?></td>

                        <td style="padding: 10px; border: 1px solid #ddd; width: 120px;">

                            <?php
                            // LOGIC 1: NÚT ĐĂNG KÝ/HỦY (CHO NGƯỜI ĐÃ ĐĂNG NHẬP)
                            // CHỈ HIỆN NÚT NÀY NẾU ĐÃ ĐĂNG NHẬP
                            if (isset($_SESSION['user_id'])) :

                                // Kiểm tra xem user đã đăng ký sự kiện này chưa
                                $is_registered = in_array($event['id'], $data['my_registrations']);
                            ?>
                                <form action="<?php echo BASE_URL; ?>/event/toggleRegistration/<?php echo $event['id']; ?>" method="POST" style="margin-bottom: 5px;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <?php if ($is_registered) : // Nếu đã đăng ký 
                                    ?>
                                        <button type="submit"
                                            style="background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; padding: 5px 10px; width: 100%;"
                                            onclick="return confirm('Bạn có chắc muốn HỦY đăng ký sự kiện này?');">
                                            Hủy đăng ký
                                        </button>
                                    <?php else : // Nếu chưa đăng ký 
                                    ?>
                                        <button type="submit"
                                            style="background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; padding: 5px 10px; width: 100%;">
                                            Đăng ký
                                        </button>
                                    <?php endif;
                                    ?>
                                </form>

                            <?php
                            else:
                            ?>
                                <a href="<?php echo BASE_URL; ?>/auth/login" style="font-size: 0.9em; color: #007bff; text-decoration: none;">Đăng nhập để đăng ký</a>
                            <?php endif; // Hết logic if(isset($_SESSION['user_id'])) 
                            ?>

                            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>

                                <a href="<?php echo BASE_URL; ?>/event/attendance/<?php echo $event['id']; ?>"
                                    style="background: #17a2b8; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; display: block; margin-top: 8px; text-align: center; font-size: 0.9em;">
                                    Điểm danh
                                </a>

                                <a href="<?php echo BASE_URL; ?>/event/edit/<?php echo $event['id']; ?>"
                                    style="color: blue; text-decoration: none; display: block; margin-top: 8px; font-size: 0.9em;">
                                    Sửa (Admin)
                                </a>

                                <form action="<?php echo BASE_URL; ?>/event/destroy/<?php echo $event['id']; ?>" method="POST" style="margin: 0; margin-top: 5px;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit"
                                        style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0; font: inherit; font-size: 0.9em; text-align: left;"
                                        onclick="return confirm('Bạn có chắc muốn xóa Sự kiện [<?php echo htmlspecialchars(addslashes($event['title'])); ?>]?');">
                                        Xóa (Admin)
                                    </button>
                                </form>
                            <?php endif;
                            ?>
                        </td>
                    </tr>
                <?php endforeach;
                ?>
            <?php endif;
            ?>
        </tbody>
    </table>
</div>

<?php
// Nạp footer, đây là file layout chung
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>