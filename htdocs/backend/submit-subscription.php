<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
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

// Validate required fields
$required_fields = ['full_name', 'email', 'phone', 'address', 'city', 'plan_id', 'plan_name', 'price'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

// Get form data
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$address = trim($_POST['address']);
$city = trim($_POST['city']);
$plan_id = intval($_POST['plan_id']);
$plan_name = trim($_POST['plan_name']);
$price = floatval($_POST['price']);
$id_number = trim($_POST['id_number'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? 'bank_transfer');

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

// Validate phone (basic)
if (strlen($phone) < 8) {
    echo json_encode(['success' => false, 'error' => 'Please enter a valid phone number']);
    exit;
}

// Handle file upload (POP)
$pop_path = '';
if (isset($_FILES['pop_file']) && $_FILES['pop_file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/pop/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileExt = strtolower(pathinfo($_FILES['pop_file']['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    
    if (!in_array($fileExt, $allowedExts)) {
        echo json_encode(['success' => false, 'error' => 'Invalid file type. Allowed: JPG, PNG, GIF, PDF']);
        exit;
    }
    
    // Check file size (max 5MB)
    if ($_FILES['pop_file']['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'File too large. Maximum 5MB allowed.']);
        exit;
    }
    
    $fileName = 'pop_' . date('Ymd_His') . '_' . uniqid() . '.' . $fileExt;
    $filePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['pop_file']['tmp_name'], $filePath)) {
        $pop_path = '/uploads/pop/' . $fileName;
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to upload payment proof']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Please upload your payment proof (POP)']);
    exit;
}

try {
    // Calculate dates
    $start_date = date('Y-m-d H:i:s');
    $expiry_date = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO subscription_requests (
        plan_id, plan_name, price, full_name, email, phone, address, city, 
        id_number, payment_method, pop_path, start_date, expiry_date, status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    
    $stmt->execute([
        $plan_id,
        $plan_name,
        $price,
        $full_name,
        $email,
        $phone,
        $address,
        $city,
        $id_number,
        $payment_method,
        $pop_path,
        $start_date,
        $expiry_date
    ]);
    
    $request_id = $pdo->lastInsertId();
    
    // Send confirmation email
    $email_sent = sendConfirmationEmail($email, $full_name, $plan_name, $price, $request_id);
    
    echo json_encode([
        'success' => true,
        'request_id' => $request_id,
        'message' => 'Subscription submitted successfully!',
        'expiry_date' => $expiry_date,
        'email_sent' => $email_sent
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

// ==================== EMAIL FUNCTION ====================
function sendConfirmationEmail($to, $name, $plan_name, $price, $request_id) {
    $subject = "Wine & Co. - Subscription Confirmation #$request_id";
    
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
            .footer { text-align: center; color: #888; font-size: 12px; padding-top: 20px; border-top: 1px solid #eee; }
            .badge { background: #1a6b3c; color: white; padding: 5px 15px; border-radius: 20px; display: inline-block; }
            .btn { background: #722f37; color: white; padding: 12px 30px; border-radius: 40px; text-decoration: none; display: inline-block; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🍷 Wine & Co. Club</h1>
                <p>Subscription Confirmation</p>
            </div>
            <div class='content'>
                <h2>Hello $name!</h2>
                <p>Thank you for subscribing to the <strong>Wine & Co. Club</strong>.</p>
                
                <div class='plan-details'>
                    <h3>📋 Plan Details</h3>
                    <p><strong>Plan:</strong> $plan_name</p>
                    <p><strong>Price:</strong> E" . number_format($price, 2) . "/month</p>
                    <p><strong>Status:</strong> <span class='badge'>Pending Approval</span></p>
                    <p><strong>Request ID:</strong> #$request_id</p>
                </div>
                
                <div style='background: #fff3cd; padding: 15px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #ffc107;'>
                    <h4>⏰ 30-Day Validity</h4>
                    <p>Your subscription will be active for <strong>30 days</strong> from the date of approval.</p>
                    <p>You will receive a confirmation email once your payment is verified.</p>
                </div>
                
                <p><strong>Next Steps:</strong></p>
                <ol>
                    <li>We will verify your payment within 24-48 hours</li>
                    <li>You will receive a confirmation email with your subscription details</li>
                    <li>Your first wine box will be delivered within 3-5 business days</li>
                </ol>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://winecoeswatini.free.je' class='btn'>Visit Our Website</a>
                </div>
                
                <p>If you have any questions, please contact us at hello@wineco.co.sz or call +268 1234 5678.</p>
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
    
    // Send email (try both mail() and fallback)
    try {
        return mail($to, $subject, $message, $headers);
    } catch(Exception $e) {
        // Log email error but don't fail the subscription
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}
?>