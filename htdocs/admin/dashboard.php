<?php
session_start();
// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Get user details from session
$userRole = $_SESSION['admin_role'] ?? 'staff';
$userName = $_SESSION['admin_name'] ?? 'User';
$userEmail = $_SESSION['admin_email'] ?? '';

// ==================== ROLE-BASED ACCESS CONTROL ====================
$allowedSections = [
    'admin' => ['dashboard', 'wines', 'orders', 'pairings', 'subscriptions', 'subscription-stats', 'subscription-requests', 'staff', 'corporate-gifts', 'gift-baskets', 'accounting-dashboard', 'accounting-reports'],
    'manager' => ['dashboard', 'wines', 'orders', 'pairings', 'subscriptions', 'subscription-stats', 'subscription-requests', 'staff', 'corporate-gifts', 'gift-baskets'],
    'staff' => ['dashboard', 'wines', 'orders', 'pairings', 'subscriptions', 'subscription-stats', 'corporate-gifts', 'gift-baskets']
];

$section = $_GET['section'] ?? 'dashboard';
if (!in_array($section, $allowedSections[$userRole] ?? ['dashboard'])) {
    $section = 'dashboard';
    $_GET['section'] = 'dashboard';
}

// Database connection
$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { die("Database connection failed"); }

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;
if ($action === 'cancel_order' && $id) { 
    $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?")->execute([$id]); 
    header('Location: ?section=orders&msg=Order Cancelled'); 
    exit; 
}
if ($action === 'update_stock' && $id) { 
    $newStock = $_GET['stock'] ?? 0; 
    $pdo->prepare("UPDATE wines SET stock_quantity = ? WHERE id = ?")->execute([$newStock, $id]); 
    header('Location: ?section=wines&msg=Stock Updated'); 
    exit; 
}

// Process order with POP/Receipt
if ($action === 'process_order' && $id) {
    $receipt_number = $_POST['receipt_number'] ?? $_GET['receipt'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'bank_transfer';
    
    if (!empty($receipt_number)) {
        $pdo->prepare("UPDATE orders SET status = 'processing', receipt_number = ?, payment_method = ? WHERE id = ?")->execute([$receipt_number, $payment_method, $id]);
        header('Location: ?section=orders&msg=Order Processed with Receipt #' . $receipt_number);
    } else {
        header('Location: ?section=orders&msg=Receipt number required');
    }
    exit;
}

// Complete order (cash delivery)
if ($action === 'complete_order' && $id) {
    $receipt_number = $_POST['receipt_number'] ?? $_GET['receipt'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'cash';
    
    if (!empty($receipt_number)) {
        $pdo->prepare("UPDATE orders SET status = 'completed', receipt_number = ?, payment_method = ? WHERE id = ?")->execute([$receipt_number, $payment_method, $id]);
        header('Location: ?section=orders&msg=Order Completed with Receipt #' . $receipt_number);
    } else {
        header('Location: ?section=orders&msg=Receipt number required for cash delivery');
    }
    exit;
}

// Get counts
$winesCount = $pdo->query("SELECT COUNT(*) FROM wines")->fetchColumn();
$pairingsCount = $pdo->query("SELECT COUNT(*) FROM pairings")->fetchColumn();
$subscriptionsCount = $pdo->query("SELECT COUNT(*) FROM subscriptions")->fetchColumn();
$staffCount = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
$lowStock = $pdo->query("SELECT COUNT(*) FROM wines WHERE stock_quantity < 20")->fetchColumn();
$criticalStock = $pdo->query("SELECT COUNT(*) FROM wines WHERE stock_quantity < 5")->fetchColumn();
$outOfStock = $pdo->query("SELECT COUNT(*) FROM wines WHERE stock_quantity = 0")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

// Get subscription request counts
$pendingRequests = $pdo->query("SELECT COUNT(*) FROM subscription_requests WHERE status = 'pending'")->fetchColumn();
$totalRequests = $pdo->query("SELECT COUNT(*) FROM subscription_requests")->fetchColumn();

// Get order counts
$pendingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$processingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'processing'")->fetchColumn();
$completedCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetchColumn();
$cancelledCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn();

// Get data
$pendingOrdersList = $pdo->query("SELECT * FROM orders WHERE status = 'pending' ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
$processingOrders = $pdo->query("SELECT * FROM orders WHERE status = 'processing' ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
$completedOrders = $pdo->query("SELECT * FROM orders WHERE status = 'completed' ORDER BY created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
$allOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
$wines = $pdo->query("SELECT * FROM wines ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$pairings = $pdo->query("SELECT * FROM pairings ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$subscriptions = $pdo->query("SELECT * FROM subscriptions ORDER BY display_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$staff = $pdo->query("SELECT * FROM staff ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$corporateGifts = $pdo->query("SELECT * FROM corporate_gifts WHERE is_active = 1 ORDER BY price ASC")->fetchAll(PDO::FETCH_ASSOC);
$giftBaskets = $pdo->query("SELECT * FROM gift_baskets WHERE is_active = 1 ORDER BY price ASC")->fetchAll(PDO::FETCH_ASSOC);

// Get accounting totals
$totalIncome = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM accounting_transactions WHERE type = 'income' AND status != 'void'")->fetchColumn();
$totalExpenses = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM accounting_transactions WHERE type = 'expense' AND status != 'void'")->fetchColumn();
$netProfit = $totalIncome - $totalExpenses;
$pendingTransactions = $pdo->query("SELECT COUNT(*) FROM accounting_transactions WHERE status = 'pending'")->fetchColumn();
$transactionCount = $pdo->query("SELECT COUNT(*) FROM accounting_transactions WHERE status != 'void'")->fetchColumn();
$profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;

// Get recent transactions
$recentTransactions = $pdo->query("SELECT * FROM accounting_transactions WHERE status != 'void' ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Get monthly summary for accounting
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

// Get category breakdown for accounting
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

$msg = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Wine & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --wine: #722f37; --green: #1a6b3c; --red: #dc3545; --orange: #fd7e14; --gold: #c9a03d; }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        .sidebar { background: var(--wine); color: white; min-height: 100vh; padding: 20px; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; margin: 5px 0; border-radius: 10px; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); }
        .card { border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border: none; margin-bottom: 20px; }
        .stat-card { background: white; border-radius: 15px; padding: 20px; text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: var(--wine); }
        .stat-number.text-warning { color: var(--orange); }
        .stat-number.text-success { color: var(--green); }
        .stat-number.text-danger { color: var(--red); }
        .btn-wine { background: var(--wine); color: white; border: none; padding: 8px 20px; border-radius: 40px; }
        .btn-wine:hover { background: #5a232a; color: white; }
        .btn-outline-wine { background: transparent; border: 1px solid var(--wine); color: var(--wine); padding: 8px 20px; border-radius: 40px; }
        .btn-outline-wine:hover { background: var(--wine); color: white; }
        .badge-stock-high { background: var(--green); color: white; padding: 5px 12px; border-radius: 20px; }
        .badge-stock-medium { background: var(--orange); color: white; }
        .badge-stock-low { background: var(--red); color: white; }
        .badge-status-pending { background: var(--orange); color: white; }
        .badge-status-processing { background: #0dcaf0; color: white; }
        .badge-status-cancelled { background: var(--red); color: white; }
        .badge-status-completed { background: var(--green); color: white; }
        .stock-level { width: 100%; height: 8px; background: #e0e0e0; border-radius: 4px; margin-top: 8px; }
        .stock-fill { height: 100%; background: var(--green); border-radius: 4px; }
        .stock-fill.low { background: var(--red); }
        .stock-fill.medium { background: var(--orange); }
        .nav-link-custom { cursor: pointer; }
        
        .role-admin { background: #dc3545; color: white; }
        .role-manager { background: #ffc107; color: #333; }
        .role-staff { background: #6c757d; color: white; }
        
        .subscriber-list { background: #f8f9fa; border-radius: 8px; padding: 10px; margin-top: 10px; max-height: 300px; overflow-y: auto; }
        .subscriber-item { border-bottom: 1px solid #eee; padding: 8px 0; font-size: 0.85rem; }
        .subscriber-item:last-child { border-bottom: none; }
        .subscriber-item strong { color: #2c1a1a; }
        
        .receipt-input { max-width: 250px; display: inline-block; }
        .payment-method-select { max-width: 200px; display: inline-block; }
        
        .pop-modal-content { max-width: 800px; }
        .pop-modal-content .modal-body { padding: 20px; text-align: center; }
        .pop-modal-content img { max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .pop-modal-content .pop-info { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px; text-align: left; }
        .pop-modal-content .pop-info strong { color: var(--wine); }
        .pop-thumbnail { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; cursor: pointer; transition: 0.3s; }
        .pop-thumbnail:hover { transform: scale(1.1); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .no-pop { color: #999; font-size: 0.85rem; }
        
        .table td, .table th { vertical-align: middle; }
        
        /* Gift Image Thumbnail */
        .gift-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #ddd;
        }
        .gift-thumbnail:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .no-image {
            color: #999;
            font-size: 0.8rem;
        }

        /* Accounting Styles */
        .category-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; }
        .transaction-row { cursor: pointer; transition: 0.2s; }
        .transaction-row:hover { background: #f8f4f0; }
        .quick-action { border: 2px dashed #ddd; border-radius: 15px; padding: 20px; text-align: center; transition: 0.3s; background: white; }
        .quick-action:hover { border-color: var(--gold); transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .quick-action i { font-size: 2.5rem; color: var(--wine); }
        .modal-content { border-radius: 20px; }
        .modal-header { background: var(--wine); color: white; border-radius: 20px 20px 0 0; }
        .btn-close-white { filter: brightness(0) invert(1); }
        .filter-bar { background: white; padding: 15px 20px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .filter-bar label { font-weight: 600; color: #1a1a2e; font-size: 0.9rem; }
        .print-only { display: none; }
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <div class="text-center mb-4">
                    <i class="fas fa-wine-bottle fa-2x"></i>
                    <h5 class="mt-2">Wine & Co.</h5>
                    <small>Welcome, <?php echo htmlspecialchars($userName); ?></small>
                    <br>
                    <small class="badge <?php echo $userRole == 'admin' ? 'role-admin' : ($userRole == 'manager' ? 'role-manager' : 'role-staff'); ?> mt-1">
                        <?php echo strtoupper($userRole); ?>
                    </small>
                </div>
                <hr>
                <a href="?section=dashboard" class="nav-link-custom <?php echo $section == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
                <a href="?section=wines" class="nav-link-custom <?php echo $section == 'wines' ? 'active' : ''; ?>"><i class="fas fa-wine-glass-alt me-2"></i> Wines & Stock</a>
                <a href="?section=orders" class="nav-link-custom <?php echo $section == 'orders' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart me-2"></i> Orders <?php if($pendingOrders>0){echo '<span class="badge bg-warning text-dark float-end">'.$pendingOrders.'</span>';} ?></a>
                <a href="?section=pairings" class="nav-link-custom <?php echo $section == 'pairings' ? 'active' : ''; ?>"><i class="fas fa-cheese me-2"></i> Pairings</a>
                <a href="?section=subscriptions" class="nav-link-custom <?php echo $section == 'subscriptions' ? 'active' : ''; ?>"><i class="fas fa-gem me-2"></i> Subscriptions</a>
                <a href="?section=subscription-stats" class="nav-link-custom <?php echo $section == 'subscription-stats' ? 'active' : ''; ?>"><i class="fas fa-chart-pie me-2"></i> Subscription Stats</a>
                
                <a href="?section=subscription-requests" class="nav-link-custom <?php echo $section == 'subscription-requests' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list me-2"></i> Sub Requests
                    <?php if($pendingRequests > 0): ?>
                    <span class="badge bg-warning text-dark float-end"><?php echo $pendingRequests; ?></span>
                    <?php endif; ?>
                </a>
                
                <?php if($userRole == 'admin' || $userRole == 'manager'): ?>
                <a href="?section=staff" class="nav-link-custom <?php echo $section == 'staff' ? 'active' : ''; ?>"><i class="fas fa-users me-2"></i> Staff Management</a>
                <?php endif; ?>
                
                <a href="?section=corporate-gifts" class="nav-link-custom <?php echo $section == 'corporate-gifts' ? 'active' : ''; ?>"><i class="fas fa-gift me-2"></i> Corporate Gifts</a>
                <a href="?section=gift-baskets" class="nav-link-custom <?php echo $section == 'gift-baskets' ? 'active' : ''; ?>"><i class="fas fa-basket-shopping me-2"></i> Gift Baskets</a>
                
                <?php if($userRole == 'admin'): ?>
                   <a href="magazine-manager.php" class="nav-link-custom <?php echo $section == 'magazine-manager' ? 'active' : ''; ?>">
                    <i class="fas fa-book me-2"></i> Magazine
                    <?php if($pendingMagazineRequests > 0): ?>
                    <span class="badge bg-warning text-dark float-end"><?php echo $pendingMagazineRequests; ?></span>
                    <?php endif; ?>
                </a>
                <!-- ====== ACCOUNTING MENU ====== -->
                <a href="?section=accounting-dashboard" class="nav-link-custom <?php echo $section == 'accounting-dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-book me-2"></i> Accounting
                </a>
                <a href="?section=accounting-reports" class="nav-link-custom <?php echo $section == 'accounting-reports' ? 'active' : ''; ?>" style="padding-left: 30px; font-size: 0.9rem;">
                    <i class="fas fa-chart-bar me-2"></i> Reports
                </a>
                <?php endif; ?>
                
                <hr>
                <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>

            <div class="col-md-10 p-4">
                <?php if($msg): ?><div class="alert alert-success alert-dismissible fade show"><?php echo $msg; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

                <!-- ==================== DASHBOARD ==================== -->
                <?php if($section=='dashboard'): ?>
                    <h2 class="mb-4">Dashboard</h2>
                    <div class="row">
                        <div class="col-md-3 mb-4"><div class="stat-card"><div class="stat-number"><?php echo $winesCount; ?></div><div>Total Wines</div></div></div>
                        <div class="col-md-3 mb-4"><div class="stat-card"><div class="stat-number"><?php echo $pairingsCount; ?></div><div>Total Pairings</div></div></div>
                        <div class="col-md-3 mb-4"><div class="stat-card"><div class="stat-number"><?php echo $subscriptionsCount; ?></div><div>Subscriptions</div></div></div>
                        <div class="col-md-3 mb-4"><div class="stat-card"><div class="stat-number">E<?php echo number_format($totalRevenue,2); ?></div><div>Revenue</div></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="card p-3"><h5>Stock Alerts</h5><div class="row"><div class="col-4"><div class="border rounded p-2"><div class="h3 text-success"><?php echo $winesCount-($lowStock+$outOfStock); ?></div><small>Healthy</small></div></div><div class="col-4"><div class="border rounded p-2"><div class="h3 text-warning"><?php echo $lowStock-$criticalStock; ?></div><small>Low</small></div></div><div class="col-4"><div class="border rounded p-2"><div class="h3 text-danger"><?php echo $criticalStock+$outOfStock; ?></div><small>Critical</small></div></div></div>
                            <div class="mt-3"><a href="?section=wines" class="btn btn-sm btn-wine">Manage Inventory</a>
                            <a href="../backend/create-purchase-order.php" target="_blank" class="btn btn-sm btn-outline-wine ms-2"><i class="fas fa-file-purchase me-1"></i>Create PO</a></div>
                        </div></div>
                        <div class="col-md-6"><div class="card p-3"><h5>Pending Orders</h5><div class="text-center"><div class="display-4 text-warning"><?php echo $pendingOrders; ?></div><p>Orders awaiting processing</p><a href="?section=orders" class="btn btn-sm btn-wine">View Orders</a></div></div></div>
                    </div>

                <!-- ==================== WINES ==================== -->
                <?php elseif($section=='wines'): ?>
                    <h2 class="mb-4"><i class="fas fa-wine-glass-alt me-2"></i>Wine Inventory</h2>
                    <div class="card"><div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <button class="btn btn-wine" onclick="showAddWineModal()"><i class="fas fa-plus me-2"></i>Add New Wine</button>
                            <a href="../backend/create-purchase-order.php" target="_blank" class="btn btn-outline-wine">
                                <i class="fas fa-file-purchase me-2"></i>Create Purchase Order
                            </a>
                        </div>
                    <div class="table-responsive"><table class="table table-hover"><thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Variety</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead><tbody>
                    <?php foreach($wines as $wine): $stockClass=$wine['stock_quantity']>=20?'badge-stock-high':($wine['stock_quantity']>=10?'badge-stock-medium':'badge-stock-low'); ?>
                    <tr><td><?php echo $wine['id']; ?></td>
                    <td><img src="<?php echo $wine['image_url']?:'images/placeholder.jpg'; ?>" style="width:50px; height:50px; object-fit:cover; border-radius:10px;" onerror="this.src='images/placeholder.jpg'"></td>
                    <td><strong><?php echo htmlspecialchars($wine['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($wine['variety']); ?></td>
                    <td>E<?php echo number_format($wine['price'],2); ?></td>
                    <td><span class="badge <?php echo $stockClass; ?>"><?php echo $wine['stock_quantity']; ?> units</span><div class="stock-level"><div class="stock-fill <?php echo $wine['stock_quantity']<20?($wine['stock_quantity']<10?'low':'medium'):''; ?>" style="width:<?php echo min(100,($wine['stock_quantity']/100)*100); ?>%"></div></div></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editWine(<?php echo $wine['id']; ?>)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteWine(<?php echo $wine['id']; ?>)"><i class="fas fa-trash"></i></button>
                        <button class="btn btn-sm btn-outline-success" onclick="updateStock(<?php echo $wine['id']; ?>,<?php echo $wine['stock_quantity']; ?>)"><i class="fas fa-boxes"></i></button>
                    </td></tr>
                    <?php endforeach; ?>
                    </tbody></table></div></div></div>

                <!-- ==================== ORDERS ==================== -->
                <?php elseif($section=='orders'): ?>
                    <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Orders Management</h2>
                    
                    <div class="row mb-4">
                        <div class="col-md-3 mb-2">
                            <div class="stat-card" style="border-left: 4px solid #fd7e14;">
                                <div class="stat-number text-warning"><?php echo $pendingCount; ?></div>
                                <div>Pending Orders</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="stat-card" style="border-left: 4px solid #0dcaf0;">
                                <div class="stat-number text-info"><?php echo $processingCount; ?></div>
                                <div>Processing</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="stat-card" style="border-left: 4px solid #1a6b3c;">
                                <div class="stat-number text-success"><?php echo $completedCount; ?></div>
                                <div>Completed</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="stat-card" style="border-left: 4px solid #dc3545;">
                                <div class="stat-number text-danger"><?php echo $cancelledCount; ?></div>
                                <div>Cancelled</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#pending">Pending <span class="badge bg-warning"><?php echo $pendingCount; ?></span></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#processing">Processing <span class="badge bg-info"><?php echo $processingCount; ?></span></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#completed">Completed <span class="badge bg-success"><?php echo $completedCount; ?></span></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#all">All Orders</a></li>
                            </ul>
                            
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pending">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Customer</th>
                                                    <th>Total</th>
                                                    <th>Payment Method</th>
                                                    <th>POP</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if(empty($pendingOrdersList)): ?>
                                                <tr><td colspan="7" class="text-center text-muted">No pending orders</td></tr>
                                            <?php else: 
                                            foreach($pendingOrdersList as $order): 
                                                $popFile = !empty($order['pop_file']) ? basename($order['pop_file']) : '';
                                                $popPath = '/uploads/pop/' . $popFile;
                                            ?>
                                            <tr>
                                                <td><strong>#<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></strong></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($order['customer_name'] ?: 'Unknown Customer'); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($order['customer_email'] ?: 'No email'); ?></small>
                                                </td>
                                                <td><strong>E<?php echo number_format($order['total'] ?? 0, 2); ?></strong></td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'N/A')); ?></span>
                                                </td>
                                                <td>
                                                    <?php if(!empty($popFile)): ?>
                                                        <button class="btn btn-sm btn-info" onclick="viewPOP('<?php echo htmlspecialchars($popPath); ?>', '<?php echo htmlspecialchars($order['customer_name']); ?>', '<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?>')" title="View POP">
                                                            <i class="fas fa-file-image"></i> View
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="no-pop">No POP</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <form method="POST" action="?section=orders&action=process_order&id=<?php echo $order['id']; ?>" class="d-flex flex-wrap gap-2 align-items-center">
                                                        <select name="payment_method" class="form-select form-select-sm" style="width: 130px;" required>
                                                            <option value="">Payment Method</option>
                                                            <option value="bank_transfer">Bank Transfer</option>
                                                            <option value="mobile_money">Mobile Money</option>
                                                            <option value="e_wallet">E-Wallet</option>
                                                            <option value="cash">Cash</option>
                                                        </select>
                                                        <input type="text" name="receipt_number" class="form-control form-control-sm" style="width: 150px;" placeholder="POP/Receipt #" required>
                                                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Process</button>
                                                    </form>
                                                    <button class="btn btn-sm btn-danger mt-1" onclick="cancelOrder(<?php echo $order['id']; ?>)"><i class="fas fa-times"></i> Cancel</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="processing">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Customer</th>
                                                    <th>Total</th>
                                                    <th>Receipt #</th>
                                                    <th>Payment Method</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if(empty($processingOrders)): ?>
                                                <tr><td colspan="7" class="text-center text-muted">No orders in processing</td></tr>
                                            <?php else: 
                                            foreach($processingOrders as $order): ?>
                                            <tr>
                                                <td><strong>#<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></strong></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($order['customer_name'] ?: 'Unknown Customer'); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($order['customer_email'] ?: 'No email'); ?></small>
                                                </td>
                                                <td><strong>E<?php echo number_format($order['total'] ?? 0, 2); ?></strong></td>
                                                <td><span class="badge bg-info"><?php echo htmlspecialchars($order['receipt_number'] ?? 'N/A'); ?></span></td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'N/A')); ?></span>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <form method="POST" action="?section=orders&action=complete_order&id=<?php echo $order['id']; ?>" class="d-flex flex-wrap gap-2 align-items-center">
                                                        <input type="text" name="receipt_number" class="form-control form-control-sm" style="width: 150px;" placeholder="Delivery Receipt #" required>
                                                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-truck"></i> Deliver</button>
                                                    </form>
                                                    <button class="btn btn-sm btn-danger mt-1" onclick="cancelOrder(<?php echo $order['id']; ?>)"><i class="fas fa-times"></i> Cancel</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="completed">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Customer</th>
                                                    <th>Total</th>
                                                    <th>Receipt #</th>
                                                    <th>Payment Method</th>
                                                    <th>Completed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if(empty($completedOrders)): ?>
                                                <tr><td colspan="6" class="text-center text-muted">No completed orders</td></tr>
                                            <?php else: 
                                            foreach($completedOrders as $order): ?>
                                            <tr>
                                                <td><strong>#<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></strong></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($order['customer_name'] ?: 'Unknown Customer'); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($order['customer_email'] ?: 'No email'); ?></small>
                                                </td>
                                                <td>E<?php echo number_format($order['total'] ?? 0, 2); ?></td>
                                                <td><span class="badge bg-success"><?php echo htmlspecialchars($order['receipt_number'] ?? 'N/A'); ?></span></td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'N/A')); ?></span>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                            </tr>
                                            <?php endforeach; endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="all">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Customer</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Receipt</th>
                                                    <th>Date</th>
                                                    <th>Invoice</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($allOrders as $order): 
                                                $statusClass = $order['status'] == 'completed' ? 'bg-success' : ($order['status'] == 'processing' ? 'bg-info' : ($order['status'] == 'cancelled' ? 'bg-danger' : 'bg-warning'));
                                                $displayStatus = $order['status'] == 'delivered' ? 'Completed' : ucfirst($order['status']);
                                            ?>
                                            <tr>
                                                <td><strong>#<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($order['customer_name'] ?: 'Unknown'); ?></td>
                                                <td>E<?php echo number_format($order['total'] ?? 0, 2); ?></td>
                                                <td><span class="badge <?php echo $statusClass; ?>"><?php echo $displayStatus; ?></span></td>
                                                <td><?php echo htmlspecialchars($order['receipt_number'] ?? '-'); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="viewInvoice(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- ==================== PAIRINGS ==================== -->
                <?php elseif($section=='pairings'): ?>
                    <h2 class="mb-4">Pairings</h2>
                    <div class="card"><div class="card-body">
                        <button class="btn btn-wine mb-3" onclick="showAddPairingModal()"><i class="fas fa-plus"></i> Add Pairing</button>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Image</th><th>Actions</th></tr></thead>
                                <tbody>
                                <?php foreach($pairings as $p): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                                    <td>E<?php echo number_format($p['price'],2); ?></td>
                                    <td>
                                        <?php if(!empty($p['image_url'])): ?>
                                            <img src="<?php echo $p['image_url']; ?>" style="width:50px; height:50px; object-fit:cover; border-radius:10px;" onerror="this.src='images/placeholder.jpg'">
                                        <?php else: ?>
                                            <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editPairing(<?php echo $p['id']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deletePairing(<?php echo $p['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div></div>

                <!-- ==================== SUBSCRIPTIONS ==================== -->
                <?php elseif($section=='subscriptions'): ?>
                    <h2 class="mb-4">Subscriptions</h2>
                    <div class="card"><div class="card-body">
                        <button class="btn btn-wine mb-3" onclick="openSubscriptionModal()"><i class="fas fa-plus"></i> Add Plan</button>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Wines/Month</th><th>Popular</th><th>Expiry (Days)</th><th>Status</th><th>Actions</th></tr></thead>
                                <tbody>
                                <?php foreach($subscriptions as $sub): ?>
                                <tr>
                                    <td><?php echo $sub['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($sub['display_name']); ?></strong></td>
                                    <td>E<?php echo number_format($sub['price'],2); ?>/month</td>
                                    <td><?php echo $sub['wines_per_month']; ?></td>
                                    <td><?php echo $sub['is_popular']?'⭐ Yes':'No'; ?></td>
                                    <td><?php echo $sub['expiry_days'] ?? 30; ?> days</td>
                                    <td>
                                        <span class="badge <?php echo ($sub['is_active'] ?? 1) ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ($sub['is_active'] ?? 1) ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editSubscription(<?php echo $sub['id']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSubscription(<?php echo $sub['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div></div>

                <!-- ==================== SUBSCRIPTION STATS ==================== -->
                <?php elseif($section=='subscription-stats'): ?>
                    <h2 class="mb-4"><i class="fas fa-chart-pie me-2"></i>Subscription Stats & Subscribers</h2>
                    
                    <?php
                    $planStats = $pdo->query("
                        SELECT 
                            s.id,
                            s.display_name,
                            s.price,
                            s.wines_per_month,
                            COUNT(sr.id) as subscriber_count
                        FROM subscriptions s
                        LEFT JOIN subscription_requests sr ON s.display_name = sr.plan_name AND sr.status IN ('active', 'approved')
                        GROUP BY s.id
                        ORDER BY s.price ASC
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    
                    $allSubscribers = $pdo->query("
                        SELECT * FROM subscription_requests 
                        WHERE status IN ('active', 'approved')
                        ORDER BY created_at DESC
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <div class="row mb-4">
                        <?php foreach($planStats as $stat): ?>
                        <div class="col-md-3 mb-3">
                            <div class="card p-3 text-center h-100">
                                <h5><?php echo htmlspecialchars($stat['display_name']); ?></h5>
                                <div class="display-4 text-warning"><?php echo $stat['subscriber_count']; ?></div>
                                <p>Active Subscribers</p>
                                <hr>
                                <small>E<?php echo number_format($stat['price'],2); ?>/month • <?php echo $stat['wines_per_month']; ?> wines</small>
                                
                                <?php if($stat['subscriber_count'] > 0): ?>
                                <button class="btn btn-sm btn-outline-wine mt-2" onclick="toggleSubscribers(<?php echo $stat['id']; ?>, '<?php echo htmlspecialchars($stat['display_name']); ?>')">
                                    <i class="fas fa-users me-1"></i> View Subscribers
                                </button>
                                <div id="subscribers-<?php echo $stat['id']; ?>" style="display:none;" class="subscriber-list mt-2">
                                    <div class="text-center text-muted small">
                                        <i class="fas fa-spinner fa-spin"></i> Loading...
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list me-2"></i>All Active Subscribers</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Plan</th>
                                            <th>Joined</th>
                                            <th>Expires</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(empty($allSubscribers)): ?>
                                        <tr><td colspan="7" class="text-center text-muted">No active subscribers yet</td></tr>
                                    <?php else: ?>
                                        <?php foreach($allSubscribers as $sub): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($sub['full_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($sub['email']); ?></td>
                                            <td><?php echo htmlspecialchars($sub['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($sub['plan_name']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($sub['created_at'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($sub['expiry_date'])); ?></td>
                                            <td><span class="badge bg-success">ACTIVE</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <!-- ==================== SUBSCRIPTION REQUESTS ==================== -->
                <?php elseif($section=='subscription-requests'): ?>
                    <h2 class="mb-4"><i class="fas fa-clipboard-list me-2"></i>Subscription Requests</h2>
                    
                    <div class="row mb-4">
                        <?php
                        $approvedCount = $pdo->query("SELECT COUNT(*) FROM subscription_requests WHERE status IN ('approved', 'active')")->fetchColumn();
                        $expiredCount = $pdo->query("SELECT COUNT(*) FROM subscription_requests WHERE status = 'expired'")->fetchColumn();
                        ?>
                        <div class="col-md-3 mb-3"><div class="stat-card"><div class="stat-number"><?php echo $totalRequests; ?></div><div>Total Requests</div></div></div>
                        <div class="col-md-3 mb-3"><div class="stat-card"><div class="stat-number text-warning"><?php echo $pendingRequests; ?></div><div>Pending</div></div></div>
                        <div class="col-md-3 mb-3"><div class="stat-card"><div class="stat-number text-success"><?php echo $approvedCount; ?></div><div>Approved</div></div></div>
                        <div class="col-md-3 mb-3"><div class="stat-card"><div class="stat-number text-danger"><?php echo $expiredCount; ?></div><div>Expired</div></div></div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#pendingRequests">Pending <span class="badge bg-warning"><?php echo $pendingRequests; ?></span></a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#allRequests">All Requests</a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#expiredRequests">Expired</a></li>
                            </ul>
                            
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pendingRequests">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Customer</th>
                                                    <th>Plan</th>
                                                    <th>Amount</th>
                                                    <th>Expires</th>
                                                    <th>POP</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                            $pendingRequestsList = $pdo->query("SELECT * FROM subscription_requests WHERE status = 'pending' ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($pendingRequestsList as $req): 
                                            ?>
                                            <tr>
                                                <td>#<?php echo $req['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($req['full_name']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($req['email']); ?></small><br>
                                                    <small><?php echo htmlspecialchars($req['phone']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($req['plan_name']); ?></td>
                                                <td>E<?php echo number_format($req['price'], 2); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($req['expiry_date'])); ?></td>
                                                <td>
                                                    <?php if($req['pop_path']): ?>
                                                        <a href="<?php echo $req['pop_path']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-file-image"></i> View POP
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-success mb-1" onclick="approveSubscription(<?php echo $req['id']; ?>)">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button class="btn btn-sm btn-danger mb-1" onclick="rejectSubscription(<?php echo $req['id']; ?>)">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if(empty($pendingRequestsList)): ?>
                                            <tr><td colspan="7" class="text-center text-muted">No pending requests</td></tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="allRequests">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Customer</th>
                                                    <th>Plan</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Expires</th>
                                                    <th>POP</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                            $allRequestsList = $pdo->query("SELECT * FROM subscription_requests ORDER BY created_at DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($allRequestsList as $req): 
                                                $statusClass = [
                                                    'pending' => 'bg-warning',
                                                    'approved' => 'bg-info',
                                                    'active' => 'bg-success',
                                                    'expired' => 'bg-danger',
                                                    'cancelled' => 'bg-secondary'
                                                ][$req['status']] ?? 'bg-secondary';
                                            ?>
                                            <tr>
                                                <td>#<?php echo $req['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($req['full_name']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($req['email']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($req['plan_name']); ?></td>
                                                <td>E<?php echo number_format($req['price'], 2); ?></td>
                                                <td><span class="badge <?php echo $statusClass; ?>"><?php echo strtoupper($req['status']); ?></span></td>
                                                <td><?php echo date('d/m/Y', strtotime($req['expiry_date'])); ?></td>
                                                <td>
                                                    <?php if($req['pop_path']): ?>
                                                        <a href="<?php echo $req['pop_path']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-file-image"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($req['status'] == 'pending'): ?>
                                                        <button class="btn btn-sm btn-success" onclick="approveSubscription(<?php echo $req['id']; ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="rejectSubscription(<?php echo $req['id']; ?>)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="expiredRequests">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Customer</th>
                                                    <th>Plan</th>
                                                    <th>Amount</th>
                                                    <th>Expired</th>
                                                    <th>POP</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                            $expiredRequestsList = $pdo->query("SELECT * FROM subscription_requests WHERE status = 'expired' ORDER BY expiry_date DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($expiredRequestsList as $req): 
                                            ?>
                                            <tr>
                                                <td>#<?php echo $req['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($req['full_name']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($req['email']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($req['plan_name']); ?></td>
                                                <td>E<?php echo number_format($req['price'], 2); ?></td>
                                                <td>
                                                    <?php echo date('d/m/Y', strtotime($req['expiry_date'])); ?>
                                                    <br>
                                                    <small class="text-danger">Expired</small>
                                                </td>
                                                <td>
                                                    <?php if($req['pop_path']): ?>
                                                        <a href="<?php echo $req['pop_path']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-file-image"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if(empty($expiredRequestsList)): ?>
                                            <tr><td colspan="6" class="text-center text-muted">No expired requests</td></tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- ==================== STAFF ==================== -->
                <?php elseif($section=='staff' && ($userRole=='admin'||$userRole=='manager')): ?>
                    <h2 class="mb-4"><i class="fas fa-users me-2"></i>Staff Management</h2>
                    <div class="card"><div class="card-body">
                        <button class="btn btn-wine mb-3" onclick="showAddStaffModal()"><i class="fas fa-plus me-2"></i> Add Staff</button>
                    <div class="table-responsive"><table class="table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th><tr></thead><tbody>
                    <?php foreach($staff as $member): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($member['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><span class="badge <?php echo $member['role']=='admin'?'role-admin':($member['role']=='manager'?'role-manager':'role-staff'); ?>"><?php echo strtoupper($member['role']); ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($member['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="editStaff(<?php echo $member['id']; ?>,'<?php echo htmlspecialchars($member['name']); ?>','<?php echo htmlspecialchars($member['email']); ?>','<?php echo $member['role']; ?>')"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-outline-warning" onclick="resetStaffPassword(<?php echo $member['id']; ?>,'<?php echo htmlspecialchars($member['email']); ?>')"><i class="fas fa-key"></i> Reset</button>
                            <?php if($member['email'] != $userEmail): ?>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteStaff(<?php echo $member['id']; ?>)"><i class="fas fa-trash"></i> Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody></table></div></div></div>

                <!-- ==================== CORPORATE GIFTS (WITH IMAGES) ==================== -->
                <?php elseif($section=='corporate-gifts'): ?>
                    <h2 class="mb-4"><i class="fas fa-gift me-2"></i>Corporate Gifts Management</h2>
                    <div class="card"><div class="card-body">
                        <button class="btn btn-wine mb-3" onclick="showAddCorporateGiftModal()"><i class="fas fa-plus me-2"></i> Add Corporate Gift</button>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Tier</th>
                                        <th>Price (E)</th>
                                        <th>Wines</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($corporateGifts as $gift): ?>
                                <tr>
                                    <td><?php echo $gift['id']; ?></td>
                                    <td>
                                        <?php if(!empty($gift['image_url'])): ?>
                                            <img src="<?php echo $gift['image_url']; ?>" class="gift-thumbnail" onerror="this.src='images/placeholder.jpg'">
                                        <?php else: ?>
                                            <span class="no-image">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($gift['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($gift['tier']); ?></td>
                                    <td>E<?php echo number_format($gift['price'],2); ?></td>
                                    <td><?php echo $gift['wines_included']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editCorporateGift(<?php echo $gift['id']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCorporateGift(<?php echo $gift['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div></div>

                <!-- ==================== GIFT BASKETS (WITH IMAGES) ==================== -->
                <?php elseif($section=='gift-baskets'): ?>
                    <h2 class="mb-4"><i class="fas fa-basket-shopping me-2"></i>Gift Baskets Management</h2>
                    <div class="card"><div class="card-body">
                        <button class="btn btn-wine mb-3" onclick="showAddGiftBasketModal()"><i class="fas fa-plus me-2"></i> Add Gift Basket</button>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price (E)</th>
                                        <th>Wines</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($giftBaskets as $basket): ?>
                                <tr>
                                    <td><?php echo $basket['id']; ?></td>
                                    <td>
                                        <?php if(!empty($basket['image_url'])): ?>
                                            <img src="<?php echo $basket['image_url']; ?>" class="gift-thumbnail" onerror="this.src='images/placeholder.jpg'">
                                        <?php else: ?>
                                            <span class="no-image">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($basket['name']); ?></strong></td>
                                    <td>E<?php echo number_format($basket['price'],2); ?></td>
                                    <td><?php echo $basket['wines_included']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editGiftBasket(<?php echo $basket['id']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteGiftBasket(<?php echo $basket['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div></div>

                <!-- ==================== ACCOUNTING DASHBOARD ==================== -->
                <?php elseif($section=='accounting-dashboard'): ?>
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

                <!-- ==================== ACCOUNTING REPORTS ==================== -->
                <?php elseif($section=='accounting-reports'): ?>
                    <?php
                    // Get filter parameters
                    $period = $_GET['period'] ?? 'month';
                    $year = $_GET['year'] ?? date('Y');
                    $month = $_GET['month'] ?? date('m');
                    $startDate = $_GET['start_date'] ?? date('Y-m-01');
                    $endDate = $_GET['end_date'] ?? date('Y-m-t');

                    $dateCondition = "DATE(transaction_date) BETWEEN '$startDate' AND '$endDate'";

                    $reportRevenue = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM accounting_transactions WHERE type = 'income' AND status != 'void' AND $dateCondition")->fetchColumn();
                    $reportExpenses = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM accounting_transactions WHERE type = 'expense' AND status != 'void' AND $dateCondition")->fetchColumn();
                    $reportNetProfit = $reportRevenue - $reportExpenses;
                    $reportProfitMargin = $reportRevenue > 0 ? ($reportNetProfit / $reportRevenue) * 100 : 0;

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

                    $reportTransactionCount = $pdo->query("SELECT COUNT(*) FROM accounting_transactions WHERE status != 'void' AND $dateCondition")->fetchColumn();
                    $avgTransactionValue = $reportTransactionCount > 0 ? ($reportRevenue + $reportExpenses) / $reportTransactionCount : 0;

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
                        <div class="filter-bar no-print">
                            <form method="GET" class="row align-items-end">
                                <input type="hidden" name="section" value="accounting-reports">
                                <div class="col-md-2 mb-2">
                                    <label>Quick Period</label>
                                    <select class="form-select form-select-sm" name="period" onchange="this.form.submit()">
                                        <?php foreach($dateRanges as $key => $range): ?>
                                        <option value="<?php echo $key; ?>" <?php echo $period == $key ? 'selected' : ''; ?>>
                                            <?php echo $range['label']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control form-control-sm" name="start_date" value="<?php echo $startDate; ?>">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label>End Date</label>
                                    <input type="date" class="form-control form-control-sm" name="end_date" value="<?php echo $endDate; ?>">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label>Year</label>
                                    <select class="form-select form-select-sm" name="year">
                                        <?php for($y = date('Y'); $y >= date('Y')-5; $y--): ?>
                                        <option value="<?php echo $y; ?>" <?php echo $y == $year ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label>Month</label>
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
                        
                        <!-- ========== KEY METRICS ========== -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="stat-card">
                                    <div class="stat-number" style="color:var(--green);">E<?php echo number_format($reportRevenue, 2); ?></div>
                                    <div class="stat-label">Total Revenue</div>
                                    <small class="text-muted"><?php echo $reportTransactionCount; ?> transactions</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="stat-card">
                                    <div class="stat-number" style="color:#dc3545;">E<?php echo number_format($reportExpenses, 2); ?></div>
                                    <div class="stat-label">Total Expenses</div>
                                    <small class="text-muted"><?php echo count($expenseBreakdown); ?> categories</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="stat-card">
                                    <div class="stat-number" style="color:<?php echo $reportNetProfit >= 0 ? 'var(--green)' : '#dc3545'; ?>;">E<?php echo number_format($reportNetProfit, 2); ?></div>
                                    <div class="stat-label">Net Profit</div>
                                    <small class="text-muted"><?php echo $reportNetProfit >= 0 ? '📈 Profitable' : '📉 Loss'; ?></small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="stat-card">
                                    <div class="stat-number" style="color:var(--gold);"><?php echo number_format($reportProfitMargin, 1); ?>%</div>
                                    <div class="stat-label">Profit Margin</div>
                                    <small class="text-muted"><?php echo $reportProfitMargin > 20 ? '✅ Healthy' : '⚠️ Needs improvement'; ?></small>
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
                                                    <div class="progress-bar" style="width:<?php echo $reportRevenue > 0 ? ($item['total'] / $reportRevenue) * 100 : 0; ?>%;background:<?php echo $item['category_color'] ?? 'var(--green)'; ?>;"></div>
                                                </div>
                                                <small class="text-muted"><?php echo $reportRevenue > 0 ? number_format(($item['total'] / $reportRevenue) * 100, 1) : 0; ?>% of total</small>
                                            </div>
                                            <?php endforeach; ?>
                                            <div class="mt-3 pt-2 border-top">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Total Income</strong>
                                                    <strong style="color:var(--green);">E<?php echo number_format($reportRevenue, 2); ?></strong>
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
                                                    <div class="progress-bar" style="width:<?php echo $reportExpenses > 0 ? ($item['total'] / $reportExpenses) * 100 : 0; ?>%;background:#dc3545;"></div>
                                                </div>
                                                <small class="text-muted"><?php echo $reportExpenses > 0 ? number_format(($item['total'] / $reportExpenses) * 100, 1) : 0; ?>% of total</small>
                                            </div>
                                            <?php endforeach; ?>
                                            <div class="mt-3 pt-2 border-top">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Total Expenses</strong>
                                                    <strong style="color:#dc3545;">E<?php echo number_format($reportExpenses, 2); ?></strong>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ========== MANAGEMENT INSIGHTS ========== -->
                        <div class="card mt-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-lightbulb me-2" style="color:var(--gold);"></i>Management Insights</h5>
                            </div>
                            <div class="card-body">
                                <div style="background: #f8f9fa; border-left: 4px solid var(--gold); padding: 15px; border-radius: 8px; margin: 10px 0;">
                                    <strong>📊 Performance Summary</strong>
                                    <ul class="list-unstyled mt-2">
                                        <li>• <strong>Revenue:</strong> E<?php echo number_format($reportRevenue, 2); ?></li>
                                        <li>• <strong>Expenses:</strong> E<?php echo number_format($reportExpenses, 2); ?></li>
                                        <li>• <strong>Net Profit:</strong> E<?php echo number_format($reportNetProfit, 2); ?></li>
                                        <li>• <strong>Profit Margin:</strong> <?php echo number_format($reportProfitMargin, 1); ?>%</li>
                                        <li>• <strong>Average Transaction:</strong> E<?php echo number_format($avgTransactionValue, 2); ?></li>
                                        <li>• <strong>Transaction Count:</strong> <?php echo $reportTransactionCount; ?></li>
                                    </ul>
                                </div>
                                
                                <?php if($reportProfitMargin > 20): ?>
                                <div style="background: #f8f9fa; border-left: 4px solid var(--green); padding: 15px; border-radius: 8px; margin: 10px 0;">
                                    <strong>✅ Positive Indicators</strong>
                                    <p class="mb-0 small">Your profit margin is healthy at <?php echo number_format($reportProfitMargin, 1); ?>%. Consider reinvesting in growth opportunities.</p>
                                </div>
                                <?php elseif($reportProfitMargin > 10): ?>
                                <div style="background: #f8f9fa; border-left: 4px solid var(--gold); padding: 15px; border-radius: 8px; margin: 10px 0;">
                                    <strong>⚠️ Moderate Performance</strong>
                                    <p class="mb-0 small">Profit margin is <?php echo number_format($reportProfitMargin, 1); ?>. Look for ways to reduce expenses or increase revenue.</p>
                                </div>
                                <?php else: ?>
                                <div style="background: #f8f9fa; border-left: 4px solid #dc3545; padding: 15px; border-radius: 8px; margin: 10px 0;">
                                    <strong>🔴 Needs Attention</strong>
                                    <p class="mb-0 small">Profit margin is low at <?php echo number_format($reportProfitMargin, 1); ?>. Review expenses and pricing strategy.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ==================== POP VIEW MODAL ==================== -->
    <div class="modal fade" id="popModal" tabindex="-1">
        <div class="modal-dialog modal-lg pop-modal-content">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--wine); color: white;">
                    <h5 class="modal-title"><i class="fas fa-file-image me-2"></i>Payment Proof (POP)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body">
                    <div class="pop-info">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>👤 Customer:</strong> <span id="popCustomerName">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-0"><strong>📋 Order #:</strong> <span id="popOrderNumber">-</span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="popImageContainer" style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; min-height: 200px; display: flex; align-items: center; justify-content: center;">
                        <img id="popImage" src="" alt="POP Image" style="max-width:100%; max-height:70vh; border-radius:8px; display:none;">
                        <div id="popLoading" style="display: block;">
                            <i class="fas fa-spinner fa-spin fa-3x" style="color: var(--wine);"></i>
                            <p class="mt-2 text-muted">Loading POP...</p>
                        </div>
                    </div>
                    
                    <div id="popNoImage" style="display:none; padding: 40px; text-align: center;"></div>
                    
                    <div id="popFileInfo" style="display:none; margin-top: 10px; padding: 10px; background: #e8f5e9; border-radius: 8px; text-align: center;"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="popDownloadLink" href="#" target="_blank" class="btn btn-wine" download>
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== ACCOUNTING MODALS ==================== -->
    <!-- Transaction Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalTitle"><i class="fas fa-plus-circle me-2"></i>Record Transaction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="transactionForm" onsubmit="saveTransaction(event)">
                        <input type="hidden" name="type" id="transactionType">
                        <input type="hidden" name="id" id="transactionId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">What happened? *</label>
                                <select class="form-select" id="transactionScenario" required onchange="handleScenarioChange()">
                                    <option value="">Select what you want to record...</option>
                                    <optgroup label="💵 INCOME">
                                        <option value="invoice_paid">Customer paid an invoice</option>
                                        <option value="cash_sale">Cash sale received</option>
                                        <option value="subscription_paid">Subscription payment received</option>
                                        <option value="gift_sale">Corporate gift sold</option>
                                        <option value="other_income">Other income received</option>
                                    </optgroup>
                                    <optgroup label="💸 EXPENSES">
                                        <option value="supplier_invoice">Supplier invoice to pay</option>
                                        <option value="staff_payment">Staff salary/wages</option>
                                        <option value="operating_expense">Operating expense (rent, utilities)</option>
                                        <option value="inventory_purchase">Inventory/Wine purchase</option>
                                        <option value="delivery_cost">Delivery/Shipping cost</option>
                                        <option value="other_expense">Other expense</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Amount *</label>
                                <div class="input-group">
                                    <span class="input-group-text">E</span>
                                    <input type="number" class="form-control" id="transactionAmount" step="0.01" required placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Payment Method</label>
                                <select class="form-select" id="transactionPaymentMethod">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date</label>
                                <input type="date" class="form-control" id="transactionDate" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description / Reference</label>
                            <input type="text" class="form-control" id="transactionDescription" placeholder="e.g., Invoice #123, Payment from John Doe, etc.">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Reference Number (Optional)</label>
                            <input type="text" class="form-control" id="transactionReference" placeholder="Invoice #, Receipt #, etc.">
                        </div>
                        
                        <div id="dynamicFields"></div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-wine w-100"><i class="fas fa-save me-2"></i>Save Transaction</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Transaction Modal -->
    <div class="modal fade" id="viewTransactionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-invoice me-2"></i>Transaction Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewTransactionBody"></div>
            </div>
        </div>
    </div>

    <!-- ==================== OTHER MODALS ==================== -->
    <div class="modal fade" id="itemModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="modalTitle">Add Item</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="modalBody"></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-wine" onclick="saveItem()">Save</button></div></div></div></div>

    <div class="modal fade" id="subscriptionModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="subscriptionModalTitle">Subscription</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" id="subscriptionId"><div class="row"><div class="col-md-6 mb-3"><label>Tier Name</label><input type="text" id="subTierName" class="form-control"></div><div class="col-md-6 mb-3"><label>Display Name *</label><input type="text" id="subDisplayName" class="form-control"></div><div class="col-md-6 mb-3"><label>Tagline</label><input type="text" id="subTagline" class="form-control"></div><div class="col-md-6 mb-3"><label>Price (E) *</label><input type="number" id="subPrice" class="form-control"></div><div class="col-md-6 mb-3"><label>Wines/Month *</label><input type="number" id="subWinesPerMonth" class="form-control"></div><div class="col-md-6 mb-3"><label>Savings %</label><input type="number" id="subSavingsPercent" class="form-control"></div><div class="col-md-6 mb-3"><label>Display Order</label><input type="number" id="subDisplayOrder" class="form-control"></div><div class="col-md-6 mb-3"><div class="form-check"><input type="checkbox" id="subIsPopular" class="form-check-input"><label>Most Popular</label></div></div>
                    <div class="col-md-6 mb-3"><label>Expiry Days (Duration)</label><input type="number" id="subExpiryDays" class="form-control" value="30"><small class="text-muted">Days before subscription expires</small></div>
                    <div class="col-md-6 mb-3"><div class="form-check mt-4"><input type="checkbox" id="subIsActive" class="form-check-input" checked><label class="form-check-label">Active (Available for purchase)</label></div></div>
                    <div class="col-md-12 mb-3"><label>Description</label><input type="text" id="subDescription" class="form-control"></div>
                    <div class="col-md-12 mb-3"><label>Features</label><textarea id="subFeatures" class="form-control" rows="3"></textarea></div>
                    <div class="col-md-12 mb-3"><label>Packaging</label><textarea id="subPackaging" class="form-control" rows="2"></textarea></div></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-wine" onclick="saveSubscription()">Save</button></div></div></div></div>

    <div class="modal fade" id="addStaffModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Add Staff</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="mb-3"><label>Full Name</label><input type="text" id="newStaffName" class="form-control"></div><div class="mb-3"><label>Email</label><input type="email" id="newStaffEmail" class="form-control"></div><div class="mb-3"><label>Password</label><input type="password" id="newStaffPassword" class="form-control"></div><div class="mb-3"><label>Confirm</label><input type="password" id="newStaffConfirm" class="form-control"></div><div class="mb-3"><label>Role</label><select id="newStaffRole" class="form-select"><option value="staff">Staff</option><option value="manager">Manager</option><?php if($userRole=='admin'){echo '<option value="admin">Admin</option>';} ?></select></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-wine" onclick="confirmAddStaff()">Add</button></div></div></div></div>

    <div class="modal fade" id="editStaffModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Edit Staff</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" id="editStaffId"><div class="mb-3"><label>Full Name</label><input type="text" id="editStaffName" class="form-control"></div><div class="mb-3"><label>Email</label><input type="email" id="editStaffEmail" class="form-control"></div><div class="mb-3"><label>Role</label><select id="editStaffRole" class="form-select"><option value="staff">Staff</option><option value="manager">Manager</option><?php if($userRole=='admin'){echo '<option value="admin">Admin</option>';} ?></select></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-wine" onclick="confirmEditStaff()">Save</button></div></div></div></div>

    <div class="modal fade" id="resetPasswordModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Reset Password</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" id="resetStaffId"><div class="mb-3"><label>Staff Email</label><input type="text" id="resetStaffEmail" class="form-control" readonly></div><div class="mb-3"><label>New Password</label><input type="password" id="newPassword" class="form-control"></div><div class="mb-3"><label>Confirm</label><input type="password" id="confirmPassword" class="form-control"></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-wine" onclick="confirmResetPassword()">Reset</button></div></div></div></div>

    <script>
    let currentEditId = null;
    let currentPairingId = null;
    let currentSubId = null;
    let currentEditStaffId = null;
    let currentGiftId = null;
    let currentBasketId = null;
    
    // ==================== ORDER FUNCTIONS ====================
    function cancelOrder(id){
        if(confirm('Cancel this order? This cannot be undone.')){
            window.location.href='?section=orders&action=cancel_order&id='+id;
        }
    }
    
    function processOrder(id){
        if(confirm('Process this order?')){
            window.location.href='?section=orders&action=process_order&id='+id;
        }
    }
    
    function completeOrder(id){
        if(confirm('Complete this order?')){
            window.location.href='?section=orders&action=complete_order&id='+id;
        }
    }
    
    function updateStock(id,stock){
        let ns=prompt('New stock quantity:',stock);
        if(ns!==null&&!isNaN(ns)){
            window.location.href='?section=wines&action=update_stock&id='+id+'&stock='+ns;
        }
    }
    
    function viewInvoice(id){
        window.open('../backend/generate-invoice.php?id='+id,'_blank','width=900,height=700');
    }
    
    // ==================== POP VIEW FUNCTION ====================
function viewPOP(popPath, customerName, orderNumber) {
    console.log('Viewing POP:', popPath);
    
    if (!popPath || popPath === '/uploads/pop/') {
        alert('No POP file uploaded for this order.');
        return;
    }
    
    document.getElementById('popCustomerName').innerText = customerName || 'Unknown';
    document.getElementById('popOrderNumber').innerText = orderNumber || 'N/A';
    
    document.getElementById('popImage').style.display = 'none';
    document.getElementById('popLoading').style.display = 'block';
    document.getElementById('popNoImage').style.display = 'none';
    document.getElementById('popFileInfo').style.display = 'none';
    
    const img = document.getElementById('popImage');
    
    // Try multiple possible paths
    const possiblePaths = [
        popPath,
        '/' + popPath.replace(/^\/uploads\/pop\//, ''),
        '/uploads/pop/' + popPath.split('/').pop(),
        popPath.split('/').pop()
    ];
    
    console.log('Trying paths:', possiblePaths);
    
    let pathIndex = 0;
    
    function tryNextPath() {
        if (pathIndex >= possiblePaths.length) {
            // All paths failed
            document.getElementById('popLoading').style.display = 'none';
            document.getElementById('popNoImage').style.display = 'block';
            document.getElementById('popNoImage').innerHTML = `
                <i class="fas fa-exclamation-circle fa-4x" style="color: #dc3545;"></i>
                <p class="mt-3 text-danger"><strong>Cannot load POP file</strong></p>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; max-width: 500px; margin: 10px auto; text-align: left;">
                    <p class="mb-1"><strong>File:</strong> <code>${popPath.split('/').pop()}</code></p>
                    <p class="mb-0"><strong>Original Path:</strong> <code>${popPath}</code></p>
                    <p class="mb-0 mt-1"><strong>Tried:</strong></p>
                    <ul class="mb-0">
                        ${possiblePaths.map(p => `<li><code>${p}</code></li>`).join('')}
                    </ul>
                </div>
                <button class="btn btn-outline-wine btn-sm mt-2" onclick="window.open('${popPath}', '_blank')">
                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                </button>
            `;
            document.getElementById('popFileInfo').style.display = 'none';
            return;
        }
        
        const currentPath = possiblePaths[pathIndex];
        console.log('Trying path:', currentPath);
        
        img.src = currentPath;
        img.onload = function() {
            document.getElementById('popImage').style.display = 'block';
            document.getElementById('popLoading').style.display = 'none';
            document.getElementById('popNoImage').style.display = 'none';
            document.getElementById('popFileInfo').style.display = 'block';
            document.getElementById('popFileInfo').innerHTML = `
                <i class="fas fa-check-circle text-success me-2"></i>
                <span class="text-success">✅ File loaded from: ${currentPath}</span>
            `;
            document.getElementById('popDownloadLink').href = currentPath;
            document.getElementById('popDownloadLink').download = popPath.split('/').pop();
        };
        img.onerror = function() {
            pathIndex++;
            tryNextPath();
        };
    }
    
    tryNextPath();
    
    document.getElementById('popDownloadLink').href = popPath;
    document.getElementById('popDownloadLink').download = popPath.split('/').pop();
    document.getElementById('popDownloadLink').target = '_blank';
    
    new bootstrap.Modal(document.getElementById('popModal')).show();
}
    
    // ==================== WINE FUNCTIONS ====================
    function showAddWineModal(){
        document.getElementById('modalTitle').innerText='Add New Wine';
        document.getElementById('modalBody').innerHTML=`<div class="row">
            <div class="col-md-6 mb-3"><label>Name *</label><input type="text" id="wineName" class="form-control"></div>
            <div class="col-md-6 mb-3"><label>Variety *</label><input type="text" id="wineVariety" class="form-control"></div>
            <div class="col-md-6 mb-3"><label>Origin *</label><input type="text" id="wineOrigin" class="form-control"></div>
            <div class="col-md-6 mb-3"><label>Price (E) *</label><input type="number" id="winePrice" class="form-control"></div>
            <div class="col-md-6 mb-3"><label>Stock</label><input type="number" id="wineStock" class="form-control" value="100"></div>
            <div class="col-md-6 mb-3"><label>Vintage</label><input type="number" id="wineVintage" class="form-control"></div>
            <div class="col-md-6 mb-3"><label>Structure</label><input type="text" id="wineStructure" class="form-control"></div>
            <div class="col-md-6 mb-3"><label>Taste</label><input type="text" id="wineTaste" class="form-control"></div>
            <div class="col-md-6 mb-3"><label>Strength</label><input type="text" id="wineStrength" class="form-control"></div>
            <div class="col-md-12 mb-3"><label>Image URL</label><input type="text" id="wineImage" class="form-control" placeholder="/uploads/wines/wine-name.jpg"></div>
            <div class="col-md-12 mb-3"><label>Description</label><textarea id="wineDescription" class="form-control" rows="2"></textarea></div>
            <div class="col-md-6 mb-3"><div class="form-check"><input type="checkbox" id="wineFeatured" class="form-check-input"><label>Featured</label></div></div>
        </div>`;
        currentEditId=null;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
    }
    
    function editWine(id){
        fetch('../backend/get-wine.php?id='+id).then(r=>r.json()).then(w=>{
            document.getElementById('modalTitle').innerText='Edit Wine';
            document.getElementById('modalBody').innerHTML=`<div class="row">
                <div class="col-md-6 mb-3"><label>Name</label><input type="text" id="wineName" class="form-control" value="${escapeHtml(w.name)}"></div>
                <div class="col-md-6 mb-3"><label>Variety</label><input type="text" id="wineVariety" class="form-control" value="${escapeHtml(w.variety)}"></div>
                <div class="col-md-6 mb-3"><label>Origin</label><input type="text" id="wineOrigin" class="form-control" value="${escapeHtml(w.origin)}"></div>
                <div class="col-md-6 mb-3"><label>Price</label><input type="number" id="winePrice" class="form-control" value="${w.price}"></div>
                <div class="col-md-6 mb-3"><label>Stock</label><input type="number" id="wineStock" class="form-control" value="${w.stock_quantity}"></div>
                <div class="col-md-6 mb-3"><label>Vintage</label><input type="number" id="wineVintage" class="form-control" value="${w.vintage||''}"></div>
                <div class="col-md-6 mb-3"><label>Structure</label><input type="text" id="wineStructure" class="form-control" value="${escapeHtml(w.structure||'')}"></div>
                <div class="col-md-6 mb-3"><label>Taste</label><input type="text" id="wineTaste" class="form-control" value="${escapeHtml(w.taste||'')}"></div>
                <div class="col-md-6 mb-3"><label>Strength</label><input type="text" id="wineStrength" class="form-control" value="${escapeHtml(w.strength||'')}"></div>
                <div class="col-md-12 mb-3"><label>Image URL</label><input type="text" id="wineImage" class="form-control" value="${escapeHtml(w.image_url||'')}"></div>
                <div class="col-md-12 mb-3"><label>Description</label><textarea id="wineDescription" class="form-control" rows="2">${escapeHtml(w.description||'')}</textarea></div>
                <div class="col-md-6 mb-3"><div class="form-check"><input type="checkbox" id="wineFeatured" class="form-check-input" ${w.featured?'checked':''}><label>Featured</label></div></div>
            </div>`;
            currentEditId=id;
            new bootstrap.Modal(document.getElementById('itemModal')).show();
        }).catch(e=>alert('Error loading wine'));
    }
    
    function saveWine(){
        let data={id:currentEditId||0,name:document.getElementById('wineName').value,variety:document.getElementById('wineVariety').value,origin:document.getElementById('wineOrigin').value,price:parseFloat(document.getElementById('winePrice').value),stock_quantity:parseInt(document.getElementById('wineStock').value)||0,vintage:parseInt(document.getElementById('wineVintage').value)||0,structure:document.getElementById('wineStructure').value,taste:document.getElementById('wineTaste').value,strength:document.getElementById('wineStrength').value,description:document.getElementById('wineDescription').value,featured:document.getElementById('wineFeatured').checked?1:0,image_url:document.getElementById('wineImage').value};
        if(!data.name||!data.variety||!data.origin||!data.price){alert('Please fill required fields');return;}
        let url=currentEditId?'../backend/update-wine.php':'../backend/add-wine.php';
        fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)}).then(r=>r.json()).then(r=>{if(r.success){alert(currentEditId?'Wine updated':'Wine added');bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();location.reload();}else{alert('Error: '+r.error);}}).catch(e=>alert('Error saving'));
    }
    
    function deleteWine(id){if(confirm('Delete this wine?')){fetch('../backend/delete-wine.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:id})}).then(r=>r.json()).then(r=>{if(r.success){alert('Deleted');location.reload();}else{alert('Error');}});}}
    
    // ==================== PAIRING FUNCTIONS ====================
    function showAddPairingModal(){
        document.getElementById('modalTitle').innerText='Add Pairing';
        document.getElementById('modalBody').innerHTML=`
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name *</label>
                    <input type="text" id="pairingName" class="form-control" placeholder="e.g., Artisan Cheese Board">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Price (E) *</label>
                    <input type="number" id="pairingPrice" class="form-control" step="0.01" placeholder="0.00">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description *</label>
                    <textarea id="pairingDescription" class="form-control" rows="3" placeholder="Describe the pairing..."></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Compatible Wines</label>
                    <input type="text" id="pairingCompatibleWines" class="form-control" placeholder="e.g., Pinot Noir, Chardonnay">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Image URL</label>
                    <input type="text" id="pairingImage" class="form-control" placeholder="/uploads/pairings/cheese-board.jpg">
                    <small class="text-muted">Upload images to /uploads/pairings/ folder</small>
                    <div id="pairingImagePreview" style="display:none; margin-top:10px;">
                        <img id="pairingPreviewImg" src="" style="max-width:150px; max-height:100px; border-radius:8px; border:1px solid #ddd; padding:5px;">
                    </div>
                </div>
            </div>
        `;
        currentPairingId=null;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
        
        // Add image preview listener
        document.getElementById('pairingImage').addEventListener('input', function() {
            const preview = document.getElementById('pairingImagePreview');
            const img = document.getElementById('pairingPreviewImg');
            if (this.value) {
                img.src = this.value;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });
    }
    
    function editPairing(id) {
        console.log('Editing pairing ID:', id);
        
        // Show loading in the modal
        document.getElementById('modalTitle').innerText = 'Loading...';
        document.getElementById('modalBody').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-3x" style="color:var(--wine);"></i>
                <p class="mt-3 text-muted">Loading pairing data...</p>
            </div>
        `;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
        
        fetch('../backend/get-pairing.php?id=' + id)
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ' - ' + response.statusText);
                }
                return response.json();
            })
            .then(pairing => {
                console.log('Pairing data:', pairing);
                
                if (pairing.error) {
                    alert('Error: ' + pairing.error);
                    bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                    return;
                }
                
                document.getElementById('modalTitle').innerText = 'Edit Pairing';
                document.getElementById('modalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" id="pairingName" class="form-control" value="${escapeHtml(pairing.name)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price (E) *</label>
                            <input type="number" id="pairingPrice" class="form-control" step="0.01" value="${pairing.price}" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description *</label>
                            <textarea id="pairingDescription" class="form-control" rows="3" required>${escapeHtml(pairing.description)}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Compatible Wines</label>
                            <input type="text" id="pairingCompatibleWines" class="form-control" value="${escapeHtml(pairing.compatible_wines || '')}" placeholder="e.g., Pinot Noir, Chardonnay">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Image URL</label>
                            <input type="text" id="pairingImage" class="form-control" value="${escapeHtml(pairing.image_url || '')}" placeholder="/uploads/pairings/cheese-board.jpg">
                            <small class="text-muted">Upload images to /uploads/pairings/ folder</small>
                            ${pairing.image_url ? `<div class="mt-2"><img src="${pairing.image_url}" style="max-width:150px; max-height:100px; border-radius:8px; border:1px solid #ddd; padding:5px;"></div>` : '<div class="mt-2 text-muted">No image set</div>'}
                        </div>
                    </div>
                `;
                currentPairingId = id;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error loading pairing: ' + error.message + '\n\nMake sure get-pairing.php exists in the backend folder.');
                bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
            });
    }
    
    function savePairing(){
        let data={
            id:currentPairingId||0,
            name:document.getElementById('pairingName').value,
            description:document.getElementById('pairingDescription').value,
            price:parseFloat(document.getElementById('pairingPrice').value),
            compatible_wines:document.getElementById('pairingCompatibleWines').value,
            image_url:document.getElementById('pairingImage').value
        };
        
        if(!data.name||!data.price||!data.description){
            alert('Required fields missing');
            return;
        }
        
        let url=currentPairingId?'../backend/update-pairing.php':'../backend/add-pairing.php';
        
        fetch(url,{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify(data)
        })
        .then(r=>r.json())
        .then(r=>{
            if(r.success){
                alert(currentPairingId?'Pairing Updated!':'Pairing Added!');
                bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                location.reload();
            }else{
                alert('Error: ' + (r.error || 'Unknown error'));
            }
        })
        .catch(e=>alert('Error saving: ' + e));
    }
    
    function deletePairing(id){if(confirm('Delete this pairing?')){fetch('../backend/delete-pairing.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:id})}).then(r=>r.json()).then(r=>{if(r.success){alert('Deleted');location.reload();}});}}
    
    // ==================== SUBSCRIPTION FUNCTIONS ====================
    function openSubscriptionModal(){
        document.getElementById('subscriptionModalTitle').innerText='Add Subscription';
        document.getElementById('subscriptionId').value='';
        document.getElementById('subTierName').value='';
        document.getElementById('subDisplayName').value='';
        document.getElementById('subTagline').value='';
        document.getElementById('subPrice').value='';
        document.getElementById('subWinesPerMonth').value='';
        document.getElementById('subSavingsPercent').value='0';
        document.getElementById('subDisplayOrder').value='';
        document.getElementById('subIsPopular').checked=false;
        document.getElementById('subDescription').value='';
        document.getElementById('subFeatures').value='';
        document.getElementById('subExpiryDays').value='30';
        document.getElementById('subIsActive').checked=true;
        currentSubId=null;
        new bootstrap.Modal(document.getElementById('subscriptionModal')).show();
    }
    
    function editSubscription(id){
        fetch('../backend/get-subscription.php?id='+id).then(r=>r.json()).then(s=>{
            document.getElementById('subscriptionModalTitle').innerText='Edit Subscription';
            document.getElementById('subscriptionId').value=s.id;
            document.getElementById('subTierName').value=s.tier_name||'';
            document.getElementById('subDisplayName').value=s.display_name||'';
            document.getElementById('subTagline').value=s.tagline||'';
            document.getElementById('subPrice').value=s.price;
            document.getElementById('subWinesPerMonth').value=s.wines_per_month;
            document.getElementById('subSavingsPercent').value=s.savings_percent||0;
            document.getElementById('subDisplayOrder').value=s.display_order||'';
            document.getElementById('subIsPopular').checked=s.is_popular==1;
            document.getElementById('subDescription').value=s.description||'';
            document.getElementById('subFeatures').value=s.features||'';
            document.getElementById('subExpiryDays').value=s.expiry_days||30;
            document.getElementById('subIsActive').checked=s.is_active==1;
            currentSubId=id;
            new bootstrap.Modal(document.getElementById('subscriptionModal')).show();
        }).catch(e=>alert('Error loading subscription'));
    }
    
    function saveSubscription(){
        let data={
            id:document.getElementById('subscriptionId').value||0,
            tier_name:document.getElementById('subTierName').value,
            display_name:document.getElementById('subDisplayName').value,
            tagline:document.getElementById('subTagline').value,
            price:parseFloat(document.getElementById('subPrice').value),
            wines_per_month:parseInt(document.getElementById('subWinesPerMonth').value),
            savings_percent:parseInt(document.getElementById('subSavingsPercent').value)||0,
            display_order:parseInt(document.getElementById('subDisplayOrder').value)||0,
            is_popular:document.getElementById('subIsPopular').checked,
            description:document.getElementById('subDescription').value,
            features:document.getElementById('subFeatures').value,
            expiry_days:parseInt(document.getElementById('subExpiryDays').value)||30,
            is_active:document.getElementById('subIsActive').checked?1:0
        };
        if(!data.display_name||!data.price||!data.wines_per_month){alert('Required fields missing');return;}
        let url=data.id?'../backend/update-subscription.php':'../backend/add-subscription.php';
        fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)}).then(r=>r.json()).then(r=>{if(r.success){alert(data.id?'Updated':'Added');bootstrap.Modal.getInstance(document.getElementById('subscriptionModal')).hide();location.reload();}else{alert('Error');}}).catch(e=>alert('Error saving'));
    }
    
    function deleteSubscription(id){if(confirm('Delete this plan?')){fetch('../backend/delete-subscription.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:id})}).then(r=>r.json()).then(r=>{if(r.success){alert('Deleted');location.reload();}});}}
    
    // ==================== SUBSCRIPTION REQUEST FUNCTIONS ====================
    function approveSubscription(id) {
        if (!confirm('Approve this subscription request?\n\nThe user will receive a confirmation email.')) return;
        
        fetch('../backend/approve-subscription.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('✅ Subscription approved! User will be notified via email.');
                location.reload();
            } else {
                alert('❌ Error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(e => alert('Error processing request'));
    }

    function rejectSubscription(id) {
        if (!confirm('Reject this subscription request?\n\nThis action cannot be undone.')) return;
        
        let reason = prompt('Please provide a reason for rejection (optional):');
        
        fetch('../backend/reject-subscription.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, reason: reason || 'No reason provided' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('❌ Subscription rejected.');
                location.reload();
            } else {
                alert('❌ Error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(e => alert('Error processing request'));
    }
    
    // ==================== SUBSCRIBER FUNCTIONS ====================
    function toggleSubscribers(planId, planName) {
        const el = document.getElementById('subscribers-' + planId);
        if (!el) return;
        
        if (el.style.display === 'none' || el.style.display === '') {
            el.style.display = 'block';
            if (!el.dataset.loaded) {
                el.innerHTML = '<div class="text-center text-muted small"><i class="fas fa-spinner fa-spin"></i> Loading subscribers...</div>';
                
                fetch('../backend/get-plan-subscribers.php?plan_id=' + planId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.subscribers && data.subscribers.length > 0) {
                            let html = '';
                            data.subscribers.forEach(sub => {
                                html += `
                                    <div class="subscriber-item">
                                        <strong>${escapeHtml(sub.full_name)}</strong><br>
                                        <small>${escapeHtml(sub.email)}</small><br>
                                        <small class="text-muted">Joined: ${formatDate(sub.created_at)}</small>
                                        <br>
                                        <small class="text-muted">Expires: ${formatDate(sub.expiry_date)}</small>
                                    </div>
                                `;
                            });
                            el.innerHTML = html;
                            el.dataset.loaded = 'true';
                        } else {
                            el.innerHTML = '<div class="text-center text-muted small">No subscribers for this plan yet</div>';
                            el.dataset.loaded = 'true';
                        }
                    })
                    .catch(() => {
                        el.innerHTML = '<div class="text-center text-danger small">Error loading subscribers</div>';
                    });
            }
        } else {
            el.style.display = 'none';
        }
    }
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-ZA', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
    
    // ==================== STAFF FUNCTIONS ====================
    function showAddStaffModal(){document.getElementById('newStaffName').value='';document.getElementById('newStaffEmail').value='';document.getElementById('newStaffPassword').value='';document.getElementById('newStaffConfirm').value='';document.getElementById('newStaffRole').value='staff';new bootstrap.Modal(document.getElementById('addStaffModal')).show();}
    
    function editStaff(id,name,email,role){
        currentEditStaffId=id;
        document.getElementById('editStaffName').value=name;
        document.getElementById('editStaffEmail').value=email;
        document.getElementById('editStaffRole').value=role;
        new bootstrap.Modal(document.getElementById('editStaffModal')).show();
    }
    
    function confirmEditStaff(){
        let name=document.getElementById('editStaffName').value;
        let email=document.getElementById('editStaffEmail').value;
        let role=document.getElementById('editStaffRole').value;
        if(!name||!email){alert('Fill all fields');return;}
        fetch('../backend/update-staff.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:currentEditStaffId,name,email,role})}).then(r=>r.json()).then(d=>{if(d.success){alert('Staff updated');bootstrap.Modal.getInstance(document.getElementById('editStaffModal')).hide();location.reload();}else{alert('Error: '+d.error);}}).catch(e=>alert('Error updating staff'));
    }
    
    function confirmAddStaff(){
        let name=document.getElementById('newStaffName').value;
        let email=document.getElementById('newStaffEmail').value;
        let password=document.getElementById('newStaffPassword').value;
        let confirm=document.getElementById('newStaffConfirm').value;
        let role=document.getElementById('newStaffRole').value;
        if(!name||!email||!password){alert('Fill all fields');return;}
        if(password!==confirm){alert('Passwords do not match');return;}
        if(password.length<6){alert('Password must be at least 6 characters');return;}
        fetch('../backend/add-staff.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name,email,password,role})}).then(r=>r.json()).then(d=>{if(d.success){alert('Staff added');bootstrap.Modal.getInstance(document.getElementById('addStaffModal')).hide();location.reload();}else{alert('Error: '+d.error);}}).catch(e=>alert('Error adding staff'));
    }
    
    function resetStaffPassword(id,email){
        document.getElementById('resetStaffId').value=id;
        document.getElementById('resetStaffEmail').value=email;
        document.getElementById('newPassword').value='';
        document.getElementById('confirmPassword').value='';
        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }
    
    function confirmResetPassword(){
        let id=document.getElementById('resetStaffId').value;
        let password=document.getElementById('newPassword').value;
        let confirm=document.getElementById('confirmPassword').value;
        if(!password){alert('Enter password');return;}
        if(password!==confirm){alert('Passwords do not match');return;}
        if(password.length<6){alert('Password must be at least 6 characters');return;}
        fetch('../backend/reset-staff-password.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id,password})}).then(r=>r.json()).then(d=>{if(d.success){alert('Password reset! New: '+password);bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();}else{alert('Error');}}).catch(e=>alert('Error resetting password'));
    }
    
    function deleteStaff(id){
        if(confirm('Delete this staff member?')){
            fetch('../backend/delete-staff.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:id})}).then(r=>r.json()).then(d=>{if(d.success){alert('Staff deleted');location.reload();}else{alert('Error: '+d.error);}}).catch(e=>alert('Error deleting staff'));
        }
    }
    
    // ==================== CORPORATE GIFTS FUNCTIONS (WITH IMAGES) ====================
    function showAddCorporateGiftModal() {
        document.getElementById('modalTitle').innerText = 'Add Corporate Gift';
        document.getElementById('modalBody').innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name *</label>
                    <input type="text" id="giftName" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Tier *</label>
                    <input type="text" id="giftTier" class="form-control" placeholder="Executive, Boardroom" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Price (E) *</label>
                    <input type="number" id="giftPrice" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Wines Included</label>
                    <input type="number" id="giftWines" class="form-control" value="3">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Image URL</label>
                    <input type="text" id="giftImage" class="form-control" placeholder="/uploads/corporate/gift-name.jpg">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description *</label>
                    <textarea id="giftDescription" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Features</label>
                    <input type="text" id="giftFeatures" class="form-control" placeholder="3 Premium Wines, Chocolates, Card">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Image Preview</label>
                    <div id="giftImagePreview" style="display:none; margin-top:10px;">
                        <img id="giftPreviewImg" src="" style="max-width:200px; max-height:150px; border-radius:10px; border:1px solid #ddd; padding:5px;">
                    </div>
                    <small class="text-muted">Upload images to /uploads/corporate/ folder</small>
                </div>
            </div>
        `;
        window.currentGiftId = null;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
        
        document.getElementById('giftImage').addEventListener('input', function() {
            const preview = document.getElementById('giftImagePreview');
            const img = document.getElementById('giftPreviewImg');
            if (this.value) {
                img.src = this.value;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });
    }
    
    function editCorporateGift(id) {
        console.log('Editing corporate gift ID:', id);
        
        fetch('../backend/get-corporate-gift.php?id=' + id)
            .then(response => response.json())
            .then(gift => {
                console.log('Gift data:', gift);
                
                if (gift.error) {
                    alert('Error: ' + gift.error);
                    return;
                }
                
                document.getElementById('modalTitle').innerText = 'Edit Corporate Gift';
                document.getElementById('modalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" id="giftName" class="form-control" value="${escapeHtml(gift.name)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tier *</label>
                            <input type="text" id="giftTier" class="form-control" value="${escapeHtml(gift.tier)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price (E) *</label>
                            <input type="number" id="giftPrice" class="form-control" step="0.01" value="${gift.price}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Wines Included</label>
                            <input type="number" id="giftWines" class="form-control" value="${gift.wines_included || 3}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Image URL</label>
                            <input type="text" id="giftImage" class="form-control" value="${escapeHtml(gift.image_url || '')}" placeholder="/uploads/corporate/gift-name.jpg">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description *</label>
                            <textarea id="giftDescription" class="form-control" rows="2" required>${escapeHtml(gift.description)}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Features</label>
                            <input type="text" id="giftFeatures" class="form-control" value="${escapeHtml(gift.features)}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Image Preview</label>
                            <div id="giftImagePreview" style="margin-top:10px;">
                                ${gift.image_url ? `<img src="${gift.image_url}" style="max-width:200px; max-height:150px; border-radius:10px; border:1px solid #ddd; padding:5px;">` : '<span class="text-muted">No image</span>'}
                            </div>
                        </div>
                    </div>
                `;
                window.currentGiftId = id;
                new bootstrap.Modal(document.getElementById('itemModal')).show();
                
                document.getElementById('giftImage').addEventListener('input', function() {
                    const preview = document.getElementById('giftImagePreview');
                    if (this.value) {
                        preview.innerHTML = `<img src="${this.value}" style="max-width:200px; max-height:150px; border-radius:10px; border:1px solid #ddd; padding:5px;">`;
                    } else {
                        preview.innerHTML = '<span class="text-muted">No image</span>';
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading gift: ' + error);
            });
    }
    
    function saveCorporateGift() {
        let data = {
            id: window.currentGiftId || 0,
            name: document.getElementById('giftName').value,
            tier: document.getElementById('giftTier').value,
            description: document.getElementById('giftDescription').value,
            features: document.getElementById('giftFeatures').value,
            price: parseFloat(document.getElementById('giftPrice').value),
            wines_included: parseInt(document.getElementById('giftWines').value) || 0,
            image_url: document.getElementById('giftImage').value || ''
        };
        
        if (!data.name || !data.tier || !data.description || !data.price) {
            alert('Please fill required fields');
            return;
        }
        
        let url = window.currentGiftId ? '../backend/update-corporate-gift.php' : '../backend/add-corporate-gift.php';
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(window.currentGiftId ? 'Gift updated!' : 'Gift added!');
                bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => alert('Error saving: ' + error));
    }
    
    function deleteCorporateGift(id) {
        if (confirm('Delete this gift?')) {
            fetch('../backend/delete-corporate-gift.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Deleted!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => alert('Error deleting: ' + error));
        }
    }
    
    // ==================== GIFT BASKETS FUNCTIONS (WITH IMAGES) ====================
    function showAddGiftBasketModal() {
        document.getElementById('modalTitle').innerText = 'Add Gift Basket';
        document.getElementById('modalBody').innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name *</label>
                    <input type="text" id="basketName" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Price (E) *</label>
                    <input type="number" id="basketPrice" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Wines Included</label>
                    <input type="number" id="basketWines" class="form-control" value="2">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Image URL</label>
                    <input type="text" id="basketImage" class="form-control" placeholder="/uploads/baskets/basket-name.jpg">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description *</label>
                    <textarea id="basketDescription" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Features</label>
                    <input type="text" id="basketFeatures" class="form-control" placeholder="2 wines, chocolates, cheese">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Image Preview</label>
                    <div id="basketImagePreview" style="display:none; margin-top:10px;">
                        <img id="basketPreviewImg" src="" style="max-width:200px; max-height:150px; border-radius:10px; border:1px solid #ddd; padding:5px;">
                    </div>
                    <small class="text-muted">Upload images to /uploads/baskets/ folder</small>
                </div>
            </div>
        `;
        window.currentBasketId = null;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
        
        document.getElementById('basketImage').addEventListener('input', function() {
            const preview = document.getElementById('basketImagePreview');
            const img = document.getElementById('basketPreviewImg');
            if (this.value) {
                img.src = this.value;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });
    }
    
    function editGiftBasket(id) {
        fetch('../backend/get-gift-basket.php?id=' + id)
            .then(response => response.json())
            .then(basket => {
                document.getElementById('modalTitle').innerText = 'Edit Gift Basket';
                document.getElementById('modalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" id="basketName" class="form-control" value="${escapeHtml(basket.name)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price (E) *</label>
                            <input type="number" id="basketPrice" class="form-control" step="0.01" value="${basket.price}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Wines Included</label>
                            <input type="number" id="basketWines" class="form-control" value="${basket.wines_included || 2}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Image URL</label>
                            <input type="text" id="basketImage" class="form-control" value="${escapeHtml(basket.image_url || '')}" placeholder="/uploads/baskets/basket-name.jpg">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description *</label>
                            <textarea id="basketDescription" class="form-control" rows="2" required>${escapeHtml(basket.description)}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Features</label>
                            <input type="text" id="basketFeatures" class="form-control" value="${escapeHtml(basket.features || '')}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Image Preview</label>
                            <div id="basketImagePreview" style="margin-top:10px;">
                                ${basket.image_url ? `<img src="${basket.image_url}" style="max-width:200px; max-height:150px; border-radius:10px; border:1px solid #ddd; padding:5px;">` : '<span class="text-muted">No image</span>'}
                            </div>
                        </div>
                    </div>
                `;
                window.currentBasketId = id;
                new bootstrap.Modal(document.getElementById('itemModal')).show();
                
                document.getElementById('basketImage').addEventListener('input', function() {
                    const preview = document.getElementById('basketImagePreview');
                    if (this.value) {
                        preview.innerHTML = `<img src="${this.value}" style="max-width:200px; max-height:150px; border-radius:10px; border:1px solid #ddd; padding:5px;">`;
                    } else {
                        preview.innerHTML = '<span class="text-muted">No image</span>';
                    }
                });
            })
            .catch(error => alert('Error loading basket: ' + error));
    }
    
    function saveGiftBasket() {
        let data = {
            id: window.currentBasketId || 0,
            name: document.getElementById('basketName').value,
            description: document.getElementById('basketDescription').value,
            features: document.getElementById('basketFeatures').value,
            price: parseFloat(document.getElementById('basketPrice').value),
            wines_included: parseInt(document.getElementById('basketWines').value) || 0,
            image_url: document.getElementById('basketImage').value || ''
        };
        
        if (!data.name || !data.description || !data.price) {
            alert('Please fill required fields');
            return;
        }
        
        let url = window.currentBasketId ? '../backend/update-gift-basket.php' : '../backend/add-gift-basket.php';
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(window.currentBasketId ? 'Basket updated!' : 'Basket added!');
                bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => alert('Error saving: ' + error));
    }
    
    function deleteGiftBasket(id) {
        if (confirm('Delete this basket?')) {
            fetch('../backend/delete-gift-basket.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Deleted!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => alert('Error deleting: ' + error));
        }
    }
    
    // ==================== ACCOUNTING FUNCTIONS ====================
    const API_URL = '../backend/accounting/';
    
    function openTransactionModal(type) {
        document.getElementById('transactionType').value = type;
        document.getElementById('transactionId').value = '';
        document.getElementById('transactionScenario').value = '';
        document.getElementById('transactionAmount').value = '';
        document.getElementById('transactionDescription').value = '';
        document.getElementById('transactionReference').value = '';
        document.getElementById('dynamicFields').innerHTML = '';
        
        const title = type === 'income' ? 'Record Income' : 'Record Expense';
        document.getElementById('transactionModalTitle').innerHTML = `<i class="fas fa-${type === 'income' ? 'plus-circle' : 'minus-circle'} me-2"></i>${title}`;
        
        new bootstrap.Modal(document.getElementById('transactionModal')).show();
    }
    
    function handleScenarioChange() {
        const scenario = document.getElementById('transactionScenario').value;
        const dynamicFields = document.getElementById('dynamicFields');
        
        let html = '';
        let description = '';
        
        switch(scenario) {
            case 'invoice_paid':
                description = 'Customer paid invoice';
                html = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customerName" placeholder="Customer name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Invoice Number</label>
                            <input type="text" class="form-control" id="invoiceNumber" placeholder="INV-001">
                        </div>
                    </div>
                `;
                break;
            case 'cash_sale':
                description = 'Cash sale';
                html = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customerName" placeholder="Customer name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sale Reference</label>
                            <input type="text" class="form-control" id="saleReference" placeholder="Sale #">
                        </div>
                    </div>
                `;
                break;
            case 'subscription_paid':
                description = 'Subscription payment received';
                html = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subscriber Name</label>
                            <input type="text" class="form-control" id="subscriberName" placeholder="Subscriber name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Plan</label>
                            <select class="form-select" id="subscriptionPlan">
                                <option value="Basic">Basic</option>
                                <option value="Premium">Premium</option>
                                <option value="VIP">VIP</option>
                            </select>
                        </div>
                    </div>
                `;
                break;
            case 'gift_sale':
                description = 'Corporate gift sold';
                html = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gift Type</label>
                            <input type="text" class="form-control" id="giftType" placeholder="Executive, Boardroom, etc.">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control" id="clientName" placeholder="Client/company name">
                        </div>
                    </div>
                `;
                break;
            case 'supplier_invoice':
                description = 'Supplier invoice to pay';
                html = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" id="supplierName" placeholder="Supplier name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Supplier Invoice #</label>
                            <input type="text" class="form-control" id="supplierInvoice" placeholder="Supplier invoice number">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expense Category</label>
                        <select class="form-select" id="expenseCategory">
                            <option value="Wine Inventory">Wine Inventory</option>
                            <option value="Packaging">Packaging</option>
                            <option value="Office Supplies">Office Supplies</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Rent">Rent</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                `;
                break;
            case 'staff_payment':
                description = 'Staff salary payment';
                html = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Staff Name</label>
                            <input type="text" class="form-control" id="staffName" placeholder="Staff name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Period</label>
                            <input type="text" class="form-control" id="payPeriod" placeholder="e.g., July 2024" value="<?php echo date('F Y'); ?>">
                        </div>
                    </div>
                `;
                break;
            case 'operating_expense':
                description = 'Operating expense';
                html = `
                    <div class="mb-3">
                        <label class="form-label">Expense Category</label>
                        <select class="form-select" id="operatingCategory">
                            <option value="Rent">Rent</option>
                            <option value="Utilities">Utilities (Water, Electricity)</option>
                            <option value="Internet">Internet/Phone</option>
                            <option value="Cleaning">Cleaning</option>
                            <option value="Maintenance">Maintenance/Repairs</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Marketing">Marketing/Advertising</option>
                            <option value="Office Supplies">Office Supplies</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vendor/Provider</label>
                        <input type="text" class="form-control" id="vendorName" placeholder="Vendor name">
                    </div>
                `;
                break;
            case 'inventory_purchase':
                description = 'Inventory/Wine purchase';
                html = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control" id="inventorySupplier" placeholder="Supplier name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Wine Type</label>
                            <input type="text" class="form-control" id="wineType" placeholder="e.g., Pinot Noir">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" placeholder="Number of bottles">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unit Price</label>
                            <div class="input-group">
                                <span class="input-group-text">E</span>
                                <input type="number" class="form-control" id="unitPrice" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                `;
                break;
            case 'delivery_cost':
                description = 'Delivery/Shipping cost';
                html = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Delivery Provider</label>
                            <input type="text" class="form-control" id="deliveryProvider" placeholder="Provider name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Order/Tracking #</label>
                            <input type="text" class="form-control" id="trackingNumber" placeholder="Tracking number">
                        </div>
                    </div>
                `;
                break;
            case 'other_income':
                description = 'Other income';
                html = `
                    <div class="mb-3">
                        <label class="form-label">Income Category</label>
                        <select class="form-select" id="otherIncomeCategory">
                            <option value="Interest Earned">Interest Earned</option>
                            <option value="Refund">Refund Received</option>
                            <option value="Commission">Commission</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Source</label>
                        <input type="text" class="form-control" id="incomeSource" placeholder="Source of income">
                    </div>
                `;
                break;
            case 'other_expense':
                description = 'Other expense';
                html = `
                    <div class="mb-3">
                        <label class="form-label">Expense Type</label>
                        <select class="form-select" id="otherExpenseType">
                            <option value="Bank Charges">Bank Charges</option>
                            <option value="Licenses & Permits">Licenses & Permits</option>
                            <option value="Professional Fees">Professional Fees</option>
                            <option value="Travel">Travel</option>
                            <option value="Training">Training</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payee</label>
                        <input type="text" class="form-control" id="payeeName" placeholder="Who was paid">
                    </div>
                `;
                break;
            default:
                html = '';
                description = '';
        }
        
        if (description && !document.getElementById('transactionDescription').value) {
            document.getElementById('transactionDescription').value = description;
        }
        
        dynamicFields.innerHTML = html;
    }
    
    function saveTransaction(e) {
        e.preventDefault();
        
        const data = {
            type: document.getElementById('transactionType').value,
            scenario: document.getElementById('transactionScenario').value,
            amount: parseFloat(document.getElementById('transactionAmount').value),
            description: document.getElementById('transactionDescription').value,
            payment_method: document.getElementById('transactionPaymentMethod').value,
            transaction_date: document.getElementById('transactionDate').value,
            reference: document.getElementById('transactionReference').value,
            id: document.getElementById('transactionId').value || null
        };
        
        const dynamicFields = document.getElementById('dynamicFields');
        const inputs = dynamicFields.querySelectorAll('input, select');
        inputs.forEach(input => {
            data[input.id] = input.value;
        });
        
        if (!data.type || !data.scenario || !data.amount || data.amount <= 0) {
            alert('Please fill in all required fields');
            return;
        }
        
        const submitBtn = document.querySelector('#transactionForm button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
        submitBtn.disabled = true;
        
        fetch(API_URL + 'save-transaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (result.success) {
                alert('✅ Transaction saved successfully!');
                bootstrap.Modal.getInstance(document.getElementById('transactionModal')).hide();
                location.reload();
            } else {
                alert('❌ Error: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            alert('❌ Error saving transaction: ' + error);
        });
    }
    
    function viewTransaction(id) {
        fetch(API_URL + 'get-transaction.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }
                
                const body = document.getElementById('viewTransactionBody');
                body.innerHTML = `
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p>${data.description || 'N/A'}</p>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Type:</strong>
                            <span class="badge ${data.type === 'income' ? 'bg-success' : 'bg-danger'}">${data.type}</span>
                        </div>
                        <div class="col-6">
                            <strong>Amount:</strong>
                            <span style="color:${data.type === 'income' ? 'var(--green)' : '#dc3545'};font-weight:bold;">E${parseFloat(data.amount).toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Category:</strong>
                            <span class="category-badge" style="background:${data.category_color || '#eee'};color:${data.category_text_color || '#333'};">${data.category || 'Uncategorized'}</span>
                        </div>
                        <div class="col-6">
                            <strong>Status:</strong>
                            <span class="badge ${data.status === 'posted' ? 'bg-success' : 'bg-warning'}">${data.status}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Payment Method:</strong>
                            <span>${data.payment_method || 'N/A'}</span>
                        </div>
                        <div class="col-6">
                            <strong>Date:</strong>
                            <span>${data.transaction_date ? new Date(data.transaction_date).toLocaleDateString() : 'N/A'}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Reference:</strong>
                        <span>${data.reference || 'N/A'}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Created:</strong>
                        <span>${data.created_at ? new Date(data.created_at).toLocaleString() : 'N/A'}</span>
                    </div>
                    ${data.auto_classified ? '<div class="alert alert-info"><i class="fas fa-robot me-2"></i>This transaction was automatically classified by the system.</div>' : ''}
                    <hr>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-danger btn-sm" onclick="deleteTransaction(${data.id})"><i class="fas fa-trash me-1"></i>Delete</button>
                        <button class="btn btn-outline-warning btn-sm" onclick="voidTransaction(${data.id})"><i class="fas fa-ban me-1"></i>Void</button>
                    </div>
                `;
                
                new bootstrap.Modal(document.getElementById('viewTransactionModal')).show();
            })
            .catch(error => alert('Error loading transaction: ' + error));
    }
    
    function deleteTransaction(id) {
        if (confirm('Are you sure you want to delete this transaction? This cannot be undone.')) {
            fetch(API_URL + 'delete-transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Transaction deleted.');
                    bootstrap.Modal.getInstance(document.getElementById('viewTransactionModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            });
        }
    }
    
    function voidTransaction(id) {
        if (confirm('Void this transaction? It will be marked as void.')) {
            fetch(API_URL + 'void-transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Transaction voided.');
                    bootstrap.Modal.getInstance(document.getElementById('viewTransactionModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            });
        }
    }
    
    // ==================== HELPER FUNCTIONS ====================
    function saveItem(){
        let title=document.getElementById('modalTitle').innerText;
        if(title.includes('Wine'))saveWine();
        else if(title.includes('Pairing'))savePairing();
        else if(title.includes('Corporate Gift'))saveCorporateGift();
        else if(title.includes('Gift Basket'))saveGiftBasket();
        else alert('Save not implemented');
    }
    
    function escapeHtml(t){
        if(!t)return '';
        const d=document.createElement('div');
        d.textContent=t;
        return d.innerHTML;
    }
    
    // Export CSV for reports
    function exportCSV() {
        let csv = "Category,Type,Amount\n";
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>