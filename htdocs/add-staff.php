<?php
session_start();

// Only Admin and Manager can add staff
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
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $plainPassword = $data['password'] ?? '';
    $role = $data['role'] ?? 'staff';
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM staff WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'error' => 'Email already exists']);
        exit;
    }
    
    // Hash password (using simple hash for demo - use password_hash in production)
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO staff (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword, $role]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>