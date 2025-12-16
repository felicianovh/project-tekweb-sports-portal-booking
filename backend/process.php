<?php
// backend/process.php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit('Invalid Request');

$act = $_POST['act'] ?? '';

// --- 1. AUTHENTICATION (Login/Register/Google) ---
if ($act === 'login') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    if ($user && ($_POST['password'] === $user['password'] || password_verify($_POST['password'], $user['password']))) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        redirect("p=dashboard");
    } else {
        redirect("p=auth/login&err=Email atau password salah");
    }
} elseif ($act === 'google_login') {
    $payload = json_decode(base64_decode(explode('.', $_POST['credential'])[1]), true);
     if ($payload && isset($payload['email'])) {
        $email = $payload['email'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$payload['name'], $email, uniqid()]);
            $user_id = $pdo->lastInsertId();
            $role = 'user';
        } else {
            $user_id = $user['user_id'];
            $role = $user['role'];
        }
        $_SESSION['user_id'] = $user_id;
        $_SESSION['name'] = $payload['name'];
        $_SESSION['role'] = $role;
        redirect("p=dashboard");
    }
} elseif ($act === 'register') {
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT)]);
        redirect("p=auth/login&success=Akun dibuat, silakan login");
    } catch (Exception $e) {
        redirect("p=auth/login&type=register&err=Email sudah terdaftar");
    }
}

// --- 2. GENERAL ACTIONS (BISA ADMIN & USER) ---
elseif ($act === 'book_now' && is_auth()) {
    $slot_ids = $_POST['slot_ids'] ?? [];
    if (empty($slot_ids)) redirect("p=booking_schedule&fid=" . $_POST['fid'] . "&date=" . $_POST['date'] . "&err=Pilih minimal 1 slot!");

    try {
        $pdo->beginTransaction();
        $total_price = 0;
        $facility_id = $_POST['fid'];

        // Cek ketersediaan slot
        $placeholders = implode(',', array_fill(0, count($slot_ids), '?'));
        $check = $pdo->prepare("SELECT COUNT(*) FROM booking_details bd JOIN bookings b ON bd.booking_id = b.booking_id WHERE bd.slot_id IN ($placeholders) AND b.status IN ('pending','paid','approved')");
        $check->execute($slot_ids);

        if ($check->fetchColumn() > 0) {
            $pdo->rollBack();
            redirect("p=booking_schedule&fid=" . $facility_id . "&date=" . $_POST['date'] . "&err=Salah satu slot sudah diambil orang lain.");
        }

        $fac = $pdo->prepare("SELECT price_per_hour FROM facilities WHERE facility_id = ?");
        $fac->execute([$facility_id]);
        $price_per_hour = $fac->fetchColumn();
        $total_price = count($slot_ids) * $price_per_hour;

        // Jika ADMIN yang booking, status langsung 'approved' (Opsional, atau tetap pending)
        // Di sini saya buat tetap 'pending' agar seragam, admin bisa approve sendiri nanti.
        $status_awal = is_admin() ? 'approved' : 'pending'; // Ubah jadi 'pending' jika admin mau bayar juga
        
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, facility_id, total_price, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $facility_id, $total_price, $status_awal]);
        $booking_id = $pdo->lastInsertId();

        $stmt_detail = $pdo->prepare("INSERT INTO booking_details (booking_id, slot_id, price) VALUES (?, ?, ?)");
        foreach ($slot_ids as $sid) {
            $stmt_detail->execute([$booking_id, $sid, $price_per_hour]);
        }
        $pdo->commit();
        redirect("p=history&success=Booking Berhasil! Total: " . format_rupiah($total_price));
    } catch (Exception $e) {
        $pdo->rollBack();
        redirect("p=booking_schedule&fid=" . $_POST['fid'] . "&date=" . $_POST['date'] . "&err=Terjadi kesalahan sistem: " . $e->getMessage());
    }
} 
elseif ($act === 'upload_pay' && is_auth()) {
    if (!empty($_FILES['proof']['name'])) {
        $relativePath = 'public/payments/' . uniqid('pay_') . '.' . pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
        $uploadTarget = '../' . $relativePath;
        if (!is_dir('../public/payments')) mkdir('../public/payments', 0777, true);
        move_uploaded_file($_FILES['proof']['tmp_name'], $uploadTarget);
        $pdo->prepare("UPDATE bookings SET proof_of_payment=?, status='paid', payment_date=NOW() WHERE booking_id=? AND user_id=?")->execute([$relativePath, $_POST['id'], $_SESSION['user_id']]);
        redirect("p=history&success=Bukti terkirim, menunggu verifikasi Admin");
    }
    redirect("p=history&err=Pilih file gambar dulu");
}
elseif ($act === 'cancel_booking') {
    $id = $_POST['id'];
    if (is_admin()) {
        $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE booking_id=?")->execute([$id]);
        redirect("p=verification&success=Booking berhasil dibatalkan paksa");
    } else {
        $stmt = $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE booking_id=? AND user_id=? AND status='pending'");
        $stmt->execute([$id, $_SESSION['user_id']]);
        redirect("p=history&success=Booking berhasil dibatalkan");
    }
}

// --- 3. ADMIN ONLY ACTIONS ---
// Letakkan di paling bawah agar tidak memblokir fungsi umum di atas
elseif (is_admin()) {
    if ($act === 'save_facility') {
         $path = $_POST['old_img'] ?? null;
        if (!empty($_FILES['img']['name'])) {
            $relativePath = 'assets/facilities/' . uniqid() . '.' . pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
            $uploadTarget = '../' . $relativePath;
            if (!is_dir('../assets/facilities')) mkdir('../assets/facilities', 0777, true);
            move_uploaded_file($_FILES['img']['tmp_name'], $uploadTarget);
            $path = $relativePath;
        }
        $sql = empty($_POST['id'])
            ? "INSERT INTO facilities (name, description, price_per_hour, image_path, is_active) VALUES (?,?,?,?,1)"
            : "UPDATE facilities SET name=?, description=?, price_per_hour=?, image_path=? WHERE facility_id=?";
        $params = [$_POST['name'], $_POST['desc'], $_POST['price'], $path];
        if (!empty($_POST['id'])) $params[] = $_POST['id'];
        $pdo->prepare($sql)->execute($params);
        redirect("p=facilities&success=Fasilitas Disimpan");
    } elseif ($act === 'del_facility') {
        $pdo->prepare("DELETE FROM facilities WHERE facility_id=?")->execute([$_POST['id']]);
        redirect("p=facilities&success=Dihapus");
    } elseif ($act === 'add_slot') {
        $start = $_POST['date'] . ' ' . $_POST['start'];
        $end = $_POST['date'] . ' ' . $_POST['end'];
        if ($start >= $end) redirect("p=slots&err=Jam mulai harus lebih awal");
        try {
            $pdo->prepare("INSERT INTO slots (facility_id, start_time, end_time) VALUES (?,?,?)")->execute([$_POST['facility_id'], $start, $end]);
            redirect("p=slots&success=Slot Dibuat");
        } catch (Exception $e) {
            redirect("p=slots&err=Gagal (Mungkin slot sudah ada)");
        }
    } elseif ($act === 'del_slot') {
        $pdo->prepare("DELETE FROM slots WHERE slot_id=?")->execute([$_POST['id']]);
        redirect("p=slots&success=Slot Dihapus");
    } elseif ($act === 'generate_slots') {
         $fid = $_POST['facility_id'];
        $start_date = new DateTime($_POST['start_date']);
        $end_date = new DateTime($_POST['end_date']);
        $start_hour = (int) explode(':', $_POST['open_time'])[0];
        $end_hour = (int) explode(':', $_POST['close_time'])[0];
        $count = 0;
        while ($start_date <= $end_date) {
            $current_date_str = $start_date->format('Y-m-d');
            for ($h = $start_hour; $h < $end_hour; $h++) {
                $s_time = sprintf("%s %02d:00:00", $current_date_str, $h);
                $e_time = sprintf("%s %02d:00:00", $current_date_str, $h + 1);
                $cek = $pdo->prepare("SELECT COUNT(*) FROM slots WHERE facility_id=? AND start_time=?");
                $cek->execute([$fid, $s_time]);
                if ($cek->fetchColumn() == 0) {
                    $pdo->prepare("INSERT INTO slots (facility_id, start_time, end_time) VALUES (?,?,?)")->execute([$fid, $s_time, $e_time]);
                    $count++;
                }
            }
            $start_date->modify('+1 day');
        }
        redirect("p=slots&success=Berhasil generate $count slot jadwal!");
    } elseif ($act === 'verify') {
        $status = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
        $pdo->prepare("UPDATE bookings SET status=?, updated_at=NOW() WHERE booking_id=?")->execute([$status, $_POST['id']]);
        redirect("p=verification&success=Status Booking Diperbarui");
    }
}

?>
