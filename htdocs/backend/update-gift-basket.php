<?php
session_start();
header('Content-Type: application/json');

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
    
    $stmt = $pdo->prepare("UPDATE gift_baskets SET 
        name = ?, 
        description = ?, 
        features = ?, 
        price = ?, 
        wines_included = ?,
        image_url = ?
        WHERE id = ?");
    $stmt->execute([
        $data['name'] ?? '',
        $data['description'] ?? '',
        $data['features'] ?? '',
        $data['price'] ?? 0,
        $data['wines_included'] ?? 2,
        $data['image_url'] ?? '',
        $data['id'] ?? 0
    ]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>