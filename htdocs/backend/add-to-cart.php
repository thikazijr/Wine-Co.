<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get session ID from request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Use session ID from request or generate one
$sessionId = isset($data['sessionId']) ? $data['sessionId'] : 'session_' . date('Ymd_His') . '_' . uniqid();

$productId = isset($data['productId']) ? intval($data['productId']) : 0;
$productType = isset($data['productType']) ? $data['productType'] : '';
$productName = isset($data['productName']) ? $data['productName'] : '';
$price = isset($data['price']) ? floatval($data['price']) : 0;
$quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create cart table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(100) NOT NULL,
        product_id INT NOT NULL,
        product_type VARCHAR(30) NOT NULL,
        product_name VARCHAR(200) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT DEFAULT 1,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_session (session_id),
        INDEX idx_product (product_id, product_type)
    )");
    
    // Validate required fields
    if (!$productId || !$productType || !$productName || !$price) {
        echo json_encode([
            'success' => false, 
            'error' => 'Missing required fields',
            'debug' => [
                'productId' => $productId,
                'productType' => $productType,
                'productName' => $productName,
                'price' => $price
            ]
        ]);
        exit;
    }
    
    // Check if item already exists in cart
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE session_id = ? AND product_id = ? AND product_type = ?");
    $stmt->execute([$sessionId, $productId, $productType]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Update quantity
        $newQty = $existing['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$newQty, $existing['id']]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Cart updated',
            'action' => 'updated',
            'quantity' => $newQty,
            'cart_id' => $existing['id'],
            'sessionId' => $sessionId
        ]);
    } else {
        // Insert new item
        $stmt = $pdo->prepare("INSERT INTO cart (session_id, product_id, product_type, product_name, price, quantity) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$sessionId, $productId, $productType, $productName, $price, $quantity]);
        $newId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Added to cart',
            'action' => 'added',
            'quantity' => $quantity,
            'cart_id' => $newId,
            'sessionId' => $sessionId
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false, 
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => 'Error: ' . $e->getMessage()
    ]);
}
?>