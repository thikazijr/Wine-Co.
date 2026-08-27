<?php
// Run this daily via cron or manually
$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update expired subscriptions
    $stmt = $pdo->prepare("UPDATE user_subscriptions SET status = 'expired' WHERE end_date < NOW() AND status = 'active'");
    $stmt->execute();
    
    echo "Subscription expiry check completed. " . $stmt->rowCount() . " subscriptions expired.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>