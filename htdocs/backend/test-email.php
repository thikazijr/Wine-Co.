<?php
// backend/test-email.php - Test email functionality

require_once 'send-email-simple.php';

echo "<h2>📧 Email Test - Wine & Co. Eswatini</h2>";
echo "<p>Testing email to: <strong>" . ADMIN_EMAIL . "</strong></p>";
echo "<hr>";

// Test data
$testData = [
    'name' => 'Wine & Co. Admin',
    'email' => 'phumza19952010@gmail.com',
    'phone' => '+268 1234 5678',
    'payment_method' => 'cash',
    'fee' => '45.00'
];

// Create a test email
$subject = '✅ Test Email - Wine & Co. Eswatini';
$message = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Email</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5ede6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header { background: #722f37; color: white; padding: 20px; border-radius: 15px 15px 0 0; text-align: center; margin: -30px -30px 20px -30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .success { color: #1a6b3c; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍷 Wine & Co. Eswatini</h1>
            <p>Email Test</p>
        </div>
        
        <h2>✅ Test Email</h2>
        <p>Dear Wine & Co. Admin,</p>
        <p>This is a test email to confirm that the email system is working correctly.</p>
        
        <div style="background: #f8f4f0; padding: 15px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #1a6b3c;">
            <p><strong>📅 Date:</strong> ' . date('d M Y H:i') . '</p>
            <p><strong>📧 From:</strong> ' . SMTP_FROM_EMAIL . '</p>
            <p><strong>🔧 SMTP Host:</strong> ' . SMTP_HOST . '</p>
            <p><strong>🔐 Status:</strong> <span class="success">✅ Working</span></p>
        </div>
        
        <p>If you received this email, your email system is configured correctly!</p>
        
        <div style="text-align: center; margin: 20px 0; padding-top: 20px; border-top: 2px solid #f5ede6; font-size: 12px; color: #999;">
            <p>&copy; ' . date('Y') . ' Wine & Co. Eswatini. All rights reserved.</p>
            <p><a href="http://winecoeswatini.free.je" style="color: #722f37;">winecoeswatini.free.je</a></p>
        </div>
    </div>
</body>
</html>
';

// Send the test email
$result = sendEmailSMTP(ADMIN_EMAIL, ADMIN_NAME, $subject, $message, true);

echo "<h3>Results:</h3>";

if ($result) {
    echo "<p style='color: green; font-size: 18px;'>✅ Test email sent successfully to <strong>" . ADMIN_EMAIL . "</strong></p>";
    echo "<p>📬 Check your inbox (and spam folder) for the test email.</p>";
    echo "<p><strong>📋 Email Subject:</strong> " . $subject . "</p>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ Failed to send test email.</p>";
    echo "<p>Check your email configuration.</p>";
}

echo "<hr>";
echo "<h3>Configuration:</h3>";
echo "<ul>";
echo "<li><strong>SMTP Host:</strong> " . SMTP_HOST . "</li>";
echo "<li><strong>SMTP Port:</strong> " . SMTP_PORT . "</li>";
echo "<li><strong>SMTP Username:</strong> " . SMTP_USERNAME . "</li>";
echo "<li><strong>From Email:</strong> " . SMTP_FROM_EMAIL . "</li>";
echo "<li><strong>From Name:</strong> " . SMTP_FROM_NAME . "</li>";
echo "<li><strong>Admin Email:</strong> " . ADMIN_EMAIL . "</li>";
echo "</ul>";
?>