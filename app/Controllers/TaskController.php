<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\ActivityLog;

class TaskController extends Controller
{
    private $taskModel;
    private $projectModel;
    private $userModel;
    private $logModel;

    public function __construct()
    {
        $this->requireLogin();

        $this->taskModel = new Task();
        $this->projectModel = new Project();
        $this->userModel = new User();
        $this->logModel = new ActivityLog();
    }

    public function index()
    {
        // Điều hướng từ nút "Công việc" (/task?project_id=1) sang Kanban
        if (isset($_GET['project_id']) && !empty($_GET['project_id'])) {
            $this->redirect(BASE_URL . '/task/kanban/' . $_GET['project_id']);
        } else {
            if (function_exists('set_flash_message')) \set_flash_message('error', 'Vui lòng chọn dự án.');
            $this->redirect(BASE_URL . '/project');
        }
    }

    /**
     * Hiển thị Kanban Board
     */
    public function kanban($projectId)
    {
        $project = $this->projectModel->findById($projectId);

        if (!$project) {
            if (function_exists('set_flash_message')) \set_flash_message('error', 'Dự án không tồn tại.');
            $this->redirect(BASE_URL . '/project');
        }

        $tasks = $this->taskModel->getTasksByProject($projectId);

        $kanbanTasks = [
            'backlog' => [],
            'todo' => [],
            'in_progress' => [],
            'done' => []
        ];

        foreach ($tasks as $task) {
            $task['assignees'] = $this->taskModel->getAssignees($task['id']);

            // Xử lý status nếu tên trong DB khác tên key mảng
            $status = $task['status'];
            if ($status == 'completed') $status = 'done';
            if (!isset($kanbanTasks[$status])) $status = 'backlog'; // Fallback

            $kanbanTasks[$status][] = $task;
        }

        $data = [
            'project' => $project,
            'tasks' => $kanbanTasks,
            'title' => 'Kanban: ' . htmlspecialchars($project['name'])
        ];

        $this->view('tasks/kanban', $data);
    }

    /**
     * Hiển thị dạng danh sách (List View)
     */
    public function list($projectId)
    {
        $project = $this->projectModel->findById($projectId);

        if (!$project) {
            \set_flash_message('error', 'Dự án không tồn tại.');
            $this->redirect(BASE_URL . '/project');
        }

        $tasks = $this->taskModel->getTasksByProject($projectId);

        foreach ($tasks as &$task) {
            $task['assignees'] = $this->taskModel->getAssignees($task['id']);
        }

        $data = [
            'project' => $project,
            'tasks' => $tasks,
            'title' => 'Danh sách công việc: ' . htmlspecialchars($project['name'])
        ];

        $this->view('tasks/list', $data);
    }

    /**
     * Tạo task mới
     */
    public function create($projectId)
    {
        $project = $this->projectModel->findById($projectId);
        if (!$project) {
            $this->redirect(BASE_URL . '/project');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'project_id' => $projectId,
                'title' => trim($_POST['title']),
                'description' => $_POST['description'] ?? '',
                'status' => $_POST['status'] ?? 'backlog',
                'start_date' => $_POST['start_date'] ?? null,
                'due_date' => $_POST['due_date'] ?? null,
                'color' => $_POST['color'] ?? '#007bff'
            ];

            if (empty($data['title'])) {
                \set_flash_message('error', 'Tiêu đề không được để trống.');
                $this->redirect(BASE_URL . '/task/create/' . $projectId);
            }

            $taskId = $this->taskModel->create($data);
            if ($taskId) {
                // Xử lý assignees (nếu có chọn từ form)
                if (!empty($_POST['assignees'])) {
                    foreach ($_POST['assignees'] as $userId) {
                        $this->taskModel->assign($taskId, $userId);
                    }
                }

                $this->logModel->create($_SESSION['user_id'], 'task_create', "Tạo task ID: $taskId trong dự án $projectId");
                \set_flash_message('success', 'Đã tạo công việc mới.');

                // Redirect về nơi đã gọi (Kanban hoặc List)
                $redirectView = $_POST['redirect_view'] ?? 'kanban';
                $this->redirect(BASE_URL . '/task/' . $redirectView . '/' . $projectId);
            } else {
                \set_flash_message('error', 'Lỗi khi tạo công việc.');
                $this->redirect(BASE_URL . '/task/create/' . $projectId);
            }
        } else {
            // Hiển thị form
            $projectMembers = $this->projectModel->getProjectMembers($projectId);

            $data = [
                'project' => $project,
                'members' => $projectMembers,
                'title' => 'Tạo công việc mới'
            ];
            $this->view('tasks/create', $data);
        }
    }

    /**
     * Sửa task
     */
    public function edit($id)
    {
        $task = $this->taskModel->findById($id);
        if (!$task) {
            \set_flash_message('error', 'Công việc không tồn tại.');
            $this->redirect(BASE_URL . '/project');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $updateData = [
                'title' => trim($_POST['title']),
                'description' => $_POST['description'],
                'status' => $_POST['status'],
                'start_date' => $_POST['start_date'],
                'due_date' => $_POST['due_date'],
                'color' => $_POST['color']
            ];

            $this->taskModel->update($id, $updateData);

            $currentAssignees = array_column($this->taskModel->getAssignees($id), 'id');
            $newAssignees = $_POST['assignees'] ?? [];


            foreach ($currentAssignees as $uid) {
                if (!in_array($uid, $newAssignees)) {
                    $this->taskModel->unassign($id, $uid);
                }
            }

            foreach ($newAssignees as $uid) {
                if (!in_array($uid, $currentAssignees)) {
                    $this->taskModel->assign($id, $uid);
                }
            }

            $this->logModel->create($_SESSION['user_id'], 'task_update', "Cập nhật task ID: $id");
            \set_flash_message('success', 'Cập nhật công việc thành công.');

            $this->redirect(BASE_URL . '/task/kanban/' . $task['project_id']);
        } else {
            $projectMembers = $this->projectModel->getProjectMembers($task['project_id']);
            $currentAssignees = $this->taskModel->getAssignees($id);
            $assigneeIds = array_column($currentAssignees, 'id');

            $data = [
                'task' => $task,
                'members' => $projectMembers,
                'assignee_ids' => $assigneeIds,
                'title' => 'Chỉnh sửa công việc'
            ];
            $this->view('tasks/edit', $data);
        }
    }

    /**
     * API cập nhật trạng thái (Dùng cho AJAX Drag & Drop)
     */
    public function update_status($id)
    {
        // Trả về JSON
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid Request']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? null;

        if ($status && $this->taskModel->updateStatus($id, $status)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Xóa task
     */
    public function delete($id)
    {
        $task = $this->taskModel->findById($id);
        if ($task) {
            $projectId = $task['project_id'];
            $this->taskModel->delete($id);
            $this->logModel->create($_SESSION['user_id'], 'task_delete', "Xóa task ID: $id");
            \set_flash_message('success', 'Đã xóa công việc.');
            $this->redirect(BASE_URL . '/task/kanban/' . $projectId);
        } else {
            $this->redirect(BASE_URL . '/project');
        }
    }
}
