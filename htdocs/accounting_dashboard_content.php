<?php
// Get accounting totals
$totalIncome = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM accounting_transactions WHERE type = 'income' AND status != 'void'")->fetchColumn();
$totalExpenses = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM accounting_transactions WHERE type = 'expense' AND status != 'void'")->fetchColumn();
$netProfit = $totalIncome - $totalExpenses;
$pendingTransactions = $pdo->query("SELECT COUNT(*) FROM accounting_transactions WHERE status = 'pending'")->fetchColumn();
$transactionCount = $pdo->query("SELECT COUNT(*) FROM accounting_transactions WHERE status != 'void'")->fetchColumn();
$profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;

// Get recent transactions
$recentTransactions = $pdo->query("SELECT * FROM accounting_transactions WHERE status != 'void' ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Get monthly summary
$monthlySummary = $pdo->query("
    SELECT 
        DATE_FORMAT(transaction_date, '%Y-%m') as month,
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expenses
    FROM accounting_transactions 
    WHERE status != 'void'
    GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// Get category breakdown
$categoryBreakdown = $pdo->query("
    SELECT 
        category,
        category_color,
        category_text_color,
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expenses
    FROM accounting_transactions 
    WHERE status != 'void'
    GROUP BY category, category_color, category_text_color
    ORDER BY category
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-book me-2" style="color:var(--wine);"></i>Smart Accounting</h2>
            <p class="text-muted">Just tell us what happened - we'll handle the accounting automatically</p>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number" style="color:var(--green);">E<?php echo number_format($totalIncome, 2); ?></div>
                <div class="stat-label"><i class="fas fa-arrow-up me-1" style="color:var(--green);"></i>Total Income</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number" style="color:#dc3545;">E<?php echo number_format($totalExpenses, 2); ?></div>
                <div class="stat-label"><i class="fas fa-arrow-down me-1" style="color:#dc3545;"></i>Total Expenses</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number" style="color:<?php echo $netProfit >= 0 ? 'var(--green)' : '#dc3545'; ?>;">E<?php echo number_format($netProfit, 2); ?></div>
                <div class="stat-label"><i class="fas fa-chart-line me-1"></i>Net Profit</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number" style="color:var(--gold);"><?php echo $pendingTransactions; ?></div>
                <div class="stat-label"><i class="fas fa-clock me-1"></i>Pending Review</div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="quick-action" onclick="openTransactionModal('income')" style="cursor:pointer;">
                <i class="fas fa-plus-circle"></i>
                <h6 class="mt-2">Record Income</h6>
                <small class="text-muted">Sales, payments received</small>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="quick-action" onclick="openTransactionModal('expense')" style="cursor:pointer;">
                <i class="fas fa-minus-circle"></i>
                <h6 class="mt-2">Record Expense</h6>
                <small class="text-muted">Purchases, bills, payments</small>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="quick-action" onclick="window.location.href='?section=accounting-reports'" style="cursor:pointer;">
                <i class="fas fa-file-alt"></i>
                <h6 class="mt-2">View Reports</h6>
                <small class="text-muted">Income Statement, Balance Sheet</small>
            </div>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2" style="color:var(--wine);"></i>Recent Transactions</h5>
            <button class="btn btn-sm btn-outline-wine" onclick="window.location.href='?section=accounting-journal'">View All</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($recentTransactions)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No transactions recorded yet</td></tr>
                        <?php else: ?>
                        <?php foreach($recentTransactions as $t): ?>
                        <tr class="transaction-row" onclick="viewTransaction(<?php echo $t['id']; ?>)">
                            <td><?php echo date('d/m/Y', strtotime($t['transaction_date'])); ?></td>
                            <td><?php echo htmlspecialchars($t['description']); ?></td>
                            <td><span class="category-badge" style="background:<?php echo $t['category_color'] ?? '#eee'; ?>;color:<?php echo $t['category_text_color'] ?? '#333'; ?>;"><?php echo htmlspecialchars($t['category']); ?></span></td>
                            <td><span class="badge <?php echo $t['type'] == 'income' ? 'bg-success' : 'bg-danger'; ?>"><?php echo ucfirst($t['type']); ?></span></td>
                            <td style="color:<?php echo $t['type'] == 'income' ? 'var(--green)' : '#dc3545'; ?>;font-weight:bold;">E<?php echo number_format($t['amount'], 2); ?></td>
                            <td><span class="badge <?php echo $t['status'] == 'posted' ? 'bg-success' : ($t['status'] == 'pending' ? 'bg-warning' : 'bg-secondary'); ?>"><?php echo ucfirst($t['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>