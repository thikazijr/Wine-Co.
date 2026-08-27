<?php
// Get filter parameters
$period = $_GET['period'] ?? 'month';
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');

// Build date conditions
$dateCondition = "DATE(transaction_date) BETWEEN '$startDate' AND '$endDate'";

// ==================== KEY METRICS ====================
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM accounting_transactions WHERE type = 'income' AND status != 'void' AND $dateCondition")->fetchColumn();
$totalExpenses = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM accounting_transactions WHERE type = 'expense' AND status != 'void' AND $dateCondition")->fetchColumn();
$netProfit = $totalRevenue - $totalExpenses;
$profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

// ==================== INCOME STATEMENT ====================
$incomeBreakdown = $pdo->query("
    SELECT category, category_color, category_text_color, COALESCE(SUM(amount), 0) as total
    FROM accounting_transactions 
    WHERE type = 'income' AND status != 'void' AND $dateCondition
    GROUP BY category, category_color, category_text_color
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

$expenseBreakdown = $pdo->query("
    SELECT category, category_color, category_text_color, COALESCE(SUM(amount), 0) as total
    FROM accounting_transactions 
    WHERE type = 'expense' AND status != 'void' AND $dateCondition
    GROUP BY category, category_color, category_text_color
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ==================== CASH FLOW ====================
$cashFlow = $pdo->query("
    SELECT 
        DATE_FORMAT(transaction_date, '%Y-%m-%d') as date,
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expenses
    FROM accounting_transactions 
    WHERE status != 'void' AND $dateCondition
    GROUP BY DATE_FORMAT(transaction_date, '%Y-%m-%d')
    ORDER BY transaction_date ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Calculate running balance
$runningBalance = 0;
foreach ($cashFlow as &$row) {
    $runningBalance += $row['income'] - $row['expenses'];
    $row['balance'] = $runningBalance;
}

// ==================== MONTHLY TREND ====================
$monthlyTrend = $pdo->query("
    SELECT 
        DATE_FORMAT(transaction_date, '%Y-%m') as month,
        COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
        COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expenses
    FROM accounting_transactions 
    WHERE status != 'void' AND YEAR(transaction_date) = $year
    GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
    ORDER BY month ASC
")->fetchAll(PDO::FETCH_ASSOC);

// ==================== TRANSACTION VOLUME ====================
$transactionCount = $pdo->query("SELECT COUNT(*) FROM accounting_transactions WHERE status != 'void' AND $dateCondition")->fetchColumn();
$avgTransactionValue = $transactionCount > 0 ? ($totalRevenue + $totalExpenses) / $transactionCount : 0;

// ==================== PAYMENT METHOD BREAKDOWN ====================
$paymentMethodBreakdown = $pdo->query("
    SELECT 
        payment_method,
        COUNT(*) as count,
        COALESCE(SUM(amount), 0) as total
    FROM accounting_transactions 
    WHERE status != 'void' AND $dateCondition
    GROUP BY payment_method
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ==================== DATE RANGE PRESETS ====================
$dateRanges = [
    'today' => ['label' => 'Today', 'start' => date('Y-m-d'), 'end' => date('Y-m-d')],
    'week' => ['label' => 'This Week', 'start' => date('Y-m-d', strtotime('monday this week')), 'end' => date('Y-m-d')],
    'month' => ['label' => 'This Month', 'start' => date('Y-m-01'), 'end' => date('Y-m-t')],
    'quarter' => ['label' => 'This Quarter', 'start' => date('Y-m-d', strtotime('first day of this quarter')), 'end' => date('Y-m-d')],
    'year' => ['label' => 'This Year', 'start' => date('Y-01-01'), 'end' => date('Y-12-31')],
];
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-chart-bar me-2" style="color:var(--wine);"></i>Management Reports</h2>
                <p class="text-muted">Comprehensive business performance insights</p>
            </div>
            <div>
                <button class="btn btn-gold me-2" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Report
                </button>
                <button class="btn btn-outline-wine" onclick="exportCSV()">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </button>
            </div>
        </div>
    </div>
    
    <!-- Filter Bar -->
    <div class="filter-bar" style="background: white; padding: 15px 20px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        <form method="GET" class="row align-items-end">
            <input type="hidden" name="section" value="accounting-reports">
            <div class="col-md-2 mb-2">
                <label style="font-weight: 600; color: var(--dark); font-size: 0.9rem;">Quick Period</label>
                <select class="form-select form-select-sm" name="period" onchange="this.form.submit()">
                    <?php foreach($dateRanges as $key => $range): ?>
                    <option value="<?php echo $key; ?>" <?php echo $period == $key ? 'selected' : ''; ?>>
                        <?php echo $range['label']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-weight: 600; color: var(--dark); font-size: 0.9rem;">Start Date</label>
                <input type="date" class="form-control form-control-sm" name="start_date" value="<?php echo $startDate; ?>">
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-weight: 600; color: var(--dark); font-size: 0.9rem;">End Date</label>
                <input type="date" class="form-control form-control-sm" name="end_date" value="<?php echo $endDate; ?>">
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-weight: 600; color: var(--dark); font-size: 0.9rem;">Year</label>
                <select class="form-select form-select-sm" name="year">
                    <?php for($y = date('Y'); $y >= date('Y')-5; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $y == $year ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-weight: 600; color: var(--dark); font-size: 0.9rem;">Month</label>
                <select class="form-select form-select-sm" name="month">
                    <?php for($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>" <?php echo $m == $month ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0,0,0,$m,1)); ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-wine btn-sm w-100">
                    <i class="fas fa-filter me-2"></i>Apply Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Print Header (only visible in print) -->
    <div class="print-only text-center mb-4" style="display:none;">
        <h2 style="color:var(--wine);">Wine & Co. Eswatini</h2>
        <h4>Management Report</h4>
        <p>Period: <?php echo date('d M Y', strtotime($startDate)); ?> - <?php echo date('d M Y', strtotime($endDate)); ?></p>
        <hr>
    </div>
    
    <!-- ========== KEY METRICS ========== -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number" style="color:var(--green);">E<?php echo number_format($totalRevenue, 2); ?></div>
                <div class="stat-label">Total Revenue</div>
                <small class="text-muted"><?php echo $transactionCount; ?> transactions</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number" style="color:#dc3545;">E<?php echo number_format($totalExpenses, 2); ?></div>
                <div class="stat-label">Total Expenses</div>
                <small class="text-muted"><?php echo count($expenseBreakdown); ?> categories</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number" style="color:<?php echo $netProfit >= 0 ? 'var(--green)' : '#dc3545'; ?>;">E<?php echo number_format($netProfit, 2); ?></div>
                <div class="stat-label">Net Profit</div>
                <small class="text-muted"><?php echo $netProfit >= 0 ? '📈 Profitable' : '📉 Loss'; ?></small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number" style="color:var(--gold);"><?php echo number_format($profitMargin, 1); ?>%</div>
                <div class="stat-label">Profit Margin</div>
                <small class="text-muted"><?php echo $profitMargin > 20 ? '✅ Healthy' : '⚠️ Needs improvement'; ?></small>
            </div>
        </div>
    </div>
    
    <!-- ========== INCOME STATEMENT ========== -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-arrow-up me-2" style="color:var(--green);"></i>Income Breakdown</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($incomeBreakdown)): ?>
                        <p class="text-muted">No income recorded for this period.</p>
                    <?php else: ?>
                        <?php foreach($incomeBreakdown as $item): ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <span class="category-badge" style="background:<?php echo $item['category_color'] ?? '#d4edda'; ?>;color:<?php echo $item['category_text_color'] ?? '#155724'; ?>;">
                                        <?php echo htmlspecialchars($item['category']); ?>
                                    </span>
                                </span>
                                <span style="font-weight:bold;color:var(--green);">E<?php echo number_format($item['total'], 2); ?></span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar" style="width:<?php echo $totalRevenue > 0 ? ($item['total'] / $totalRevenue) * 100 : 0; ?>%;background:<?php echo $item['category_color'] ?? 'var(--green)'; ?>;"></div>
                            </div>
                            <small class="text-muted"><?php echo $totalRevenue > 0 ? number_format(($item['total'] / $totalRevenue) * 100, 1) : 0; ?>% of total</small>
                        </div>
                        <?php endforeach; ?>
                        <div class="mt-3 pt-2 border-top">
                            <div class="d-flex justify-content-between">
                                <strong>Total Income</strong>
                                <strong style="color:var(--green);">E<?php echo number_format($totalRevenue, 2); ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-arrow-down me-2" style="color:#dc3545;"></i>Expense Breakdown</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($expenseBreakdown)): ?>
                        <p class="text-muted">No expenses recorded for this period.</p>
                    <?php else: ?>
                        <?php foreach($expenseBreakdown as $item): ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <span class="category-badge" style="background:<?php echo $item['category_color'] ?? '#f8d7da'; ?>;color:<?php echo $item['category_text_color'] ?? '#721c24'; ?>;">
                                        <?php echo htmlspecialchars($item['category']); ?>
                                    </span>
                                </span>
                                <span style="font-weight:bold;color:#dc3545;">E<?php echo number_format($item['total'], 2); ?></span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar" style="width:<?php echo $totalExpenses > 0 ? ($item['total'] / $totalExpenses) * 100 : 0; ?>%;background:#dc3545;"></div>
                            </div>
                            <small class="text-muted"><?php echo $totalExpenses > 0 ? number_format(($item['total'] / $totalExpenses) * 100, 1) : 0; ?>% of total</small>
                        </div>
                        <?php endforeach; ?>
                        <div class="mt-3 pt-2 border-top">
                            <div class="d-flex justify-content-between">
                                <strong>Total Expenses</strong>
                                <strong style="color:#dc3545;">E<?php echo number_format($totalExpenses, 2); ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ========== CASH FLOW SUMMARY ========== -->
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2" style="color:var(--gold);"></i>Cash Flow Summary</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Income</th>
                            <th class="text-end">Expenses</th>
                            <th class="text-end">Net Cash Flow</th>
                            <th class="text-end">Running Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $displayCashFlow = array_slice($cashFlow, -14); // Show last 14 days
                        foreach($displayCashFlow as $row): 
                        $net = $row['income'] - $row['expenses'];
                        ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                            <td class="text-end" style="color:var(--green);">E<?php echo number_format($row['income'], 2); ?></td>
                            <td class="text-end" style="color:#dc3545;">E<?php echo number_format($row['expenses'], 2); ?></td>
                            <td class="text-end" style="color:<?php echo $net >= 0 ? 'var(--green)' : '#dc3545'; ?>;">
                                E<?php echo number_format($net, 2); ?>
                            </td>
                            <td class="text-end" style="font-weight:bold;color:<?php echo $row['balance'] >= 0 ? 'var(--green)' : '#dc3545'; ?>;">
                                E<?php echo number_format($row['balance'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- ========== MONTHLY TREND ========== -->
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2" style="color:var(--wine);"></i>Monthly Trend - <?php echo $year; ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Income</th>
                            <th class="text-end">Expenses</th>
                            <th class="text-end">Profit</th>
                            <th class="text-end">Margin</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($monthlyTrend as $row): 
                            $profit = $row['income'] - $row['expenses'];
                            $margin = $row['income'] > 0 ? ($profit / $row['income']) * 100 : 0;
                            $trendClass = $profit >= 0 ? 'up' : 'down';
                            $trendIcon = $profit >= 0 ? '📈' : '📉';
                        ?>
                        <tr>
                            <td><?php echo date('F', strtotime($row['month'] . '-01')); ?></td>
                            <td class="text-end" style="color:var(--green);">E<?php echo number_format($row['income'], 2); ?></td>
                            <td class="text-end" style="color:#dc3545;">E<?php echo number_format($row['expenses'], 2); ?></td>
                            <td class="text-end" style="color:<?php echo $profit >= 0 ? 'var(--green)' : '#dc3545'; ?>;font-weight:bold;">
                                E<?php echo number_format($profit, 2); ?>
                            </td>
                            <td class="text-end"><?php echo number_format($margin, 1); ?>%</td>
                            <td><span class="badge <?php echo $profit >= 0 ? 'bg-success' : 'bg-danger'; ?>"><?php echo $trendIcon; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- ========== PAYMENT METHOD BREAKDOWN ========== -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2" style="color:var(--gold);"></i>Payment Methods</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($paymentMethodBreakdown)): ?>
                        <p class="text-muted">No payment data available.</p>
                    <?php else: ?>
                        <?php foreach($paymentMethodBreakdown as $method): ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <i class="fas fa-<?php echo getPaymentIcon($method['payment_method']); ?> me-2"></i>
                                    <?php echo ucfirst(str_replace('_', ' ', $method['payment_method'])); ?>
                                </span>
                                <span style="font-weight:bold;">E<?php echo number_format($method['total'], 2); ?></span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar" style="width:<?php echo ($totalRevenue + $totalExpenses) > 0 ? ($method['total'] / ($totalRevenue + $totalExpenses)) * 100 : 0; ?>%;background:var(--gold);"></div>
                            </div>
                            <small class="text-muted"><?php echo $method['count']; ?> transactions</small>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2" style="color:var(--gold);"></i>Management Insights</h5>
                </div>
                <div class="card-body">
                    <div style="background: #f8f9fa; border-left: 4px solid var(--gold); padding: 15px; border-radius: 8px; margin: 10px 0;">
                        <strong>📊 Performance Summary</strong>
                        <ul class="list-unstyled mt-2">
                            <li>• <strong>Revenue:</strong> E<?php echo number_format($totalRevenue, 2); ?></li>
                            <li>• <strong>Expenses:</strong> E<?php echo number_format($totalExpenses, 2); ?></li>
                            <li>• <strong>Net Profit:</strong> E<?php echo number_format($netProfit, 2); ?></li>
                            <li>• <strong>Profit Margin:</strong> <?php echo number_format($profitMargin, 1); ?>%</li>
                            <li>• <strong>Average Transaction:</strong> E<?php echo number_format($avgTransactionValue, 2); ?></li>
                            <li>• <strong>Transaction Count:</strong> <?php echo $transactionCount; ?></li>
                        </ul>
                    </div>
                    
                    <?php if($profitMargin > 20): ?>
                    <div style="background: #f8f9fa; border-left: 4px solid var(--green); padding: 15px; border-radius: 8px; margin: 10px 0;">
                        <strong>✅ Positive Indicators</strong>
                        <p class="mb-0 small">Your profit margin is healthy at <?php echo number_format($profitMargin, 1); ?>%. Consider reinvesting in growth opportunities.</p>
                    </div>
                    <?php elseif($profitMargin > 10): ?>
                    <div style="background: #f8f9fa; border-left: 4px solid var(--gold); padding: 15px; border-radius: 8px; margin: 10px 0;">
                        <strong>⚠️ Moderate Performance</strong>
                        <p class="mb-0 small">Profit margin is <?php echo number_format($profitMargin, 1); ?>. Look for ways to reduce expenses or increase revenue.</p>
                    </div>
                    <?php else: ?>
                    <div style="background: #f8f9fa; border-left: 4px solid #dc3545; padding: 15px; border-radius: 8px; margin: 10px 0;">
                        <strong>🔴 Needs Attention</strong>
                        <p class="mb-0 small">Profit margin is low at <?php echo number_format($profitMargin, 1); ?>. Review expenses and pricing strategy.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportCSV() {
    // Collect data from tables and export as CSV
    let csv = "Category,Type,Amount\n";
    
    // Add income data
    <?php foreach($incomeBreakdown as $item): ?>
    csv += `"<?php echo addslashes($item['category']); ?>","Income",<?php echo $item['total']; ?>\n`;
    <?php endforeach; ?>
    
    // Add expense data
    <?php foreach($expenseBreakdown as $item): ?>
    csv += `"<?php echo addslashes($item['category']); ?>","Expense",<?php echo $item['total']; ?>\n`;
    <?php endforeach; ?>
    
    // Add monthly trend
    csv += "\nMonth,Income,Expenses,Profit\n";
    <?php foreach($monthlyTrend as $row): 
        $profit = $row['income'] - $row['expenses'];
    ?>
    csv += `"<?php echo date('M Y', strtotime($row['month'] . '-01')); ?>",<?php echo $row['income']; ?>,<?php echo $row['expenses']; ?>,<?php echo $profit; ?>\n`;
    <?php endforeach; ?>
    
    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `management_report_<?php echo date('Y-m-d'); ?>.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function getPaymentIcon(method) {
    const icons = {
        'cash': 'money-bill-wave',
        'bank_transfer': 'university',
        'mobile_money': 'mobile-alt',
        'credit_card': 'credit-card',
        'other': 'coins'
    };
    return icons[method] || 'coins';
}
</script>

<?php
function getPaymentIcon($method) {
    $icons = [
        'cash' => 'money-bill-wave',
        'bank_transfer' => 'university',
        'mobile_money' => 'mobile-alt',
        'credit_card' => 'credit-card',
        'other' => 'coins'
    ];
    return $icons[$method] ?? 'coins';
}
?>