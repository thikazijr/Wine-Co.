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
    
    // Check if trying to delete self
    if ($id == $_SESSION['staff_id'] ?? 0) {
        echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
        exit;
    }
    
    // Check role of staff being deleted
    $stmt = $pdo->prepare("SELECT role FROM staff WHERE id = ?");
    $stmt->execute([$id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($staff && $staff['role'] == 'admin' && $_SESSION['admin_role'] != 'admin') {
        echo json_encode(['success' => false, 'error' => 'Only Admin can delete Admin accounts']);
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Staff deleted successfully']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>