<h2 class="text-2xl font-bold text-slate-800 mb-6">Katalog Lapangan</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($facs as $f): ?>
        <a href="?p=booking_schedule&fid=<?= $f['facility_id'] ?>" class="bg-white p-4 rounded-xl shadow-sm border flex items-center gap-4 hover:border-blue-900 transition group">
            <div class="w-20 h-20 bg-slate-200 rounded-lg overflow-hidden flex-shrink-0"><?php if ($f['image_path']): ?><img src="<?= $f['image_path'] ?>" class="w-full h-full object-cover"><?php endif; ?></div>
            <div>
                <h3 class="font-bold text-lg text-slate-800 group-hover:text-blue-900"><?= $f['name'] ?></h3>
                <p class="text-sm text-slate-500"><?= format_rupiah($f['price_per_hour']) ?>/jam</p>
            </div>
        </a>
    <?php endforeach; ?>
</div>