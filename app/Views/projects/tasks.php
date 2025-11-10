<?php
// Nạp header MỚI (đã có layout sidebar)
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="flex justify-between items-center mb-5">
    <a href="<?php echo BASE_URL; ?>/project" class="text-sm font-medium text-blue-600 hover:text-blue-800">
        &larr; Quay lại Danh sách Dự án
    </a>
    <button onclick="showAddTaskModal()"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
        <ion-icon name="add-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
        Thêm Task mới
    </button>
</div>

<div class="flex overflow-x-auto space-x-4 pb-4 h-[80vh]">

    <?php
    // Lặp qua CẤU HÌNH CỘT (do Controller $data['statuses'] gửi qua)
    foreach ($data['statuses'] as $status_key => $status_name):
        $tasks_in_column = $data['tasks_by_status'][$status_key] ?? [];
        $count = count($tasks_in_column);

        // LOGIC MÀU MỚI (Theo yêu cầu của bạn)
        $column_bg = 'bg-gray-100'; // Mặc định
        $column_border = 'border-gray-300';
        $column_text = 'text-gray-700';

        switch ($status_key) {
            case 'backlog':
                $column_bg = 'bg-blue-100';
                $column_border = 'border-blue-300';
                $column_text = 'text-blue-800';
                break;
            case 'todo':
                $column_bg = 'bg-purple-100';
                $column_border = 'border-purple-300';
                $column_text = 'text-purple-800';
                break;
            case 'in_progress':
                $column_bg = 'bg-yellow-100';
                $column_border = 'border-yellow-300';
                $column_text = 'text-yellow-800';
                break;
            case 'overdue':
                $column_bg = 'bg-red-100';
                $column_border = 'border-red-300';
                $column_text = 'text-red-800';
                break;
            case 'done':
                $column_bg = 'bg-green-100';
                $column_border = 'border-green-300';
                $column_text = 'text-green-800';
                break;
        }
    ?>

        <div class="w-80 flex-shrink-0 <?php echo $column_bg; ?> rounded-lg shadow-inner flex flex-col h-[80vh]">
            <div class="p-3 flex justify-between items-center border-b <?php echo $column_border; ?> flex-shrink-0">
                <h3 class="text-sm font-semibold <?php echo $column_text; ?>">
                    <?php echo $status_name; ?>
                    <span class="ml-1 opacity-70 font-normal">(<?php echo $count; ?>)</span>
                </h3>
                <ion-icon name="ellipsis-horizontal" class="opacity-70"></ion-icon>
            </div>

            <div class="p-3 space-y-3 kanban-column-tasks flex-1 overflow-y-auto"
                data-status-key="<?php echo $status_key; ?>">

                <?php if ($count == 0 && $status_key != 'overdue'): ?>
                    <div class="text-center text-sm text-gray-500 py-4 empty-state-message">Kéo task vào đây</div>
                <?php elseif ($count == 0 && $status_key == 'overdue'): ?>
                    <div class="text-center text-sm text-gray-500 py-4">Không có task quá hạn</div>
                <?php endif; ?>

                <?php foreach ($tasks_in_column as $task): ?>
                    <div class="bg-white rounded-md shadow p-3 cursor-grab task-card"
                        data-task-id="<?php echo $task['id']; ?>">

                        <h4 class="text-sm font-medium text-gray-900 border-l-4 pl-2"
                            style="border-left-color: <?php echo htmlspecialchars($task['color'] ?? '#007bff'); ?> !important;">
                            <?php echo htmlspecialchars($task['title']); ?>
                        </h4>

                        <div class="mt-2 text-sm text-gray-600 prose prose-sm max-w-none">
                            <?php echo purify_html($task['description']); ?>
                        </div>

                        <?php if (!empty($task['attachment_link'])): ?>
                            <a href="<?php echo htmlspecialchars($task['attachment_link']); ?>" target="_blank"
                                class="mt-2 text-xs text-blue-600 hover:text-blue-800 inline-flex items-center">
                                <ion-icon name="attach-outline" class="mr-1"></ion-icon>
                                Xem đính kèm
                            </a>
                        <?php endif; ?>

                        <div class="mt-3 flex justify-between items-center text-sm text-gray-500">
                            <div>
                                <ion-icon name="people-outline" class="inline-block h-4 w-4 -mt-0.5"></ion-icon>
                                <span class="text-xs"><?php echo htmlspecialchars($task['assigned_user_name'] ?? 'Chưa gán'); ?></span>
                            </div>
                            <?php if ($task['due_date']): ?>
                                <span class="<?php echo ($status_key == 'overdue' && $task['STATUS'] != 'done') ? 'text-red-600 font-medium' : ''; ?> text-xs">
                                    <ion-icon name="calendar-outline" class="inline-block h-4 w-4 -mt-0.5"></ion-icon>
                                    <?php echo date('d/m/Y', strtotime($task['due_date'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mt-2 text-right">
                            <form action="<?php echo BASE_URL; ?>/project/deleteTask/<?php echo $data['project']['id']; ?>/<?php echo $task['id']; ?>" method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700" onclick="return confirm('Xóa Task?');">Xóa Task</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; // Hết vòng lặp Task Card 
                ?>

            </div>
        </div> <?php endforeach; // Hết vòng lặp Cột 
                ?>

</div>
<div id="addTaskModalOverlay" onclick="hideAddTaskModal()"
    class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40">
</div>

<div id="addTaskModalContent"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">

    <div class="bg-white rounded-lg shadow-xl overflow-hidden max-w-lg w-full">
        <form action="<?php echo BASE_URL; ?>/project/storeTask/<?php echo $data['project']['id']; ?>" method="POST"
            onsubmit="event.stopPropagation()">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    Tạo Task mới
                </h3>
            </div>

            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề Task: <sup>*</sup></label>
                    <input type="text" name="title" id="title" class="block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả:</label>
                    <input type="hidden" name="description" id="description_input">
                    <trix-editor input="description_input" class="min-h-[150px]"></trix-editor>
                </div>

                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Gán cho (chọn nhiều):</label>
                    <select name="assigned_to[]" id="assigned_to" multiple
                        class="block w-full rounded-md border-gray-300 shadow-sm">
                        <?php foreach ($data['all_members'] as $member) : ?>
                            <option value="<?php echo $member['user_id']; ?>"><?php echo htmlspecialchars($member['NAME']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu:</label>
                        <input type="date" name="start_date" id="start_date"
                            class="block w-full rounded-md border-gray-300 shadow-sm"
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn:</label>
                        <input type="date" name="due_date" id="due_date" class="block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Chọn màu Task:</label>
                        <input type="color" name="color" id="color"
                            class="block w-full rounded-md border-gray-300 shadow-sm"
                            value="#007bff">
                    </div>
                    <div>
                        <label for="attachment_link" class="block text-sm font-medium text-gray-700 mb-1">Đính kèm Link:</label>
                        <input type="url" name="attachment_link" id="attachment_link"
                            class="block w-full rounded-md border-gray-300 shadow-sm"
                            placeholder="https://example.com">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
                <button type="button" onclick="hideAddTaskModal()"
                    class="text-sm font-medium text-gray-700 hover:text-gray-900">
                    Hủy bỏ
                </button>
                <button type="submit"
                    class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    Lưu Task
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Nạp footer MỚI
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>


<script>
    // Hàm JS cho Modal (Sửa lại để dùng classList)
    function showAddTaskModal() {
        document.getElementById('addTaskModalOverlay').classList.remove('hidden');
        document.getElementById('addTaskModalContent').classList.remove('hidden');
    }

    function hideAddTaskModal() {
        document.getElementById('addTaskModalOverlay').classList.add('hidden');
        document.getElementById('addTaskModalContent').classList.add('hidden');
    }

    // Hàm JS cho Kéo-thả (ĐÃ SỬA LỖI)
    document.addEventListener('DOMContentLoaded', function() {

        const columns = document.querySelectorAll('.kanban-column-tasks');

        columns.forEach(column => {
            let isOverdueColumn = column.dataset.statusKey === 'overdue';

            new Sortable(column, {
                group: {
                    name: 'kanban-board',
                    pull: true,
                    // SỬA LỖI 3: Chỉ cấm thả vào "Overdue"
                    put: !isOverdueColumn
                },
                animation: 150,
                // FIX 1: Chỉ cho phép kéo các item có class "task-card"
                draggable: ".task-card",

                // HÀM MỚI: Khi BẮT ĐẦU kéo
                onStart: function(evt) {
                    // Ẩn tất cả các thông báo "Kéo task vào đây"
                    document.querySelectorAll('.empty-state-message').forEach(el => el.style.display = 'none');
                },

                // HÀM MỚI: Khi THẢ XONG (sau onEnd)
                onSort: function(evt) {
                    // Kiểm tra lại tất cả các cột
                    checkEmptyColumns();
                },

                onEnd: function(evt) {
                    const item = evt.item;
                    const toColumn = evt.to;
                    const taskId = item.dataset.taskId;
                    const newStatus = toColumn.dataset.statusKey;

                    if (!taskId || !newStatus) return;

                    updateTaskStatus(taskId, newStatus);
                }
            });
        });
    });

    // HÀM MỚI: Kiểm tra các cột rỗng
    function checkEmptyColumns() {
        document.querySelectorAll('.kanban-column-tasks').forEach(column => {
            const taskCount = column.querySelectorAll('.task-card').length;
            const emptyMessage = column.querySelector('.empty-state-message');

            if (emptyMessage) {
                if (taskCount === 0) {
                    emptyMessage.style.display = 'block'; // Hiện
                } else {
                    emptyMessage.style.display = 'none'; // Ẩn
                }
            }
        });
    }

    // Hàm gửi yêu cầu ngầm (AJAX) - ĐÃ SỬA LỖI RELOAD
    function updateTaskStatus(taskId, newStatus) {

        // SỬA LỖI 3: Chỉ cấm gán status "overdue"
        if (newStatus === 'overdue') {
            showToast('Không thể di chuyển task vào cột "Quá hạn"', 'error');
            setTimeout(() => {
                location.reload();
            }, 1000); // Tải lại để hoàn tác
            return;
        }

        fetch('<?php echo BASE_URL; ?>/project/moveTask/<?php echo $data['project']['id']; ?>/' + taskId, {
                method: 'POST',
                // ... (code fetch giữ nguyên) ...
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    'csrf_token': '<?php echo $_SESSION['csrf_token']; ?>',
                    'new_status': newStatus
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server error');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // THÀNH CÔNG: Chỉ hiện Toast, KHÔNG reload
                    showToast(data.message || 'Cập nhật task thành công!', 'success');
                    checkEmptyColumns(); // Cập nhật lại thông báo "cột rỗng"

                    const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
                    // NÂNG CAO: Tải lại trang CHỈ KHI kéo task ra khỏi cột Quá hạn
                    if (taskElement && taskElement.closest('.kanban-column-tasks').dataset.statusKey === 'overdue') {
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }

                } else {
                    // THẤT BẠI: Hiện Toast LỖI và reload để hoàn tác
                    showToast(data.message || 'Lỗi! Không thể cập nhật task.', 'error');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            })
            .catch(error => {
                // LỖI MẠNG: Hiện Toast LỖI và reload để hoàn tác
                showToast('Lỗi mạng hoặc server. Vui lòng thử lại.', 'error');
                console.error('Fetch Error:', error);
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
    }
</script>