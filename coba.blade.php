@extends('layouts.app')

@section('title', 'SIPENA | Kepegawaian')

@section('breadcrumb')
<div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
    <span>Beranda</span>
    <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('kepegawaian') }}" class="text-indigo-600 hover:text-indigo-500 transition-colors">Kepegawaian</a>
</div>
@endsection

@section('content')
<div class="space-y-4">

    {{-- ===== HERO CARD ===== --}}
    <div class="relative bg-gradient-to-br from-indigo-700 via-indigo-600 to-blue-600 rounded-2xl shadow-lg overflow-hidden">

        {{-- Decorative background shapes --}}
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none">
            <div class="absolute -top-12 -right-12 w-56 h-56 bg-white rounded-full"></div>
            <div class="absolute -bottom-16 -left-12 w-72 h-72 bg-white rounded-full"></div>
            <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-white rounded-full transform -translate-y-1/2"></div>
        </div>

        <div class="relative px-5 py-5 flex flex-col md:flex-row items-center md:items-stretch gap-5">

            {{-- Avatar --}}
            <div class="relative shrink-0 self-center md:self-auto flex items-center">
                <div class="w-24 h-24 rounded-2xl bg-white/20 backdrop-blur-sm border-2 border-white/30 flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-user-tie text-4xl text-white"></i>
                </div>
                <span class="absolute -bottom-2 -right-2 inline-flex items-center gap-1
                {{ $pegawai?->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}
                text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm border border-white/50">
                    <i class="fa-solid fa-circle text-[5px]"></i>
                    {{ $pegawai?->status_label ?? 'N/A' }}
                </span>
            </div>

            {{-- Identity --}}
            <div class="flex-1 text-center md:text-left text-white flex flex-col justify-center">
                <p class="text-indigo-200 text-[10px] font-semibold uppercase tracking-widest mb-1">Profil Pegawai</p>
                <h1 class="text-lg font-bold leading-snug">
                    {{ $pegawai?->nama_gelar ?? $pegawai?->nama ?? 'Data Tidak Ditemukan' }}
                </h1>
                <p class="mt-1 text-indigo-200 text-xs font-medium tracking-wide">
                    NIP: {{ $pegawai?->nip ?? '-' }}
                </p>
                <div class="mt-3 flex flex-wrap justify-center md:justify-start gap-2">
                    <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-sm border border-white/20 text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-user-shield text-indigo-200 text-[10px]"></i>
                        {{ $pegawai?->jenis_pegawai?->jenis ?? 'N/A' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-sm border border-white/20 text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-building text-indigo-200 text-[10px]"></i>
                        {{ $pegawai?->unit_kerja?->name ?? 'N/A' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-sm border border-white/20 text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-graduation-cap text-indigo-200 text-[10px]"></i>
                        {{ $pegawai?->jabatan ?? 'N/A' }}
                    </span>
                </div>
            </div>

            {{-- Divider --}}
            <div class="hidden md:block w-px bg-white/10 self-stretch mx-1"></div>

            {{-- QUICK STATS --}}
            <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-4 gap-2 shrink-0 self-center w-full md:w-auto">
                {{-- Usia --}}
                <div class="flex items-center gap-2.5 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-user text-[11px] text-white/80"></i>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-[0.15em] text-indigo-200/70 font-medium">Usia</p>
                        <p class="mt-0.5 text-xs font-semibold text-white">{{ $pegawai?->age ?? 'N/A' }} Tahun</p>
                    </div>
                </div>

                {{-- Masa Kerja --}}
                <div class="flex items-center gap-2.5 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-clock text-[11px] text-white/80"></i>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-[0.15em] text-indigo-200/70 font-medium">Masa Kerja</p>
                        <p class="mt-0.5 text-xs font-semibold text-white">{{ $pegawai?->masa_kerja ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Bergabung --}}
                <div class="flex items-center gap-2.5 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-calendar-plus text-[11px] text-white/80"></i>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-[0.15em] text-indigo-200/70 font-medium">Bergabung</p>
                        <p class="mt-0.5 text-xs font-semibold text-white">{{ $pegawai?->tanggal_bergabung?->translatedFormat('d M Y') ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Kontrak / Pensiun --}}
                <div class="flex items-center gap-2.5 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-calendar-xmark text-[11px] text-white/80"></i>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-[0.15em] text-indigo-200/70 font-medium">
                            @if ($pegawai?->status_pegawai?->status === 'Kontrak') Habis Kontrak
                            @elseif ($pegawai?->status_pegawai?->status === 'Tetap') Pensiun
                            @else Tanggal Akhir @endif
                        </p>
                        <p class="mt-0.5 text-xs font-semibold text-white">
                            @if ($pegawai?->status_pegawai?->status === 'Kontrak') {{ $pegawai?->tanggal_habis_kontrak?->translatedFormat('d M Y') ?? 'N/A' }}
                            @elseif ($pegawai?->status_pegawai?->status === 'Tetap') {{ $pegawai?->tanggal_pensiun?->translatedFormat('d M Y') ?? 'N/A' }}
                            @else N/A @endif
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== MAIN GRID ===== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 items-start">

        {{-- ================= LEFT CONTENT ================= --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- ===== INFORMASI PRIBADI ===== --}}
            <section class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">

                {{-- Header --}}
                <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-regular fa-address-card text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800">Informasi Pribadi</h2>
                        <p class="text-[11px] text-slate-500">Data identitas pegawai</p>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4">

                    @php
                        $personalFields = [
                            ['icon' => 'fa-regular fa-id-card',      'color' => 'text-indigo-500', 'label' => 'NIK KTP',                 'value' => $pegawai?->ktp ?? '-'],
                            ['icon' => 'fa-solid fa-file-invoice',    'color' => 'text-violet-500', 'label' => 'NPWP',                    'value' => $pegawai?->npwp ?? '-'],
                            ['icon' => 'fa-solid fa-cake-candles',    'color' => 'text-pink-500',   'label' => 'Tempat, Tanggal Lahir',   'value' => ($pegawai?->tempat_lahir ?? '-') . ', ' . ($pegawai?->tanggal_lahir?->translatedFormat('d F Y') ?? '-')],
                            ['icon' => 'fa-solid fa-venus-mars',      'color' => 'text-sky-500',    'label' => 'Jenis Kelamin',           'value' => $pegawai?->jenis_kelamin_label ?? '-'],
                            ['icon' => 'fa-solid fa-envelope',        'color' => 'text-blue-500',   'label' => 'Email YARSI',             'value' => $pegawai?->email_yarsi ?? '-'],
                            ['icon' => 'fa-solid fa-phone',           'color' => 'text-emerald-500','label' => 'No. Telepon',             'value' => $pegawai?->no_telpon ?? '-'],
                        ];
                    @endphp

                    {{-- Personal Fields Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach ($personalFields as $field)
                        <div class="flex items-start gap-3 rounded-xl bg-slate-50 border border-slate-100 p-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-slate-100 flex items-center justify-center {{ $field['color'] }} shrink-0 mt-0.5">
                                <i class="{{ $field['icon'] }} text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-medium">{{ $field['label'] }}</p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-800 leading-relaxed break-words">{{ $field['value'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Address Row --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-2.5">

                        {{-- Alamat KTP --}}
                        <div class="flex items-start gap-3 rounded-xl bg-orange-50 border border-orange-100 p-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-orange-100 flex items-center justify-center text-orange-500 shrink-0 mt-0.5">
                                <i class="fa-solid fa-house text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-medium">Alamat KTP</p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-800 leading-relaxed">{{ $pegawai?->alamat_ktp ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Alamat Domisili --}}
                        <div class="flex items-start gap-3 rounded-xl bg-rose-50 border border-rose-100 p-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-rose-100 flex items-center justify-center text-rose-500 shrink-0 mt-0.5">
                                <i class="fa-solid fa-map-location-dot text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-medium">Alamat Domisili</p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-800 leading-relaxed">
                                    @if ($pegawai?->alamat_domisili && $pegawai?->alamat_domisili !== $pegawai?->alamat_ktp)
                                        {{ $pegawai->alamat_domisili }}
                                    @else
                                        <span class="text-slate-400 font-normal italic text-xs">Sama dengan alamat KTP</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                    </div>

                </div>
            </section>

            {{-- ===== DATA KELUARGA ===== --}}
            <section class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">

                {{-- Header --}}
                <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-pink-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center">
                            <i class="fa-solid fa-people-roof text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-slate-800">Data Keluarga</h2>
                            <p class="text-[11px] text-slate-500">Data pasangan & tanggungan</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        data-modal-target="modal-tambah-keluarga"
                        data-modal-toggle="modal-tambah-keluarga"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-3 py-1.5 transition-colors shadow-sm">
                        <i class="fa-solid fa-plus text-[9px]"></i>
                        Tambah
                    </button>
                </div>

                {{-- Scroll Area --}}
                <div class="divide-y divide-slate-50 max-h-64 overflow-y-auto">

                    {{-- Empty State (tampilkan jika tidak ada data) --}}
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="w-12 h-12 rounded-full bg-pink-50 flex items-center justify-center mb-3">
                            <i class="fa-solid fa-people-roof text-pink-300 text-lg"></i>
                        </div>
                        <p class="text-sm font-medium text-slate-500">Belum ada data keluarga</p>
                        <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah" untuk menambahkan</p>
                    </div>

                    {{-- Item --}}
                    <div class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-slate-50/80 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-person-dress text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">Siti Aminah, S.E.</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px] text-slate-500">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 text-rose-600 px-2 py-0.5 font-medium">
                                        <i class="fa-solid fa-heart text-[7px]"></i>Istri
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span>Perempuan</span>
                                    <span class="text-slate-300">•</span>
                                    <span>Bandung, 12 Mei 1988</span>
                                </div>
                            </div>
                        </div>
                        <button class="w-7 h-7 rounded-lg hover:bg-rose-50 text-slate-300 hover:text-rose-500 transition-colors shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>

                    {{-- Tambah item lagi sesuai loop data $keluarga --}}

                </div>

            </section>

        </div>

        {{-- ================= RIGHT SIDEBAR ================= --}}
        <div class="space-y-4">

            {{-- ===== INFORMASI KEPEGAWAIAN ===== --}}
            <section class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">

                {{-- Header --}}
                <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-sky-50 to-white">
                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                        <i class="fa-solid fa-briefcase text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800">Informasi Kepegawaian</h2>
                        <p class="text-[11px] text-slate-500">Status dan data pekerjaan</p>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4 space-y-2">

                    @php
                        $statusClass = match($pegawai?->status) {
                            'active'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'inactive' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default    => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                        $employmentFields = [
                            ['label' => 'Jabatan',           'value' => $pegawai?->jabatan ?? '-',                                               'icon' => 'fa-solid fa-id-badge',        'color' => 'text-indigo-500'],
                            ['label' => 'Golongan',          'value' => $pegawai?->golongan ?? '-',                                              'icon' => 'fa-solid fa-layer-group',     'color' => 'text-blue-500'],
                            ['label' => 'Unit Kerja',        'value' => $pegawai?->unit_kerja?->name ?? '-',                                     'icon' => 'fa-solid fa-sitemap',         'color' => 'text-sky-500'],
                            ['label' => 'Tanggal Bergabung', 'value' => $pegawai?->tanggal_bergabung?->translatedFormat('d F Y') ?? '-',         'icon' => 'fa-regular fa-calendar-plus', 'color' => 'text-violet-500'],
                        ];
                    @endphp

                    {{-- Status Badge --}}
                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-slate-400 font-medium mb-2">Status Pegawai</p>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold border {{ $statusClass }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ $pegawai?->status_pegawai?->status ?? '-' }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">
                                {{ $pegawai?->jenis_pegawai?->jenis ?? '' }}
                            </span>
                        </div>
                    </div>

                    {{-- Employment Fields --}}
                    @foreach ($employmentFields as $ef)
                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 border border-slate-100 px-3 py-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white border border-slate-100 flex items-center justify-center {{ $ef['color'] }} shrink-0">
                            <i class="{{ $ef['icon'] }} text-[11px]"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] uppercase tracking-wide text-slate-400 font-medium">{{ $ef['label'] }}</p>
                            <p class="mt-0.5 text-xs font-semibold text-slate-800 truncate">{{ $ef['value'] }}</p>
                        </div>
                    </div>
                    @endforeach

                </div>
            </section>

            {{-- ===== DATA REKENING ===== --}}
            <section class="relative bg-gradient-to-br from-indigo-600 via-indigo-700 to-blue-700 rounded-2xl overflow-hidden shadow-lg text-white">

                {{-- Decorative --}}
                <div class="absolute inset-0 opacity-[0.08] pointer-events-none">
                    <div class="absolute -top-8 -right-8 w-36 h-36 bg-white rounded-full"></div>
                    <div class="absolute -bottom-10 -left-6 w-28 h-28 bg-white rounded-full"></div>
                </div>

                <div class="relative p-4">

                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-[9px] uppercase tracking-[0.2em] text-indigo-200/70 font-medium">Rekening Payroll</p>
                            <h2 class="mt-0.5 text-sm font-bold">{{ $pegawai?->bank ?? 'Bank BCA' }}</h2>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10">
                            <i class="fa-solid fa-building-columns text-sm"></i>
                        </div>
                    </div>

                    {{-- Card Number --}}
                    <div class="bg-white/10 rounded-xl px-4 py-3 border border-white/10 backdrop-blur-sm">
                        <p class="text-[9px] uppercase tracking-[0.2em] text-indigo-200/70 font-medium mb-1">Nomor Rekening</p>
                        <p class="text-base font-bold tracking-[0.15em] font-mono">
                            {{ $pegawai?->no_rekening ?? '**** **** ****' }}
                        </p>
                    </div>

                    {{-- Footer Info --}}
                    <div class="mt-3 flex items-center justify-between gap-2">
                        <div>
                            <p class="text-[9px] uppercase tracking-wide text-indigo-200/70 font-medium">Atas Nama</p>
                            <p class="mt-0.5 text-xs font-semibold">{{ $pegawai?->nama ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] uppercase tracking-wide text-indigo-200/70 font-medium">Status</p>
                            <p class="mt-0.5 text-xs font-semibold">Payroll Aktif</p>
                        </div>
                    </div>

                </div>
            </section>

            {{-- ===== RINGKASAN PEGAWAI ===== --}}
            <section class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">

                <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-cyan-50 to-white">
                    <div class="w-8 h-8 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center">
                        <i class="fa-solid fa-chart-simple text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800">Ringkasan</h2>
                        <p class="text-[11px] text-slate-500">Informasi singkat kepegawaian</p>
                    </div>
                </div>

                <div class="p-4 grid grid-cols-2 gap-2.5">
                    <div class="rounded-xl bg-indigo-50 border border-indigo-100 p-3 text-center">
                        <p class="text-[10px] uppercase tracking-wide text-indigo-400 font-medium">Masa Kerja</p>
                        <p class="mt-1 text-sm font-bold text-indigo-700">{{ $pegawai?->masa_kerja ?? '8 Tahun' }}</p>
                    </div>
                    <div class="rounded-xl bg-pink-50 border border-pink-100 p-3 text-center">
                        <p class="text-[10px] uppercase tracking-wide text-pink-400 font-medium">Tanggungan</p>
                        <p class="mt-1 text-sm font-bold text-pink-700">3 Orang</p>
                    </div>
                    <div class="rounded-xl bg-sky-50 border border-sky-100 p-3 text-center">
                        <p class="text-[10px] uppercase tracking-wide text-sky-400 font-medium">Sisa Cuti</p>
                        <p class="mt-1 text-sm font-bold text-sky-700">12 Hari</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center">
                        <p class="text-[10px] uppercase tracking-wide text-emerald-400 font-medium">Kehadiran</p>
                        <p class="mt-1 text-sm font-bold text-emerald-700">96%</p>
                    </div>
                </div>

            </section>

        </div>
    </div>
</div>

{{-- ===== MODAL: Tambah Anggota Keluarga ===== --}}
<div id="modal-tambah-keluarga" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-2xl shadow-xl">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-indigo-600 text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Tambah Anggota Keluarga</h3>
                </div>
                <button type="button"
                        data-modal-hide="modal-tambah-keluarga"
                        class="text-gray-400 hover:bg-gray-100 hover:text-gray-700 rounded-lg w-7 h-7 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-5 py-4 space-y-3.5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" placeholder="Contoh: Siti Aminah, S.E."
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition-all placeholder:text-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hubungan</label>
                    <select class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition-all bg-white">
                        <option value="">Pilih hubungan...</option>
                        <option>Istri</option>
                        <option>Suami</option>
                        <option>Anak</option>
                        <option>Orang Tua</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jenis Kelamin</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="Laki-laki" class="accent-indigo-600">
                            <span class="text-sm text-gray-700">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="Perempuan" class="accent-indigo-600">
                            <span class="text-sm text-gray-700">Perempuan</span>
                        </label>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tempat Lahir</label>
                        <input type="text" placeholder="Jakarta"
                               class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition-all placeholder:text-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Lahir</label>
                        <input type="date"
                               class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition-all">
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-gray-100">
                <button type="button"
                        data-modal-hide="modal-tambah-keluarga"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="button"
                        class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-colors focus:ring-4 focus:ring-indigo-200 focus:outline-none">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan
                </button>
            </div>

        </div>
    </div>
</div>

@endsection