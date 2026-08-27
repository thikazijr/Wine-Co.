<?php
// Secure POP file download
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Unauthorized access');
}

$file = isset($_GET['file']) ? $_GET['file'] : '';

if (empty($file)) {
    die('No file specified');
}

// Remove any path traversal attempts
$file = basename($file);

// Define the upload directory
$uploadDir = __DIR__ . '/../uploads/pop/';
$filePath = $uploadDir . $file;

// Check if file exists
if (!file_exists($filePath)) {
    die('File not found: ' . $file);
}

// Get file extension
$fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));

// Set appropriate headers
header('Content-Type: ' . mime_content_type($filePath));
header('Content-Disposition: inline; filename="' . $file . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output the file
readfile($filePath);
exit;
?>