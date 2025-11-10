<?php
// Nạp header MỚI
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            <?php echo $data['title']; ?> (<?php echo count($data['users']); ?>)
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            Quản lý vai trò hệ thống và vai trò trong Ban của thành viên.
        </p>
    </div>
    <div class="border-t border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò Hệ thống</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Hành động</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $stt = 1; ?>
                <?php foreach ($data['users'] as $user) : ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $stt; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['NAME']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php
                            $role_text = 'Guest';
                            $role_class = 'bg-gray-100 text-gray-800';
                            switch ($user['system_role']) {
                                case 'admin':
                                    $role_text = 'Admin (Quản trị)';
                                    $role_class = 'bg-red-100 text-red-800';
                                    break;
                                case 'subadmin':
                                    $role_text = 'Sub-Admin (Quản lý)';
                                    $role_class = 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'member':
                                    $role_text = 'Member (Thành viên)';
                                    $role_class = 'bg-green-100 text-green-800';
                                    break;
                            }
                            ?>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $role_class; ?>">
                                <?php echo $role_text; ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                            <a href="<?php echo BASE_URL; ?>/member/manage/<?php echo $user['id']; ?>"
                                class="btn btn-primary">
                                Phân quyền (Ban)
                            </a>

                            <?php
                            // Form đổi Vai trò Hệ thống (chỉ Super Admin thấy)
                            if (
                                $_SESSION['user_role'] == 'admin' &&
                                $user['id'] != $_SESSION['user_id'] &&
                                $user['system_role'] != 'admin'
                            ) :
                            ?>
                                <form action="<?php echo BASE_URL; ?>/member/updateSystemRole/<?php echo $user['id']; ?>" method="POST" class="inline-flex space-x-2">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <select name="system_role" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                        <option value="guest" <?php echo ($user['system_role'] == 'guest') ? 'selected' : ''; ?>>Guest</option>
                                        <option value="member" <?php echo ($user['system_role'] == 'member') ? 'selected' : ''; ?>>Member</option>
                                        <option value="subadmin" <?php echo ($user['system_role'] == 'subadmin') ? 'selected' : ''; ?>>Sub-Admin</option>
                                    </select>
                                    <button type="submit"
                                        class="btn btn-secondary">
                                        Lưu
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $stt++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>