<?php
// app/Controllers/TransactionController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Transaction; // "use" Model mới

class TransactionController extends Controller
{
    private $transactionModel;

    public function __construct()
    {
        // Yêu cầu vai trò: Chỉ Admin và Sub-admin mới được quản lý quỹ
        $this->requireRole(['admin', 'subadmin']);

        // Nạp Model
        require_once ROOT_PATH . '/app/Models/Transaction.php';
        $this->transactionModel = new Transaction();
    }

    /**
     * (READ) Hiển thị trang danh sách Thu/Chi
     */
    public function index()
    {
        // Lấy tất cả giao dịch
        $transactions = $this->transactionModel->findAllWithUser();

        // Lấy tổng quan (Thu, Chi, Số dư)
        $totals = $this->transactionModel->getTotals();

        $data = [
            'title' => 'Quản lý Quỹ CLB',
            'transactions' => $transactions,
            'totals' => $totals // Gửi cục data tổng quan ra View
        ];

        $this->view('transactions/index', $data);
    }

    /**
     * (CREATE) Hiển thị form tạo giao dịch mới (GET)
     */
    public function create()
    {
        $data = [
            'title' => 'Tạo Giao dịch mới',
            // Dữ liệu rỗng cho form
            'type' => 'expense', // Mặc định là 'chi'
            'amount' => '',
            'description' => '',
            'date' => date('Y-m-d'), // Mặc định là hôm nay
            // Lỗi
            'amount_err' => '',
            'description_err' => '',
            'date_err' => ''
        ];
        $this->view('transactions/create', $data);
    }

    /**
     * (CREATE) Xử lý lưu giao dịch mới (POST)
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/transaction');
        }

        // Kiểm tra CSRF
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/transaction');
            exit;
        }

        $data = [
            'title' => 'Tạo Giao dịch mới',
            // Lấy dữ liệu từ form
            'type' => $_POST['type'],
            'amount' => trim($_POST['amount']),
            'description' => trim($_POST['description']),
            'date' => $_POST['date'],
            // Lỗi
            'amount_err' => '',
            'description_err' => '',
            'date_err' => ''
        ];

        // --- Validate Dữ liệu ---
        if (empty($data['amount'])) {
            $data['amount_err'] = 'Vui lòng nhập số tiền';
        } elseif (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            $data['amount_err'] = 'Số tiền phải là một số dương';
        }

        if (empty($data['description'])) {
            $data['description_err'] = 'Vui lòng nhập mô tả';
        }

        if (empty($data['date'])) {
            $data['date_err'] = 'Vui lòng chọn ngày';
        }

        // Kiểm tra xem có lỗi không
        if (empty($data['amount_err']) && empty($data['description_err']) && empty($data['date_err'])) {
            // Không lỗi -> Lưu vào CSDL
            if ($this->transactionModel->create($data)) {
                // Ghi log
                $log_detail = 'Đã tạo giao dịch (' . $data['type'] . '): ' . number_format($data['amount']) . 'đ cho [' . $data['description'] . ']';
                \log_activity('transaction_created', $log_detail);

                \set_flash_message('success', 'Tạo giao dịch thành công!');
                $this->redirect(BASE_URL . '/transaction');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể tạo giao dịch.');
                $this->view('transactions/create', $data);
            }
        } else {
            // Có lỗi -> Hiển thị lại form với lỗi
            $this->view('transactions/create', $data);
        }
    }

    /**
     * (UPDATE) Hiển thị form Sửa giao dịch (GET)
     */
    public function edit($id)
    {
        // 1. Lấy giao dịch
        $tx = $this->transactionModel->findById($id);
        if (!$tx) {
            \set_flash_message('error', 'Không tìm thấy giao dịch.');
            $this->redirect(BASE_URL . '/transaction');
        }

        // (Kiểm tra quyền nâng cao: Có thể chỉ người tạo hoặc admin mới được sửa)
        // (Tạm thời bỏ qua)

        $data = [
            'title' => 'Chỉnh sửa Giao dịch',
            'id' => $id,
            'type' => $tx['type'],
            'amount' => $tx['amount'],
            'description' => $tx['description'],
            'date' => $tx['date'],
            // Lỗi
            'amount_err' => '',
            'description_err' => '',
            'date_err' => ''
        ];
        $this->view('transactions/edit', $data);
    }

    /**
     * (UPDATE) Xử lý cập nhật giao dịch (POST)
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/transaction');
        }

        // Kiểm tra CSRF
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/transaction');
            exit;
        }

        $data = [
            'title' => 'Chỉnh sửa Giao dịch',
            'id' => $id,
            'type' => $_POST['type'],
            'amount' => trim($_POST['amount']),
            'description' => trim($_POST['description']),
            'date' => $_POST['date'],
            // Lỗi
            'amount_err' => '',
            'description_err' => '',
            'date_err' => ''
        ];

        // --- Validate (Giống hệt store) ---
        if (empty($data['amount'])) {
            $data['amount_err'] = 'Vui lòng nhập số tiền';
        } elseif (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            $data['amount_err'] = 'Số tiền phải là một số dương';
        }
        if (empty($data['description'])) {
            $data['description_err'] = 'Vui lòng nhập mô tả';
        }
        if (empty($data['date'])) {
            $data['date_err'] = 'Vui lòng chọn ngày';
        }

        if (empty($data['amount_err']) && empty($data['description_err']) && empty($data['date_err'])) {
            if ($this->transactionModel->update($id, $data)) {
                // Ghi log
                $log_detail = 'Đã cập nhật giao dịch (ID: ' . $id . ') thành (' . $data['type'] . '): ' . number_format($data['amount']) . 'đ cho [' . $data['description'] . ']';
                \log_activity('transaction_updated', $log_detail);

                \set_flash_message('success', 'Cập nhật giao dịch thành công!');
                $this->redirect(BASE_URL . '/transaction');
            } else {
                \set_flash_message('error', 'Có lỗi CSDL, không thể cập nhật.');
                $this->view('transactions/edit', $data);
            }
        } else {
            $this->view('transactions/edit', $data);
        }
    }

    /**
     * (DELETE) Xử lý xóa giao dịch (POST)
     */
    public function destroy($id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect(BASE_URL . '/transaction');
        }

        // Kiểm tra CSRF
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
            \set_flash_message('error', 'Yêu cầu không hợp lệ.');
            $this->redirect(BASE_URL . '/transaction');
            exit;
        }

        // Lấy thông tin trước khi xóa (để log)
        $tx = $this->transactionModel->findById($id);
        if (!$tx) {
            \set_flash_message('error', 'Không tìm thấy giao dịch để xóa.');
            $this->redirect(BASE_URL . '/transaction');
        }

        if ($this->transactionModel->delete($id)) {
            // Ghi log
            $log_detail = 'Đã xóa giao dịch (ID: ' . $id . '): [' . $tx['description'] . ']';
            \log_activity('transaction_deleted', $log_detail);

            \set_flash_message('success', 'Xóa giao dịch thành công!');
        } else {
            \set_flash_message('error', 'Có lỗi CSDL, không thể xóa giao dịch.');
        }

        $this->redirect(BASE_URL . '/transaction');
    }
}
