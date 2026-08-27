<?php
session_start();

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
    die("Connection failed: " . $e->getMessage());
}

// Get low stock wines
$lowStockWines = $pdo->query("SELECT * FROM wines WHERE stock_quantity < 30 ORDER BY stock_quantity ASC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
$poNumber = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - Wine & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print { .no-print { display: none; } }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        .po-container { max-width: 1000px; margin: 40px auto; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .po-header { background: linear-gradient(135deg, #1a6b3c, #2a8e50); color: white; padding: 30px; }
        .po-header img { height: 45px; margin-bottom: 10px; border-radius: 10px; background: white; padding: 5px 10px; }
        .po-body { padding: 30px; }
        .btn-wine { background: #722f37; color: white; border: none; padding: 10px 25px; border-radius: 40px; }
        .btn-wine:hover { background: #5a232a; }
        .critical-item { background: #f8d7da; border-left: 4px solid #dc3545; }
        .low-item { background: #fff3cd; border-left: 4px solid #ffc107; }
        .btn-secondary { background: #6c757d; color: white; border: none; padding: 10px 25px; border-radius: 40px; }
        .btn-secondary:hover { background: #5a6268; color: white; }
    </style>
</head>
<body>
    <div class="po-container">
        <div class="po-header">
            <div class="row">
                <div class="col-6">
                    <img src="/uploads/wines/logo.jpg" alt="Wine & Co.">
                    <h2><i class="fas fa-file-purchase me-2"></i>PURCHASE ORDER</h2>
                    <small>Supplier Order Form</small>
                </div>
                <div class="col-6 text-end">
                    <p><strong>PO #:</strong> <?php echo $poNumber; ?><br>
                    <strong>Date:</strong> <?php echo date('d F Y'); ?></p>
                </div>
            </div>
        </div>
        <div class="po-body">
            <?php if (empty($lowStockWines)): ?>
                <div class="alert alert-success">✅ All stock levels are healthy. No purchase needed.</div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Low Stock Alert!</strong> The following items need to be reordered:
                </div>
                
                <table class="table table-bordered">
                    <thead style="background:#722f37; color:white">
                        <tr><th>Product</th><th>Current Stock</th><th>Suggested Qty</th><th>Select</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($lowStockWines as $wine): 
                            $suggested = $wine['stock_quantity'] < 10 ? 100 : ($wine['stock_quantity'] < 20 ? 60 : 30);
                            $class = $wine['stock_quantity'] < 10 ? 'critical-item' : 'low-item';
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td><strong><?php echo htmlspecialchars($wine['name']); ?></strong><br><small><?php echo htmlspecialchars($wine['variety']); ?></small></td>
                            <td><span class="badge bg-danger"><?php echo $wine['stock_quantity']; ?> units</span></td>
                            <td><input type="number" class="form-control form-control-sm reorder-qty" value="<?php echo $suggested; ?>" style="width:100px"></td>
                            <td><input type="checkbox" class="form-check-input select-item" data-name="<?php echo htmlspecialchars($wine['name']); ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Supplier Information</h5>
                    <input type="text" id="supplierName" class="form-control mb-2" value="Premium Wine Distributors SA">
                    <input type="text" id="contactPerson" class="form-control" value="John Malan">
                </div>
                <div class="col-md-6">
                    <h5>Shipping Address</h5>
                    <textarea id="shippingAddress" class="form-control" rows="3">Wine & Co. Warehouse, Mbabane Industrial Park, Mbabane, Eswatini</textarea>
                </div>
            </div>
            
            <?php if (!empty($lowStockWines)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <h5>Order Summary</h5>
                    <table class="table table-bordered" id="summaryTable">
                        <thead><tr><th>Item</th><th>Quantity</th><th>Total</th></tr></thead>
                        <tbody><tr><td colspan="3" class="text-center">Select items to see summary</td></tr></tbody>
                        <tfoot><tr class="table-warning"><td colspan="2" class="text-end"><strong>Total:</strong></td><td><strong id="totalAmount">E0.00</strong></td></tr></tfoot>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="text-center mt-4 no-print">
                <button class="btn btn-wine" onclick="calculateTotal()"><i class="fas fa-calculator me-2"></i>Calculate</button>
                <button class="btn btn-wine" onclick="window.print()"><i class="fas fa-print me-2"></i>Print PO</button>
                <button class="btn btn-secondary" onclick="window.close()"><i class="fas fa-times me-2"></i>Close</button>
            </div>
        </div>
    </div>

    <script>
        function calculateTotal() {
            let selected = [];
            let total = 0;
            let rows = document.querySelectorAll('.select-item');
            let qtyInputs = document.querySelectorAll('.reorder-qty');
            
            rows.forEach((cb, index) => {
                if (cb.checked) {
                    let row = cb.closest('tr');
                    let name = cb.getAttribute('data-name') || 'Item';
                    let qty = qtyInputs[index]?.value || 0;
                    let price = 500;
                    let itemTotal = qty * price;
                    total += itemTotal;
                    selected.push(`<tr><td>${name}</td><td>${qty}</td><td>E${itemTotal.toFixed(2)}</td></tr>`);
                }
            });
            
            let tbody = document.querySelector('#summaryTable tbody');
            if (selected.length) {
                tbody.innerHTML = selected.join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">No items selected</td></tr>';
            }
            document.getElementById('totalAmount').innerText = `E${total.toFixed(2)}`;
        }
    </script>
</body>
</html>