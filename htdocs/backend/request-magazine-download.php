<?php
// backend/request-magazine-download.php - Handle magazine download requests

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Include email functions
require_once 'send-email-simple.php';
require_once 'email-templates/magazine-request.php';

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['name']) || empty($data['email'])) {
    echo json_encode(['success' => false, 'error' => 'Name and email are required']);
    exit;
}

try {
    // Create table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS magazine_download_requests (
        id INT PRIMARY KEY AUTO_INCREMENT,
        customer_name VARCHAR(200) NOT NULL,
        customer_email VARCHAR(200) NOT NULL,
        customer_phone VARCHAR(50),
        payment_method VARCHAR(50) DEFAULT 'cash',
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        approved_at TIMESTAMP NULL,
        ip_address VARCHAR(45),
        download_token VARCHAR(100),
        notes TEXT
    )");

    // Generate download token
    $token = bin2hex(random_bytes(32));
    
    $stmt = $pdo->prepare("INSERT INTO magazine_download_requests (customer_name, customer_email, customer_phone, payment_method, status, ip_address, download_token) VALUES (?, ?, ?, ?, 'pending', ?, ?)");
    $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'] ?? '',
        $data['payment_method'] ?? 'cash',
        $_SERVER['REMOTE_ADDR'] ?? '',
        $token
    ]);
    
    $requestId = $pdo->lastInsertId();
    
    // ============================================================
    // SEND EMAIL NOTIFICATION TO ADMIN
    // ============================================================
    $adminEmailData = [
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'] ?? 'Not provided',
        'payment_method' => $data['payment_method'] ?? 'cash',
        'request_id' => $requestId,
        'fee' => '45.00'
    ];
    
    $email = getMagazineRequestEmail($adminEmailData);
    
    // Send to admin
    $adminEmail = ADMIN_EMAIL;
    $adminName = ADMIN_NAME;
    
    $emailSent = sendEmailSMTP($adminEmail, $adminName, $email['subject'], $email['message'], true);
    
    // ============================================================
    // SEND CONFIRMATION EMAIL TO CUSTOMER
    // ============================================================
    $customerSubject = '📖 Magazine Download Request Received - Wine & Co. Eswatini';
    $customerMessage = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body { font-family: Arial, sans-serif; background: #f5ede6; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
            .header { background: #722f37; color: white; padding: 20px; border-radius: 15px 15px 0 0; text-align: center; margin: -30px -30px 20px -30px; }
            .header h1 { margin: 0; font-size: 24px; }
            .footer { margin-top: 20px; padding-top: 20px; border-top: 2px solid #f5ede6; text-align: center; font-size: 12px; color: #999; }
            .info-box { background: #f8f4f0; padding: 15px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #c9a03d; }
            .wine-logo { font-size: 28px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1><span class="wine-logo">🍷</span> Wine & Co. Eswatini</h1>
                <p>Request Received</p>
            </div>
            
            <h2>📖 Your Magazine Download Request</h2>
            <p>Dear ' . htmlspecialchars($data['name']) . ',</p>
            <p>We have received your request to download the <strong>Wine&Co. Boutique Magazine</strong>.</p>
            
            <div class="info-box">
                <p><strong>📋 Request ID:</strong> #' . $requestId . '</p>
                <p><strong>📅 Date:</strong> ' . date('d M Y H:i') . '</p>
                <p><strong>💰 Fee:</strong> E45.00</p>
                <p><strong>📌 Status:</strong> <span style="color: #856404;">⏳ Pending Approval</span></p>
            </div>
            
            <p>Our team will review your request and notify you via email once approved.</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <p style="color: #666; font-size: 14px;">Thank you for choosing Wine & Co. Eswatini!</p>
                <p style="color: #666; font-size: 14px;">🍷 Sip responsibly • 18+ only</p>
            </div>
            
            <div class="footer">
                <p>This is an automated notification from Wine & Co. Eswatini.</p>
                <p>&copy; ' . date('Y') . ' Wine & Co. Eswatini. All rights reserved.</p>
                <p><a href="http://winecoeswatini.free.je" style="color: #722f37;">winecoeswatini.free.je</a></p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    sendEmailSMTP($data['email'], $data['name'], $customerSubject, $customerMessage, true);
    
    echo json_encode([
        'success' => true, 
        'requestId' => $requestId,
        'emailSent' => $emailSent
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}
?>