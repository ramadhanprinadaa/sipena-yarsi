<div class="space-y-6">
    {{-- ===== HEADER & FILTER SECTION ===== --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Ringkasan Kehadiran</h3>
            {{-- Asumsi properti $infoPeriodeAktif berasal dari class Livewire --}}
            <p class="text-sm text-gray-500 mt-0.5">
                <i class="fa-regular fa-calendar-check mr-1.5 text-teal-500"></i>
                Periode: <span class="font-medium text-gray-700">{{ $infoPeriodeAktif ?? 'Bulan Berjalan (Juni 2026)' }}</span>
            </p>
        </div>

        {{-- Filter Flowbite --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <i class="fa-regular fa-calendar text-gray-400"></i>
                </div>
                <input type="text" wire:model.live="selectedPeriodeMulai" datepicker datepicker-format="dd/mm/yyyy"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full ps-10 p-2.5"
                    placeholder="Periode Mulai">
            </div>
            <span class="text-gray-400">-</span>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <i class="fa-regular fa-calendar text-gray-400"></i>
                </div>
                <input type="text" wire:model.live="selectedPeriodeSelesai" datepicker datepicker-format="dd/mm/yyyy"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full ps-10 p-2.5"
                    placeholder="Periode Selesai">
            </div>
            @if(isset($selectedPeriodeMulai) || isset($selectedPeriodeSelesai))
                <button type="button" wire:click="$set('selectedPeriodeMulai', null); $set('selectedPeriodeSelesai', null)" class="px-3 py-2 text-sm font-medium text-rose-600 bg-rose-50 rounded-lg hover:bg-rose-100 transition border border-rose-100">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            @endif
        </div>
    </div>

    {{-- ===== KONDISIONAL EMPTY STATE ===== --}}
    {{-- Asumsi $hasPresensiData mengecek apakah data ada di periode tersebut --}}
    @if(isset($hasPresensiData) && !$hasPresensiData)
        <div class="flex flex-col items-center justify-center min-h-[300px] p-8 text-center bg-white border border-gray-100 rounded-2xl shadow-sm">
            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4 shadow-inner border border-gray-100">
                <i class="fa-regular fa-folder-open text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-700 mb-1">Data Presensi Kosong</h3>
            <p class="text-sm text-gray-500 whitespace-pre-line leading-relaxed max-w-sm">
                Belum ada rekam jejak presensi untuk periode yang Anda pilih. Silakan sesuaikan filter tanggal.
            </p>
        </div>
    @else

        {{-- ===== 8 CARDS GRID SECTION ===== --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            {{-- 1. Total Hadir --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group hover:border-emerald-200 transition">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="fa-solid fa-user-check text-7xl text-emerald-600"></i>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-500">Total Hadir</p>
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">20 <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            </div>

            {{-- 2. Tidak Hadir --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group hover:border-rose-200 transition">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="fa-solid fa-user-xmark text-7xl text-rose-600"></i>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 shadow-sm border border-rose-100">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                </div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-500">Tidak Hadir</p>
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">1 <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            </div>

            {{-- 3. Lembur --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group hover:border-indigo-200 transition">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="fa-solid fa-business-time text-7xl text-indigo-600"></i>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100">
                        <i class="fa-solid fa-business-time"></i>
                    </div>
                </div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-500">Frekuensi Lembur</p>
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">3 <span class="text-xs font-medium text-gray-400">Kali</span></h4>
            </div>

            {{-- 4. Cuti --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group hover:border-fuchsia-200 transition">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="fa-solid fa-plane-departure text-7xl text-fuchsia-600"></i>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-fuchsia-50 flex items-center justify-center text-fuchsia-600 shadow-sm border border-fuchsia-100">
                        <i class="fa-solid fa-plane-departure"></i>
                    </div>
                </div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-500">Jumlah Cuti</p>
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">2 <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            </div>

            {{-- 5. Izin --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group hover:border-sky-200 transition">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="fa-solid fa-envelope-open-text text-7xl text-sky-600"></i>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 shadow-sm border border-sky-100">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                </div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-500">Jumlah Izin</p>
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">1 <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            </div>

            {{-- 6. Sakit --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group hover:border-amber-200 transition">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="fa-solid fa-notes-medical text-7xl text-amber-600"></i>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shadow-sm border border-amber-100">
                        <i class="fa-solid fa-notes-medical"></i>
                    </div>
                </div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-500">Jumlah Sakit</p>
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">0 <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            </div>

            {{-- 7. Total Jam Kerja --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group hover:border-blue-200 transition">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="fa-solid fa-stopwatch text-7xl text-blue-600"></i>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                        <i class="fa-solid fa-stopwatch"></i>
                    </div>
                </div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-500">Total Jam Kerja</p>
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">156<span class="text-xs font-medium text-gray-400">h</span> 45<span class="text-xs font-medium text-gray-400">m</span></h4>
            </div>

            {{-- 8. Total Jam Lembur --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group hover:border-violet-200 transition">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="fa-solid fa-clock text-7xl text-violet-600"></i>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600 shadow-sm border border-violet-100">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-500">Total Jam Lembur</p>
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">8<span class="text-xs font-medium text-gray-400">h</span> 30<span class="text-xs font-medium text-gray-400">m</span></h4>
            </div>
        </div>

        {{-- ===== TIMELINE SECTION ===== --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 relative overflow-hidden">
            <h3 class="text-base font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-list-ul text-gray-400"></i>
                Riwayat Presensi 5 Hari Terakhir
            </h3>

            {{--
                Timeline Vertikal.
                Skenario default: Dihitung mundur dari H-1 (Minggu, 31 Mei 2026)
            --}}
            <ol class="relative border-s-2 border-gray-100 ml-4">

                {{-- Item 1: Lembur di Hari Libur (H-1) --}}
                <li class="mb-7 ms-7">
                    <span class="absolute flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-full -start-4 ring-4 ring-white shadow-sm border border-indigo-200">
                        <i class="fa-solid fa-business-time text-xs text-indigo-600"></i>
                    </span>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 gap-2">
                        <h3 class="text-base font-bold text-gray-900 flex items-center flex-wrap gap-2">
                            Lembur
                            <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded border border-indigo-200 uppercase tracking-wider">Hari Libur</span>
                        </h3>
                        <time class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Minggu, 31 Mei 2026</time>
                    </div>
                    <p class="text-sm font-medium text-gray-600 mt-1">Check-in: <span class="text-gray-900 font-bold">09:00</span> | Check-out: <span class="text-gray-900 font-bold">14:00</span></p>
                    <p class="text-xs text-gray-400 mt-0.5">Total Jam Lembur: 5h 0m</p>
                </li>

                {{-- Item 2: Hadir Normal (H-3 karena H-2 Sabtu libur) --}}
                <li class="mb-7 ms-7">
                    <span class="absolute flex items-center justify-center w-8 h-8 bg-emerald-100 rounded-full -start-4 ring-4 ring-white shadow-sm border border-emerald-200">
                        <i class="fa-solid fa-check text-xs text-emerald-600"></i>
                    </span>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 gap-2">
                        <h3 class="text-base font-bold text-gray-900 flex items-center flex-wrap gap-2">
                            Hadir Normal
                            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded border border-emerald-200 uppercase tracking-wider">Tepat Waktu</span>
                        </h3>
                        <time class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Jumat, 29 Mei 2026</time>
                    </div>
                    <p class="text-sm font-medium text-gray-600 mt-1">Check-in: <span class="text-gray-900 font-bold">07:45</span> | Check-out: <span class="text-gray-900 font-bold">16:10</span></p>
                    <p class="text-xs text-gray-400 mt-0.5">Total Jam Kerja: 8h 25m</p>
                </li>

                {{-- Item 3: Hadir Kurang Jam / Terlambat (H-4) --}}
                <li class="mb-7 ms-7">
                    <span class="absolute flex items-center justify-center w-8 h-8 bg-amber-100 rounded-full -start-4 ring-4 ring-white shadow-sm border border-amber-200">
                        <i class="fa-solid fa-clock-rotate-left text-xs text-amber-600"></i>
                    </span>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 gap-2">
                        <h3 class="text-base font-bold text-gray-900 flex items-center flex-wrap gap-2">
                            Hadir (Kurang Jam)
                            <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded border border-amber-200 uppercase tracking-wider">Terlambat</span>
                        </h3>
                        <time class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Kamis, 28 Mei 2026</time>
                    </div>
                    <p class="text-sm font-medium text-gray-600 mt-1">Check-in: <span class="text-amber-600 font-bold">08:15</span> | Check-out: <span class="text-gray-900 font-bold">16:00</span></p>
                    <p class="text-xs text-gray-400 mt-0.5">Total Jam Kerja: 7h 45m (Kurang 15m)</p>
                </li>

                {{-- Item 4: Izin (H-5) --}}
                <li class="mb-7 ms-7">
                    <span class="absolute flex items-center justify-center w-8 h-8 bg-sky-100 rounded-full -start-4 ring-4 ring-white shadow-sm border border-sky-200">
                        <i class="fa-solid fa-envelope-open-text text-xs text-sky-600"></i>
                    </span>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 gap-2">
                        <h3 class="text-base font-bold text-gray-900 flex items-center flex-wrap gap-2">
                            Izin
                            <span class="bg-sky-100 text-sky-700 text-[10px] font-bold px-2 py-0.5 rounded border border-sky-200 uppercase tracking-wider">Disetujui</span>
                        </h3>
                        <time class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Rabu, 27 Mei 2026</time>
                    </div>
                    <p class="text-sm font-medium text-gray-600 mt-1 italic">Izin Keperluan Keluarga</p>
                    <p class="text-xs text-gray-400 mt-0.5">Check-in: - | Check-out: -</p>
                </li>

                {{-- Item 5: Tidak Hadir (H-6) --}}
                <li class="ms-7">
                    <span class="absolute flex items-center justify-center w-8 h-8 bg-rose-100 rounded-full -start-4 ring-4 ring-white shadow-sm border border-rose-200">
                        <i class="fa-solid fa-user-xmark text-xs text-rose-600"></i>
                    </span>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 gap-2">
                        <h3 class="text-base font-bold text-gray-900 flex items-center flex-wrap gap-2">
                            Tidak Hadir
                            <span class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded border border-rose-200 uppercase tracking-wider">Tanpa Keterangan</span>
                        </h3>
                        <time class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Selasa, 26 Mei 2026</time>
                    </div>
                    <p class="text-sm font-medium text-rose-600 mt-1">Tidak ada data absensi masuk dan keluar.</p>
                </li>

            </ol>
        </div>

    @endif
</div>