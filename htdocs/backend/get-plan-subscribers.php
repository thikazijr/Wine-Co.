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
    
    $planId = $_GET['plan_id'] ?? 0;
    
    if (!$planId) {
        echo json_encode(['success' => false, 'error' => 'Plan ID required']);
        exit;
    }
    
    // First get the plan name
    $stmt = $pdo->prepare("SELECT display_name FROM subscriptions WHERE id = ?");
    $stmt->execute([$planId]);
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$plan) {
        echo json_encode(['success' => false, 'error' => 'Plan not found']);
        exit;
    }
    
    // Get subscribers for this plan
    $stmt = $pdo->prepare("
        SELECT full_name, email, phone, created_at, expiry_date, status 
        FROM subscription_requests 
        WHERE plan_name = ? AND status IN ('active', 'approved')
        ORDER BY created_at DESC
    ");
    $stmt->execute([$plan['display_name']]);
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'plan_name' => $plan['display_name'],
        'subscribers' => $subscribers,
        'count' => count($subscribers)
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>