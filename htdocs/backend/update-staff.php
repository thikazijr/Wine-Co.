<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SESSION['admin_role'] != 'admin' && $_SESSION['admin_role'] != 'manager') {
    echo json_encode(['success' => false, 'error' => 'Permission denied']);
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
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $role = $data['role'] ?? 'staff';
    
    if (empty($name) || empty($email)) {
        echo json_encode(['success' => false, 'error' => 'Name and email required']);
        exit;
    }
    
    // Check if email exists for another user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM staff WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'error' => 'Email already exists']);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE staff SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->execute([$name, $email, $role, $id]);
    
    echo json_encode(['success' => true, 'message' => 'Staff updated successfully']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>