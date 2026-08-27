<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Get session ID
$sessionId = null;
if (isset($data['sessionId']) && !empty($data['sessionId'])) {
    $sessionId = $data['sessionId'];
} else {
    session_start();
    $sessionId = session_id();
}

$cartId = $data['cartId'] ?? 0;
$quantity = $data['quantity'] ?? 1;

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($quantity <= 0) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
        $stmt->execute([$cartId, $sessionId]);
    } else {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?");
        $stmt->execute([$quantity, $cartId, $sessionId]);
    }
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>