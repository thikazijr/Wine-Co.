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
    
    // Check if table exists, if not create it with image_url
    $pdo->exec("CREATE TABLE IF NOT EXISTS gift_baskets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        tier VARCHAR(50) DEFAULT 'Gift Basket',
        description TEXT,
        features TEXT,
        price DECIMAL(10,2) NOT NULL,
        wines_included INT DEFAULT 2,
        image_url VARCHAR(255) DEFAULT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Check if image_url column exists, if not add it
    $columns = $pdo->query("SHOW COLUMNS FROM gift_baskets LIKE 'image_url'")->fetch();
    if (!$columns) {
        $pdo->exec("ALTER TABLE gift_baskets ADD COLUMN image_url VARCHAR(255) DEFAULT NULL AFTER wines_included");
    }
    
    $stmt = $pdo->prepare("SELECT * FROM gift_baskets WHERE is_active = 1 ORDER BY price ASC");
    $stmt->execute();
    $baskets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($baskets);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>