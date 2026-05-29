@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Presensi')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
        <a href="{{ route('manajemen-presensi') }}" class="text-indigo-600 hover:text-indigo-500">Presensi</a>
    </div>
@endsection

@section('content')
    <div x-data="{
            openDetailImport: false,
            openLoadingDetailImport: false,
            openDetailRiwayat: false,
            openLoadingDetailRiwayat: false,
        }"
        @open-loading-detail-import.window="openLoadingDetailImport = true"
        @open-loading-detail-riwayat.window="openLoadingDetailRiwayat = true"
        @open-detail-import.window="openDetailImport = true; openLoadingDetailImport = false"
        @open-detail-riwayat.window="openDetailRiwayat = true; openLoadingDetailRiwayat = false"
        @close-detail-import.window="openDetailImport = false"
        @close-detail-riwayat.window="openDetailRiwayat = false"
        class="flex flex-col gap-6">

        @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan']))
            <!-- Tabel Riwayat Impor File Presensi & Upload File Presensi -->
            <div class="grid grid-cols-1 lg:grid-cols-7 gap-4 items-stretch">

                <!-- Tabel Riwayat Impor File Presensi -->
                <div class="lg:col-span-5 bg-white/30 backdrop-blur-2xl p-3 rounded-lg h-full">
                    <livewire:manajemen.presensi.tabel-riwayat-import-presensi />
                </div>

                <!-- Upload File Presensi -->
                <div class="lg:col-span-2 bg-white/30 backdrop-blur-2xl p-3 rounded-lg h-full">
                    <livewire:manajemen.presensi.import-presensi />
                </div>
            </div>
        @endif

        <div class="flex flex-col gap-4 bg-white/30 backdrop-blur-2xl p-3 rounded-lg h-full">
            <livewire:manajemen.presensi.tabel-riwayat-presensi />
        </div>

        <div class="flex flex-col gap-4 bg-white/30 backdrop-blur-2xl p-3 rounded-lg h-full">
            <livewire:manajemen.presensi.tabel-rekapitulasi-presensi />
        </div>

        {{-- Modal Detail Import Presensi --}}
        <template x-teleport="body">
            <div x-show="openLoadingDetailImport || openDetailImport" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click.self="openDetailImport = false"
                @keydown.escape.window="openDetailImport = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
                style="display: none;">

                <div x-show="openLoadingDetailImport" class="flex flex-col items-center gap-4">
                    <div class="w-10 h-10 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                    <div class="text-sm font-medium tracking-wide text-white">
                        Memuat Data...
                    </div>
                </div>

                <div x-show="openDetailImport" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.stop>
                    <livewire:manajemen.presensi.detail-import-presensi />
                </div>
            </div>
        </template>

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
                    <livewire:manajemen.presensi.detail-riwayat-presensi />
                </div>
            </div>
        </template>
    </div>
@endsection
