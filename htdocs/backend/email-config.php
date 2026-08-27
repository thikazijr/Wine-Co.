<?php
// backend/email-config.php - Email configuration for Wine & Co. Eswatini

// Email settings using Gmail SMTP with App Password
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'phumza19952010@gmail.com');
define('SMTP_PASSWORD', 'zgat pupt jaua frgk');
define('SMTP_FROM_EMAIL', 'phumza19952010@gmail.com');
define('SMTP_FROM_NAME', 'Wine & Co. Eswatini');

// Admin email recipient
define('ADMIN_EMAIL', 'phumza19952010@gmail.com');
define('ADMIN_NAME', 'Wine & Co. Admin');

// Email template directory
define('EMAIL_TEMPLATE_DIR', __DIR__ . '/../email-templates/');

// Enable/disable email sending (for testing)
define('EMAIL_ENABLED', true);
?>