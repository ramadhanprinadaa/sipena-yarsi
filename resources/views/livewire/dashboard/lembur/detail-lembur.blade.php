<div>
@if ($openDetail && $selectedLembur)
    <template x-teleport="body">

        {{-- Blurred Backdrop + Modal Wrapper --}}
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        >
            {{-- Modal Box --}}
            <div
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl flex flex-col overflow-hidden"
            >

                {{-- ─── Gradient Header ─── --}}
                <div class="relative flex-shrink-0 bg-gradient-to-br from-blue-600 via-blue-500 to-sky-400 px-8 pt-8 pb-14 overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/10"></div>
                    <div class="absolute -bottom-14 left-16 w-56 h-56 rounded-full bg-white/5"></div>

                    <div class="relative z-10 flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border border-white/25">
                                <i class="fa-solid fa-moon text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-widest uppercase text-white/70 mb-1">Pencatatan Lembur</p>
                                <h2 class="text-2xl font-bold text-white">Detail Lembur</h2>
                            </div>
                        </div>
                        <button
                            wire:click="closeDetail"
                            class="w-10 h-10 rounded-full bg-white/15 border border-white/25 text-white hover:bg-white/25 transition-colors flex items-center justify-center cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    {{-- Status Badge --}}
                    @php
                        $st = $selectedLembur->status ?? '';
                        $dotColor = match(strtolower($st)) {
                            'selesai'  => 'bg-emerald-400 shadow-emerald-400/50',
                            'ditolak'  => 'bg-rose-400 shadow-rose-400/50',
                            'proses'   => 'bg-amber-400 shadow-amber-400/50',
                            default    => 'bg-slate-300',
                        };
                    @endphp
                    <div class="relative z-10 mt-5 inline-flex items-center gap-2 bg-white/15 border border-white/30 backdrop-blur px-4 py-1.5 rounded-full">
                        <span class="w-2 h-2 rounded-full shadow-lg {{ $dotColor }}"></span>
                        <span class="text-sm font-semibold text-white">{{ ucfirst(str_replace('_', ' ', $st)) }}</span>
                    </div>
                </div>

                {{-- ─── Stats Strip Floating ─── --}}
                <div class="mx-8 -mt-6 grid grid-cols-4 bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden z-20 relative flex-shrink-0">
                    <div class="flex items-center gap-3 px-5 py-4 border-r border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-calendar-day text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal</p>
                            <p class="text-sm font-bold text-slate-800 leading-tight">
                                {{ \Carbon\Carbon::parse($selectedLembur->tanggal_lembur)->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-4 border-r border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-play text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jam Mulai</p>
                            <p class="text-sm font-bold text-slate-800 leading-tight">{{ $selectedLembur->jam_mulai ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-4 border-r border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-stop text-rose-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jam Selesai</p>
                            <p class="text-sm font-bold text-slate-800 leading-tight">{{ $selectedLembur->jam_selesai ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-sun text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jenis Hari</p>
                            <p class="text-sm font-bold text-slate-800 leading-tight">{{ $selectedLembur->jenis_hari ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- ─── Body 2 Kolom ─── --}}
                <div class="grid lg:grid-cols-2 gap-5 px-8 pt-5 pb-8 items-stretch">

                    {{-- ── Kolom Kiri: Informasi Surat ── --}}
                    <div class="flex flex-col gap-4">
                        <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Informasi Surat</p>

                        {{-- Identity Card: Nomor Surat --}}
                        <div class="flex items-center gap-4 bg-gradient-to-br from-blue-50 to-sky-50 border border-blue-200 rounded-2xl p-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-sky-400 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-file-contract text-white text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-base font-bold text-slate-900 truncate">
                                    {{ $selectedLembur?->suratPerintahLembur?->nomor_surat ?? '-' }}
                                </p>
                                <p class="text-xs font-semibold text-blue-600 mt-0.5 truncate">
                                    {{ $selectedLembur?->suratPerintahLembur?->unitKerja?->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        {{-- Nomor Surat & Unit Kerja detail --}}
                        <div class="grid grid-cols-1 gap-3">
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Nomor Surat</p>
                                <p class="text-sm font-bold text-slate-800">{{ $selectedLembur?->suratPerintahLembur?->nomor_surat ?? '-' }}</p>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Unit Kerja</p>
                                <p class="text-sm font-bold text-slate-800">{{ $selectedLembur?->suratPerintahLembur?->unitKerja?->name ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Status — mt-auto ke bawah --}}
                        @php
                            $isSelesai  = strtolower($st) === 'selesai';
                            $isDitolak  = strtolower($st) === 'ditolak';
                            $statusCard = $isSelesai ? 'bg-emerald-50 border-emerald-200' : ($isDitolak ? 'bg-rose-50 border-rose-200' : 'bg-amber-50 border-amber-200');
                            $statusIcon = $isSelesai ? 'bg-emerald-500' : ($isDitolak ? 'bg-rose-500' : 'bg-amber-400');
                            $statusFA   = $isSelesai ? 'fa-circle-check' : ($isDitolak ? 'fa-circle-xmark' : 'fa-clock');
                            $statusText = $isSelesai ? 'text-emerald-800' : ($isDitolak ? 'text-rose-800' : 'text-amber-800');
                        @endphp
                        <div class="flex items-center gap-3 {{ $statusCard }} border rounded-xl p-3 mt-auto">
                            <div class="w-9 h-9 rounded-xl {{ $statusIcon }} flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid {{ $statusFA }} text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Lembur</p>
                                <p class="text-sm font-bold {{ $statusText }}">{{ ucfirst(str_replace('_', ' ', $st)) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ── Kolom Kanan: Detail Kegiatan ── --}}
                    <div class="flex flex-col gap-4">
                        <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Detail Kegiatan</p>

                        {{-- Nama Kegiatan --}}
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Nama Kegiatan</p>
                            <p class="text-sm font-bold text-slate-800">{{ $selectedLembur->alasan_lembur ?? '-' }}</p>
                        </div>

                        {{-- Hasil Pekerjaan — flex-1 mengisi sisa ruang --}}
                        @if($selectedLembur?->laporanHasilLembur)
                            <div class="flex flex-col flex-1 bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fa-solid fa-file-lines text-slate-400 text-sm"></i>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Hasil Pekerjaan</p>
                                </div>
                                <p class="text-sm font-semibold text-slate-800 leading-relaxed flex-1">
                                    {{ $selectedLembur->laporanHasilLembur->hasil_pekerjaan }}
                                </p>
                            </div>
                        @else
                            <div class="flex flex-col flex-1 bg-slate-50 border border-dashed border-slate-300 rounded-xl p-4 items-center justify-center text-center">
                                <i class="fa-solid fa-file-circle-xmark text-slate-300 text-2xl mb-2"></i>
                                <p class="text-sm text-slate-400">Belum ada laporan hasil pekerjaan.</p>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </template>
@endif
</div>