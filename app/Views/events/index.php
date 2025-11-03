<?php
// Nạp header MỚI (đã có layout sidebar)
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="flex justify-end mb-5">
    <?php
    if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) :
    ?>
        <a href="<?php echo BASE_URL; ?>/event/create"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
            Tạo Sự kiện mới
        </a>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <?php if (empty($data['events'])) : ?>

        <div class="lg:col-span-3 bg-white shadow rounded-lg p-6 text-center">
            <p class="text-gray-500">Chưa có sự kiện nào.</p>
        </div>

    <?php else : ?>

        <?php foreach ($data['events'] as $event) : ?>

            <div class="bg-white shadow rounded-lg overflow-hidden flex flex-col">

                <div class="p-5 sm:p-6 flex-grow">
                    <h3 class="text-lg font-medium text-gray-900">
                        <?php echo htmlspecialchars($event['title']); ?>
                    </h3>

                    <p class="mt-2 text-sm text-gray-600">
                        <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 150))); ?>
                        <?php if (strlen($event['description']) > 150) echo '...'; ?>
                    </p>

                    <div class="mt-4 space-y-2">
                        <div class="flex items-center text-sm text-gray-500">
                            <ion-icon name="calendar-outline" class="flex-shrink-0 mr-2 h-5 w-5 text-gray-400"></ion-icon>
                            <span><?php echo date('d/m/Y H:i', strtotime($event['start_time'])); ?></span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <ion-icon name="location-outline" class="flex-shrink-0 mr-2 h-5 w-5 text-gray-400"></ion-icon>
                            <span><?php echo htmlspecialchars($event['location'] ?? 'Chưa cập nhật'); ?></span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <ion-icon name="person-outline" class="flex-shrink-0 mr-2 h-5 w-5 text-gray-400"></ion-icon>
                            <span>Tạo bởi: <?php echo htmlspecialchars($event['creator_name']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col space-y-3">

                        <?php if (isset($_SESSION['user_id'])) : ?>
                            <?php $is_registered = in_array($event['id'], $data['my_registrations']); ?>

                            <form action="<?php echo BASE_URL; ?>/event/toggleRegistration/<?php echo $event['id']; ?>" method="POST" class="w-full">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <?php if ($is_registered) : ?>
                                    <button type="submit"
                                        class="w-full inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700"
                                        onclick="return confirm('Bạn có chắc muốn HỦY đăng ký sự kiện này?');">
                                        Hủy đăng ký
                                    </button>
                                <?php else : ?>
                                    <button type="submit"
                                        class="w-full inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                        Đăng ký tham gia
                                    </button>
                                <?php endif; ?>
                            </form>
                        <?php else : ?>
                            <a href="<?php echo BASE_URL; ?>/auth/login" class="w-full inline-flex justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                                Đăng nhập để đăng ký
                            </a>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'subadmin')) : ?>
                            <div class="flex justify-between items-center pt-2">
                                <a href="<?php echo BASE_URL; ?>/event/attendance/<?php echo $event['id']; ?>"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-cyan-600 hover:bg-cyan-700">
                                    <ion-icon name="checkmark-done-outline" class="-ml-0.5 mr-1.5 h-4 w-4"></ion-icon>
                                    Điểm danh
                                </a>
                                <div class="space-x-3">
                                    <a href="<?php echo BASE_URL; ?>/event/edit/<?php echo $event['id']; ?>" class="text-sm font-medium text-yellow-600 hover:text-yellow-900">Sửa</a>

                                    <form action="<?php echo BASE_URL; ?>/event/destroy/<?php echo $event['id']; ?>" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-900"
                                            onclick="return confirm('Bạn có chắc muốn xóa Sự kiện [<?php echo htmlspecialchars(addslashes($event['title'])); ?>]?');">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div> <?php endforeach; ?>
    <?php endif; ?>

</div> <?php
        // Nạp footer MỚI
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
        ?>