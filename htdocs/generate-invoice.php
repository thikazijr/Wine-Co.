<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/index.php');
    exit;
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed");
}

$orderId = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? 'view';

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found");
}

$items = json_decode($order['items'], true);
$subtotal = $order['total'];
$vat = $subtotal * 0.15;
$total = $subtotal + $vat;
$shipping = $subtotal > 1000 ? 0 : 99;

// Generate invoice number
$invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad($order['id'], 4, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $invoiceNo; ?> - Wine & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 0; }
            .invoice-container { margin: 0; padding: 20px; }
        }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        .invoice-container { max-width: 900px; margin: 40px auto; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .invoice-header { background: linear-gradient(135deg, #722f37, #9e4a55); color: white; padding: 30px; }
        .invoice-body { padding: 30px; }
        .btn-wine { background: #722f37; color: white; border: none; padding: 10px 25px; border-radius: 40px; margin: 5px; }
        .btn-wine:hover { background: #5a232a; color: white; }
        .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-delivered { background: #1a6b3c; color: white; }
        .status-pending { background: #fd7e14; color: white; }
        .status-processing { background: #0dcaf0; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
        .company-logo { font-size: 2rem; font-weight: bold; }
        .border-dashed { border: 1px dashed #ddd; }
        .total-row { background: #f8f9fa; font-weight: bold; }
        .thankyou { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row">
                <div class="col-6">
                    <div class="company-logo">
                        <i class="fas fa-wine-bottle me-2"></i>Wine & Co.
                    </div>
                    <small class="opacity-75">Eswatini's Premier Wine Club</small>
                </div>
                <div class="col-6 text-end">
                    <h3>TAX INVOICE</h3>
                    <p class="mb-0"><strong>Invoice #:</strong> <?php echo $invoiceNo; ?></p>
                    <p class="mb-0"><strong>Date:</strong> <?php echo date('d F Y', strtotime($order['created_at'])); ?></p>
                    <p><strong>Order #:</strong> <?php echo $order['id']; ?></p>
                </div>
            </div>
        </div>

        <!-- Invoice Body -->
        <div class="invoice-body">
            <!-- Bill To & Shipping Info -->
            <div class="row mb-4">
                <div class="col-6">
                    <h5><i class="fas fa-user me-2"></i>Bill To:</h5>
                    <p class="mb-1"><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                    <p class="mb-1"><?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p class="mb-1"><?php echo htmlspecialchars($order['customer_phone'] ?? 'N/A'); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($order['customer_address'] ?? 'No address provided')); ?></p>
                </div>
                <div class="col-6">
                    <h5><i class="fas fa-truck me-2"></i>Order Details:</h5>
                    <p class="mb-1"><strong>Order Status:</strong> 
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </p>
                    <p class="mb-1"><strong>Payment Method:</strong> Cash on Delivery</p>
                    <p><strong>Delivery Time:</strong> 3-5 business days</p>
                </div>
            </div>

            <!-- Items Table -->
            <table class="table table-bordered">
                <thead style="background: #722f37; color: white;">
                    <tr>
                        <th>#</th>
                        <th>Item Description</th>
                        <th>Quantity</th>
                        <th>Unit Price (E)</th>
                        <th>Total (E)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                    foreach ($items as $item): 
                    ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                            <br><small class="text-muted">Wine</small>
                        </td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>E<?php echo number_format($item['price'], 2); ?></td>
                        <td>E<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                        <td>E<?php echo number_format($order['total'], 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>VAT (15%):</strong></td>
                        <td>E<?php echo number_format($order['total'] * 0.15, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Delivery Fee:</strong></td>
                        <td>E<?php echo number_format($shipping, 2); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" class="text-end"><strong>TOTAL AMOUNT:</strong></td>
                        <td><strong>E<?php echo number_format($order['total'] + ($order['total'] * 0.15) + $shipping, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Payment Instructions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Payment Instructions:</strong> Pay cash upon delivery. Please have exact change ready.
                    </div>
                </div>
            </div>

            <!-- Company Details -->
            <div class="row mt-3">
                <div class="col-6">
                    <small class="text-muted">
                        <strong>Wine & Co. Eswatini</strong><br>
                        Mbabane, Eswatini<br>
                        Tel: +268 1234 5678<br>
                        Email: hello@wineco.co.sz<br>
                        VAT Registration: SWZ-123456
                    </small>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1"></i> Payment Due: Upon Delivery<br>
                        <i class="fas fa-receipt me-1"></i> E&OE
                    </small>
                </div>
            </div>

            <!-- Thank You Message -->
            <div class="thankyou">
                <i class="fas fa-heart text-danger me-2"></i>
                Thank you for choosing Wine & Co.!
                <br>
                <small>We hope you enjoy your wine experience. Sip responsibly.</small>
            </div>
        </div>
    </div>

    <!-- Action Buttons (Visible only on screen, not when printing) -->
    <div class="text-center no-print" style="margin-bottom: 40px;">
        <button class="btn btn-wine" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Print Invoice
        </button>
        <button class="btn btn-outline-wine" onclick="window.close()">
            <i class="fas fa-times me-2"></i>Close
        </button>
        <a href="?section=orders" class="btn btn-outline-wine">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
    </div>

    <script>
        // Auto print if action=print
        <?php if ($action === 'print'): ?>
        window.onload = function() { window.print(); }
        <?php endif; ?>
    </script>
</body>
</html>