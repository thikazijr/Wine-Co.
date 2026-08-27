<?php
// backend/log-download.php
header('Content-Type: application/json');

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create downloads table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS magazine_downloads (
        id INT PRIMARY KEY AUTO_INCREMENT,
        customer_email VARCHAR(100),
        payment_method VARCHAR(50),
        amount DECIMAL(10,2) DEFAULT 45.00,
        ip_address VARCHAR(45),
        downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("INSERT INTO magazine_downloads (customer_email, payment_method, amount, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $data['email'] ?? null,
        $data['payment_method'] ?? 'cash',
        $data['amount'] ?? 45.00,
        $_SERVER['REMOTE_ADDR'] ?? null
    ]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>