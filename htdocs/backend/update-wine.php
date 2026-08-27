<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("UPDATE wines SET 
        name = ?, 
        variety = ?, 
        origin = ?, 
        price = ?, 
        stock_quantity = ?, 
        vintage = ?, 
        structure = ?, 
        taste = ?, 
        strength = ?, 
        description = ?, 
        featured = ?, 
        image_url = ? 
        WHERE id = ?");
    $stmt->execute([
        $data['name'] ?? '',
        $data['variety'] ?? '',
        $data['origin'] ?? '',
        $data['price'] ?? 0,
        $data['stock_quantity'] ?? 0,
        $data['vintage'] ?? 0,
        $data['structure'] ?? '',
        $data['taste'] ?? '',
        $data['strength'] ?? '',
        $data['description'] ?? '',
        $data['featured'] ?? 0,
        $data['image_url'] ?? '',
        $data['id'] ?? 0
    ]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>