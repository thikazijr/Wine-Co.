<?php
// email-templates/magazine-request.php - Admin notification for new request

function getMagazineRequestEmail($data) {
    $subject = '📖 New Magazine Download Request - Wine & Co. Eswatini';
    
    $message = '
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
            .header p { margin: 5px 0 0 0; opacity: 0.8; }
            .info-box { background: #f8f4f0; padding: 15px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #c9a03d; }
            .info-box strong { color: #722f37; }
            .button { display: inline-block; background: #722f37; color: white; padding: 12px 30px; text-decoration: none; border-radius: 40px; font-weight: bold; margin: 10px 0; }
            .button:hover { background: #5a232a; }
            .footer { margin-top: 20px; padding-top: 20px; border-top: 2px solid #f5ede6; text-align: center; font-size: 12px; color: #999; }
            .badge { display: inline-block; background: #c9a03d; color: #1a1a2e; padding: 2px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
            .wine-logo { font-size: 28px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1><span class="wine-logo">🍷</span> Wine & Co. Eswatini</h1>
                <p>Magazine Download Request</p>
            </div>
            
            <h2>📖 New Magazine Download Request</h2>
            <p>A customer has requested to download the <strong>Wine&Co. Boutique Magazine</strong>.</p>
            
            <div class="info-box">
                <p><strong>👤 Name:</strong> ' . htmlspecialchars($data['name']) . '</p>
                <p><strong>📧 Email:</strong> <a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></p>
                <p><strong>📱 Phone:</strong> ' . htmlspecialchars($data['phone'] ?? 'Not provided') . '</p>
                <p><strong>💳 Payment Method:</strong> ' . ucfirst(str_replace('_', ' ', $data['payment_method'] ?? 'Cash')) . '</p>
                <p><strong>📅 Requested:</strong> ' . date('d M Y H:i') . '</p>
                <p><strong>💰 Fee:</strong> E' . ($data['fee'] ?? '45.00') . '</p>
            </div>
            
            <div style="text-align: center;">
                <a href="http://winecoeswatini.free.je/admin/magazine-manager.php" class="button">🔐 Review Request in Admin</a>
            </div>
            
            <div style="text-align: center; margin: 15px 0;">
                <span class="badge">⏳ Pending Approval</span>
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
    
    return ['subject' => $subject, 'message' => $message];
}
?>