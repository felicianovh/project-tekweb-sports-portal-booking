<div class="max-w-6xl mx-auto fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-black text-blue-900">Riwayat Semua Booking</h1>
            <p class="text-slate-500">Daftar seluruh transaksi member.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Member</th>
                        <th class="p-4">Fasilitas</th>
                        <th class="p-4">Tanggal Booking</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (empty($histories)): ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">Belum ada data booking masuk.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($histories as $h): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4 font-mono text-xs text-slate-500">#<?= $h['booking_id'] ?></td>
                                <td class="p-4 font-bold"><?= htmlspecialchars($h['uname']) ?></td>
                                <td class="p-4 text-blue-900 font-semibold"><?= htmlspecialchars($h['fname']) ?></td>
                                <td class="p-4 text-slate-600">
                                    <?= date('d M Y', strtotime($h['booking_date'])) ?>
                                    <div class="text-xs text-slate-400">
                                        <?= date('H:i', strtotime($h['booking_date'])) ?> WIB
                                    </div>
                                </td>
                                <td class="p-4 font-bold text-slate-700"><?= format_rupiah($h['total_price']) ?></td>
                                <td class="p-4">
                                    <?php
                                    $bg = 'bg-gray-100 text-gray-600';
                                    if ($h['status'] == 'approved') $bg = 'bg-green-100 text-green-700';
                                    if ($h['status'] == 'paid') $bg = 'bg-yellow-100 text-yellow-700';
                                    if ($h['status'] == 'pending') $bg = 'bg-orange-100 text-orange-600';
                                    if ($h['status'] == 'rejected' || $h['status'] == 'cancelled') $bg = 'bg-red-100 text-red-600';
                                    ?>
                                    <span class="<?= $bg ?> px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                        <?= $h['status'] ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <?php if ($h['status'] == 'approved'): ?>
                                        <a href="?p=invoice&id=<?= $h['booking_id'] ?>" class="text-blue-600 hover:text-blue-800 font-bold text-xs border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition">
                                            <i class="fas fa-file-invoice"></i> Invoice
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>