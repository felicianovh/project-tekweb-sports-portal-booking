<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Sport Booking</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .hover-dark-blue:hover {
            background-color: #1e3a8a !important;
            color: white !important;
        }

        .nav-item.active {
            background-color: #1e3a8a;
            color: white;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.4);
        }

        .flatpickr-calendar {
            box-shadow: none !important;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            width: 100% !important;
            max-width: 100%;
            margin-bottom: 20px;
        }

        .flatpickr-day.selected {
            background: #1e3a8a !important;
            border-color: #1e3a8a !important;
        }

        .slot-checkbox:checked+label {
            background-color: #1e3a8a;
            color: white;
            border-color: #1e3a8a;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .slot-disabled {
            background-color: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
            border: 1px solid #e2e8f0;
        }

        div:where(.swal2-container) div:where(.swal2-popup) {
            font-family: 'Inter', sans-serif !important;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #invoiceArea,
            #invoiceArea * {
                visibility: visible;
            }

            #invoiceArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>

    <script>
        function konfirmasiCancel(urlTujuan) {
            Swal.fire({
                title: 'Konfirmasi Cancel',
                text: "Apakah Anda yakin ingin membatalkan/menolak data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Cancel!',
                cancelButtonText: 'Tidak, Kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlTujuan;
                }
            })
        }
    </script>
</head>

<body class="text-slate-800">

    <?php if (isset($_GET['err'])): ?><script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '<?= htmlspecialchars($_GET['err']) ?>',
                confirmButtonColor: '#1e3a8a'
            });
        </script><?php endif; ?>
    <?php if (isset($_GET['success'])): ?><script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= htmlspecialchars($_GET['success']) ?>',
                confirmButtonColor: '#1e3a8a'
            });
        </script><?php endif; ?>

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-white border-r hidden md:flex flex-col z-20 shadow-xl h-full">

            <div class="h-16 flex items-center px-6 border-b shrink-0">
                <span class="text-xl font-black text-blue-900">SPORT<span class="text-slate-800">PORTAL</span></span>
            </div>

            <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">
                <div class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase">Menu</div>

                <a href="?p=dashboard" class="nav-item flex items-center px-3 py-2.5 rounded-lg font-medium text-slate-600 transition hover-dark-blue <?= $page == 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home w-6"></i> Dashboard
                </a>

                <a href="?p=booking" class="nav-item flex items-center px-3 py-2.5 rounded-lg font-medium text-slate-600 transition hover-dark-blue <?= $page == 'booking' || $page == 'booking_schedule' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt w-6"></i> Katalog
                </a>

                <?php if (is_auth() && !is_admin()): ?>
                    <div class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase">Member</div>
                    <a href="?p=history" class="nav-item flex items-center px-3 py-2.5 rounded-lg font-medium text-slate-600 transition hover-dark-blue <?= $page == 'history' ? 'active' : '' ?>">
                        <i class="fas fa-history w-6"></i> Riwayat
                    </a>
                <?php endif; ?>

                <?php if (is_admin()): ?>
                    <div class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase">Admin</div>

                    <a href="?p=facilities" class="nav-item flex items-center px-3 py-2.5 rounded-lg font-medium text-slate-600 transition hover-dark-blue <?= $page == 'facilities' ? 'active' : '' ?>">
                        <i class="fas fa-warehouse w-6"></i> Fasilitas
                    </a>

                    <a href="?p=slots" class="nav-item flex items-center px-3 py-2.5 rounded-lg font-medium text-slate-600 transition hover-dark-blue <?= $page == 'slots' ? 'active' : '' ?>">
                        <i class="fas fa-clock w-6"></i> Jadwal
                    </a>

                    <a href="?p=verification" class="nav-item flex items-center px-3 py-2.5 rounded-lg font-medium text-slate-600 transition hover-dark-blue <?= $page == 'verification' ? 'active' : '' ?>">
                        <i class="fas fa-check-double w-6"></i> Verifikasi
                    </a>

                    <a href="?p=reports" class="nav-item flex items-center px-3 py-2.5 rounded-lg font-medium text-slate-600 transition hover-dark-blue <?= $page == 'reports' ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar w-6"></i> Laporan
                    </a>

                    <a href="?p=admin_history" class="nav-item flex items-center px-3 py-2.5 rounded-lg font-medium text-slate-600 transition hover-dark-blue <?= $page == 'admin_history' ? 'active' : '' ?>">
                        <i class="fas fa-list-alt w-6"></i> Riwayat Booking
                    </a>
                <?php endif; ?>
            </nav>

            <div class="p-4 border-t bg-slate-50 shrink-0">
                <?php if (is_auth()): ?>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-900 text-white flex items-center justify-center font-bold">
                            <?= substr($_SESSION['name'], 0, 1) ?>
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-bold text-sm truncate"><?= $_SESSION['name'] ?></div>
                            <div class="text-xs text-slate-500 uppercase"><?= $_SESSION['role'] ?></div>
                        </div>
                    </div>
                    <a href="?p=logout" class="block w-full text-center bg-white border border-red-200 text-red-600 py-2 rounded-lg text-sm font-bold hover:bg-red-50">Logout</a>
                <?php else: ?>
                    <a href="?p=auth/login" class="block w-full text-center bg-blue-900 text-white py-2 rounded-lg text-sm font-bold hover:bg-blue-800">Login Member</a>
                <?php endif; ?>
            </div>
        </aside>

        <main class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-16 bg-white border-b flex items-center justify-between px-6 z-10 shadow-sm shrink-0">
                <div class="md:hidden font-bold text-blue-900">SPORTPORTAL</div>
                <div class="ml-auto">
                    <?= is_auth() ? '<span class="text-sm font-bold text-slate-700">Hi, ' . $_SESSION['name'] . '</span>' : '<a href="?p=auth/login" class="text-blue-900 font-bold hover:underline">Login</a>' ?>
                </div>
            </header>

            <div class="flex-1 overflow-auto p-4 md:p-8 bg-slate-50">
                <div class="max-w-6xl mx-auto fade-in">