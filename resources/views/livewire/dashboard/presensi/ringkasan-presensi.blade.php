<div class="space-y-6">
    {{-- ===== HEADER & FILTER SECTION ===== --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Ringkasan Kehadiran</h3>
            {{-- Asumsi properti $infoPeriodeAktif berasal dari class Livewire --}}
            <p class="text-sm text-gray-500 mt-0.5">
                <i class="fa-regular fa-calendar-check mr-1.5 text-teal-500"></i>
                Periode:
                <span class="font-medium text-gray-700">
                    {{ $this->infoPeriodeAktif }}
                </span>
            </p>
        </div>

        {{-- Filter Tanggal --}}
        <div class="flex flex-wrap items-center gap-2">

            {{-- Periode Mulai --}}
            <div
                x-data="{ picker: null }"
                x-init="picker = new Datepicker($refs.input, {
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    language: 'id'
                });
                $refs.input.addEventListener('changeDate', () => {
                    $wire.set('selectedPeriodeMulai', $refs.input.value);
                });"
                class="relative">

                <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                    <i class="fa-regular fa-calendar text-gray-400"></i>
                </div>
                <input
                    type="text"
                    x-ref="input"
                    wire:model.live="selectedPeriodeMulai"
                    class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-teal-500 focus:border-teal-500 block w-full ps-10 p-2.5"
                    placeholder="Pilih Periode Mulai">
                <button type="button" x-show="$wire.selectedPeriodeMulai"
                    @click="
                    $wire.set('selectedPeriodeMulai', null);
                    picker.setDate({ clear: true });
                    $refs.input.value = '';"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Separator --}}
            <span class="text-gray-400">-</span>

            {{-- Periode Selesai --}}
            <div
                x-data="{ picker: null }"
                x-init="picker = new Datepicker($refs.input, {
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    language: 'id'
                });
                $refs.input.addEventListener('changeDate', () => {
                    $wire.set('selectedPeriodeSelesai', $refs.input.value);
                });"
                class="relative">

                <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                    <i class="fa-regular fa-calendar text-gray-400"></i>
                </div>
                <input
                    type="text"
                    x-ref="input"
                    wire:model.live="selectedPeriodeSelesai"
                    class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-teal-500 focus:border-teal-500 block w-full ps-10 p-2.5"
                    placeholder="Pilih Periode Selesai">
                <button type="button" x-show="$wire.selectedPeriodeSelesai"
                    @click="
                    $wire.set('selectedPeriodeSelesai', null);
                    picker.setDate({ clear: true });
                    $refs.input.value = '';"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    @php $user = auth()->user(); $pegawai = $user->pegawai; @endphp

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
            @if(!$pegawai)
                <h4 class="text-xl font-bold text-gray-800 mt-0.5">N/A <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            @else
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $this->ringkasanData['hadir']  ?? 'N/A'}}
                    <span class="text-xs font-medium text-gray-400">Hari</span>
                </h4>
            @endif
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
            @if(!$pegawai)
                <h4 class="text-xl font-bold text-gray-800 mt-0.5">N/A <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            @else
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $this->ringkasanData['tidak_hadir'] ?? 'N/A' }}
                    <span class="text-xs font-medium text-gray-400">Hari</span>
                </h4>
            @endif
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
            <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-500">Jumlah Lembur</p>
            @if(!$pegawai)
                <h4 class="text-xl font-bold text-gray-800 mt-0.5">N/A <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            @else
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $this->ringkasanData['lembur'] ?? 'N/A' }}
                    <span class="text-xs font-medium text-gray-400">Hari</span>
                </h4>
            @endif
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
            @if(!$pegawai)
                <h4 class="text-xl font-bold text-gray-800 mt-0.5">N/A <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            @else
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $this->ringkasanData['cuti'] ?? 'N/A' }}
                    <span class="text-xs font-medium text-gray-400">Hari</span>
                </h4>
            @endif
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
            @if(!$pegawai)
                <h4 class="text-xl font-bold text-gray-800 mt-0.5">N/A <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            @else
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $this->ringkasanData['izin'] ?? 'N/A' }}
                    <span class="text-xs font-medium text-gray-400">Hari</span>
                </h4>
            @endif
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
            @if(!$pegawai)
                <h4 class="text-xl font-bold text-gray-800 mt-0.5">N/A <span class="text-xs font-medium text-gray-400">Hari</span></h4>
            @else
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $this->ringkasanData['sakit'] ?? 'N/A' }}
                    <span class="text-xs font-medium text-gray-400">Hari</span>
                </h4>
            @endif
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
            @if(!$pegawai)
                <h4 class="text-xl font-bold text-gray-800 mt-0.5">N/A <span class="text-xs font-medium text-gray-400">h</span> <span class="text-xs font-medium text-gray-400">m</span></h4>
            @else
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $this->ringkasanData['total_jam_kerja']['jam']  ?? 0 }}
                    <span class="text-sm font-semibold text-gray-400 mr-1">h</span>

                    {{ $this->ringkasanData['total_jam_kerja']['menit']  ?? 0 }}
                    <span class="text-sm font-semibold text-gray-400">m</span>
                </h4>
            @endif
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
            @if(!$pegawai)
                <h4 class="text-xl font-bold text-gray-800 mt-0.5">N/A <span class="text-xs font-medium text-gray-400">h</span> <span class="text-xs font-medium text-gray-400">m</span></h4>
            @else
                <h4 class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ $this->ringkasanData['total_jam_lembur']['jam'] ?? 0 }}
                    <span class="text-sm font-semibold text-gray-400 mr-1">h</span>
                    {{ $this->ringkasanData['total_jam_lembur']['menit'] ?? 0 }}
                    <span class="text-sm font-semibold text-gray-400">m</span>
                </h4>
            @endif
        </div>
    </div>

    {{-- ===== RIWAYAT 5 HARI TERAKHIR ===== --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 relative overflow-hidden">
        <h4 class="text-base font-bold text-gray-800 mb-5 flex items-center gap-2">
            <i class="fa-solid fa-history text-teal-500 text-sm"></i>
            Riwayat Presensi 5 Hari Terakhir
        </h4>

        @if($this->isRiwayatKosongTotal)
            {{-- HANDLE EMPTY STATE COMPONENT --}}
            <div class="flex flex-col items-center justify-center py-12 px-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 border border-gray-200">
                    <i class="fa-regular fa-folder-open text-2xl text-gray-400"></i>
                </div>
                <h5 class="text-sm font-semibold text-gray-700 text-center">Tidak Ada Data Presensi</h5>
                <p class="text-xs text-gray-400 text-center mt-1 max-w-sm leading-relaxed">
                    {{ $this->emptyStateMessageRiwayat }}
                </p>
            </div>
        @else
            {{-- TIMELINE LIST --}}
            <ol class="relative border-s border-gray-200 ms-3 space-y-6">
                @foreach($this->riwayatTerakhir as $riwayat)

                    {{-- JIKA HARI INI KOSONG (Tapi hari lain ada datanya) --}}
                    @if($riwayat->is_empty_day)
                        <li class="ms-7">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-50 rounded-full -start-4 ring-4 ring-white border border-dashed border-gray-300">
                                <i class="fa-solid fa-minus text-gray-400 text-[10px]"></i>
                            </span>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 gap-2">
                                <h3 class="text-sm font-medium text-gray-400 italic">
                                    {{ $riwayat->pesan }}
                                </h3>
                                <time class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                                    {{ \Carbon\Carbon::parse($riwayat->tanggal)->translatedFormat('l, d M Y') }}
                                </time>
                            </div>
                        </li>

                    {{-- JIKA HARI INI ADA DATANYA --}}
                    @else
                        <li class="ms-7">
                            {{-- Icon Dinamis Berdasarkan Status Kehadiran --}}
                            <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -start-4 ring-4 ring-white shadow-sm border"
                                  style="{{ $riwayat->statusKehadiran?->badge_style }}">
                                @if(in_array($riwayat->status_kehadiran_id, [1, 2]))
                                    <i class="fa-solid fa-user-check text-xs"></i>
                                @elseif($riwayat->status_kehadiran_id == 5)
                                    <i class="fa-solid fa-plane-departure text-xs"></i>
                                @elseif(in_array($riwayat->status_kehadiran_id, [3, 4, 6]))
                                    <i class="fa-solid fa-user-xmark text-xs"></i>
                                @else
                                    <i class="fa-solid fa-envelope-open-text text-xs"></i>
                                @endif
                            </span>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 gap-2">
                                <h3 class="text-base font-bold text-gray-900 flex items-center flex-wrap gap-2">
                                    {{ $riwayat->statusKehadiran?->status ?? 'Tanpa Status' }}

                                    {{-- Badge Status Datang --}}
                                    @if($riwayat->jam_masuk)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded border uppercase tracking-wider
                                            {{ $riwayat->status_datang === 'Terlambat' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                            {{ $riwayat->status_datang === 'Datang Lebih Awal' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                            {{ $riwayat->status_datang === 'Tepat Waktu' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                        ">
                                            {{ $riwayat->status_datang }}
                                        </span>
                                    @endif

                                    {{-- Badge Status Pulang --}}
                                    @if($riwayat->jam_keluar)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded border uppercase tracking-wider
                                            {{ $riwayat->status_pulang === 'Pulang Lebih Awal' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                            {{ $riwayat->status_pulang === 'Tepat Waktu' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                        ">
                                            {{ $riwayat->status_pulang }}
                                        </span>
                                    @endif
                                </h3>
                                <time class="text-xs font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded-md border border-gray-200">
                                    {{ \Carbon\Carbon::parse($riwayat->tanggal)->translatedFormat('l, d M Y') }}
                                </time>
                            </div>

                            {{-- Info Jam Masuk dan Keluar --}}
                            <p class="text-xs text-gray-500 mt-0.5">
                                <i class="fa-regular fa-clock mr-1 text-gray-400"></i>
                                Check-in: <span class="font-semibold text-gray-700">{{ $riwayat->jam_masuk ? \Carbon\Carbon::parse($riwayat->jam_masuk)->format('H:i') : '-' }}</span>
                                |
                                Check-out: <span class="font-semibold text-gray-700">{{ $riwayat->jam_keluar ? \Carbon\Carbon::parse($riwayat->jam_keluar)->format('H:i') : '-' }}</span>
                            </p>
                        </li>
                    @endif
                @endforeach
            </ol>

        @endif


    </div>
</div>