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
    
    // Create table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS corporate_gifts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        tier VARCHAR(50) DEFAULT 'Corporate',
        description TEXT,
        features TEXT,
        price DECIMAL(10,2) NOT NULL,
        wines_included INT DEFAULT 3,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert sample data if empty
    $count = $pdo->query("SELECT COUNT(*) FROM corporate_gifts")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO corporate_gifts (name, tier, description, features, price, wines_included) VALUES
            ('Executive Gift Box', 'Executive', 'Premium gift box for executives', '3 Premium Wines, Artisan Chocolates, Personalised Card', 1499.00, 3),
            ('Boardroom Collection', 'Boardroom', 'Luxury collection for boardroom gifting', '6 Reserve Wines, Gourmet Hamper, Crystal Decanter', 3499.00, 6),
            ('Chairman\\'s Reserve', 'Chairman\\'s Reserve', 'Ultimate luxury gift', '12 Rare Vintages, Handcrafted Wooden Case, Private Tasting Event', 7999.00, 12)
        ");
    }
    
    $stmt = $pdo->query("SELECT * FROM corporate_gifts WHERE is_active = 1 ORDER BY price ASC");
    $gifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($gifts);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>