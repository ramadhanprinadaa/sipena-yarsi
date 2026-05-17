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
        <div class="relative bg-gradient-to-br from-indigo-700 via-indigo-600 to-blue-600 rounded-2xl shadow-xl overflow-hidden">
            {{-- Decorative background shapes --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-white rounded-full"></div>
                <div class="absolute -bottom-16 -left-10 w-80 h-80 bg-white rounded-full"></div>
            </div>
            <div class="relative p-6 flex flex-col md:flex-row items-center md:items-start gap-6">
                {{-- Avatar --}}
                <div class="relative shrink-0 self-center">
                    <div class="w-28 h-28 rounded-2xl bg-white/20 backdrop-blur-sm border-2 border-white/30 flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-user-tie text-5xl text-white"></i>
                    </div>
                    {{-- Status badge --}}
                    <span class="absolute -bottom-2 -right-2 inline-flex items-center gap-1
                    {{ $pegawai?->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}
                    text-xs font-bold px-2 py-0.5 rounded-full shadow">
                        <i class="fa-solid fa-circle text-[6px]"></i>
                        {{ $pegawai?->status_label ?? 'N/A' }}
                    </span>
                </div>
                {{-- Identity --}}
                <div class="flex-1 text-center md:text-left text-white self-center">
                    <p class="text-indigo-200 text-[12px] font-semibold uppercase tracking-widest mb-1">Profil Pegawai</p>
                    <h1 class="text-xl font-extrabold leading-tight">
                        {{ $pegawai?->nama_gelar ?? $pegawai?->nama ?? 'Data Tidak Ditemukan' }}
                    </h1>
                    <p class="mt-1.5 text-indigo-200 text-xs font-medium tracking-wide">
                        NIP: {{ $pegawai?->nip ?? 'N/A' }}
                    </p>
                    {{-- Badges --}}
                    <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-2">
                        <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-sm border border-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-md">
                            <i class="fa-solid fa-user-shield text-indigo-200"></i>
                            {{ $pegawai?->jenis_pegawai?->jenis ?? 'N/A' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-sm border border-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-md">
                            <i class="fa-solid fa-building text-indigo-200"></i>
                            {{ $pegawai?->unit_kerja?->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>
                {{-- QUICK STATS --}}
                <div class="hidden xl:grid grid-cols-2 gap-2.5 shrink-0 self-end">
                    {{-- Usia --}}
                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5 min-w-[165px]">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fa-regular fa-user text-[12px] text-white/80"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] uppercase tracking-[0.15em] text-indigo-100/60">
                                Usia
                            </p>
                            <p class="mt-0.5 text-xs font-semibold text-white truncate">
                                {{ $pegawai?->age ?? 'N/A' }} Tahun
                            </p>
                        </div>
                    </div>
                    {{-- Masa Kerja --}}
                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5 min-w-[165px]">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fa-regular fa-clock text-[12px] text-white/80"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] uppercase tracking-[0.15em] text-indigo-100/60">
                                Masa Kerja
                            </p>
                            <p class="mt-0.5 text-xs font-semibold text-white truncate">
                                {{ $pegawai?->masa_kerja ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    {{-- Bergabung --}}
                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5 min-w-[165px]">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fa-regular fa-calendar-plus text-[12px] text-white/80"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] uppercase tracking-[0.15em] text-indigo-100/60">
                                Bergabung
                            </p>
                            <p class="mt-0.5 text-xs font-semibold text-white truncate">
                                {{ $pegawai?->tanggal_bergabung?->translatedFormat('d M Y') ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    {{-- Kontrak / Pensiun --}}
                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5 min-w-[165px]">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fa-regular fa-calendar text-[12px] text-white/80"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] uppercase tracking-[0.15em] text-indigo-100/60">
                                @if ($pegawai?->status_pegawai?->status === 'Kontrak')
                                    Habis Kontrak
                                @elseif ($pegawai?->status_pegawai?->status === 'Tetap')
                                    Pensiun
                                @else
                                    Masa Kerja
                                @endif
                            </p>
                            <p class="mt-0.5 text-xs font-semibold text-white truncate">
                                @if ($pegawai?->status_pegawai?->status === 'Kontrak')
                                    {{ $pegawai?->tanggal_habis_kontrak?->translatedFormat('d M Y') ?? 'N/A' }}
                                @elseif ($pegawai?->status_pegawai?->status === 'Tetap')
                                    {{ $pegawai?->tanggal_pensiun?->translatedFormat('d M Y') ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
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
                                ['icon' => 'fa-regular fa-id-card', 'color' => 'text-indigo-500', 'label' => 'NIK KTP', 'value' => $pegawai?->ktp ?? '-'],
                                ['icon' => 'fa-solid fa-file-invoice', 'color' => 'text-violet-500', 'label' => 'NPWP', 'value' => $pegawai?->npwp ?? '-'],
                                ['icon' => 'fa-solid fa-cake-candles', 'color' => 'text-pink-500',   'label' => 'Tempat, Tanggal Lahir', 'value' => ($pegawai?->tempat_lahir ?? '-') . ', ' . ($pegawai?->tanggal_lahir?->translatedFormat('d F Y') ?? '-')],
                                ['icon' => 'fa-solid fa-venus-mars', 'color' => 'text-sky-500', 'label' => 'Jenis Kelamin', 'value' => $pegawai?->jenis_kelamin ?? '-'],
                                ['icon' => 'fa-solid fa-envelope', 'color' => 'text-blue-500', 'label' => 'Email YARSI',             'value' => $pegawai?->email_yarsi ?? '-'],
                                ['icon' => 'fa-solid fa-phone', 'color' => 'text-emerald-500','label' => 'No. Telepon',             'value' => $pegawai?->no_telpon ?? '-'],
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
                <section class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm h-[64vh] flex flex-col">
                    {{-- Header --}}
                    <div
                        class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-pink-50 to-white shrink-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-people-roof text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-sm font-semibold text-slate-800 truncate">
                                    Data Keluarga
                                </h2>
                                <p class="text-[11px] text-slate-500 truncate">
                                    Data pasangan & tanggungan pegawai
                                </p>
                            </div>
                        </div>
                        {{-- Total --}}
                        <div
                            class="px-2.5 py-1 rounded-lg bg-pink-100 text-pink-700 text-[11px] font-semibold shrink-0">
                            {{ $pegawai?->keluarga?->count() ?? 0 }} Orang
                        </div>
                    </div>
                    {{-- Table --}}
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full table-fixed border-collapse">
                            {{-- Head --}}
                            <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-100">
                                <tr class="text-[11px] uppercase tracking-wide text-slate-500">
                                    <th class="w-[28%] px-4 py-3 text-left font-semibold">
                                        Nama
                                    </th>
                                    <th class="w-[18%] px-3 py-3 text-left font-semibold">
                                        Hubungan
                                    </th>
                                    <th class="w-[24%] px-3 py-3 text-left font-semibold">
                                        TTL
                                    </th>
                                    <th class="w-[18%] px-3 py-3 text-left font-semibold">
                                        Pekerjaan
                                    </th>
                                    <th class="w-[12%] px-3 py-3 text-center font-semibold">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            {{-- Body --}}
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($pegawai?->keluarga ?? [] as $keluarga)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        {{-- Nama --}}
                                        <td class="px-4 py-3 align-middle">
                                            <div class="flex items-center gap-3 min-w-0">
                                                {{-- Avatar --}}
                                                <div
                                                    class="w-9 h-9 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-user text-sm"></i>
                                                </div>
                                                {{-- Content --}}
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold text-slate-800 truncate">
                                                        {{ $keluarga->nama ?? 'N/A' }}
                                                    </p>
                                                    <p class="text-[11px] text-slate-400 truncate">
                                                        {{ $keluarga->no_telpon ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Hubungan --}}
                                        <td class="px-3 py-3 align-middle">
                                            @php
                                                $hubungan = strtolower($keluarga->hubungan ?? '');
                                            @endphp
                                            <div class="truncate">
                                                @if (str_contains($hubungan, 'anak'))
                                                    <span
                                                        class="inline-flex max-w-full items-center gap-1 rounded-full bg-sky-50 text-sky-600 px-2 py-1 text-[11px] font-semibold truncate">
                                                        <i class="fa-solid fa-child-reaching text-[8px] shrink-0"></i>
                                                        <span class="truncate">
                                                            Anak
                                                        </span>
                                                    </span>
                                                @elseif (
                                                    str_contains($hubungan, 'orang tua') ||
                                                        str_contains($hubungan, 'ayah') ||
                                                        str_contains($hubungan, 'ibu'))
                                                    <span
                                                        class="inline-flex max-w-full items-center gap-1 rounded-full bg-amber-50 text-amber-600 px-2 py-1 text-[11px] font-semibold truncate">
                                                        <i class="fa-solid fa-people-group text-[8px] shrink-0"></i>
                                                        <span class="truncate">
                                                            Orang Tua
                                                        </span>
                                                    </span>
                                                @elseif (
                                                    str_contains($hubungan, 'istri') ||
                                                        str_contains($hubungan, 'suami') ||
                                                        str_contains($hubungan, 'pasangan'))
                                                    <span
                                                        class="inline-flex max-w-full items-center gap-1 rounded-full bg-pink-50 text-pink-600 px-2 py-1 text-[11px] font-semibold truncate">
                                                        <i class="fa-solid fa-heart text-[8px] shrink-0"></i>
                                                        <span class="truncate">
                                                            Pasangan
                                                        </span>
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex max-w-full items-center rounded-full bg-slate-100 text-slate-600 px-2 py-1 text-[11px] font-medium truncate">
                                                        {{ $keluarga->hubungan ?? '-' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        {{-- TTL --}}
                                        <td class="px-3 py-3 align-middle">
                                            <div class="min-w-0">
                                                <p class="text-xs text-slate-700 truncate">
                                                    {{ $keluarga->tempat_lahir ?? '-' }}
                                                </p>
                                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                    {{ $keluarga->tanggal_lahir
                                                        ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->translatedFormat('d M Y')
                                                        : '-' }}
                                                </p>
                                            </div>
                                        </td>
                                        {{-- Pekerjaan --}}
                                        <td class="px-3 py-3 align-middle">
                                            <p class="text-xs text-slate-700 truncate">
                                                {{ $keluarga->pekerjaan ?? '-' }}
                                            </p>
                                        </td>
                                        {{-- Action --}}
                                        <td class="px-3 py-3 align-middle text-center">
                                            <button
                                                type="button"
                                                class="w-8 h-8 rounded-xl hover:bg-rose-50 text-slate-300 hover:text-rose-500 transition-colors inline-flex items-center justify-center">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            {{-- Empty State --}}
                                            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                                                <div
                                                    class="w-14 h-14 rounded-full bg-pink-50 flex items-center justify-center mb-4">
                                                    <i class="fa-solid fa-people-roof text-pink-300 text-xl"></i>
                                                </div>
                                                <p class="text-sm font-medium text-slate-500">
                                                    Belum ada data keluarga
                                                </p>
                                                <p class="text-xs text-slate-400 mt-1">
                                                    Data keluarga pegawai akan tampil di sini
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
            {{-- ================= RIGHT SIDEBAR ================= --}}
            <div class="flex flex-col space-y-4">
                {{-- ===== INFORMASI KEPEGAWAIAN ===== --}}
                <section class="flex-1 bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm items-start">
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
                                ['label' => 'Jabatan', 'value' => $pegawai?->memimpin_unit ? 'Pimpinan' : ($pegawai ? 'Pegawai' : 'N/A'), 'icon' => 'fa-solid fa-id-badge', 'color' => 'text-indigo-500'],
                                ['label' => 'Unit Kerja', 'value' => $pegawai?->unit_kerja?->name ?? '-', 'icon' => 'fa-solid fa-sitemap', 'color' => 'text-sky-500'],
                                ['label' => 'Tanggal Bergabung', 'value' => $pegawai?->tanggal_bergabung?->translatedFormat('d F Y') ?? '-', 'icon' => 'fa-regular fa-calendar-plus', 'color' => 'text-violet-500'],
                            ];
                        @endphp
                        {{-- Status Badge --}}
                        <div class="flex items-center gap-3 rounded-xl bg-slate-50 border border-slate-100 p-3">
                            <div class="w-7 h-7 rounded-lg bg-white border border-slate-100 flex items-center justify-center text-emerald-500 shrink-0">
                                <i class="fa-solid fa-user-pen text-[11px]"></i>
                            </div>
                            <div class="w-full justify-between">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-medium mb-2">Status Pegawai</p>
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold border {{ $statusClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        {{ $pegawai?->status_pegawai?->status ?? '-' }}
                                    </span>
                                    <span class="text-[12px] text-slate-400 font-medium">
                                        {{ $pegawai?->jenis_pegawai?->jenis ?? '' }}
                                    </span>
                                </div>
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
                                <p class="mt-1 text-sm font-bold text-indigo-700">{{ $pegawai?->masa_kerja ?? 'N/A' }}</p>
                            </div>
                            <div class="rounded-xl bg-pink-50 border border-pink-100 p-3 text-center">
                                <p class="text-[10px] uppercase tracking-wide text-pink-400 font-medium">Tanggungan</p>
                                <p class="mt-1 text-sm font-bold text-pink-700">{{ $pegawai?->keluarga->count() . ' Orang' ?? 'N/A' }}</p>
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
                                <h2 class="mt-0.5 text-sm font-bold">{{ 'Bank ' . ($pegawai?->rekening?->nama_bank ?? 'Mandiri') }}</h2>
                            </div>
                            <div class="w-9 h-9 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10">
                                <i class="fa-solid fa-building-columns text-sm"></i>
                            </div>
                        </div>
                        {{-- Card Number --}}
                        <div class="bg-white/10 rounded-xl px-4 py-3 border border-white/10 backdrop-blur-sm">
                            <p class="text-[9px] uppercase tracking-[0.2em] text-indigo-200/70 font-medium mb-1">Nomor Rekening</p>
                            <p class="text-base font-bold tracking-[0.15em] font-mono">
                                {{ $pegawai?->rekening->nomor_rekening ?? '**** **** ****' }}
                            </p>
                        </div>
                        {{-- Footer Info --}}
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <div>
                                <p class="text-[9px] uppercase tracking-wide text-indigo-200/70 font-medium">Atas Nama</p>
                                <p class="mt-0.5 text-xs font-semibold">{{ $pegawai?->rekening->nama_rekening ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] uppercase tracking-wide text-indigo-200/70 font-medium">Status</p>
                                <p class="mt-0.5 text-xs font-semibold">Payroll Aktif</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection