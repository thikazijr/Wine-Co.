<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/config.php';

session_start();

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM staff WHERE email = ? AND is_active = 1");
$stmt->execute([$email]);
$staff = $stmt->fetch();

if ($staff && password_verify($password, $staff['password'])) {
    $_SESSION['staff_id'] = $staff['id'];
    $_SESSION['staff_name'] = $staff['name'];
    $_SESSION['staff_role'] = $staff['role'];
    
    echo json_encode([
        'success' => true,
        'staff' => [
            'id' => $staff['id'],
            'name' => $staff['name'],
            'email' => $staff['email'],
            'role' => $staff['role']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
}
?>