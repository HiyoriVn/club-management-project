<?php
// Nạp header
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="task-board-page" style="max-width: 1200px; margin: 20px auto;">

    <div style="margin-bottom: 20px;">
        <a href="<?php echo BASE_URL; ?>/project" style="color: #007bff; text-decoration: none;">&larr; Quay lại Danh sách Dự án</a>
        <h1 style="margin-top: 10px;"><?php echo htmlspecialchars($data['title']); ?></h1>
        <button onclick="document.getElementById('addTaskModal').style.display='block'" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em;">
            + Thêm Task mới
        </button>
    </div>

    <div class="kanban-board" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">

        <?php
        $statuses = [
            'todo' => ['title' => 'Cần làm', 'bg' => '#ffebee'],
            'in_progress' => ['title' => 'Đang làm', 'bg' => '#e3f2fd'],
            'done' => ['title' => 'Hoàn thành', 'bg' => '#e8f5e9']
        ];
        ?>

        <?php foreach ($statuses as $status_key => $status_info): ?>
            <div class="kanban-column" style="background: <?php echo $status_info['bg']; ?>; border: 1px solid #ddd; border-radius: 5px;">
                <h3 style="padding: 10px 15px; margin: 0; border-bottom: 1px solid #ddd;">
                    <?php echo $status_info['title']; ?> (<?php echo count($data['tasks_by_status'][$status_key]); ?>)
                </h3>

                <div class="task-list" style="padding: 15px; min-height: 300px;">
                    <?php if (empty($data['tasks_by_status'][$status_key])): ?>
                        <p style="color: #6c757d; font-style: italic; text-align: center;">Trống</p>
                    <?php endif; ?>

                    <?php foreach ($data['tasks_by_status'][$status_key] as $task): ?>
                        <div class="task-card" style="background: #fff; border: 1px solid #ccc; border-radius: 3px; padding: 10px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <strong style="display: block; margin-bottom: 5px;"><?php echo htmlspecialchars($task['title']); ?></strong>
                            <p style="font-size: 0.9em; color: #333; margin: 0 0 10px 0;"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>

                            <div style="font-size: 0.8em; color: #6c757d; border-top: 1px solid #eee; padding-top: 8px;">
                                <?php if ($task['due_date']): ?>
                                    <span><strong>Due:</strong> <?php echo date('d/m/Y', strtotime($task['due_date'])); ?> | </span>
                                <?php endif; ?>
                                <span><strong>Gán cho:</strong> <?php echo htmlspecialchars($task['assigned_user_name'] ?? 'Chưa gán'); ?></span>
                            </div>

                            <div style="text-align: right; margin-top: 10px;">
                                <form action="<?php echo BASE_URL; ?>/project/deleteTask/<?php echo $data['project']['id']; ?>/<?php echo $task['id']; ?>" method="POST" style="display: inline-block; margin-left: 5px;">
                                    <button type="submit" style="background: none; border: none; color: red; cursor: pointer;" onclick="return confirm('Xóa Task?');">Xóa</button>
                                </form>

                                <?php if ($status_key != 'in_progress'): ?>
                                    <form action="<?php echo BASE_URL; ?>/project/moveTask/<?php echo $data['project']['id']; ?>/<?php echo $task['id']; ?>" method="POST" style="display: inline-block;">
                                        <input type="hidden" name="new_status" value="in_progress">
                                        <button type="submit" style="background: none; border: none; color: #007bff; cursor: pointer;">► Đang làm</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($status_key != 'done'): ?>
                                    <form action="<?php echo BASE_URL; ?>/project/moveTask/<?php echo $data['project']['id']; ?>/<?php echo $task['id']; ?>" method="POST" style="display: inline-block;">
                                        <input type="hidden" name="new_status" value="done">
                                        <button type="submit" style="background: none; border: none; color: green; cursor: pointer;">✔ Hoàn thành</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($status_key != 'todo'): ?>
                                    <form action="<?php echo BASE_URL; ?>/project/moveTask/<?php echo $data['project']['id']; ?>/<?php echo $task['id']; ?>" method="POST" style="display: inline-block;">
                                        <input type="hidden" name="new_status" value="todo">
                                        <button type="submit" style="background: none; border: none; color: #6c757d; cursor: pointer;">◄ Cần làm</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>


<div id="addTaskModal" style="display: none; position: fixed; z-index: 10; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 5px;">

        <span onclick="document.getElementById('addTaskModal').style.display='none'" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>

        <h3>Tạo Task mới (cho Dự án: <?php echo htmlspecialchars($data['project']['NAME']); ?>)</h3>

        <form action="<?php echo BASE_URL; ?>/project/storeTask/<?php echo $data['project']['id']; ?>" method="POST">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="title">Tiêu đề Task: <sup>*</sup></label>
                <input type="text" name="title" id="title" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;" required>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="description">Mô tả:</label>
                <textarea name="description" id="description" rows="4" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="assigned_to">Gán cho:</label>
                    <select name="assigned_to" id="assigned_to" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; background: #fff;">
                        <option value="">-- (Chưa gán) --</option>
                        <?php foreach ($data['all_members'] as $member) : ?>
                            <option value="<?php echo $member['user_id']; ?>"><?php echo htmlspecialchars($member['NAME']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="due_date">Ngày hết hạn:</label>
                    <input type="date" name="due_date" id="due_date" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;">
                </div>
            </div>
            <input type="submit" value="Lưu Task" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
        </form>
    </div>
</div>


<?php
// Nạp footer
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>