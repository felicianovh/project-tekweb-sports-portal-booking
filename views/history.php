<h2 class="text-2xl font-bold mb-6">Riwayat Booking</h2>
<div class="space-y-4">
    <?php foreach ($rows as $r):
        $st = $r['status'];
        // Tentukan warna badge
        $color = 'blue';
        if ($st == 'approved') $color = 'green';
        if ($st == 'pending') $color = 'yellow';
        if ($st == 'cancelled' || $st == 'rejected') $color = 'red';

        // Hitung Deadline
        $deadline = date('Y-m-d H:i:s', strtotime($r['booking_date'] . ' +30 minutes'));

        // Ambil detail jam main
        $d = $pdo->prepare("SELECT s.start_time FROM booking_details bd JOIN slots s ON bd.slot_id=s.slot_id WHERE bd.booking_id=? ORDER BY s.start_time");
        $d->execute([$r['booking_id']]);
        $ds = $d->fetchAll();
        $time_str = $ds ? date('d M Y, H:i', strtotime($ds[0]['start_time'])) . " (" . count($ds) . " Jam)" : "";
    ?>

        <div class="bg-white p-6 rounded-xl border shadow-sm flex flex-col md:flex-row justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-<?= $color ?>-100 text-<?= $color ?>-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-<?= $color ?>-200">
                        <?= $st == 'cancelled' ? 'DIBATALKAN / KADALUARSA' : $st ?>
                    </span>

                    <?php if ($st == 'pending'): ?>
                        <span class="text-xs text-red-600 font-bold bg-red-50 px-2 py-1 rounded border border-red-100">
                            <i class="fas fa-stopwatch animate-pulse mr-1"></i>
                            Bayar dalam: <span class="countdown-timer" data-deadline="<?= $deadline ?>">00:00:00</span>
                        </span>
                    <?php endif; ?>
                </div>

                <h3 class="font-bold text-xl text-slate-800"><?= $r['fname'] ?></h3>
                <p class="text-slate-500 text-sm mt-1 mb-2"><?= $time_str ?></p>
                <div class="font-bold text-lg text-blue-900"><?= format_rupiah($r['total_price']) ?></div>
            </div>

            <div class="flex items-center gap-2">
                <?php if ($st == 'approved'): ?>
                    <a href="?p=invoice&id=<?= $r['booking_id'] ?>" class="bg-blue-50 text-blue-700 px-6 py-2 rounded-lg font-bold text-sm hover:bg-blue-100 border border-blue-200 transition">
                        <i class="fas fa-print mr-2"></i> Invoice
                    </a>
                <?php endif; ?>

                <?php if ($st == 'pending'): ?>
                    <form action="backend/process.php" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-2">
                        <input type="hidden" name="act" value="upload_pay">
                        <input type="hidden" name="id" value="<?= $r['booking_id'] ?>">
                        <input type="file" name="proof" class="text-sm border rounded p-1 w-48 bg-slate-50" required>
                        <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow">
                            Upload
                        </button>
                    </form>

                    <form id="form-cancel-<?= $r['booking_id'] ?>" action="backend/process.php" method="POST">
                        <input type="hidden" name="act" value="cancel_booking">
                        <input type="hidden" name="id" value="<?= $r['booking_id'] ?>">

                        <button type="button" onclick="cancelViaForm(<?= $r['booking_id'] ?>)" class="bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition">
                            Batal
                        </button>
                    </form>

                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($rows)): ?>
        <div class="text-center py-10 text-slate-400">Belum ada riwayat booking.</div>
    <?php endif; ?>
</div>

<script>
    function cancelViaForm(idBooking) {
        Swal.fire({
            title: 'Konfirmasi Cancel',
            text: "Yakin ingin membatalkan booking ini? Slot akan dilepas kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Merah
            cancelButtonColor: '#3085d6', // Biru
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-cancel-' + idBooking).submit();
            }
        })
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        function updateTimers() {
            const timers = document.querySelectorAll('.countdown-timer');

            timers.forEach(timer => {
                const deadlineAttr = timer.getAttribute('data-deadline');
                const deadlineDate = new Date(deadlineAttr.replace(" ", "T"));
                const now = new Date();
                const diff = deadlineDate - now;

                if (diff <= 0) {
                    timer.innerHTML = "Waktu Habis";
                    timer.parentElement.classList.add('text-gray-500');
                    timer.parentElement.classList.remove('text-red-600', 'bg-red-50');

                    if (diff > -2000) {
                        window.location.reload();
                    }
                } else {
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    const h = hours.toString().padStart(2, '0');
                    const m = minutes.toString().padStart(2, '0');
                    const s = seconds.toString().padStart(2, '0');

                    timer.innerHTML = `${h}:${m}:${s}`;
                }
            });
        }

        setInterval(updateTimers, 1000);
        updateTimers();
    });

</script>
