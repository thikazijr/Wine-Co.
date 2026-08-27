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
    
    $stmt = $pdo->prepare("INSERT INTO subscriptions (
        tier_name, display_name, tagline, price, wines_per_month, 
        description, features, packaging, savings_percent, is_popular, 
        display_order, expiry_days, is_active
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['tier_name'] ?? '',
        $data['display_name'] ?? '',
        $data['tagline'] ?? '',
        $data['price'] ?? 0,
        $data['wines_per_month'] ?? 1,
        $data['description'] ?? '',
        $data['features'] ?? '',
        $data['packaging'] ?? '',
        $data['savings_percent'] ?? 0,
        $data['is_popular'] ? 1 : 0,
        $data['display_order'] ?? 0,
        $data['expiry_days'] ?? 30,
        $data['is_active'] ? 1 : 0
    ]);
    
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>