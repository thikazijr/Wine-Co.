<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Wine & Co. Eswatini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --wine: #722f37; --gold: #c9a03d; --green: #1a6b3c; }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        .navbar { background: white; border-bottom: 2px solid var(--gold); padding: 15px 0; }
        .navbar-brand { color: var(--wine) !important; font-size: 1.8rem; font-weight: 700; }
        .nav-link { color: #4a2c2a !important; font-weight: 500; margin: 0 10px; }
        .nav-link:hover { color: var(--wine) !important; }
        .btn-wine { background: var(--wine); color: white; border-radius: 40px; padding: 10px 30px; border: none; transition: 0.3s; }
        .btn-wine:hover { background: #5a232a; color: white; transform: translateY(-2px); }
        .confirmation-card { background: white; border-radius: 20px; padding: 50px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
        .success-icon { font-size: 5rem; color: var(--green); margin-bottom: 20px; }
        .order-number { background: #f8f4f0; padding: 15px 30px; border-radius: 12px; display: inline-block; font-size: 1.2rem; }
        .order-number strong { color: var(--wine); }
        .details-row { background: #faf8f6; border-radius: 12px; padding: 20px; margin: 15px 0; }
        footer { background: #1a1a2e; color: #aaa; margin-top: 60px; padding: 50px 0; }
        footer a { color: #aaa; text-decoration: none; }
        footer a:hover { color: var(--gold); }
        .cart-icon { position: relative; color: var(--wine); text-decoration: none; font-size: 1.2rem; }
        .cart-count { position: absolute; top: -12px; right: -18px; background: var(--gold); color: #333; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; }
        .order-item { border-bottom: 1px solid #eee; padding: 10px 0; }
        .order-item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirmation-card">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="fw-bold" style="color: var(--wine);">Order Confirmed! 🎉</h2>
                    <p class="text-muted">Thank you for your order. We'll start processing it right away.</p>
                    
                    <?php
                    // Get order ID from URL
                    $orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
                    $orderNumber = '';
                    $orderDetails = null;
                    
                    if ($orderId > 0) {
                        $host = 'sql306.infinityfree.com';
                        $dbname = 'if0_42164424_if0_42164424_wineco';
                        $username = 'if0_42164424';
                        $password = 'aZ8j5lRv2DjU2';
                        
                        try {
                            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
                            $stmt->execute([$orderId]);
                            $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($orderDetails) {
                                $orderNumber = $orderDetails['order_number'];
                                $items = json_decode($orderDetails['items'], true);
                            }
                        } catch(PDOException $e) {
                            // Silent fail
                        }
                    }
                    ?>
                    
                    <?php if ($orderDetails): ?>
                    <div class="order-number mt-3">
                        <strong>Order #:</strong> <?php echo htmlspecialchars($orderNumber); ?>
                    </div>
                    
                    <div class="row details-row mt-4">
                        <div class="col-md-6 text-start">
                            <h6><i class="fas fa-user me-2"></i>Customer Details</h6>
                            <p class="mb-1"><strong><?php echo htmlspecialchars($orderDetails['customer_name']); ?></strong></p>
                            <p class="mb-1"><?php echo htmlspecialchars($orderDetails['customer_email']); ?></p>
                            <p class="mb-0"><?php echo htmlspecialchars($orderDetails['customer_phone']); ?></p>
                        </div>
                        <div class="col-md-6 text-start">
                            <h6><i class="fas fa-map-pin me-2"></i>Delivery Address</h6>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($orderDetails['customer_address'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6><i class="fas fa-shopping-bag me-2"></i>Order Items</h6>
                        <div class="text-start">
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                <div class="order-item d-flex justify-content-between">
                                    <span><?php echo htmlspecialchars($item['product_name']); ?> <span class="text-muted">x<?php echo $item['quantity']; ?></span></span>
                                    <span>E<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-start">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>E<?php echo number_format($orderDetails['subtotal'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tax (15% VAT):</span>
                            <span>E<?php echo number_format($orderDetails['tax'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Delivery:</span>
                            <span><?php echo $orderDetails['shipping'] > 0 ? 'E' . number_format($orderDetails['shipping'], 2) : 'FREE'; ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong style="color: var(--wine); font-size: 1.2rem;">E<?php echo number_format($orderDetails['total'], 2); ?></strong>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <span class="badge bg-warning text-dark p-2">
                            <i class="fas fa-clock me-1"></i> Status: Pending
                        </span>
                        <span class="badge bg-info text-dark p-2 ms-2">
                            <i class="fas fa-credit-card me-1"></i> Payment: <?php echo ucfirst(str_replace('_', ' ', $orderDetails['payment_method'] ?? 'Cash')); ?>
                        </span>
                    </div>
                    
                    <?php else: ?>
                    <div class="mt-3">
                        <p class="text-muted">Order details loaded successfully.</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <p class="text-muted small">
                            <i class="fas fa-envelope me-1"></i> A confirmation email has been sent to your email address.
                        </p>
                        <p class="text-muted small">
                            <i class="fas fa-phone me-1"></i> For any questions, contact us at +268 1234 5678
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="index.php" class="btn btn-wine">
                            <i class="fas fa-home me-2"></i>Return Home
                        </a>
                        <a href="shop.php" class="btn btn-outline-wine">
                            <i class="fas fa-wine-glass me-2"></i>Continue Shopping
                        </a>
                        <a href="shop.php" class="btn btn-outline-wine">
                            <i class="fas fa-print me-2"></i>Print Receipt
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>