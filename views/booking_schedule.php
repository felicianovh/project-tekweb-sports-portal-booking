<div class="flex items-center gap-4 mb-6"><a href="?p=booking" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow hover:bg-slate-100"><i class="fas fa-arrow-left"></i></a>
    <div>
        <h2 class="text-2xl font-bold text-slate-800"><?= $f['name'] ?></h2>
        <p class="text-slate-500 text-sm">Pilih jadwal bermain</p>
    </div>
</div>
<div class="flex flex-col lg:flex-row gap-8">
    <div class="lg:w-1/3">
        <div class="bg-white p-6 rounded-2xl shadow-sm border sticky top-6">
            <h3 class="font-bold text-slate-800 mb-4 text-center">Pilih Tanggal</h3><input type="text" id="datepicker" class="hidden">
        </div>
    </div>
    <div class="lg:w-2/3">
        <div class="bg-white p-6 rounded-2xl shadow-sm border h-full relative pb-24">
            <h3 class="font-bold text-slate-800 mb-2">Pilih Jam</h3>
            <p class="text-slate-500 text-sm mb-6 border-b pb-4">Slot untuk: <span class="font-bold text-blue-900"><?= date('d F Y', strtotime($date)) ?></span></p>
            <?php if (!$list): ?><div class="p-10 text-center bg-slate-50 rounded-xl border border-dashed text-slate-400"><i class="far fa-calendar-times fa-3x mb-3"></i><br>Tidak ada jadwal tersedia.</div><?php else: ?>
                <form action="backend/process.php" method="POST" id="bookingForm"><input type="hidden" name="act" value="book_now"><input type="hidden" name="fid" value="<?= $fid ?>"><input type="hidden" name="date" value="<?= $date ?>">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
                        <?php foreach ($list as $s): $booked = $s['is_booked'] > 0 || $s['is_available'] == 0;
                            $start = date('H:i', strtotime($s['start_time']));
                            $end = date('H:i', strtotime($s['end_time'])); ?>
                            <?php if ($booked): ?><div class="slot-disabled p-4 rounded-xl text-center border">
                                    <div class="font-bold"><?= $start ?> - <?= $end ?></div>
                                    <div class="text-xs uppercase font-bold mt-1">Booked</div>
                                </div><?php else: ?><div><input type="checkbox" name="slot_ids[]" value="<?= $s['slot_id'] ?>" id="slot_<?= $s['slot_id'] ?>" class="hidden slot-checkbox" onchange="calcTotal()"><label for="slot_<?= $s['slot_id'] ?>" class="block bg-white border border-slate-200 text-slate-600 p-4 rounded-xl text-center cursor-pointer hover:border-blue-900 transition select-none">
                                        <div class="font-bold"><?= $start ?> - <?= $end ?></div>
                                        <div class="text-xs font-bold mt-1"><?= format_rupiah($f['price_per_hour']) ?></div>
                                    </label></div><?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="fixed bottom-6 right-6 left-6 md:left-auto md:w-96 bg-blue-900 text-white p-4 rounded-2xl shadow-2xl flex justify-between items-center transform translate-y-full transition-transform duration-300" id="totalBar" style="z-index: 50;">
                        <div>
                            <div class="text-xs text-blue-200 uppercase font-bold">Total Pembayaran</div>
                            <div class="text-xl font-bold" id="totalPrice">Rp 0</div>
                            <div class="text-xs opacity-75" id="totalSlots">0 Slot dipilih</div>
                        </div><?php if (is_auth()): ?><button class="bg-white text-blue-900 px-6 py-2 rounded-lg font-bold hover:bg-blue-50 transition">Bayar</button><?php else: ?><a href="?p=auth/login" class="bg-white text-blue-900 px-6 py-2 rounded-lg font-bold hover:bg-blue-50 transition text-sm">Login</a><?php endif; ?>
                    </div>
                </form><?php endif; ?>
        </div>
    </div>
</div>
<script>
    flatpickr("#datepicker", {
        inline: true,
        defaultDate: "<?= $date ?>",
        minDate: "today",
        onChange: function(d, s) {
            window.location.href = "?p=booking_schedule&fid=<?= $fid ?>&date=" + s;
        }
    });
    const pricePerSlot = <?= $f['price_per_hour'] ?>;

    function calcTotal() {
        const c = document.querySelectorAll('.slot-checkbox:checked').length;
        const t = c * pricePerSlot;
        const b = document.getElementById('totalBar');
        document.getElementById('totalPrice').innerText = 'Rp ' + t.toLocaleString('id-ID');
        document.getElementById('totalSlots').innerText = c + ' Slot dipilih';
        if (c > 0) b.style.transform = 'translateY(0)';
        else b.style.transform = 'translateY(150%)';
    }
</script>