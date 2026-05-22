@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Pengguna')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a href="{{ route('manajemen-pegawai') }}" class="text-indigo-600 hover:text-indigo-500">Pengguna</a>
    </div>
@endsection

@section('content')
    <div x-data="{
        openAddModal: false,
        openDetailModal: false,
        openLoadingDetail: false
    }" @open-add-modal.window="openAddModal = true" @close-add-modal.window="openAddModal = false"
        @open-detail-modal.window="openDetailModal = true; openLoadingDetail = false;"
        @close-detail-modal.window="openDetailModal = false" @open-loading-detail.window="openLoadingDetail = true"
        class="flex flex-col h-full min-h-0">

        {{-- Header --}}
        <div class="flex justify-between mb-4 ps-3 pt-1">
            <div class="flex flex-col gap-1 font-poppins">
                <h1 class="text-2xl font-semibold">Daftar Pengguna</h1>
                <p class="text-sm font-medium">Kelola akun pengguna dan hak akses</p>
            </div>
            <div x-data="{ show: false, message: '', type: 'success' }"
                x-on:notify.window="
                    show = true;
                    message = $event.detail.message;
                    type = $event.detail.type;
                    setTimeout(() => show = false, 10000)
                "
                x-show="show" x-transition class="mb-4 p-4 text-sm rounded-lg"
                :class="{
                    'bg-green-200 text-green-800': type === 'success',
                    'bg-red-200 text-red-800': type === 'error'
                }">
                <i class="fa-solid fa-circle-check mr-2"></i>
                <span x-text="message"></span>
            </div>
        </div>

        {{-- Tabel Pengguna --}}
        <div class="flex-1 bg-white/30 backdrop-blur-xl shadow-md rounded-xl p-6">
            <livewire:manajemen.users.index />
        </div>

        {{-- Modal Tambah Pengguna --}}
        <template x-teleport="body">
            <div x-show="openAddModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click.self="openAddModal = false"
                @keydown.escape.window="openAddModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
                style="display: none;">
                <div x-show="openAddModal" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.stop>
                    <livewire:manajemen.users.add-user />
                </div>
            </div>
        </template>

        {{-- Modal Detail Pengguna --}}
        <template x-teleport="body">
            <div x-show="openDetailModal || openLoadingDetail" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click.self="openDetailModal = false"
                @keydown.escape.window="openDetailModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
                style="display: none;">

                <div x-show="openLoadingDetail" class="flex flex-col items-center gap-4">
                    <div class="w-10 h-10 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                    <div class="text-sm font-medium tracking-wide text-white">
                        Memuat Data...
                    </div>
                </div>

                <div x-show="openDetailModal" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.stop>
                    <livewire:manajemen.users.detail-user />
                </div>
            </div>
        </template>
    </div>
@endsection
