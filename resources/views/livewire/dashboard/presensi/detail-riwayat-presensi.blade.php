<div class="relative bg-white w-[95vw] md:w-[50vw] max-h-[85vh] mx-auto rounded-2xl shadow-2xl flex flex-col overflow-hidden"
     x-data="{ activeTabDetail: 'info' }"
     @open-detail-riwayat.window="activeTabDetail = 'info'">

    {{-- Header --}}
    <header class="flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-teal-50 via-white to-emerald-50">

        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-teal-200 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-emerald-200 rounded-full blur-3xl"></div>
        </div>

        {{-- Header Information --}}
        <div class="relative flex items-center gap-4">
            <div class="w-11 h-11 rounded-2xl bg-teal-500 flex items-center justify-center shadow-lg shadow-teal-200">
                <i class="fa-solid fa-calendar-check text-white text-xl"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    Detail Presensi Saya
                </h2>

                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    <i class="fa-regular fa-calendar text-teal-500"></i>
                    <span class="font-medium">
                        {{ $presensi ? \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('l, d F Y') : '-' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Tombol Close Atas --}}
        <button type="button" @click="$dispatch('close-detail-riwayat')"
            class="relative w-8 h-8 flex items-center justify-center rounded-full cursor-pointer text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </header>

    @if($presensi)
        {{-- Tabs Navigation --}}
        <nav class="flex px-5 border-b border-slate-200 bg-slate-50/50 shrink-0 gap-6">
            <button @click="activeTabDetail = 'info'"
                :class="activeTabDetail === 'info' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="py-3 border-b-2 font-medium text-sm transition-colors duration-200 outline-none">
                Informasi Utama
            </button>
            <button @click="activeTabDetail = 'riwayat'"
                :class="activeTabDetail === 'riwayat' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="py-3 border-b-2 font-medium text-sm transition-colors duration-200 outline-none flex items-center gap-2">
                Riwayat Perubahan
                @if($presensi->presensiLogs->count() > 0)
                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold">
                        {{ $presensi->presensiLogs->count() }}
                    </span>
                @endif
            </button>
        </nav>

        {{-- Body Content --}}
        <div class="flex-1 overflow-y-auto p-5 bg-slate-50/50">

            {{-- TAB 1: INFORMASI UTAMA --}}
            <div x-show="activeTabDetail === 'info'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-3">

                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Kehadiran</p>
                        <h4 class="text-sm font-bold text-slate-800">
                            {{ $presensi->statusKehadiran?->kondisi ?? 'Tidak Diketahui' }}
                        </h4>
                    </div>
                    <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border"
                          style="{{ $presensi->statusKehadiran?->badge_style ?? 'background-color: #f1f5f9; color: #64748b; border-color: #e2e8f0;' }}">
                        {{ $presensi->statusKehadiran?->status ?? 'N/A' }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-md bg-teal-50 text-teal-600 flex items-center justify-center">
                                <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Check In</span>
                        </div>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '--:--' }}
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Check Out</span>
                        </div>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') : '--:--' }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-teal-50 flex items-center justify-center text-teal-600">
                        <i class="fa-regular fa-clock text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Waktu Kerja</p>
                        <p class="text-base font-bold text-slate-800 mt-0.5">
                            {{ $presensi->total_jam_kerja ?? 'Belum ada kalkulasi' }}
                        </p>
                    </div>
                </div>

                @if($presensi->latestLog)
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                        <i class="fa-solid fa-circle-info text-amber-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">Terdapat Perubahan Data</p>
                            <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                                Data presensi ini telah disesuaikan oleh administrator. Silakan cek tab <strong>Riwayat Perubahan</strong> untuk melihat detail log perubahan.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- TAB 2: RIWAYAT PERUBAHAN --}}
            <div x-show="activeTabDetail === 'riwayat'"
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">

                @if ($this->riwayatPerubahan->count() > 0)
                    <div class="relative border-s-2 border-slate-200 ml-3 space-y-8 pb-4 mt-2">
                        @foreach ($this->riwayatPerubahan as $log)
                            <div class="ms-6 relative">
                                <div class="absolute w-3 h-3 bg-teal-500 rounded-full -start-[31px] top-1.5 ring-4 ring-white shadow-sm"></div>

                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                                    <div class="flex items-center justify-between mb-2 gap-4">
                                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                                                <i class="fa-solid fa-user-pen text-xs text-slate-500"></i>
                                            </div>
                                            {{-- Menampilkan Siapa Yang Mengubah --}}
                                            {{ $log['editor_name'] }}
                                        </h4>
                                        <time class="text-xs font-medium text-slate-500 bg-slate-50 px-2.5 py-1 rounded-md border border-slate-100 shrink-0">
                                            {{ $log['waktu'] }}
                                        </time>
                                    </div>

                                    @if ($log['keterangan'])
                                        <p class="text-sm text-slate-600 mb-3 bg-slate-50 p-2.5 rounded-lg border border-slate-100 border-l-2 border-l-teal-500">
                                            <span class="font-semibold text-slate-700">Catatan:</span> {{ $log['keterangan'] }}
                                        </p>
                                    @endif

                                    {{-- Menampilkan Array Perubahan --}}
                                    @if (count($log['changes']) > 0)
                                        <ul class="space-y-2">
                                            @foreach ($log['changes'] as $change)
                                                <li class="text-sm flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 bg-white p-2 rounded border border-slate-100">
                                                    <span class="font-medium text-slate-700 min-w-[120px]">{{ $change['label'] }}</span>
                                                    <div class="flex items-center gap-2 text-slate-600 flex-1 flex-wrap">
                                                        <span class="px-2 py-0.5 bg-rose-50 text-rose-600 rounded text-xs font-medium border border-rose-100 line-through decoration-rose-300">
                                                            {{ $change['old'] }}
                                                        </span>
                                                        <i class="fa-solid fa-arrow-right text-slate-300 text-[10px]"></i>
                                                        <span class="px-2 py-0.5 bg-teal-50 text-teal-600 rounded text-xs font-bold border border-teal-100">
                                                            {{ $change['new'] }}
                                                        </span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4 border border-slate-200">
                            <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-400"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-700">Belum Ada Perubahan</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs">Data presensi ini belum pernah diubah atau diedit oleh administrator.</p>
                    </div>
                @endif
            </div>

        </div>
    @else
        <div class="flex flex-col items-center justify-center py-20 flex-1">
            <div class="w-8 h-8 border-4 border-teal-200 border-t-teal-600 rounded-full animate-spin mb-4"></div>
            <p class="text-sm text-slate-500 font-medium">Memuat rincian data...</p>
        </div>
    @endif

    {{-- Footer --}}
    <footer class="border-t border-slate-100 p-3 flex items-center justify-end gap-3 shrink-0">
        <button type="button" @click="$dispatch('close-detail-riwayat')"
            class="px-5 py-2 rounded-md text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-800 font-medium transition cursor-pointer shadow-sm">
            Tutup
        </button>
    </footer>
</div>