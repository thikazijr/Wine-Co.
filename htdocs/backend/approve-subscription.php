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
    
    if ($request['status'] != 'pending') {
        echo json_encode(['success' => false, 'error' => 'Request already processed']);
        exit;
    }
    
    // Update status to approved
    $stmt = $pdo->prepare("UPDATE subscription_requests SET 
        status = 'approved', 
        processed_at = NOW() 
        WHERE id = ?");
    $stmt->execute([$id]);
    
    // Send approval email
    $email_sent = sendApprovalEmail(
        $request['email'],
        $request['full_name'],
        $request['plan_name'],
        $request['price'],
        $request['expiry_date']
    );
    
    echo json_encode([
        'success' => true,
        'message' => 'Subscription approved',
        'email_sent' => $email_sent
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function sendApprovalEmail($to, $name, $plan_name, $price, $expiry_date) {
    $subject = "Wine & Co. - Subscription Approved! 🍷";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #f5ede6; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
            .header { background: #722f37; color: white; padding: 20px; border-radius: 12px 12px 0 0; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px 0; }
            .plan-details { background: #f8f4f0; padding: 15px 20px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #c9a03d; }
            .badge { background: #1a6b3c; color: white; padding: 5px 15px; border-radius: 20px; display: inline-block; }
            .btn { background: #722f37; color: white; padding: 12px 30px; border-radius: 40px; text-decoration: none; display: inline-block; }
            .footer { text-align: center; color: #888; font-size: 12px; padding-top: 20px; border-top: 1px solid #eee; }
            .validity-box { background: #fff3cd; padding: 15px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #ffc107; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🍷 Subscription Approved!</h1>
                <p>Welcome to the Wine & Co. Club</p>
            </div>
            <div class='content'>
                <h2>Dear $name,</h2>
                <p>Great news! Your subscription to the <strong>Wine & Co. Club</strong> has been approved.</p>
                
                <div class='plan-details'>
                    <h3>📋 Your Plan Details</h3>
                    <p><strong>Plan:</strong> $plan_name</p>
                    <p><strong>Price:</strong> E" . number_format($price, 2) . "/month</p>
                    <p><strong>Status:</strong> <span class='badge'>ACTIVE</span></p>
                </div>
                
                <div class='validity-box'>
                    <h4>⏰ 30-Day Validity</h4>
                    <p>Your subscription is valid until: <strong>" . date('d F Y', strtotime($expiry_date)) . "</strong></p>
                    <p>You will receive your first wine box within 3-5 business days.</p>
                </div>
                
                <h4>🎁 What's Included:</h4>
                <ul>
                    <li>✓ Premium wines delivered to your door</li>
                    <li>✓ Tasting notes with every bottle</li>
                    <li>✓ Free delivery within Eswatini</li>
                    <li>✓ Exclusive members-only discounts</li>
                    <li>✓ Cancel anytime</li>
                </ul>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://winecoeswatini.free.je' class='btn'>Visit Our Website</a>
                </div>
                
                <p>If you have any questions, please contact us at <a href='mailto:hello@wineco.co.sz'>hello@wineco.co.sz</a> or call +268 1234 5678.</p>
                
                <p>Cheers,<br>The Wine & Co. Team</p>
            </div>
            <div class='footer'>
                <p>© 2025 Wine & Co. — All prices in Swaziland Lilangeni (E/SZL)</p>
                <p><small>Sip responsibly • 18+ only</small></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: Wine & Co. <hello@wineco.co.sz>" . "\r\n";
    $headers .= "Reply-To: hello@wineco.co.sz" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    // Try to send email
    $result = mail($to, $subject, $message, $headers);
    
    // Log the result
    error_log("Approval email sent to $to: " . ($result ? 'SUCCESS' : 'FAILED'));
    
    return $result;
}
?>