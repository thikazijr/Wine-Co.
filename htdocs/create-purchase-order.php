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
    die("Connection failed");
}

// Get low stock wines for purchase order suggestion
$lowStockWines = $pdo->query("SELECT * FROM wines WHERE stock_quantity < 30 ORDER BY stock_quantity ASC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

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
        .po-container { max-width: 900px; margin: 40px auto; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .po-header { background: linear-gradient(135deg, #1a6b3c, #2a8e50); color: white; padding: 30px; }
        .po-body { padding: 30px; }
        .btn-wine { background: #722f37; color: white; border: none; padding: 10px 25px; border-radius: 40px; }
        .btn-wine:hover { background: #5a232a; }
        .low-stock-item { background: #fff3cd; border-left: 4px solid #ffc107; margin-bottom: 10px; padding: 10px; }
    </style>
</head>
<body>
    <div class="po-container">
        <div class="po-header">
            <div class="row">
                <div class="col-6">
                    <h2><i class="fas fa-file-purchase me-2"></i>PURCHASE ORDER</h2>
                    <small>Supplier Order Form</small>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-0"><strong>PO #:</strong> <?php echo $poNumber; ?></p>
                    <p><strong>Date:</strong> <?php echo date('d F Y'); ?></p>
                </div>
            </div>
        </div>
        <div class="po-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Low Stock Alert!</strong> The following items need to be reordered:
            </div>
            
            <?php foreach ($lowStockWines as $wine): ?>
            <div class="low-stock-item">
                <div class="row">
                    <div class="col-6">
                        <strong><?php echo htmlspecialchars($wine['name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($wine['variety']); ?></small>
                    </div>
                    <div class="col-3">
                        Current Stock: <strong class="text-danger"><?php echo $wine['stock_quantity']; ?> units</strong>
                    </div>
                    <div class="col-3">
                        Reorder Qty: <input type="number" class="form-control form-control-sm" value="50" style="width: 80px; display: inline-block;">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="mt-4">
                <h5>Supplier Information</h5>
                <div class="row">
                    <div class="col-6">
                        <label>Supplier Name</label>
                        <input type="text" class="form-control" placeholder="Enter supplier name" value="Premium Wine Distributors SA">
                    </div>
                    <div class="col-6">
                        <label>Contact Person</label>
                        <input type="text" class="form-control" placeholder="Contact person" value="John Malan">
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4 no-print">
                <button class="btn btn-wine" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print PO
                </button>
                <button class="btn btn-secondary" onclick="window.close()">
                    <i class="fas fa-times me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</body>
</html>