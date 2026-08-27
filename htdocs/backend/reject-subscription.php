<?php
session_start();
header('Content-Type: application/json');

// Check if admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
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
    $reason = $data['reason'] ?? 'No reason provided';
    
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'Invalid request ID']);
        exit;
    }
    
    // Get request details
    $stmt = $pdo->prepare("SELECT * FROM subscription_requests WHERE id = ?");
    $stmt->execute([$id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        echo json_encode(['success' => false, 'error' => 'Request not found']);
        exit;
    }
    
    // Update status to cancelled
    $stmt = $pdo->prepare("UPDATE subscription_requests SET 
        status = 'cancelled', 
        admin_notes = ?,
        processed_at = NOW() 
        WHERE id = ?");
    $stmt->execute([$reason, $id]);
    
    // Send rejection email
    $email_sent = sendRejectionEmail(
        $request['email'],
        $request['full_name'],
        $request['plan_name'],
        $reason
    );
    
    echo json_encode([
        'success' => true,
        'email_sent' => $email_sent
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function sendRejectionEmail($to, $name, $plan_name, $reason) {
    $subject = "Wine & Co. - Subscription Update";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #f5ede6; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
            .header { background: #dc3545; color: white; padding: 20px; border-radius: 12px 12px 0 0; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px 0; }
            .footer { text-align: center; color: #888; font-size: 12px; padding-top: 20px; border-top: 1px solid #eee; }
            .reason-box { background: #f8f4f0; padding: 15px 20px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #dc3545; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>📋 Subscription Update</h1>
            </div>
            <div class='content'>
                <h2>Dear $name,</h2>
                <p>Thank you for your interest in the <strong>Wine & Co. Club</strong>.</p>
                
                <p>We regret to inform you that your subscription request for the <strong>$plan_name</strong> plan could not be approved at this time.</p>
                
                <div class='reason-box'>
                    <h4>📝 Reason:</h4>
                    <p>$reason</p>
                </div>
                
                <p>If you have any questions or would like to discuss this further, please don't hesitate to contact us.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://winecoeswatini.free.je' class='btn' style='background: #722f37; color: white; padding: 12px 30px; border-radius: 40px; text-decoration: none; display: inline-block;'>Visit Our Website</a>
                </div>
                
                <p>You are welcome to reapply or try a different subscription plan at any time.</p>
                
                <p>Best regards,<br>The Wine & Co. Team</p>
            </div>
            <div class='footer'>
                <p>© 2025 Wine & Co. — All prices in Swaziland Lilangeni (E/SZL)</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: Wine & Co. <hello@wineco.co.sz>" . "\r\n";
    $headers .= "Reply-To: hello@wineco.co.sz" . "\r\n";
    
    $result = mail($to, $subject, $message, $headers);
    error_log("Rejection email sent to $to: " . ($result ? 'SUCCESS' : 'FAILED'));
    
    return $result;
}
?>