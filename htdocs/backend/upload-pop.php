<?php
session_start();
header('Content-Type: application/json');

// Allow from any origin
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['pop_file']) || $_FILES['pop_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['pop_file'];
$sessionId = $_POST['sessionId'] ?? '';
$orderId = $_POST['order_id'] ?? 0;
$customerEmail = $_POST['customer_email'] ?? 'guest';

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'application/pdf'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Please upload JPG, PNG, GIF, BMP, or PDF.']);
    exit;
}

// Validate file size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'File size must be less than 5MB.']);
    exit;
}

// Create upload directory if it doesn't exist (using absolute path)
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/pop/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'POP_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;
$filePath = '/uploads/pop/' . $filename;
$fullPath = $uploadDir . $filename;

// Move the file
if (move_uploaded_file($file['tmp_name'], $fullPath)) {
    // If order_id is provided, update the order with pop_file
    if ($orderId > 0) {
        try {
            // Check if pop_file column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'pop_file'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE orders ADD COLUMN pop_file VARCHAR(255) NULL");
            }
            $stmt = $pdo->prepare("UPDATE orders SET pop_file = ? WHERE id = ?");
            $stmt->execute([$filePath, $orderId]);
        } catch(PDOException $e) {
            // Column might already exist, ignore
        }
    }
    
    echo json_encode([
        'success' => true,
        'fileName' => $filename,
        'filePath' => $filePath,
        'fullPath' => $fullPath,
        'message' => 'File uploaded successfully'
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save file. Please check folder permissions.']);
}
?>