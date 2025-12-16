<h2 class="text-2xl font-bold mb-4">Generator Jadwal</h2>
<div class="bg-white p-6 rounded-xl shadow border mb-6">
    <form action="backend/process.php" method="POST" class="grid md:grid-cols-2 gap-4">
        <input type="hidden" name="act" value="generate_slots">
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">
                Fasilitas
            </label>
            <select name="facility_id" class="border p-3 rounded-lg w-full">
                <?php foreach ($fs as $f) echo "<option value='{$f['facility_id']}'>{$f['name']}</option>"; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Tanggal</label>
            <div class="flex gap-2"><input type="date" name="start_date" class="border p-3 rounded-lg w-full" required><input type="date" name="end_date" class="border p-3 rounded-lg w-full" required></div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Jam</label>
            <div class="flex gap-2"><input type="time" name="open_time" value="08:00" class="border p-3 rounded-lg w-full"><input type="time" name="close_time" value="22:00" class="border p-3 rounded-lg w-full"></div>
        </div>
        <div class="flex items-end">
            <button class="bg-blue-900 text-white p-3 rounded-lg font-bold w-full hover:bg-blue-800">GENERATE</button>
        </div>
    </form>
</div>