@extends('layouts.app')

@section('title', 'SIPENA | Unit Kerja')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Konfigurasi</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a wire:navigate href="{{ route('konfigurasi-unit-kerja') }}" class="text-indigo-600 hover:text-indigo-500">Unit Kerja</a>
    </div>
@endsection

@section('content')
    <div
        x-data="{
            openModal: false,
            openDetail: false,
            openLoadingDetail: false
        }"
        @open-detail.window="openDetail = true; openLoadingDetail = false;"
        @open-loading-detail.window="openLoadingDetail = true;"
        @close-modal.window="openModal = false"
        @close-detail.window="openDetail = false"
        class="flex flex-col h-full min-h-0">

        {{-- Header --}}
        <div class="flex justify-between mb-4 ps-3 pt-1 items-end">
            <div class="flex flex-col gap-1 font-poppins">
                <h1 class="text-2xl font-semibold">Unit Kerja</h1>
                <p class="text-sm font-medium">Kelola daftar unit kerja dalam organisasi Anda.</p>
            </div>

            <div class="flex flex-row justify-between gap-2">
                {{-- Message Notification --}}
                <div
                    x-data="{ show: false, message: '', type: 'success' }"
                    x-on:notify.window="
                        show = true;
                        message = $event.detail.message;
                        type = $event.detail.type;
                        setTimeout(() => show = false, 10000)
                    "
                    x-show="show"
                    x-transition
                    class="px-3 h-10 text-xs rounded-md flex items-center"
                    :class="{
                        'bg-green-200 text-green-800': type === 'success',
                        'bg-red-200 text-red-800': type === 'error'
                    }"
                >
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    <span x-text="message"></span>
                </div>
                <button
                    @click="openModal = true"
                    class="flex items-center px-3 h-10 justify-center cursor-pointer bg-indigo-500 hover:bg-indigo-700 text-white text-sm rounded-md transition">
                    <i class="fa-solid fa-plus text-sm mr-2"></i>
                    Tambah Unit Kerja
                </button>
            </div>
        </div>

        <div class="flex-1 bg-white/30 backdrop-blur-xl shadow-md rounded-xl p-6">
            <livewire:config.unit-kerja.tabel-unit-kerja />
        </div>

        {{-- Modal Detail Unit Kerja --}}
        <template x-teleport="body">
            <div
                x-show="openLoadingDetail || openDetail"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click.self="openDetail = false"
                @keydown.escape.window="openDetail = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-md"
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
                    x-show="openDetail"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                    @click.stop>
                    <livewire:config.unit-kerja.detail-unit-kerja />
                </div>
            </div>
        </template>

        {{-- Modal Form Tambah Unit Kerja --}}
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
                    <livewire:config.unit-kerja.tambah-unit-kerja />
                </div>
            </div>
        </template>
    </div>
@endsection
