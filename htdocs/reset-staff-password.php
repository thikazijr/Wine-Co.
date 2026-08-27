<?php
session_start();

// Only Admin and Manager can reset staff passwords
if (!isset($_SESSION['admin_logged_in']) || ($_SESSION['admin_role'] != 'admin' && $_SESSION['admin_role'] != 'manager')) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;
    $newPassword = $data['password'] ?? '';
    
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
        exit;
    }
    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE staff SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $id]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>