@extends('layouts.app')

@section('title', 'SIPENA | Kepegawaian')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right text-xs"></i>
        <a wire:navigate href="{{ route('kepegawaian') }}" class="text-indigo-600 hover:text-indigo-500">Pegawai</a>
    </div>
@endsection

@section('content')

<div x-data="{
        activeTab: $persist('biodata').as('tab_aktif_detail_pegawai'),
    }"
    class="flex flex-col space-y-3">

    {{-- ===== HERO CARD ===== --}}
    <div
        class="relative bg-gradient-to-br from-indigo-700 via-indigo-600 to-blue-600 rounded-2xl shadow-xl overflow-hidden">
        {{-- Decorative background shapes --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-10 -right-10 w-64 h-64 bg-white rounded-full"></div>
            <div class="absolute -bottom-16 -left-10 w-80 h-80 bg-white rounded-full"></div>
        </div>
        <div class="relative p-6 flex flex-col md:flex-row items-center md:items-start gap-6">
            {{-- Avatar --}}
            <div class="relative shrink-0 self-center hover:-rotate-3 transform transition-transform duration-300">
                <div
                    class="w-28 h-28 rounded-2xl bg-white/20 backdrop-blur-sm border-2 border-white/30 flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-user-tie text-6xl text-white"></i>
                </div>
                {{-- Status badge --}}
                <span
                    class="absolute -bottom-2 -right-2 inline-flex items-center gap-1
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
                    {{ $pegawai?->nama_gelar ?? ($pegawai?->nama ?? 'Data Tidak Ditemukan') }}
                </h1>
                <p class="mt-1.5 text-indigo-200 text-xs font-medium tracking-wide">
                    NIP: {{ $pegawai?->nip ?? 'N/A' }}
                </p>
                {{-- Badges --}}
                <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-2">
                    <span
                        class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-sm border border-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-md">
                        <i class="fa-solid fa-user-shield text-indigo-200"></i>
                        {{ $pegawai?->jenis_pegawai?->jenis ?? 'N/A' }}
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-sm border border-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-md">
                        <i class="fa-solid fa-building text-indigo-200"></i>
                        {{ $pegawai?->unit_kerja?->name ?? 'N/A' }}
                    </span>
                </div>
            </div>

            {{-- QUICK STATS --}}
            <div class="hidden xl:grid grid-cols-2 gap-2.5 shrink-0 self-end">
                {{-- Usia --}}
                <div
                    class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5 min-w-[165px]">
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
                <div
                    class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5 min-w-[165px]">
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
                <div
                    class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5 min-w-[165px]">
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
                <div
                    class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/10 backdrop-blur-md px-3 py-2.5 min-w-[165px]">
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

    {{-- ===== TABS & CONTENT CONTAINER ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        @php
            $tabs = [
                ['key' => 'biodata', 'label' => 'Biodata', 'icon' => 'fa-user'],
                ['key' => 'keluarga', 'label' => 'Keluarga', 'icon' => 'fa-people-roof'],
                ['key' => 'rekening', 'label' => 'Rekening', 'icon' => 'fa-building-columns'],
                ['key' => 'pendidikan', 'label' => 'Pendidikan', 'icon' => 'fa-graduation-cap'],
                ['key' => 'kepegawaian', 'label' => 'Kepegawaian', 'icon' => 'fa-briefcase'],
                ['key' => 'kepangkatan', 'label' => 'Kepangkatan', 'icon' => 'fa-medal'],
                ['key' => 'pelatihan', 'label' => 'Pelatihan', 'icon' => 'fa-chalkboard-user'],
                ['key' => 'tugas-belajar', 'label' => 'Tugas Belajar', 'icon' => 'fa-book-open-reader'],
                ['key' => 'arsip-dokumen', 'label' => 'Arsip Dokumen', 'icon' => 'fa-folder-open'],
            ];
        @endphp

        {{-- Navigation Tabs --}}
        <div class="border-b border-gray-200 bg-slate-50/50">
            <div
                class="
                    overflow-x-auto px-2 sm:px-6 flex
                    [&::-webkit-scrollbar]:h-1.5
                    [&::-webkit-scrollbar-track]:bg-transparent
                    [&::-webkit-scrollbar-thumb]:rounded-full
                    [&::-webkit-scrollbar-thumb]:bg-slate-300
                    hover:[&::-webkit-scrollbar-thumb]:bg-slate-400
                ">
                <ul class="flex flex-nowrap items-center text-sm font-semibold text-center text-gray-500 min-w-max">
                    @foreach ($tabs as $tab)
                        <li class="mr-1">
                            <button @click="activeTab = '{{ $tab['key'] }}'"
                                class="inline-flex items-center justify-center py-4 px-5 sm:px-6 rounded-t-lg transition-all duration-200 cursor-pointer outline-none hover:text-gray-700 hover:bg-gray-100"
                                :class="{ 'text-indigo-600 border-indigo-600 bg-white shadow-[0_-2px_0_0_#5d4aec_inset] hover:bg-white': activeTab === '{{ $tab['key'] }}' }">

                                <i class="fa-solid {{ $tab['icon'] }} mr-2.5 text-base transition-colors"
                                    :class="{ 'text-indigo-600': activeTab === '{{ $tab['key'] }}' }">
                                </i>

                                <span>{{ $tab['label'] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="relative p-5 min-h-[calc(100vh-395px)]">

            {{-- Wrapper Tab Biodata --}}
            <div x-show="activeTab === 'biodata'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">
                <livewire:dashboard.pegawai.biodata :pegawai="$pegawai" />
            </div>

            {{-- Wrapper Tab Keluarga --}}
            <div x-show="activeTab === 'keluarga'" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">
                <livewire:dashboard.pegawai.keluarga :pegawai_id="$pegawai?->id" />
            </div>

            {{-- Wrapper Tab Rekening --}}
            <div x-show="activeTab === 'rekening'" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">
                <livewire:dashboard.pegawai.rekening :pegawai_id="$pegawai?->id" />
            </div>

            {{-- Wrapper Tab Rekening --}}
            <div x-show="activeTab === 'pendidikan'" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">
                <livewire:dashboard.pegawai.pendidikan :pegawai_id="$pegawai?->id" />
            </div>

            <div x-show="activeTab === 'kepegawaian'" x-cloak>
                <x-under-development title="Riwayat Kepegawaian" icon="fa-briefcase" />
            </div>

            <div x-show="activeTab === 'kepangkatan'" x-cloak>
                <x-under-development title="Riwayat Kepangkatan" icon="fa-medal" />
            </div>

            <div x-show="activeTab === 'pelatihan'" x-cloak>
                <x-under-development title="Riwayat Pelatihan" icon="fa-chalkboard-user" />
            </div>

            <div x-show="activeTab === 'tugas-belajar'" x-cloak>
                <x-under-development title="Riwayat Tugas Belajar" icon="fa-book-open-reader" />
            </div>

            <div x-show="activeTab === 'arsip-dokumen'" x-cloak>
                <x-under-development title="Riwayat Arsip Dokumen" icon="fa-book-open-reader" />
            </div>

        </div>
    </div>
</div>
@endsection
