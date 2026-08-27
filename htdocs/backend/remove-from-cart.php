<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$cartId = isset($data['cartId']) ? intval($data['cartId']) : 0;
$sessionId = isset($data['sessionId']) ? $data['sessionId'] : session_id();

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
    $stmt->execute([$cartId, $sessionId]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>