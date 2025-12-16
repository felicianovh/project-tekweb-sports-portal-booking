<h2 class="text-2xl font-bold mb-4">Kelola Fasilitas</h2>
<div class="bg-white p-6 rounded-xl shadow border mb-6">
    <form action="backend/process.php" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
        <input type="hidden" name="act" value="save_facility">
        <input name="name" placeholder="Nama" class="border p-3 rounded-lg w-full bg-slate-50" required>
        <input name="price" type="number" placeholder="Harga" class="border p-3 rounded-lg w-full bg-slate-50" required>
        <textarea name="desc" placeholder="Desc" class="border p-3 rounded-lg w-full bg-slate-50 md:col-span-2"></textarea>
        <div class="md:col-span-2"><input type="file" name="img" class="border p-2 w-full rounded-lg"></div>
        <button class="bg-blue-900 text-white p-3 rounded-lg font-bold hover:bg-blue-800 md:col-span-2">Simpan</button>
    </form>
</div>
<div class="grid gap-6 md:grid-cols-3">
    <?php foreach ($fs as $f): ?>
        <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
            <div class="h-32 bg-slate-200 relative"><?php if ($f['image_path']): ?><img src="<?= $f['image_path'] ?>" class="w-full h-full object-cover"><?php endif; ?></div>
            <div class="p-4">
                <h3 class="font-bold text-lg"><?= $f['name'] ?></h3>
                <p class="text-slate-500 text-sm mb-3"><?= format_rupiah($f['price_per_hour']) ?></p>
                <form action="backend/process.php" method="POST" onsubmit="return confirm('Hapus?')">
                    <input type="hidden" name="act" value="del_facility">
                    <input type="hidden" name="id" value="<?= $f['facility_id'] ?>">
                    <button class="text-red-500 text-sm font-bold hover:underline">Hapus</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>