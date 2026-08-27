<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get session ID from GET or POST
$sessionId = isset($_GET['sessionId']) ? $_GET['sessionId'] : (isset($_POST['sessionId']) ? $_POST['sessionId'] : '');

// If no session ID provided, check if it was sent in the body (for POST requests)
if (empty($sessionId)) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $sessionId = isset($data['sessionId']) ? $data['sessionId'] : '';
}

// If still empty, use PHP session ID as fallback
if (empty($sessionId)) {
    session_start();
    $sessionId = session_id();
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Make sure cart table exists
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
    
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'count' => intval($row['total']),
        'sessionId' => $sessionId
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'count' => 0, 
        'error' => $e->getMessage(),
        'sessionId' => $sessionId
    ]);
}
?>