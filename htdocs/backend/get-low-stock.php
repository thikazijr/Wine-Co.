<?php
header('Content-Type: application/json');

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $stmt = $pdo->query("SELECT id, name, stock_quantity, price FROM wines WHERE stock_quantity < 30 ORDER BY stock_quantity ASC");
    echo json_encode($stmt->fetchAll());
} catch(Exception $e) {
    echo json_encode([]);
}
?>