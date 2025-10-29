<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="file-manager" style="max-width: 1000px; margin: 20px auto;">
    <h1><?php echo $data['title']; ?></h1>

    <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
        <div class="upload-form" style="margin-bottom: 30px; padding: 20px; border: 1px dashed #007bff; border-radius: 5px; background: #eef7ff;">
            <h3>Upload File mới</h3>

            <?php if (!empty($data['upload_err'])): ?>
                <p style="color: red; background: #ffebee; padding: 10px; border: 1px solid red; border-radius: 3px;"><?php echo $data['upload_err']; ?></p>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/file/upload" method="POST" enctype="multipart/form-data">

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="fileToUpload">Chọn file:</label><br>
                    <input type="file" name="fileToUpload" id="fileToUpload" required style="margin-top: 5px;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="department_id">Thuộc Ban (tùy chọn):</label>
                    <select name="department_id" id="department_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; background: #fff; margin-top: 5px;">
                        <option value="">-- (Chung cho cả CLB) --</option>
                        <?php foreach ($data['all_departments'] as $dep) : ?>
                            <option value="<?php echo $dep['id']; ?>"><?php echo htmlspecialchars($dep['NAME']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="submit" value="Upload File" name="submit" style="background: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            </form>
        </div>
    <?php endif; ?>
    <h3>Danh sách Tài liệu</h3>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tên File</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Thuộc Ban</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Người upload</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Ngày upload</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['files'])) : ?>
                <tr>
                    <td colspan="5" style="padding: 10px; text-align: center;">Chưa có tài liệu nào.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($data['files'] as $file) : ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($file['file_name']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($file['department_name'] ?? 'Chung'); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($file['uploader_name']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo date('d/m/Y H:i', strtotime($file['uploaded_at'])); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <a href="<?php echo UPLOAD_URL . $file['file_path']; ?>" target="_blank" style="color: green; text-decoration: none; margin-right: 10px;" title="Tải về">
                                &#x2B07; Tải về </a>

                            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
                                <form action="<?php echo BASE_URL; ?>/file/destroy/<?php echo $file['id']; ?>" method="POST" style="display: inline-block; margin: 0;">
                                    <button type="submit" style="background: none; border: none; color: red; cursor: pointer; padding: 0;"
                                        onclick="return confirm('Bạn có chắc muốn xóa file [<?php echo htmlspecialchars(addslashes($file['file_name'])); ?>]?');" title="Xóa">
                                        &#x274C; </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>