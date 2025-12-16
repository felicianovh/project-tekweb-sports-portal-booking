<h2 class="text-2xl font-bold mb-4">Laporan</h2>
<div class="bg-gradient-to-r from-blue-900 to-indigo-800 text-white p-8 rounded-2xl shadow-lg mb-8">
    <div>
        <div class="text-blue-200 text-sm font-bold uppercase">Total Pendapatan</div>
        <div class="text-4xl font-black mt-1"><?= format_rupiah($inc) ?></div>
    </div>
</div>
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase">
            <tr>
                <th class="p-4">Fasilitas</th>
                <th class="p-4">Total</th>
                <th class="p-4 text-right">Rp</th>
            </tr>
        </thead>
        <tbody class="divide-y"><?php foreach ($rows as $r): ?><tr>
                    <td class="p-4 font-bold"><?= $r['name'] ?></td>
                    <td class="p-4"><?= $r['cnt'] ?>x</td>
                    <td class="p-4 text-right font-bold text-green-600"><?= format_rupiah($r['tot']) ?></td>
                </tr><?php endforeach; ?></tbody>
    </table>
</div>