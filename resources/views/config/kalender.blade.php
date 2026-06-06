@extends('layouts.app')

@section('title', 'SIPENA | Kalender')

@section('breadcrumb')
    <div class="flex flex-wrap items-center space-x-2 text-sm font-medium text-gray-500">
        <span>Konfigurasi</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
        <a wire:navigate href="{{ route('konfigurasi-kalender') }}" class="text-indigo-600 hover:text-indigo-500 transition">
            Kalender
        </a>
    </div>
@endsection

@section('content')
    <div
        x-data="{
            openModal: false,
            openDetailModal: false,
            openLoadingDetail: false
        }"
        @open-detail-modal.window="openDetailModal = true; openLoadingDetail = false;"
        @open-loading-detail.window="openLoadingDetail = true;"
        @close-add-modal.window="openModal = false"
        @close-detail-modal.window="openDetailModal = false"
        class="flex flex-col h-full min-h-0"
    >
        <!-- Header -->
        <div class="flex justify-between mb-4 ps-3 pt-1 items-end">

            {{-- Header --}}
            <div class="flex flex-col gap-1 font-poppins">
                <h1 class="text-2xl font-semibold">Kalender</h1>
                <p class="text-sm font-medium">Kelola kalender akademik untuk mengatur hari libur dalam tahun ajaran.</p>
            </div>
            <!-- Action -->
            <div class="flex items-center gap-2">
                <button
                    @click="openModal = true"
                    class="inline-flex items-center h-10 px-4 text-sm font-medium text-white transition bg-indigo-500 rounded-md shadow-sm hover:bg-indigo-600 cursor-pointer">
                    <i class="mr-2 fa-solid fa-plus text-xs"></i>
                    Tambah Hari Libur
                </button>
            </div>
        </div>

        <div class="flex-1 bg-white/30 backdrop-blur-xl shadow-md rounded-xl p-6">
            <livewire:config.kalender.index />
        </div>

        <!-- Modal Tambah Hari Libur -->
        <template x-teleport="body">
            <div
                x-show="openModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click.self="openModal = false"
                @keydown.escape.window="openModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
                style="display: none;">
                <div
                    x-show="openModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                    @click.stop>
                    <livewire:config.kalender.tambah-hari-libur />
                </div>
            </div>
        </template>

        <!-- Modal Detail Hari -->
        <template x-teleport="body">
            <div
                x-show="openLoadingDetail || openDetailModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click.self="openDetailModal = false"
                @keydown.escape.window="openDetailModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
                style="display: none;">

                <div
                    x-show="openLoadingDetail"
                    class="flex flex-col items-center gap-4">
                    <div class="w-10 h-10 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                    <div class="text-sm font-medium tracking-wide text-white">
                        Memuat Data...
                    </div>
                </div>
                <div
                    x-show="openDetailModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                    @click.stop>
                    <livewire:config.kalender.detail-tanggal />
                </div>
            </div>
        </template>
    </div>
@endsection