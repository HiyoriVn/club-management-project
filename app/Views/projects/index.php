<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="flex justify-end mb-5">
    <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
        <a href="<?php echo BASE_URL; ?>/project/create"
            class="btn btn-primary">
            <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
            Tạo Dự án mới
        </a>
    <?php endif; ?>
</div>

<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Danh sách Dự án (<?php echo count($data['projects']); ?>)
        </h3>
    </div>
    <div class="border-t border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Dự án</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leader</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thuộc Ban</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày bắt đầu</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Hành động</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($data['projects'])) : ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Chưa có Dự án nào.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($data['projects'] as $project) : ?>
                        <tr>
                            <td class="px-6 py-4">
                                <a href="<?php echo BASE_URL; ?>/project/tasks/<?php echo $project['id']; ?>" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                    <?php echo htmlspecialchars($project['NAME']); ?>
                                </a>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>...</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $status = $project['STATUS'];
                                $status_text = $status;
                                $tag_class = 'bg-gray-100 text-gray-800'; // Mặc định (planning)

                                switch ($status) {
                                    case 'in_progress':
                                        $status_text = 'Đang làm';
                                        $tag_class = 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'completed':
                                        $status_text = 'Hoàn thành';
                                        $tag_class = 'bg-green-100 text-green-800';
                                        break;
                                    case 'cancelled':
                                        $status_text = 'Đã hủy';
                                        $tag_class = 'bg-red-100 text-red-800';
                                        break;
                                    case 'planning':
                                        $status_text = 'Kế hoạch';
                                        break;
                                }
                                ?>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $tag_class; ?>">
                                    <?php echo htmlspecialchars($status_text); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($project['leader_name'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($project['department_name'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $project['start_date'] ? date('d/m/Y', strtotime($project['start_date'])) : 'N/A'; ?></td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
                                    <a href="<?php echo BASE_URL; ?>/project/manage/<?php echo $project['id']; ?>"
                                        class="btn-action btn-secondary">
                                        Quản lý
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/project/edit/<?php echo $project['id']; ?>" class="btn-action btn-warning">Sửa</a>

                                    <form action="<?php echo BASE_URL; ?>/project/destroy/<?php echo $project['id']; ?>" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit" class="btn-action btn-danger"
                                            onclick="return confirm('Bạn có chắc muốn xóa Dự án [<?php echo htmlspecialchars(addslashes($project['NAME'])); ?>]?');">
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

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>