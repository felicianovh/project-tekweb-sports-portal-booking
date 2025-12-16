<?php if (is_admin()): ?>
    <h2 class="text-2xl font-bold text-slate-800 mb-6">Dashboard Admin</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-900">
            <div class="text-slate-500 text-xs font-bold uppercase">Total Booking</div>
            <div class="text-3xl font-black text-slate-800 mt-2"><?= $stat1 ?></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500">
            <div class="text-slate-500 text-xs font-bold uppercase">Perlu Verifikasi</div>
            <div class="text-3xl font-black text-slate-800 mt-2"><?= $stat2 ?></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
            <div class="text-slate-500 text-xs font-bold uppercase">Total Pendapatan</div>
            <div class="text-3xl font-black text-slate-800 mt-2"><?= $val3 ?></div>
        </div>
    </div>
<?php else: ?>
    <h2 class="text-2xl font-bold text-slate-800 mb-6">Dashboard Member</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-900">
            <div class="text-slate-500 text-xs font-bold uppercase">Fasilitas</div>
            <div class="text-3xl font-black text-slate-800 mt-2"><?= $stat1 ?></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500">
            <div class="text-slate-500 text-xs font-bold uppercase">Menunggu Bayar</div>
            <div class="text-3xl font-black text-slate-800 mt-2"><?= $stat2 ?></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
            <div class="text-slate-500 text-xs font-bold uppercase">Siap Main</div>
            <div class="text-3xl font-black text-slate-800 mt-2"><?= $val3 ?></div>
        </div>
    </div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-lg">Fasilitas Populer</h3><a href="?p=booking" class="text-blue-900 text-sm font-bold hover:underline">Lihat Semua</a>
    </div>
    <div class="flex flex-nowrap overflow-x-auto gap-6 pb-6 w-full snap-x">
        <?php foreach ($facs as $f): ?>
            <a href="?p=booking_schedule&fid=<?= $f['facility_id'] ?>" class="min-w-[300px] w-[300px] flex-shrink-0 snap-center group bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-lg transition block">
                <div class="h-40 bg-slate-200 relative">
                    <?php if ($f['image_path']): ?>
                        <img src="<?= $f['image_path'] ?>" class="w-full h-full object-cover">
                    <?php endif; ?>
                    <div class="absolute bottom-2 right-2 bg-blue-900 text-white px-3 py-1 rounded-lg text-xs font-bold"><?= format_rupiah($f['price_per_hour']) ?></div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-lg text-slate-800 group-hover:text-blue-900 transition truncate"><?= $f['name'] ?></h3>
                    <div class="mt-3 w-full bg-slate-100 text-center py-2 rounded-lg text-sm font-bold text-slate-600 hover:bg-blue-900 hover:text-white transition">Cek Jadwal</div>
                </div>
            </a>
        <?php endforeach; ?>
        <div class="min-w-[1px] w-[1px] flex-shrink-0"></div>
    </div>

<?php endif; ?>
