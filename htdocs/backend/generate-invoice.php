<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Unauthorized access');
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed");
}

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$orderId) {
    die('Order ID required');
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die('Order not found');
}

// ============================================================
// IMPORTANT: Get items from order_items table
// ============================================================
$items = [];
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no items in order_items, try to get from the items JSON column
if (empty($items) && !empty($order['items'])) {
    $items = json_decode($order['items'], true);
    if (!is_array($items)) {
        $items = [];
    }
}

// Calculate totals
$subtotal = 0;
foreach ($items as $item) {
    $price = floatval($item['price'] ?? $item['unit_price'] ?? 0);
    $qty = intval($item['quantity'] ?? $item['qty'] ?? 1);
    $subtotal += $price * $qty;
}

$vat = $subtotal * 0.15;
$delivery = floatval($order['shipping'] ?? $order['delivery_fee'] ?? 0);
$total = floatval($order['total'] ?? $subtotal + $vat + $delivery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --wine: #722f37; --gold: #c9a03d; }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; padding: 40px 0; }
        .invoice-wrapper { max-width: 900px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden; }
        .invoice-header { background: linear-gradient(135deg, var(--wine), #5a232a); color: white; padding: 30px 40px; }
        .invoice-header h1 { font-size: 2rem; font-weight: 700; }
        .invoice-body { padding: 30px 40px; }
        .invoice-footer { background: #f8f4f0; padding: 20px 40px; text-align: center; border-top: 2px solid var(--gold); }
        .total-row { background: var(--wine); color: white; font-weight: bold; font-size: 1.2rem; }
        .total-row td { padding: 15px; }
        .company-name { font-size: 1.8rem; font-weight: 700; color: var(--wine); }
        .company-name i { color: var(--gold); }
        @media print {
            .no-print { display: none !important; }
            .invoice-wrapper { box-shadow: none !important; border-radius: 0 !important; }
            body { background: white !important; padding: 0 !important; }
        }
        .order-items-table th { background: #f8f4f0; color: var(--wine); }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .btn-wine { background: var(--wine); color: white; border: none; padding: 8px 24px; border-radius: 40px; }
        .btn-wine:hover { background: #5a232a; color: white; }
        .btn-outline-wine { background: transparent; border: 2px solid var(--wine); color: var(--wine); padding: 8px 24px; border-radius: 40px; }
        .btn-outline-wine:hover { background: var(--wine); color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-wrapper">
            <!-- Header -->
            <div class="invoice-header d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-wine-bottle me-2"></i>Wine & Co.</h1>
                    <p class="mb-0 opacity-75">Eswatini • Premium Wine Merchant</p>
                </div>
                <div class="text-end">
                    <h5 class="mb-0">INVOICE</h5>
                    <small>#<?php echo htmlspecialchars($order['order_number'] ?? 'ORD-' . str_pad($order['id'], 6, '0', STR_PAD_LEFT)); ?></small>
                    <br>
                    <small><?php echo date('d M Y', strtotime($order['created_at'])); ?></small>
                </div>
            </div>
            
            <!-- Body -->
            <div class="invoice-body">
                <!-- Bill To -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Bill To:</h6>
                        <p class="mb-0"><strong><?php echo htmlspecialchars($order['customer_name'] ?? 'Customer'); ?></strong></p>
                        <p class="mb-0"><?php echo htmlspecialchars($order['customer_email'] ?? ''); ?></p>
                        <p class="mb-0"><?php echo htmlspecialchars($order['customer_phone'] ?? ''); ?></p>
                        <p class="mb-0"><?php echo htmlspecialchars($order['customer_address'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h6 class="text-muted mb-1">Order Details:</h6>
                        <p class="mb-0"><strong>Order #:</strong> <?php echo htmlspecialchars($order['order_number'] ?? 'ORD-' . str_pad($order['id'], 6, '0', STR_PAD_LEFT)); ?></p>
                        <p class="mb-0"><strong>Date:</strong> <?php echo date('d M Y', strtotime($order['created_at'])); ?></p>
                        <p class="mb-0"><strong>Status:</strong> 
                            <span class="status-badge <?php echo $order['status'] == 'completed' ? 'status-paid' : ($order['status'] == 'processing' ? 'status-processing' : 'status-pending'); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </p>
                        <?php if(!empty($order['payment_method'])): ?>
                        <p class="mb-0"><strong>Payment:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Items Table -->
                <table class="table table-bordered order-items-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($items)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No items found</td>
                        </tr>
                        <?php else: 
                        $counter = 1;
                        foreach($items as $item): 
                            $itemName = $item['product_name'] ?? $item['name'] ?? 'Item';
                            $itemPrice = floatval($item['price'] ?? $item['unit_price'] ?? 0);
                            $itemQty = intval($item['quantity'] ?? $item['qty'] ?? 1);
                            $itemTotal = $itemPrice * $itemQty;
                        ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><strong><?php echo htmlspecialchars($itemName); ?></strong></td>
                            <td class="text-center"><?php echo $itemQty; ?></td>
                            <td class="text-end">E<?php echo number_format($itemPrice, 2); ?></td>
                            <td class="text-end">E<?php echo number_format($itemTotal, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- Totals -->
                        <tr>
                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end">E<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end"><strong>VAT (15%):</strong></td>
                            <td class="text-end">E<?php echo number_format($vat, 2); ?></td>
                        </tr>
                        <?php if($delivery > 0): ?>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Delivery:</strong></td>
                            <td class="text-end">E<?php echo number_format($delivery, 2); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="total-row">
                            <td colspan="4" class="text-end">TOTAL:</td>
                            <td class="text-end">E<?php echo number_format($total, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Notes -->
                <?php if(!empty($order['notes'])): ?>
                <div class="mt-3">
                    <h6 class="text-muted">Notes:</h6>
                    <p class="small"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="mt-4 text-center text-muted small">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    Thank you for choosing Wine & Co.!
                    <br>
                    <span class="opacity-75">Sip responsibly • 18+ only</span>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="invoice-footer d-flex justify-content-between align-items-center">
                <div>
                    <span class="company-name"><i class="fas fa-wine-bottle me-2"></i>Wine & Co.</span>
                    <br>
                    <small>Eswatini's Premium Wine Merchant</small>
                </div>
                <div class="no-print">
                    <button class="btn btn-wine" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Invoice
                    </button>
                    <button class="btn btn-outline-wine ms-2" onclick="window.close()">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>