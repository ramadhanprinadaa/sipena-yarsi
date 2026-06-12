@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Pegawai')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
        <a wire:navigate href="{{ route('manajemen-pegawai') }}"
            class="text-indigo-600 hover:text-indigo-500 transition-colors duration-150">Pegawai</a>
    </div>
@endsection

@section('content')

    <div x-data="{ openAddModal: false, openImportModal: false, openProgressModal: false }" @open-progress-modal.window="openProgressModal = true"
        @close-add-modal.window="openAddModal = false" @close-import-modal.window="openImportModal = false"
        class="flex flex-col h-full min-h-0">

        <!-- Header -->
        <div class="flex items-end justify-between mb-4 ps-3 pt-1">
            @php
                $user = auth()->user();
                $unitName = null;
                if ($user->hasRole('SDM Universitas')) {
                    $unitName = $user->pegawai?->unit_kerja?->unitSdm?->name;
                }
                if ($user->hasRole('Pimpinan')) {
                    $unitName = $user->pegawai?->memimpin_unit?->name;
                }
            @endphp

            <div class="flex flex-col gap-1 font-poppins">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-semibold">
                        Database Pegawai
                    </h1>
                    @if ($unitName)
                        <span class="font-medium text-lg text-gray-600">
                            - {{ $unitName }}
                        </span>
                    @endif
                </div>
                <span class="text-sm font-medium">Kelola dan pantau data pegawai di organisasi anda.</span>
            </div>

            @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan']))
                <div class="flex flex-row justify-between gap-2">
                    <!-- Notification Message -->
                    <div x-data="{ show: false, message: '', type: 'success' }"
                        x-on:notify.window="
                            show = true;
                            message = $event.detail.message;
                            type = $event.detail.type;
                            setTimeout(() => show = false, 10000)
                        "
                        x-show="show" x-transition class="px-3 h-10 text-xs rounded-md flex items-center"
                        :class="{
                            'bg-green-200 text-green-800': type === 'success',
                            'bg-red-200 text-red-800': type === 'error'
                        }">
                        <i class="fa-solid fa-circle-check mr-2"></i>
                        <span x-text="message"></span>
                    </div>

                    <!-- Button Add Pegawai & Import -->
                    <div class="flex flex-none flex-wrap gap-2">
                        <button @click="$dispatch('export-table')"
                            class="flex items-center px-3 w-35 h-10 justify-center cursor-pointer bg-emerald-600 hover:bg-emerald-800 text-white text-sm rounded-md transition">
                            <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i>
                            Export Excel
                        </button>
                        <button @click="openImportModal = true"
                            class="flex items-center px-3 w-35 h-10 justify-center cursor-pointer bg-sky-600 hover:bg-sky-800 text-white text-sm rounded-md transition">
                            <i class="fa-solid fa-cloud-arrow-up mr-2"></i>
                            Import Excel
                        </button>
                        <button @click="openAddModal = true"
                            class="flex items-center px-3 h-10 justify-center cursor-pointer bg-indigo-500 hover:bg-indigo-700 text-white text-sm rounded-md transition">
                            <i class="fa-solid fa-user-plus mr-2"></i>
                            Tambah Pegawai
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Table Pegawai -->
        <div class="flex-1 bg-white/30 backdrop-blur-xl shadow-md rounded-xl p-6">
            <livewire:manajemen.pegawai.tabel-pegawai />
        </div>

        <!-- Modal Import Pegawai -->
        <template x-teleport="body">
            <div x-show="openImportModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click.self="openImportModal = false"
                @keydown.escape.window="openImportModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
                style="display: none;">
                <div x-show="openImportModal" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.stop>
                    <livewire:manajemen.pegawai.impor-pegawai />
                </div>
            </div>
        </template>

        <!-- Modal Progress Import -->
        <template x-teleport="body">
            <div x-show="openProgressModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click.self="openProgressModal = false"
                @keydown.escape.window="openProgressModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
                style="display: none;">
                <div x-show="openProgressModal" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.stop>

                    <h1>Progress Import</h1>
                </div>
            </div>
        </template>

        <!-- Modal Tambah Pegawai -->
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
                    <livewire:manajemen.pegawai.tambah-pegawai />
                </div>
            </div>
        </template>

    </div>

@endsection
