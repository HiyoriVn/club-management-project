<?php require_once ROOT_PATH . '/app/Views/layout/header.php'; ?>

<div class="max-w-6xl mx-auto">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <ion-icon name="folder-open-outline" class="mr-2 text-indigo-600"></ion-icon>
                Kho Tài liệu
            </h2>
            <p class="text-sm text-gray-500 mt-1">Lưu trữ và chia sẻ tài liệu nội bộ.</p>
        </div>

        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
            class="mt-4 md:mt-0 btn btn-primary">
            <ion-icon name="cloud-upload-outline" class="mr-2 text-lg"></ion-icon>
            Tải lên tài liệu
        </button>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6 flex gap-4">
        <div class="w-full md:w-64">
            <select onchange="window.location.href='?dept_id='+this.value" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value=""> Tất cả tài liệu </option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= $dept['id'] ?>" <?= (isset($_GET['dept_id']) && $_GET['dept_id'] == $dept['id']) ? 'selected' : '' ?>>
                        Ban: <?= htmlspecialchars($dept['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên file</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phân loại</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người đăng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đăng</th>
                    <th class="relative px-6 py-3"><span class="sr-only">Hành động</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($files)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            Chưa có tài liệu nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($files as $file): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded flex items-center justify-center text-2xl text-gray-500">
                                        <?php
                                        // Icon theo đuôi file
                                        $ext = strtolower($file['type']);
                                        $icon = 'document-outline';
                                        if (in_array($ext, ['jpg', 'png', 'jpeg', 'gif'])) $icon = 'image-outline';
                                        if (in_array($ext, ['pdf'])) $icon = 'document-text-outline';
                                        if (in_array($ext, ['xls', 'xlsx', 'csv'])) $icon = 'grid-outline';
                                        if (in_array($ext, ['doc', 'docx'])) $icon = 'document-outline';
                                        if (in_array($ext, ['zip', 'rar'])) $icon = 'file-tray-full-outline';
                                        ?>
                                        <ion-icon name="<?= $icon ?>"></ion-icon>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-xs" title="<?= htmlspecialchars($file['file_name']) ?>">
                                            <?= htmlspecialchars($file['file_name']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($file['department_id']): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Ban: <?= htmlspecialchars($file['department_name']) ?>
                                    </span>
                                <?php elseif ($file['project_id']): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Dự án: <?= htmlspecialchars($file['project_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Chung
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($file['uploader_name']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y', strtotime($file['uploaded_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= BASE_URL ?>/../uploads/<?= $file['file_path'] ?>" download="<?= $file['file_name'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Tải xuống">
                                    <ion-icon name="download-outline" class="text-lg"></ion-icon>
                                </a>

                                <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_id'] == $file['uploaded_by']): ?>
                                    <a href="<?= BASE_URL ?>/file/delete/<?= $file['id'] ?>" onclick="return confirm('Xóa file này vĩnh viễn?')" class="text-red-600 hover:text-red-900" title="Xóa">
                                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="uploadModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('uploadModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <ion-icon name="cloud-upload" class="text-indigo-600 text-xl"></ion-icon>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Tải lên tài liệu mới
                        </h3>
                        <div class="mt-4">
                            <form action="<?= BASE_URL ?>/file/upload" method="POST" enctype="multipart/form-data">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Chọn file</label>
                                        <input type="file" name="file" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Thuộc Ban (Tùy chọn)</label>
                                        <select name="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value=""> Tài liệu chung </option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="pt-4 flex justify-end gap-3">
                                        <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="btn btn-secondary-outline">Hủy</button>
                                        <button type="submit" class="btn btn-primary">Tải lên</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/Views/layout/footer.php'; ?>