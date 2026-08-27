<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create orders table if not exists with all columns
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(50) UNIQUE,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        customer_phone VARCHAR(30),
        customer_address TEXT,
        items TEXT,
        subtotal DECIMAL(10,2) DEFAULT 0,
        tax DECIMAL(10,2) DEFAULT 0,
        shipping DECIMAL(10,2) DEFAULT 0,
        total DECIMAL(10,2) DEFAULT 0,
        status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
        payment_method VARCHAR(50) DEFAULT NULL,
        receipt_number VARCHAR(100) DEFAULT NULL,
        pop_file VARCHAR(255) DEFAULT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create order_items table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        product_id INT NULL,
        product_type VARCHAR(50) DEFAULT 'wine',
        product_name VARCHAR(255) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        INDEX idx_order_id (order_id)
    )");
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Log received data for debugging
    error_log("Order data: " . print_r($data, true));
    
    // Validate required fields
    $required = ['customerName', 'customerEmail', 'customerPhone', 'customerAddress', 'items'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
            exit;
        }
    }
    
    // Generate unique order number
    $orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
    
    // Calculate totals from items
    $items = $data['items'] ?? [];
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += floatval($item['price']) * intval($item['quantity']);
    }
    $tax = $subtotal * 0.15; // 15% VAT
    $shipping = $subtotal > 500 ? 0 : 50; // E50 delivery fee, FREE over E500
    $total = $subtotal + $tax + $shipping;
    
    // Get payment method
    $paymentMethod = $data['paymentMethod'] ?? 'cash';
    $popFile = $data['popFileName'] ?? $data['popUploaded'] ?? null;
    $popFilePath = $data['popFilePath'] ?? null;
    $notes = $data['notes'] ?? '';
    $sessionId = $data['sessionId'] ?? null;
    
    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (
        order_number,
        customer_name,
        customer_email,
        customer_phone,
        customer_address,
        items,
        subtotal,
        tax,
        shipping,
        total,
        payment_method,
        pop_file,
        notes,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    
    $stmt->execute([
        $orderNumber,
        $data['customerName'],
        $data['customerEmail'],
        $data['customerPhone'],
        $data['customerAddress'],
        json_encode($items),
        $subtotal,
        $tax,
        $shipping,
        $total,
        $paymentMethod,
        $popFilePath ?: $popFile,
        $notes
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    // ============================================================
    // IMPORTANT: Insert each item into order_items table
    // ============================================================
    $itemStmt = $pdo->prepare("INSERT INTO order_items (
        order_id, product_id, product_type, product_name, price, quantity
    ) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $itemStmt->execute([
            $orderId,
            $item['id'] ?? null,
            $item['type'] ?? 'wine',
            $item['product_name'] ?? $item['name'] ?? 'Item',
            floatval($item['price']),
            intval($item['quantity'])
        ]);
    }
    
    // Clear cart if session ID is provided
    if ($sessionId) {
        try {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
            $stmt->execute([$sessionId]);
        } catch(PDOException $e) {
            // Cart table might not exist, ignore
        }
    }
    
    echo json_encode([
        'success' => true,
        'orderNumber' => $orderNumber,
        'orderId' => $orderId,
        'total' => $total,
        'subtotal' => $subtotal,
        'tax' => $tax,
        'shipping' => $shipping,
        'paymentMethod' => $paymentMethod,
        'popUploaded' => $popFile,
        'itemsCount' => count($items),
        'message' => 'Order placed successfully!'
    ]);
    
} catch(PDOException $e) {
    error_log("Order error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log("Order error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>