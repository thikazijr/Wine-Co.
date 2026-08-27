<?php
// backend/download-magazine.php - Secure magazine download with token verification

session_start();

// Check if user has a valid token
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    header('Location: ../view-magazine.php?error=invalid_token');
    exit;
}

// Database connection
$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed");
}

// Verify token in database
$stmt = $pdo->prepare("SELECT * FROM magazine_download_requests WHERE download_token = ? AND status = 'approved'");
$stmt->execute([$token]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    header('Location: ../view-magazine.php?error=invalid_token');
    exit;
}

// Check if token is expired (7 days)
$expiryDate = strtotime($request['approved_at']) + (7 * 24 * 60 * 60);
if (time() > $expiryDate) {
    header('Location: ../view-magazine.php?error=token_expired');
    exit;
}

// Get PDF path from settings
$settingsStmt = $pdo->query("SELECT setting_value FROM magazine_settings WHERE setting_key = 'pdf_path'");
$settings = $settingsStmt->fetch(PDO::FETCH_ASSOC);
$pdfPath = $settings['setting_value'] ?? 'downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf';

$file = '../' . $pdfPath;

// Try alternative paths if file not found
if (!file_exists($file)) {
    $alternativePaths = [
        '../downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf',
        '../downloads/WineCo_Boutique_Magazine_Professional_Edition (1).pdf',
        '../downloads/WineCo_Boutique_Magazine.pdf'
    ];
    foreach ($alternativePaths as $altPath) {
        if (file_exists($altPath)) {
            $file = $altPath;
            break;
        }
    }
}

if (!file_exists($file)) {
    die('File not found. Please contact support.');
}

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="WineCo_Boutique_Magazine.pdf"');
header('Content-Length: ' . filesize($file));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Clear output buffer
ob_clean();
flush();

// Read the file
readfile($file);
exit;
?>