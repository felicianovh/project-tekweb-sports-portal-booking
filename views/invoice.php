<?php
// --- LOGIKA PENGGABUNGAN WAKTU & HARGA ---
$merged_details = [];
foreach ($details as $d) {
    // Ambil index array terakhir
    $last_idx = count($merged_details) - 1;

    // Cek apakah slot ini adalah lanjutan dari slot sebelumnya
    // Syarat: Array tidak kosong DAN Waktu Selesai slot sebelumnya == Waktu Mulai slot sekarang
    if ($last_idx >= 0 && $merged_details[$last_idx]['end_time'] === $d['start_time']) {
        // GABUNGKAN: Update waktu selesai menjadi waktu selesai slot ini
        $merged_details[$last_idx]['end_time'] = $d['end_time'];
        // JUMLAHKAN HARGA: Tambahkan harga slot ini ke total harga baris tersebut
        // Pastikan Anda sudah update query SQL index.php sesuai instruksi di atas
        $merged_details[$last_idx]['price'] += $d['price'] ?? 0;
    } else {
        // BARIS BARU: Jika waktunya putus atau ini data pertama
        $merged_details[] = [
            'start_time' => $d['start_time'],
            'end_time'   => $d['end_time'],
            'price'      => $d['price'] ?? 0 // Default 0 jika lupa update query
        ];
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-xl mt-10" id="invoiceArea">
    <div class="text-center border-b pb-6 mb-6">
        <h1 class="text-3xl font-black text-blue-900">INVOICE</h1>
        <p class="text-slate-500">#INV-<?= str_pad($inv['booking_id'], 6, '0', STR_PAD_LEFT) ?></p>
    </div>

    <div class="flex justify-between mb-8">
        <div>
            <p class="text-xs text-slate-400 uppercase font-bold">Ditagihkan Ke</p>
            <p class="font-bold text-lg"><?= $inv['uname'] ?></p>
            <p class="text-slate-500"><?= $inv['email'] ?></p>
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-400 uppercase font-bold">Status</p>
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-sm">LUNAS</span>
        </div>
    </div>

    <table class="w-full mb-8">
        <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase">
            <tr>
                <th class="p-3 text-left">Fasilitas / Waktu</th>
                <th class="p-3 text-right">Harga</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <tr>
                <td class="p-3 font-bold text-blue-900"><?= $inv['fname'] ?></td>
                <td class="p-3"></td>
            </tr>

            <?php foreach ($merged_details as $item): ?>
                <tr>
                    <td class="p-3 text-slate-600 pl-6">
                        <?= date('d M Y, H:i', strtotime($item['start_time'])) ?> - <?= date('H:i', strtotime($item['end_time'])) ?>
                    </td>
                    <td class="p-3 text-right text-slate-700 font-semibold">
                        <?= format_rupiah($item['price']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot class="border-t-2 border-slate-200">
            <tr>
                <td class="p-3 font-bold text-lg">Total Pembayaran</td>
                <td class="p-3 text-right font-black text-xl text-blue-900">
                    <?= format_rupiah($inv['total_price']) ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="text-center no-print">
        <button onclick="window.print()" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-800 transition">
            Cetak Invoice
        </button>
        <a href="?p=history" class="ml-4 text-slate-500 hover:underline">Kembali</a>
    </div>
</div>