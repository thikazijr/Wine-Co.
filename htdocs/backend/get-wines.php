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
    
    $featured = isset($_GET['featured']) ? $_GET['featured'] : false;
    
    if ($featured) {
        $stmt = $pdo->prepare("SELECT * FROM wines WHERE featured = 1 AND in_stock = 1 LIMIT 8");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM wines WHERE in_stock = 1 ORDER BY id DESC");
    }
    
    $stmt->execute();
    $wines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($wines);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>