<?php
// email-templates/magazine-approved.php

function getMagazineApprovedEmail($data) {
    $subject = '✅ Magazine Download Approved - Wine & Co. Eswatini';
    
    $downloadLink = isset($data['download_token']) 
        ? 'http://winecoeswatini.free.je/backend/download-magazine.php?token=' . $data['download_token']
        : 'http://winecoeswatini.free.je/downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf';
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Magazine Download Approved</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5ede6; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
            .header { background: #1a6b3c; color: white; padding: 20px; border-radius: 15px 15px 0 0; text-align: center; margin: -30px -30px 20px -30px; }
            .header h1 { margin: 0; font-size: 24px; }
            .header p { margin: 0; opacity: 0.8; }
            .button { display: inline-block; background: #1a6b3c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 40px; font-weight: bold; margin: 10px 0; }
            .button:hover { background: #155a2e; }
            .footer { margin-top: 20px; padding-top: 20px; border-top: 2px solid #f5ede6; text-align: center; font-size: 12px; color: #999; }
            .info-box { background: #f8f4f0; padding: 15px; border-radius: 10px; margin: 15px 0; }
            .success-box { background: #d4edda; padding: 20px; border-radius: 10px; margin: 15px 0; text-align: center; border: 2px solid #1a6b3c; }
            .wine-logo { font-size: 28px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1><span class="wine-logo">🍷</span> Wine & Co. Eswatini</h1>
                <p>Magazine Download Approved</p>
            </div>
            
            <div class="success-box">
                <h2>✅ Your Download Request Has Been Approved!</h2>
                <p>Dear ' . htmlspecialchars($data['name']) . ',</p>
                <p>We are pleased to inform you that your request to download the <strong>Wine&Co. Boutique Magazine</strong> has been approved.</p>
            </div>
            
            <div class="info-box">
                <h3>📖 Download Your Magazine</h3>
                <p>Click the button below to download your copy of the Wine&Co. Boutique Magazine.</p>
                <div style="text-align: center;">
                    <a href="' . $downloadLink . '" class="button">📥 Download Magazine</a>
                </div>
                <p style="font-size: 12px; color: #666; text-align: center; margin-top: 10px;">
                    🔒 This download link is valid for 7 days.
                </p>
            </div>
            
            <div class="info-box">
                <h3>📋 Order Details</h3>
                <p><strong>Request ID:</strong> #' . ($data['request_id'] ?? 'N/A') . '</p>
                <p><strong>Date:</strong> ' . date('d M Y H:i') . '</p>
                <p><strong>Fee Paid:</strong> E' . ($data['fee'] ?? '45.00') . '</p>
            </div>
            
            <div style="text-align: center; margin: 20px 0;">
                <p style="color: #666;">Thank you for choosing Wine & Co. Eswatini!</p>
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
    
    return ['subject' => $subject, 'message' => $message];
}
?>