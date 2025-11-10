<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form action="<?php echo BASE_URL; ?>/member/assign/<?php echo $data['user']['id']; ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Gán vai trò mới</h3>
                    <p class="mt-1 text-sm text-gray-500">Cho: <?php echo htmlspecialchars($data['user']['NAME']); ?></p>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">1. Chọn Ban:</label>
                            <select name="department_id" id="department_id" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                <option value="">-- Chọn một Ban --</option>
                                <?php foreach ($data['all_departments'] as $dep): ?>
                                    <option value="<?php echo $dep['id']; ?>"><?php echo htmlspecialchars($dep['NAME']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">2. Chọn Vai trò:</label>
                            <select name="role_id" id="role_id" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                <option value="">-- Chọn một Vai trò --</option>
                                <?php foreach ($data['all_roles'] as $role): ?>
                                    <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['NAME']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-right">
                    <button type="submit"
                            class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
                        Gán
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white shadow overflow-hidden rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Các vai trò hiện tại (<?php echo count($data['current_roles']); ?>)
                </h3>
            </div>
            <div class="border-t border-gray-200 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Ban</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Vai trò</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Hành động</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($data['current_roles'])) : ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Thành viên này chưa được gán vai trò nào.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($data['current_roles'] as $role) : ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($role['department_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($role['role_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="<?php echo BASE_URL; ?>/member/revoke/<?php echo $data['user']['id']; ?>/<?php echo $role['assignment_id']; ?>" method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Bạn có chắc muốn thu hồi vai trò [<?php echo htmlspecialchars(addslashes($role['role_name'])); ?>] tại [<?php echo htmlspecialchars(addslashes($role['department_name'])); ?>]?');">
                                                Thu hồi
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