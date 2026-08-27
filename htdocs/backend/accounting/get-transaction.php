<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if (!$id) {
        echo json_encode(['error' => 'ID required']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM accounting_transactions WHERE id = ?");
    $stmt->execute([$id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($transaction) {
        echo json_encode($transaction);
    } else {
        echo json_encode(['error' => 'Transaction not found']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>