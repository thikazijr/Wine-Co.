<?php
define('DB_HOST', 'sql306.infinityfree.com');
define('DB_NAME', 'if0_42164424_if0_42164424_wineco');  // ← CORRECT name!
define('DB_USER', 'if0_42164424');
define('DB_PASS', 'aZ8j5lRv2DjU2');

define('SITE_NAME', 'Wine & Co. Eswatini');
define('SITE_URL', 'http://winecoeswatini.free.je');
define('CURRENCY', 'E');
define('CURRENCY_NAME', 'Lilangeni');

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

function getDB() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function formatPrice($price) {
    return "E" . number_format($price, 2);
}

function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
}
?>