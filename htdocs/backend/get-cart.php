<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$sessionId = isset($_GET['sessionId']) ? $_GET['sessionId'] : session_id();

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE session_id = ? ORDER BY added_at DESC");
    $stmt->execute([$sessionId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total = 0;
    foreach ($items as &$item) {
        $total += $item['price'] * $item['quantity'];
        $item['image_url'] = '';
        
        try {
            $pType = strtolower($item['product_type'] ?? '');
            $pId = intval($item['product_id'] ?? 0);
            
            if ($pType === 'wine') {
                $imgStmt = $pdo->prepare("SELECT image_url FROM wines WHERE id = ? LIMIT 1");
                $imgStmt->execute([$pId]);
                $item['image_url'] = $imgStmt->fetchColumn() ?: '';
            } elseif ($pType === 'pairing') {
                $imgStmt = $pdo->prepare("SELECT image_url FROM pairings WHERE id = ? LIMIT 1");
                $imgStmt->execute([$pId]);
                $item['image_url'] = $imgStmt->fetchColumn() ?: '';
            } elseif ($pType === 'basket' || $pType === 'gift_basket') {
                $imgStmt = $pdo->prepare("SELECT image_url FROM gift_baskets WHERE id = ? LIMIT 1");
                $imgStmt->execute([$pId]);
                $item['image_url'] = $imgStmt->fetchColumn() ?: '';
            } elseif ($pType === 'corporate' || $pType === 'corporate_gift') {
                $imgStmt = $pdo->prepare("SELECT image_url FROM corporate_gifts WHERE id = ? LIMIT 1");
                $imgStmt->execute([$pId]);
                $item['image_url'] = $imgStmt->fetchColumn() ?: '';
            }
        } catch(Exception $ex) {
            // Ignore image lookup errors
        }
        
        if (empty($item['image_url'])) {
            $item['image_url'] = '/uploads/wines/logo.jpg';
        }
    }
    
    echo json_encode([
        'items' => $items, 
        'total' => $total, 
        'itemCount' => count($items)
    ]);
} catch(PDOException $e) {
    echo json_encode(['items' => [], 'total' => 0, 'itemCount' => 0, 'error' => $e->getMessage()]);
}
?>