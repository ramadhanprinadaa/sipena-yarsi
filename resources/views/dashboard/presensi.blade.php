@extends('layouts.app')

@section('title', 'SIPENA | Presensi')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
        <a href="{{ route('presensi') }}" class="text-indigo-600 hover:text-indigo-500">Presensi</a>
    </div>
@endsection

@section('content')
    <div
        x-data="{
            activeTab: $persist('ringkasan').as('tab_aktif_presensi'),
            openLoadingDetailRiwayat: false,
            openDetailRiwayat: false,
        }"
        @open-loading-detail-riwayat.window="openLoadingDetailRiwayat = true"
        @open-detail-riwayat.window="openDetailRiwayat = true; openLoadingDetailRiwayat = false"
        @close-detail-riwayat.window="openDetailRiwayat = false"
        class="w-full">

        {{-- ===== HERO CARD ===== --}}
        <div
            class="relative bg-gradient-to-br from-teal-600 via-emerald-600 to-cyan-600 rounded-2xl shadow-lg overflow-hidden">
            {{-- Decorative background shapes --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-16 -right-16 w-72 h-72 bg-white rounded-full"></div>
                <div class="absolute -bottom-20 -left-10 w-80 h-80 bg-white rounded-full"></div>
            </div>

            <div class="relative p-6 flex flex-col md:flex-row items-center md:items-center gap-6">

                {{-- Main Icon (Glassmorphism) --}}
                <div class="shrink-0">
                    <div
                        class="w-20 h-20 bg-white/20 border border-white/30 backdrop-blur-md rounded-2xl flex items-center justify-center text-white shadow-inner transform rotate-3 hover:rotate-0 transition-transform duration-300">
                        <i class="fa-regular fa-calendar-check text-4xl drop-shadow-md"></i>
                    </div>
                </div>

                {{-- Hero Text --}}
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">Presensi Saya</h1>
                    <p class="text-emerald-50 mt-1.5 text-sm max-w-xl leading-relaxed">
                        Pantau ringkasan kehadiran, statistik keterlambatan, dan riwayat presensi harian Anda secara mandiri
                        di sini.
                    </p>
                </div>

                {{-- Info Badges --}}
                <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">

                    {{-- Badge Tanggal --}}
                    <div
                        class="flex items-center gap-3 px-4 py-3 min-w-[200px]
                bg-white/10 backdrop-blur-md border border-white/20
                rounded-xl shadow-sm">

                        <div
                            class="flex items-center justify-center shrink-0 w-10 h-10 rounded-full bg-emerald-500/40 border border-emerald-300/30">
                            <i class="fa-regular fa-calendar-days text-lg text-white"></i>
                        </div>

                        <div class="flex flex-col min-w-0">
                            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-100">
                                Tanggal Hari Ini
                            </span>

                            <span class="text-sm font-bold text-white leading-tight">
                                {{ now()->translatedFormat('l, d F Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Badge Jam --}}
                    <div
                        class="flex items-center gap-3 px-4 py-3 min-w-[200px] bg-white/10 backdrop-blur-md border border-white/20 rounded-xl shadow-sm">

                        <div
                            class="flex items-center justify-center shrink-0 w-10 h-10 rounded-full bg-emerald-500/40 border border-emerald-300/30">
                            <i class="fa-regular fa-clock text-lg text-white animate-pulse"></i>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-100">
                                Waktu Saat Ini
                            </span>

                            <span class="text-base font-bold text-white font-mono leading-tight" x-data="{ time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }"
                                x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)" x-text="time">
                            </span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- ===== TABS & CONTENT CONTAINER ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-4">

            {{-- Navigation Tabs --}}
            <div class="border-b border-gray-200 bg-slate-50/50 px-2 sm:px-6">
                <ul class="flex flex-wrap -mb-px text-sm font-semibold text-center text-gray-500">
                    <li class="mr-2">
                        <button @click="activeTab = 'ringkasan'"
                            :class="activeTab === 'ringkasan' ?
                                'text-teal-600 border-teal-600 bg-white shadow-[0_-2px_0_0_#0d9488_inset]' :
                                'border-transparent hover:text-gray-700 hover:border-gray-300'"
                            class="inline-flex items-center justify-center py-4 px-5 sm:px-6 rounded-t-lg transition-all duration-200 cursor-pointer outline-none">
                            <i class="fa-solid fa-chart-pie mr-2.5 text-base transition-colors"
                                :class="activeTab === 'ringkasan' ? 'text-teal-600' : 'text-gray-400'"></i>
                            Ringkasan Kehadiran
                        </button>
                    </li>
                    <li class="mr-2">
                        <button @click="activeTab = 'riwayat'"
                            :class="activeTab === 'riwayat' ?
                                'text-teal-600 border-teal-600 bg-white shadow-[0_-2px_0_0_#0d9488_inset]' :
                                'border-transparent hover:text-gray-700 hover:border-gray-300'"
                            class="inline-flex items-center justify-center py-4 px-5 sm:px-6 rounded-t-lg transition-all duration-200 cursor-pointer outline-none">
                            <i class="fa-solid fa-table-list mr-2.5 text-base transition-colors"
                                :class="activeTab === 'riwayat' ? 'text-teal-600' : 'text-gray-400'"></i>
                            Riwayat Presensi
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Main Content Area --}}
            <div class="relative p-5 min-h-[calc(100vh-365px)]">

                {{-- Wrapper Tab Ringkasan --}}
                <div x-show="activeTab === 'ringkasan'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;">

                    <livewire:dashboard.presensi.ringkasan-presensi />

                </div>

                {{-- Wrapper Tab Riwayat --}}
                <div x-show="activeTab === 'riwayat'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;">

                    <livewire:dashboard.presensi.riwayat-presensi />

                </div>
            </div>

        </div>

        {{-- Modal Detail Riwayat Presensi --}}
        <template x-teleport="body">
            <div x-show="openLoadingDetailRiwayat || openDetailRiwayat"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click.self="openDetailRiwayat = false"
                @keydown.escape.window="openDetailRiwayat = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
                style="display: none;">

                <div x-show="openLoadingDetailRiwayat" class="flex flex-col items-center gap-4">
                    <div class="w-10 h-10 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                    <div class="text-sm font-medium tracking-wide text-white">
                        Memuat Data...
                    </div>
                </div>

                <div x-show="openDetailRiwayat" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.stop>
                    <livewire:dashboard.presensi.detail-riwayat-presensi />
                </div>
            </div>
        </template>
    </div>
@endsection
