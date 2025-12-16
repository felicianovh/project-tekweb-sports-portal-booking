<?php
// index.php
require_once 'config/database.php';

// Pastikan Zona Waktu sesuai (WIB)
date_default_timezone_set('Asia/Jakarta');

// FITUR AUTO CANCEL
try {
    $pdo->query("UPDATE bookings SET status = 'cancelled'
                 WHERE status = 'pending'
                 AND booking_date < (NOW() - INTERVAL 30 MINUTE)");
} catch (Exception $e) {
}

// ROUTING & LOGIC
$page = $_GET['p'] ?? 'dashboard';

if ($page === 'auth/login') {
    require 'views/auth/login.php';
    exit;
}
if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?p=auth/login");
    exit;
}

$restricted = ['history', 'facilities', 'slots', 'verification', 'reports', 'invoice', 'admin_history'];

if (!is_auth() && in_array($page, $restricted)) {
    redirect("p=auth/login&err=Silakan login");
}

if (is_auth() && !is_admin() && in_array($page, ['facilities', 'slots', 'verification', 'reports', 'admin_history'])) {
    redirect("p=dashboard");
}

$data = [];

// --- DASHBOARD ---
if ($page === 'dashboard') {
    if (is_admin()) {
        $stat1 = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        $stat2 = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='paid'")->fetchColumn();
        $stat3 = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status='approved'")->fetchColumn();
        $val3  = format_rupiah($stat3 ?? 0);
    } else {
        $uid   = $_SESSION['user_id'] ?? 0;
        $stat1 = $pdo->query("SELECT COUNT(*) FROM facilities WHERE is_active=1")->fetchColumn();
        $stat2 = $pdo->query("SELECT COUNT(*) FROM bookings WHERE user_id='$uid' AND status='pending'")->fetchColumn();
        $stat3 = $pdo->query("SELECT COUNT(*) FROM bookings WHERE user_id='$uid' AND status='approved'")->fetchColumn();
        $val3  = $stat3;
    }
    $facs = $pdo->query("SELECT * FROM facilities WHERE is_active=1 LIMIT 10")->fetchAll();
}

// --- CATALOG ---
elseif ($page === 'booking') {
    $facs = $pdo->query("SELECT * FROM facilities WHERE is_active=1")->fetchAll();
}

// --- SCHEDULE ---
elseif ($page === 'booking_schedule') {
    $fid  = $_GET['fid'] ?? 0;
    $date = $_GET['date'] ?? date('Y-m-d');

    $stmt = $pdo->prepare("SELECT * FROM facilities WHERE facility_id=?");
    $stmt->execute([$fid]);
    $f = $stmt->fetch();

    $sql = "SELECT s.*, 
            (SELECT COUNT(*) FROM booking_details bd 
             JOIN bookings b ON bd.booking_id = b.booking_id 
             WHERE bd.slot_id = s.slot_id 
             AND b.status IN ('pending','paid','approved')) as is_booked 
            FROM slots s 
            WHERE facility_id=? AND DATE(start_time)=? 
            ORDER BY start_time";

    $stmt2 = $pdo->prepare($sql);
    $stmt2->execute([$fid, $date]);
    $list_raw = $stmt2->fetchAll();

    $list = [];
    $hari_ini = date('Y-m-d');
    $waktu_sekarang = time();

    foreach ($list_raw as $slot) {
        $jam_saja = date('H:i:s', strtotime($slot['start_time']));
        $waktu_slot_ts = strtotime("$date $jam_saja");
        $batas_cutoff = $waktu_slot_ts - 600;

        if ($date !== $hari_ini) {
            $list[] = $slot;
        } else {
            if ($waktu_sekarang < $batas_cutoff) {
                $list[] = $slot;
            }
        }
    }
}

// --- HISTORY (MEMBER) ---
elseif ($page === 'history' && is_auth()) {
    $stmt = $pdo->prepare("SELECT b.*, f.name as fname FROM bookings b JOIN facilities f ON b.facility_id=f.facility_id WHERE b.user_id=? ORDER BY b.booking_date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $rows = $stmt->fetchAll();
}

// --- INVOICE ---
elseif ($page === 'invoice' && is_auth()) {
    $bid = $_GET['id'];
    $q = $pdo->prepare("SELECT b.*, f.name as fname, u.name as uname, u.email FROM bookings b JOIN facilities f ON b.facility_id=f.facility_id JOIN users u ON b.user_id=u.user_id WHERE b.booking_id=? AND b.user_id=? AND b.status='approved'");

    // Logika agar admin bisa lihat invoice siapapun
    if (is_admin()) {
        $q = $pdo->prepare("SELECT b.*, f.name as fname, u.name as uname, u.email FROM bookings b JOIN facilities f ON b.facility_id=f.facility_id JOIN users u ON b.user_id=u.user_id WHERE b.booking_id=?");
        $q->execute([$bid]);
    } else {
        $q->execute([$bid, $_SESSION['user_id']]);
    }

    $inv = $q->fetch();

    if (!$inv) redirect("p=history&err=Invoice tidak ditemukan");

    $slots_stmt = $pdo->prepare("SELECT s.start_time, s.end_time, bd.price 
                                 FROM booking_details bd 
                                 JOIN slots s ON bd.slot_id=s.slot_id 
                                 WHERE bd.booking_id=? 
                                 ORDER BY s.start_time");
    $slots_stmt->execute([$bid]);
    $details = $slots_stmt->fetchAll();
}

// --- ADMIN PAGES ---
elseif (is_admin()) {
    if ($page === 'facilities') {
        $fs = $pdo->query("SELECT * FROM facilities")->fetchAll();
    } elseif ($page === 'slots') {
        $fs = $pdo->query("SELECT * FROM facilities")->fetchAll();
    } elseif ($page === 'verification') {
        $vs = $pdo->query("SELECT b.*, u.name as uname, f.name as fname 
                           FROM bookings b 
                           JOIN users u ON b.user_id=u.user_id 
                           JOIN facilities f ON b.facility_id=f.facility_id 
                           WHERE b.status IN ('paid', 'pending') 
                           ORDER BY b.booking_date DESC")->fetchAll();
    } elseif ($page === 'reports') {
        $inc = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status='approved'")->fetchColumn() ?? 0;
        $rows = $pdo->query("SELECT f.name, COUNT(*) as cnt, SUM(b.total_price) as tot FROM bookings b JOIN facilities f ON b.facility_id=f.facility_id WHERE b.status='approved' GROUP BY f.facility_id ORDER BY tot DESC")->fetchAll();

    } elseif ($page === 'admin_history') {
        $histories = $pdo->query("SELECT b.*, u.name as uname, f.name as fname 
                                  FROM bookings b 
                                  JOIN users u ON b.user_id = u.user_id 
                                  JOIN facilities f ON b.facility_id = f.facility_id 
                                  ORDER BY b.booking_date DESC")->fetchAll();
    }
}

require 'views/layout/header.php';

$viewPath = "views/$page.php";
if (file_exists($viewPath)) {
    require $viewPath;
} else {
    echo "<div class='container p-4 text-center text-danger font-weight-bold'>Halaman '$page' tidak ditemukan.</div>";
}

require 'views/layout/footer.php';

