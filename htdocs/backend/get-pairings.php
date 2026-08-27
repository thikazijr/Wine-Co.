<?php
// Disable error reporting for this file
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table if not exists with image_url column
    $pdo->exec("CREATE TABLE IF NOT EXISTS pairings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        compatible_wines VARCHAR(255),
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Check if image_url column exists, if not add it
    try {
        $pdo->query("SELECT image_url FROM pairings LIMIT 1");
    } catch(PDOException $e) {
        $pdo->exec("ALTER TABLE pairings ADD COLUMN image_url VARCHAR(255) DEFAULT NULL");
    }
    
    $stmt = $pdo->query("SELECT * FROM pairings ORDER BY id DESC");
    $pairings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Make sure prices are numbers
    foreach ($pairings as &$p) {
        $p['price'] = floatval($p['price']);
        $p['image_url'] = $p['image_url'] ?? '';
    }
    
    echo json_encode($pairings);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>