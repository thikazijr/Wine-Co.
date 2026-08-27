<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Unauthorized access');
}

$file = isset($_GET['file']) ? $_GET['file'] : '';

if (empty($file)) {
    die('No file specified');
}

// Get just the filename (remove any path)
$filename = basename($file);

// Try to find the file in multiple locations
$searchPaths = [
    __DIR__ . '/../uploads/pop/' . $filename,
    __DIR__ . '/../uploads/' . $filename,
    __DIR__ . '/../' . $filename,
    __DIR__ . '/' . $filename
];

$filePath = null;
foreach ($searchPaths as $path) {
    if (file_exists($path)) {
        $filePath = $path;
        break;
    }
}

if (!$filePath) {
    // If file not found, show error with debug info
    echo "<h3>File not found</h3>";
    echo "<p>Searched in these locations:</p>";
    echo "<ul>";
    foreach ($searchPaths as $path) {
        echo "<li>" . $path . " " . (file_exists($path) ? '✅ Found!' : '❌ Not found') . "</li>";
    }
    echo "</ul>";
    echo "<p><strong>Filename:</strong> " . htmlspecialchars($filename) . "</p>";
    echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
    exit;
}

// Get file extension and set correct mime type
$fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'pdf' => 'application/pdf',
    'bmp' => 'image/bmp',
    'webp' => 'image/webp'
];

$mimeType = $mimeTypes[$fileExt] ?? 'application/octet-stream';

// Display the file
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

readfile($filePath);
exit;
?>