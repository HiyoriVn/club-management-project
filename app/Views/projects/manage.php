<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="mb-5">
    <a href="<?php echo BASE_URL; ?>/project" class="text-sm font-medium text-blue-600 hover:text-blue-800">
        &larr; Quay lại Danh sách Dự án
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form action="<?php echo BASE_URL; ?>/project/addMember/<?php echo $data['project']['id']; ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Thêm thành viên</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Cho dự án: <?php echo htmlspecialchars($data['project']['NAME']); ?>
                    </p>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">1. Chọn Thành viên:</label>
                            <select name="user_id" id="user_id" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                <option value="">-- Chọn một người --</option>
                                <?php foreach ($data['all_users'] as $user): ?>
                                    <?php if ($user['system_role'] == 'guest') continue; // Lọc Guest 
                                    ?>
                                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['NAME']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">2. Chọn Vai trò:</label>
                            <select name="role" id="role" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                <option value="Member" selected>Member (Thành viên)</option>
                                <option value="Leader">Leader (Trưởng dự án)</option>
                                <option value="Collaborator">Collaborator (Cộng tác viên)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-right">
                    <button type="submit"
                        class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
                        Thêm vào Dự án
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white shadow overflow-hidden rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Thành viên hiện tại (<?php echo count($data['current_members']); ?>)
                </h3>
            </div>
            <div class="border-t border-gray-200 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Thành viên</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò trong Dự án</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Hành động</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($data['current_members'])) : ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Dự án này chưa có thành viên.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($data['current_members'] as $member) : ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($member['NAME']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($member['project_role']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="<?php echo BASE_URL; ?>/project/removeMember/<?php echo $data['project']['id']; ?>/<?php echo $member['assignment_id']; ?>" method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Bạn có chắc muốn xóa [<?php echo htmlspecialchars(addslashes($member['NAME'])); ?>] khỏi dự án?');">
                                                Xóa
                                            </button>
                                        </form>
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