<div>

                {{-- Detail Laporan Modal --}}
                @if($openDetailLaporan && $selectedLemburDetail)
                <template x-teleport="body">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4">
                        <div class="relative w-full max-w-4xl max-h-full bg-white rounded-3xl shadow-2xl overflow-hidden overflow-y-auto">
                            {{-- Header --}}
                            <div class="relative bg-gradient-to-br from-indigo-600 via-blue-500 to-sky-400 px-8 pt-8 pb-14 overflow-hidden">
                                <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/10"></div>
                                <div class="absolute -bottom-14 left-16 w-56 h-56 rounded-full bg-white/5"></div>
                                <div class="relative z-10 flex items-start justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border border-white/25">
                                            <i class="fa-solid fa-business-time text-white text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold tracking-widest uppercase text-white/70 mb-1">Laporan Lembur</p>
                                            <h2 class="text-2xl font-bold text-white">Detail Laporan Lembur</h2>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="closeDetailLaporan" class="w-10 h-10 rounded-full bg-white/15 border border-white/25 text-white hover:bg-white/25 transition-colors flex items-center justify-center cursor-pointer">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <div class="relative z-10 mt-5 inline-flex items-center gap-2 bg-white/15 border border-white/30 backdrop-blur px-4 py-1.5 rounded-full">
                                    @php
                                        $statusLaporan = $this->getLaporanStatus($selectedLemburDetail);
                                        $dotColor = match(true) {
                                            $statusLaporan === 'Disetujui' => 'bg-emerald-400 shadow-emerald-400/50',
                                            $statusLaporan === 'Ditolak' => 'bg-rose-400 shadow-rose-400/50',
                                            str_contains($statusLaporan, 'Menunggu') => 'bg-amber-400 shadow-amber-400/50',
                                            default => 'bg-slate-300',
                                        };
                                    @endphp
                                    <span class="w-2 h-2 rounded-full shadow-lg {{ $dotColor }}"></span>
                                    <span class="text-sm font-semibold text-white">{{ $statusLaporan }}</span>
                                </div>
                            </div>

                            {{-- Stats Strip --}}
                            <div class="mx-8 -mt-8 grid grid-cols-3 bg-white rounded-2xl shadow-xl border border-indigo-100 overflow-hidden z-20 relative">
                                <div class="flex items-center gap-3 px-5 py-4 border-r border-slate-100">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-calendar-day text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal</p>
                                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ \Carbon\Carbon::parse($selectedLemburDetail->tanggal_lembur)->translatedFormat('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 px-5 py-4 border-r border-slate-100">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-clock text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jam Aktual</p>
                                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ $selectedLemburDetail->laporanHasilLembur->jam_mulai ?? '-' }} - {{ $selectedLemburDetail->laporanHasilLembur->jam_selesai ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 px-5 py-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-hourglass-half text-emerald-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jenis Hari</p>
                                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ $selectedLemburDetail->jenis_hari }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="grid lg:grid-cols-2 gap-5 px-8 pt-5 pb-8 overflow-y-auto max-h-[60vh]">
                                <div class="flex flex-col space-y-4 h-full">
                                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Informasi Pegawai & Laporan</p>
                                    <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200 rounded-2xl p-4">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">{{ strtoupper(substr($selectedLemburDetail->pegawai->nama ?? 'P', 0, 1)) }}</div>
                                        <div>
                                            <p class="text-base font-bold text-slate-900">{{ $selectedLemburDetail->pegawai->nama ?? '-' }}</p>
                                            <p class="text-xs font-semibold text-indigo-600 mt-0.5">{{ $selectedLemburDetail->pegawai->nip ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 col-span-2">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Kegiatan (SPL)</p>
                                            <p class="text-sm font-bold text-slate-800">{{ $selectedLemburDetail->suratPerintahLembur->nama_kegiatan ?? '-' }}</p>
                                        </div>
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 col-span-2">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Hasil Pekerjaan</p>
                                            <p class="text-sm font-semibold text-slate-800">{{ $selectedLemburDetail->laporanHasilLembur->hasil_pekerjaan ?? '-' }}</p>
                                        </div>
                                    </div>

                                    {{-- Approved By --}}
                                    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-xl p-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-user-check text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Disetujui Oleh</p>
                                            <p class="text-sm font-bold text-emerald-800">
                                                {{ $selectedLemburDetail->laporanHasilLembur->persetujuan->sortByDesc('approved_at')->first()?->approver?->pegawai?->nama ?? '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mt-2">Riwayat Persetujuan Laporan</p>
                                    @if($selectedLemburDetail->laporanHasilLembur->persetujuan->count())
                                        <div class="space-y-2">
                                            @foreach($selectedLemburDetail->laporanHasilLembur->persetujuan->sortByDesc('approved_at') as $approval)
                                                <div class="flex items-start gap-3 {{ $approval->status === 'Disetujui' ? 'bg-emerald-50 border-emerald-200' : ($approval->status === 'Ditolak' ? 'bg-rose-50 border-rose-200' : 'bg-blue-50 border-blue-200') }} border rounded-xl px-4 py-2">
                                                    <div class="w-9 h-9 rounded-xl {{ $approval->status === 'Disetujui' ? 'bg-emerald-500' : ($approval->status === 'Ditolak' ? 'bg-rose-500' : 'bg-blue-500') }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        <i class="fa-solid {{ $approval->status === 'Disetujui' ? 'fa-check' : ($approval->status === 'Ditolak' ? 'fa-xmark' : 'fa-clock') }} text-white text-sm"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-bold {{ $approval->status === 'Disetujui' ? 'text-emerald-900' : ($approval->status === 'Ditolak' ? 'text-rose-900' : 'text-blue-900') }} truncate">{{ $approval->approver->pegawai->nama ?? $approval->approver->name }}</p>
                                                        <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $approval->role_approval }} · {{ $approval->status }}</p>
                                                        @if($approval->catatan)<p class="text-xs text-slate-600 mt-1 italic">"{{ $approval->catatan }}"</p>@endif
                                                    </div>
                                                    <p class="text-xs text-slate-400 mt-0.5">{{ $approval->approved_at ? \Carbon\Carbon::parse($approval->approved_at)->translatedFormat('d M Y, H:i') : '-' }}</p>
                                                </div>
                                            @endforeach
                                        </div>  
                                    @else
                                        <div class="flex h-full bg-slate-50 border border-dashed border-slate-300 rounded-xl p-4 text-center justify-center items-center">
                                            <p class="text-sm text-slate-500">Belum ada riwayat persetujuan laporan.</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-4 flex flex-col">
                                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">File Laporan</p>
                                    @if($selectedLemburDetail->laporanHasilLembur->file_laporan)
                                        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 border border-amber-200 rounded-2xl p-4 flex-1 flex flex-col gap-4">
                                            <div class="w-full h-full">
                                                @if(Str::endsWith($selectedLemburDetail->laporanHasilLembur->file_laporan, ['.jpg', '.jpeg', '.png']))
                                                    <img src="{{ asset('storage/' . $selectedLemburDetail->laporanHasilLembur->file_laporan) }}" class="w-full h-full object-contain rounded-xl border border-amber-200 bg-white" />
                                                @else
                                                    <iframe src="{{ asset('storage/' . $selectedLemburDetail->laporanHasilLembur->file_laporan) }}" class="w-full h-full rounded-xl border border-amber-200"></iframe>
                                                @endif
                                            </div>
                                            <div class="flex gap-3 h-12">
                                                <a href="{{ asset('storage/' . $selectedLemburDetail->laporanHasilLembur->file_laporan) }}" target="_blank" class="w-full h-full rounded-xl bg-amber-400 hover:bg-amber-500 text-white text-sm font-semibold flex items-center justify-center transition-colors"><i class="fa-solid fa-eye mr-2"></i> 
                                                Preview
                                                </a>
                                                <a href="{{ asset('storage/' . $selectedLemburDetail->laporanHasilLembur->file_laporan) }}" download class="w-full h-full rounded-xl bg-amber-400 hover:bg-amber-500 text-white text-sm font-semibold flex items-center justify-center transition-colors"><i class="fa-solid fa-download mr-2"></i>
                                                Unduh
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-slate-50 flex-1 flex flex-col items-center justify-center border border-dashed border-slate-300 rounded-2xl p-6 text-center">
                                            <i class="fa-solid fa-file-slash text-slate-300 text-4xl mb-3"></i>
                                            <p class="text-sm text-slate-500 font-medium">Tidak ada file laporan</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                @endif

</div>