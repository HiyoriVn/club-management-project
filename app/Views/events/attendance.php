<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="mb-5">
    <a href="<?php echo BASE_URL; ?>/event" class="text-sm font-medium text-blue-600 hover:text-blue-800">
        &larr; Quay lại Danh sách Sự kiện
    </a>
</div>

<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            <?php echo $data['title']; ?>
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            Tổng số người đã đăng ký: <strong><?php echo count($data['participants']); ?></strong>
        </p>
    </div>
    <div class="border-t border-gray-200 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Thành viên</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($data['participants'])) : ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Chưa có ai đăng ký sự kiện này.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($data['participants'] as $p) : ?>
                        <tr class="<?php echo ($p['status'] == 'checked_in') ? 'bg-green-50' : ''; ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($p['NAME']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($p['email']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($p['status'] == 'checked_in') : ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Đã Check-in
                                    </span>
                                <?php elseif ($p['status'] == 'registered') : ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Đã Đăng ký
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <?php echo htmlspecialchars($p['status']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <?php if ($p['status'] == 'registered') : ?>
                                    <form action="<?php echo BASE_URL; ?>/event/checkin/<?php echo $data['event']['id']; ?>/<?php echo $p['attendance_id']; ?>" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                            Check-in
                                        </button>
                                    </form>
                                <?php elseif ($p['status'] == 'checked_in') : ?>
                                    <form action="<?php echo BASE_URL; ?>/event/undocheckin/<?php echo $data['event']['id']; ?>/<?php echo $p['attendance_id']; ?>" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                                            Hoàn tác
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