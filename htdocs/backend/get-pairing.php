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
    
    $stmt = $pdo->prepare("SELECT * FROM pairings WHERE id = ?");
    $stmt->execute([$id]);
    $pairing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pairing) {
        // Make sure price is a float
        $pairing['price'] = floatval($pairing['price']);
        echo json_encode($pairing);
    } else {
        echo json_encode(['error' => 'Pairing not found']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>