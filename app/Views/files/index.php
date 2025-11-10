<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <?php
    // Xác định xem user có phải là admin/subadmin không
    $is_admin = (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin'));

    // CỘT 1: Form Upload (Chỉ Admin/Subadmin thấy)
    if ($is_admin) :
    ?>
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <form action="<?php echo BASE_URL; ?>/file/upload" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Đăng tải tệp mới</h3>

                        <?php if (!empty($data['upload_err'])): ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo $data['upload_err']; ?></p>
                        <?php endif; ?>

                        <div class="mt-5 space-y-4">
                            <div>
                                <label for="fileToUpload" class="block text-sm font-medium text-gray-700 mb-1">Chọn tệp:</label>
                                <input type="file" name="fileToUpload" id="fileToUpload" required
                                    class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-full file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Thuộc Ban (tùy chọn):</label>
                                <select name="department_id" id="department_id" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                    <option value="">Công khai</option>
                                    <?php foreach ($data['all_departments'] as $dep) : ?>
                                        <option value="<?php echo $dep['id']; ?>"><?php echo htmlspecialchars($dep['NAME']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-right">
                        <button type="submit" name="submit"
                            class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            Đăng tải
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>


    <div class="<?php echo $is_admin ? 'lg:col-span-2' : 'lg:col-span-3'; ?>">
        <div class="bg-white shadow overflow-hidden rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Danh sách Tài liệu
                </h3>
            </div>
            <div class="border-t border-gray-200 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên tài liệu</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thuộc Ban</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người đăng tải</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian đăng tải</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Hành động</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($data['files'])) : ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có tài liệu nào.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($data['files'] as $file) : ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($file['file_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($file['department_name'] ?? 'Chung'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($file['uploader_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo date('d/m/Y H:i', strtotime($file['uploaded_at'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                        <a href="<?php echo UPLOAD_URL . $file['file_path']; ?>" target="_blank"
                                            class="inline-flex items-center justify-center w-16 h-8 px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                            Tải về
                                        </a>

                                        <?php if ($is_admin) : ?>
                                            <form action="<?php echo BASE_URL; ?>/file/destroy/<?php echo $file['id']; ?>" method="POST" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <button type="submit" class="inline-flex justify-center items-center w-16 h-8 px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700"
                                                    onclick="return confirm('Bạn có chắc muốn xóa file [<?php echo htmlspecialchars(addslashes($file['file_name'])); ?>]?');" title="Xóa">
                                                    Xóa
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div> <?php
        // Nạp footer MỚI
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
        ?>