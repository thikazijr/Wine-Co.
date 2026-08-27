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
    
    // Check if image_url column exists
    try {
        $pdo->query("SELECT image_url FROM pairings LIMIT 1");
    } catch(PDOException $e) {
        $pdo->exec("ALTER TABLE pairings ADD COLUMN image_url VARCHAR(255) DEFAULT NULL");
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("INSERT INTO pairings (name, description, price, compatible_wines, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['name'] ?? '',
        $data['description'] ?? '',
        $data['price'] ?? 0,
        $data['compatible_wines'] ?? '',
        $data['image_url'] ?? ''
    ]);
    
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>