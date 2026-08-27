<?php
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

session_start();
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$pdo = getDB();

$wines = $pdo->query("SELECT COUNT(*) as count FROM wines")->fetch();
$pairings = $pdo->query("SELECT COUNT(*) as count FROM pairings")->fetch();
$orders = $pdo->query("SELECT COUNT(*) as count FROM orders")->fetch();
$revenue = $pdo->query("SELECT COALESCE(SUM(total), 0) as total FROM orders")->fetch();
$lowStock = $pdo->query("SELECT COUNT(*) as count FROM wines WHERE stock_quantity < 20")->fetch();

echo json_encode([
    'totalWines' => $wines['count'],
    'totalPairings' => $pairings['count'],
    'totalOrders' => $orders['count'],
    'totalRevenue' => $revenue['total'],
    'lowStockItems' => $lowStock['count']
]);
?>