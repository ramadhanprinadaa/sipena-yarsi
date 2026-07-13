@extends('layouts.app')

@section('title', 'SIPENA | Sumber Daya')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Sumber Daya</span>
        <i class="fa-solid fa-chevron-right text-xs"></i>
        <a href="{{ route('sumber-daya') }}" class="text-indigo-600 hover:text-indigo-500">Sumber Daya</a>
    </div>
@endsection

@section('content')
    <div
        x-data="{
            openAddModal: false
        }"
        @close-add-modal.window="openAddModal = false"
        class="flex flex-col h-full min-h-0">

        <!-- Header -->
        <div class="flex items-end justify-between mb-4 ps-3 pt-1">
            <div class="flex flex-col gap-1 font-poppins">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-semibold">
                        Sumber Daya
                    </h1>
                </div>
                <span class="text-sm font-medium">Kumpulan dokumen, panduan, dan sumber informasi untuk mendukung layanan serta proses administrasi.</span>
            </div>

            <!-- Button Tambah Sumber Daya  -->
            @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan']))
                <div class="flex flex-row justify-between gap-2">

                    <button @click="openAddModal = true"
                        class="flex items-center px-3 h-10 justify-center cursor-pointer bg-indigo-500 hover:bg-indigo-700 text-white text-sm rounded-md transition">
                        <i class="fa-solid fa-folder-plus mr-2"></i>
                        Tambah Sumber Daya
                    </button>
                </div>
            @endif
        </div>

        <!-- Table Sumber Daya -->
        <div class="flex-1 bg-white/30 backdrop-blur-xl shadow-md rounded-xl p-6">
            <livewire:resources.tabel-sumber-daya />
        </div>

        <!-- Modal Sumber Daya -->
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
                    <livewire:resources.tambah-sumber-daya />
                </div>
            </div>
        </template>
    </div>
@endsection
