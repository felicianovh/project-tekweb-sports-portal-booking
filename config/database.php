<?php
// config/database.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- KONFIGURASI ---
define('BASE_PATH', '/project-tekweb');

define('DB_HOST', 'localhost');
define('DB_NAME', 'portal_booking');
define('DB_USER', 'root');
define('DB_PASS', '');

define('GOOGLE_CLIENT_ID', '364478216304-lqq04lp4liilk9kkr41lfv6t37no4bgv.apps.googleusercontent.com'); 

// --- KONEKSI DB ---
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) { die("Database Error: " . $e->getMessage()); }

// --- HELPER FUNCTIONS ---
function is_auth() { return isset($_SESSION['user_id']); }
function is_admin() { return is_auth() && ($_SESSION['role'] ?? '') === 'admin'; }
// Redirect helper yang otomatis menambahkan BASE_PATH
function redirect($url) { 
    // Kita asumsikan redirect selalu ke index.php
    header("Location: " . BASE_PATH . "/index.php?" . $url); 
    exit; 
}
function format_rupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }
?>