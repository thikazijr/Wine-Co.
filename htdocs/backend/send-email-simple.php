<?php
// backend/send-email-simple.php - Simplified email sending

require_once 'email-config.php';

function sendEmailSMTP($to, $toName, $subject, $message, $isHtml = true) {
    if (!EMAIL_ENABLED) {
        error_log("EMAIL DISABLED: Would have sent to $to with subject: $subject");
        return true;
    }
    
    // Simple mail function
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/' . ($isHtml ? 'html' : 'plain') . '; charset=UTF-8';
    $headers[] = 'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>';
    $headers[] = 'Reply-To: ' . SMTP_FROM_EMAIL;
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    
    $headersString = implode("\r\n", $headers);
    
    $result = mail($to, $subject, $message, $headersString);
    error_log("Email sent to $to: " . ($result ? 'SUCCESS' : 'FAILED'));
    
    return $result;
}

// Fallback function
function sendEmail($to, $toName, $subject, $message, $isHtml = true) {
    return sendEmailSMTP($to, $toName, $subject, $message, $isHtml);
}
?>