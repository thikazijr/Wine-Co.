<?php
// email-templates/magazine-rejected.php

function getMagazineRejectedEmail($data) {
    $subject = '❌ Magazine Download Request - Wine & Co. Eswatini';
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Magazine Request Status</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5ede6; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
            .header { background: #dc3545; color: white; padding: 20px; border-radius: 15px 15px 0 0; text-align: center; margin: -30px -30px 20px -30px; }
            .header h1 { margin: 0; font-size: 24px; }
            .header p { margin: 0; opacity: 0.8; }
            .button { display: inline-block; background: #722f37; color: white; padding: 12px 30px; text-decoration: none; border-radius: 40px; font-weight: bold; margin: 10px 0; }
            .button:hover { background: #5a232a; }
            .footer { margin-top: 20px; padding-top: 20px; border-top: 2px solid #f5ede6; text-align: center; font-size: 12px; color: #999; }
            .info-box { background: #f8f4f0; padding: 15px; border-radius: 10px; margin: 15px 0; }
            .wine-logo { font-size: 28px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1><span class="wine-logo">🍷</span> Wine & Co. Eswatini</h1>
                <p>Magazine Download Request</p>
            </div>
            
            <h2>📖 Request Status Update</h2>
            <p>Dear ' . htmlspecialchars($data['name']) . ',</p>
            
            <div class="info-box" style="border-left: 4px solid #dc3545;">
                <p>We regret to inform you that your request to download the <strong>Wine&Co. Boutique Magazine</strong> has been declined.</p>
                ' . (!empty($data['reason']) ? '<p><strong>Reason:</strong> ' . htmlspecialchars($data['reason']) . '</p>' : '') . '
            </div>
            
            <div style="text-align: center; margin: 20px 0;">
                <p style="color: #666;">If you have any questions, please contact us.</p>
                <a href="mailto:phumza19952010@gmail.com" class="button">📧 Contact Support</a>
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