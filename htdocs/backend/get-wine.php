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
    
    // Check if table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'wines'")->rowCount() > 0;
    if (!$tableExists) {
        echo json_encode([]);
        exit;
    }
    
    $featured = isset($_GET['featured']) && $_GET['featured'] == 'true';
    $sql = "SELECT * FROM wines WHERE 1=1";
    if ($featured) {
        $sql .= " AND featured = 1";
    }
    $sql .= " ORDER BY id DESC";
    
    $stmt = $pdo->query($sql);
    $wines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($wines);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>