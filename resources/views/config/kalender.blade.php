@extends('layouts.app')

@section('title', 'SIPENA | Kalender')

@section('breadcrumb')
    <div class="flex flex-wrap items-center space-x-2 text-sm font-medium text-gray-400">
        <span>Konfigurasi</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a wire:navigate href="{{ route('konfigurasi-kalender') }}" class="text-indigo-600 hover:text-indigo-500 transition">
            Kalender
        </a>
    </div>
@endsection

@section('content')
    <div
        x-data="{ openModal: false }"
        @close-add-modal.window="openModal = false"
    >
        <!-- Header Page -->
        <div class="flex items-end justify-between mb-4">
            <div class="font-poppins ms-2">
                <h1 class="text-2xl font-semibold text-gray-800">
                    Kalender
                </h1>
                <p class="mt-1 text-sm font-medium">
                    Kelola kalender akademik untuk mengatur hari libur dalam tahun ajaran.
                </p>
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

        <div class="flex-1">
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
    </div>
@endsection