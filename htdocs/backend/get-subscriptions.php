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
    
    $stmt = $pdo->query("SELECT * FROM subscriptions WHERE is_active = 1 ORDER BY price ASC");
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($subscriptions);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>