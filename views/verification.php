<h2 class="text-2xl font-bold mb-6">Kelola Booking & Verifikasi</h2>

<div class="grid gap-6 md:grid-cols-2">
    <?php if (!$vs) echo "<p class='col-span-2 text-slate-400 text-center py-10 bg-white rounded-xl border border-dashed'>Tidak ada data pending/verifikasi.</p>"; ?>

    <?php foreach ($vs as $v):
        $is_paid = ($v['status'] == 'paid');
    ?>
        <div class="bg-white p-6 rounded-xl shadow-sm border <?= $is_paid ? 'border-blue-200' : 'border-yellow-200' ?>">

            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="text-xs font-bold uppercase px-2 py-1 rounded <?= $is_paid ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' ?>">
                        <?= $v['status'] ?>
                    </span>
                    <div class="font-bold text-lg mt-2"><?= $v['fname'] ?></div>
                    <div class="text-sm text-slate-500"><i class="fas fa-user mr-1"></i> <?= $v['uname'] ?></div>
                    <div class="text-xs text-slate-400 mt-1"><?= $v['booking_date'] ?></div>
                </div>
                <div class="font-bold text-lg text-green-600"><?= format_rupiah($v['total_price']) ?></div>
            </div>

            <?php if ($is_paid): ?>
                <a href="<?= $v['proof_of_payment'] ?>" target="_blank" class="block w-full text-center border border-dashed border-blue-300 bg-blue-50 text-blue-700 py-3 rounded-lg text-sm font-bold mb-4 hover:bg-blue-100 transition">
                    <i class="fas fa-image mr-2"></i> Lihat Bukti Transfer
                </a>
                <div class="flex gap-3">
                    <form action="backend/process.php" method="POST" class="flex-1">
                        <input type="hidden" name="act" value="verify">
                        <input type="hidden" name="id" value="<?= $v['booking_id'] ?>">
                        <button name="action" value="reject" class="w-full bg-red-100 text-red-600 py-2 rounded-lg font-bold hover:bg-red-200">Tolak</button>
                    </form>
                    <form action="backend/process.php" method="POST" class="flex-1">
                        <input type="hidden" name="act" value="verify">
                        <input type="hidden" name="id" value="<?= $v['booking_id'] ?>">
                        <button name="action" value="approve" class="w-full bg-green-600 text-white py-2 rounded-lg font-bold hover:bg-green-700">Terima</button>
                    </form>
                </div>

            <?php else: ?>
                <div class="text-sm text-center text-slate-500 italic mb-4 bg-slate-50 p-2 rounded">
                    User belum upload bukti bayar.
                </div>
                <form action="backend/process.php" method="POST" onsubmit="return confirm('Batalkan booking ini secara paksa?')">
                    <input type="hidden" name="act" value="cancel_booking">
                    <input type="hidden" name="id" value="<?= $v['booking_id'] ?>">
                    <button class="w-full bg-slate-100 border border-slate-300 text-slate-600 py-2 rounded-lg font-bold hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                        <i class="fas fa-ban mr-1"></i> Cancel Booking (Fiktif)
                    </button>
                </form>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
</div>