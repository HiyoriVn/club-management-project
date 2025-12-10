<?php
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<!-- Action Bar -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Báo cáo & Thống kê</h2>
        <p class="text-sm text-gray-600 mt-1">Tổng quan hoạt động CLB</p>
    </div>

    <div class="flex space-x-3">
        <a href="<?php echo BASE_URL; ?>/report/export?type=overview"
            class="btn btn-secondary">
            <ion-icon name="download-outline" class="-ml-1 mr-2 h-5 w-5"></ion-icon>
            Xuất Excel
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <!-- Total Users -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <ion-icon name="people-outline" class="h-6 w-6 text-indigo-600"></ion-icon>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Thành viên</dt>
                        <dd class="text-3xl font-bold text-gray-900">
                            <?php echo $data['stats']['total_users']; ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Events -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <ion-icon name="calendar-outline" class="h-6 w-6 text-green-600"></ion-icon>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Sự kiện</dt>
                        <dd class="text-3xl font-bold text-gray-900">
                            <?php echo $data['stats']['total_events']; ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Projects -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <ion-icon name="briefcase-outline" class="h-6 w-6 text-yellow-600"></ion-icon>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Dự án</dt>
                        <dd class="text-3xl font-bold text-gray-900">
                            <?php echo $data['stats']['total_projects']; ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <ion-icon name="cash-outline" class="h-6 w-6 text-blue-600"></ion-icon>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Số dư</dt>
                        <dd class="text-2xl font-bold text-gray-900">
                            <?php echo number_format($data['stats']['balance'], 0, ',', '.'); ?>đ
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Events Chart -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Số lượng Sự kiện (6 tháng)</h3>
        <canvas id="eventsChart"></canvas>
    </div>

    <!-- Finance Chart -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Thu/Chi (6 tháng)</h3>
        <canvas id="financeChart"></canvas>
    </div>
</div>

<!-- Top Departments -->
<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Ban có nhiều thành viên nhất</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">STT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên Ban</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số thành viên</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($data['charts']['top_departments'])): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                            Chưa có dữ liệu
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $rank = 1; ?>
                    <?php foreach ($data['charts']['top_departments'] as $dept): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $rank; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($dept['department_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo $dept['member_count']; ?> người
                            </td>
                        </tr>
                        <?php $rank++; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Events Chart
    const eventsData = <?php echo json_encode($data['charts']['events_by_month']); ?>;
    const eventsLabels = eventsData.map(d => d.month);
    const eventsValues = eventsData.map(d => parseInt(d.count));

    new Chart(document.getElementById('eventsChart'), {
        type: 'bar',
        data: {
            labels: eventsLabels,
            datasets: [{
                label: 'Số sự kiện',
                data: eventsValues,
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Finance Chart
    const financeData = <?php echo json_encode($data['charts']['finance_by_month']); ?>;
    const financeLabels = financeData.map(d => d.month);
    const incomeValues = financeData.map(d => parseFloat(d.income));
    const expenseValues = financeData.map(d => parseFloat(d.expense));

    new Chart(document.getElementById('financeChart'), {
        type: 'line',
        data: {
            labels: financeLabels,
            datasets: [{
                    label: 'Thu',
                    data: incomeValues,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Chi',
                    data: expenseValues,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>