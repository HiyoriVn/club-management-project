<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Transaction;
use App\Models\ActivityLog;

class TransactionController extends Controller
{
    private $transactionModel;
    private $logModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->transactionModel = new Transaction();
        $this->logModel = new ActivityLog();
    }

    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $transactions = $this->transactionModel->getAll($limit, $offset);
        $totals = $this->transactionModel->getTotals();

        // Tính số dư (Balance)
        $income = 0;
        $expense = 0;
        foreach ($totals as $t) {
            if ($t['type'] == 'income') $income = $t['total'];
            if ($t['type'] == 'expense') $expense = $t['total'];
        }

        $this->view('transactions/index', [
            'transactions' => $transactions,
            'total_income' => $income,
            'total_expense' => $expense,
            'balance' => $income - $expense,
            'page' => $page,
            'title' => 'Quản lý Tài chính'
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'type' => $_POST['type'],
                'amount' => str_replace(',', '', $_POST['amount']), // Xử lý nếu user nhập 100,000
                'date' => $_POST['date'],
                'description' => trim($_POST['description']),
                'created_by' => $_SESSION['user_id'],
                'approved_by' => ($_SESSION['user_role'] == 'admin') ? $_SESSION['user_id'] : null
            ];

            if ($this->transactionModel->create($data)) {
                $this->logModel->create($_SESSION['user_id'], 'trans_create', "Tạo giao dịch " . $data['type']);
                \set_flash_message('success', 'Đã tạo giao dịch.');
                $this->redirect(BASE_URL . '/transaction');
            }
        }
        $this->view('transactions/create', ['title' => 'Tạo phiếu Thu/Chi']);
    }

    // Các hàm edit, delete tương tự...
    public function delete($id)
    {
        if ($_SESSION['user_role'] !== 'admin') $this->redirect(BASE_URL . '/transaction');
        $this->transactionModel->delete($id);
        \set_flash_message('success', 'Đã xóa giao dịch.');
        $this->redirect(BASE_URL . '/transaction');
    }
}
