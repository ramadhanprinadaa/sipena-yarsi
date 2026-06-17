<div>
@if($openDetail)

    {{-- Blurred Backdrop --}}
    <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"></div>

    {{-- Detail Cuti Modal --}}
    <div
        x-data="{}"
        x-show="{{ $openDetail ? 'true' : 'false' }}"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
        x-cloak
    >
        <div class="relative w-full max-w-4xl max-h-full bg-white rounded-3xl shadow-2xl overflow-hidden">

            {{-- ─── Gradient Header ─── --}}
            <div class="relative bg-gradient-to-br from-blue-600 via-purple-500 to-fuchsia-400 px-8 pt-8 pb-14 overflow-hidden">
                {{-- Decorative circles --}}
                <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/10"></div>
                <div class="absolute -bottom-14 left-16 w-56 h-56 rounded-full bg-white/5"></div>

                <div class="relative z-10 flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border border-white/25">
                            <i class="fa-solid fa-umbrella-beach text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold tracking-widest uppercase text-white/70 mb-1">Pengajuan Cuti</p>
                            <h2 class="text-2xl font-bold text-white">Detail Pengajuan Cuti</h2>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeDetailModal"
                        class="w-10 h-10 rounded-full bg-white/15 border border-white/25 text-white hover:bg-white/25 transition-colors flex items-center justify-center cursor-pointer"
                    >
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Status Badge --}}
                @if($selectedCuti)
                <div class="relative z-10 mt-5 inline-flex items-center gap-2 bg-white/15 border border-white/30 backdrop-blur px-4 py-1.5 rounded-full">
                    @php
                        $status = $selectedCuti->status ?? '';
                        $dotColor = match($status) {
                            'disetujui'  => 'bg-emerald-400 shadow-emerald-400/50',
                            'ditolak'    => 'bg-rose-400 shadow-rose-400/50',
                            'pending'    => 'bg-amber-400 shadow-amber-400/50',
                            default      => 'bg-slate-300',
                        };
                    @endphp
                    <span class="w-2 h-2 rounded-full shadow-lg {{ $dotColor }}"></span>
                    <span class="text-sm font-semibold text-white">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                </div>
                @endif
            </div>

            {{-- ─── Stats Strip (Floating) ─── --}}
            @if($selectedCuti)
            <div class="mx-8 -mt-8 grid grid-cols-3 bg-white rounded-2xl shadow-xl border border-violet-100 overflow-hidden z-20 relative">
                <div class="flex items-center gap-3 px-5 py-4 border-r border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-calendar-day text-violet-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mulai</p>
                        <p class="text-sm font-bold text-slate-800 leading-tight">
                            {{ \Carbon\Carbon::parse($selectedCuti->tanggal_mulai)->translatedFormat('d M Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-5 py-4 border-r border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-calendar-check text-pink-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Selesai</p>
                        <p class="text-sm font-bold text-slate-800 leading-tight">
                            {{ $selectedCuti->tanggal_selesai ? \Carbon\Carbon::parse($selectedCuti->tanggal_selesai)->translatedFormat('d M Y') : '-' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-5 py-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-clock text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Durasi</p>
                        <p class="text-sm font-bold text-slate-800 leading-tight">
                            @if($selectedCuti->jenisCuti?->dihitung_per_jam)
                                {{ $selectedCuti->jumlah_jam ?? '-' }} Jam
                            @else
                                {{ $selectedCuti->jumlah_hari_cuti ?? '-' }} Hari
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- ─── Body ─── --}}
            @if($selectedCuti)
            <div class="grid lg:grid-cols-2 gap-5 px-8 pt-5 pb-8">

                {{-- ── Left Column ── --}}
                <div class="space-y-4">
                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Informasi Pegawai</p>

                    {{-- Pegawai Identity Card --}}
                    <div class="flex items-center gap-4 bg-gradient-to-br from-violet-50 to-fuchsia-50 border border-violet-200 rounded-2xl p-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-600 to-fuchsia-500 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                            {{ strtoupper(substr($selectedCuti->pegawai->nama ?? 'P', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-base font-bold text-slate-900">{{ $selectedCuti->pegawai->nama ?? '-' }}</p>
                            <p class="text-xs font-semibold text-violet-600 mt-0.5">{{ $selectedCuti->pegawai->nip ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Jenis Cuti</p>
                            <p class="text-sm font-bold text-slate-800">{{ $selectedCuti->jenisCuti->nama ?? '-' }}</p>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Tgl Pengajuan</p>
                            <p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($selectedCuti->created_at)->translatedFormat('d M Y') }}</p>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 col-span-2">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Keterangan</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $selectedCuti->keterangan ?? '-' }}</p>
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
                                {{ $selectedCuti->approvals?->sortByDesc('approved_at')->first()?->approver?->pegawai?->nama ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Riwayat Persetujuan --}}
                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mt-2">Riwayat Persetujuan</p>

                    @if($selectedCuti->approvals && $selectedCuti->approvals->count())
                        <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                            @foreach($selectedCuti->approvals->sortByDesc('approved_at') as $approval)
                                @php
                                    $approvalStatus = $approval->status ?? '';
                                    $isApproved = $approvalStatus === 'disetujui';
                                    $isRejected = $approvalStatus === 'ditolak';
                                    $cardBg    = $isApproved ? 'bg-emerald-50 border-emerald-200' : ($isRejected ? 'bg-rose-50 border-rose-200' : 'bg-blue-50 border-blue-200');
                                    $iconBg    = $isApproved ? 'bg-emerald-500' : ($isRejected ? 'bg-rose-500' : 'bg-blue-500');
                                    $nameColor = $isApproved ? 'text-emerald-900' : ($isRejected ? 'text-rose-900' : 'text-blue-900');
                                    $roleColor = $isApproved ? 'text-emerald-600' : ($isRejected ? 'text-rose-600' : 'text-blue-600');
                                    $icon      = $isApproved ? 'fa-check' : ($isRejected ? 'fa-xmark' : 'fa-clock');
                                @endphp
                                <div class="flex items-start gap-3 {{ $cardBg }} border rounded-xl px-4 py-2">
                                    <div class="w-9 h-9 rounded-xl {{ $iconBg }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fa-solid {{ $icon }} text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold {{ $nameColor }} truncate">
                                            {{ $approval->approver?->pegawai?->nama ?? $approval->approved_by }}
                                        </p>
                                        <p class="text-xs font-semibold {{ $roleColor }} mt-0.5">
                                            {{ $approval->role_approval ?? '-' }} · {{ ucfirst(str_replace('_', ' ', $approvalStatus)) }}
                                        </p>
                                        @if($approval->catatan)
                                            <p class="text-xs text-slate-600 mt-1 italic">"{{ $approval->catatan }}"</p>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $approval->approved_at ? \Carbon\Carbon::parse($approval->approved_at)->translatedFormat('d M Y, H:i') : '-' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-slate-50 border border-dashed border-slate-300 rounded-xl p-4 text-center">
                            <p class="text-sm text-slate-500">Belum ada riwayat persetujuan.</p>
                        </div>
                    @endif

                </div>

                {{-- ── Right Column ── --}}
                <div class="space-y-4 flex flex-col">

                    {{-- Dokumen Pendukung --}}
                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Dokumen Pendukung</p>

                    @if($selectedCuti->dokumen_pendukung)
                        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 border border-amber-200 rounded-2xl p-4 h-full flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-amber-400 flex items-center justify-center flex-shrink-0">
                                        @if(Str::endsWith($selectedCuti->dokumen_pendukung, ['.jpg', '.jpeg', '.png']))
                                            <i class="fa-solid fa-file-image text-white text-lg"></i>
                                        @else
                                            <i class="fa-solid fa-file-pdf text-white text-lg"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-amber-900 break-all">{{ basename($selectedCuti->dokumen_pendukung) }}</p>
                                        <p class="text-xs text-amber-700 font-medium">
                                            @if(Str::endsWith($selectedCuti->dokumen_pendukung, ['.pdf'])) PDF
                                            @elseif(Str::endsWith($selectedCuti->dokumen_pendukung, ['.jpg', '.jpeg'])) JPEG
                                            @elseif(Str::endsWith($selectedCuti->dokumen_pendukung, ['.png'])) PNG
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex w-full h-full">
                            @if(Str::endsWith($selectedCuti->dokumen_pendukung, ['.pdf']))
                                <iframe
                                    src="{{ asset('storage/' . $selectedCuti->dokumen_pendukung) }}"
                                    class="w-full h-full rounded-xl border border-amber-200"
                                ></iframe>
                            @elseif(Str::endsWith($selectedCuti->dokumen_pendukung, ['.jpg', '.jpeg', '.png']))
                                <img
                                    src="{{ asset('storage/' . $selectedCuti->dokumen_pendukung) }}"
                                    alt="Dokumen Pendukung"
                                    class="w-full h-full object-contain rounded-xl border border-amber-200 bg-white"
                                />
                            @else
                                <p class="text-sm text-amber-700">Pratinjau tidak tersedia.</p>
                            @endif
                            </div>

                            <div class="flex h-15 gap-3">
                                <a
                                        href="{{ asset('storage/' . $selectedCuti->dokumen_pendukung) }}"
                                        target="_blank"
                                        class="w-full h-full rounded-xl bg-amber-400 hover:bg-amber-500 transition-colors flex items-center justify-center"
                                >
                                        <i class="fa-solid fa-eye text-white"></i>
                                        <span class="ml-2 text-sm font-semibold text-white">Preview Dokumen</span>
                                </a>
                                <a
                                        href="{{ asset('storage/' . $selectedCuti->dokumen_pendukung) }}"
                                        download="{{ basename($selectedCuti->dokumen_pendukung) }}"
                                        class="w-full h-full rounded-xl bg-amber-400 hover:bg-amber-500 transition-colors flex items-center justify-center"
                                >
                                        <i class="fa-solid fa-download text-white"></i>
                                        <span class="ml-2 text-sm font-semibold text-white">Unduh Dokumen</span>
                                </a>
                            </div>

                        </div>
                    @else
                        <div class="bg-slate-50 h-110 flex items-center justify-center border border-dashed border-slate-300 rounded-2xl p-6 text-center">
                            <i class="fa-solid fa-file-slash text-slate-300 text-3xl mb-2"></i>
                            <p class="text-sm text-slate-500 font-medium">Tidak ada dokumen pendukung</p>
                        </div>
                    @endif



                </div>
            </div>

            @else
                <div class="py-16 text-center px-8">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-circle-info text-slate-400 text-2xl"></i>
                    </div>
                    <p class="text-slate-500 font-medium">Tidak ada detail cuti yang bisa ditampilkan.</p>
                </div>
            @endif

        </div>
    </div>

@endif
</div>