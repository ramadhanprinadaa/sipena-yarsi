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
    <div
        x-data="{ openAddModal: false, openImportModal: false, openProgressModal: false }"
        class="flex flex-col h-full min-h-0"
    >

        <!-- Tabel Riwayat Impor File Presensi & Upload File Presensi -->
        <div class="grid grid-cols-1 lg:grid-cols-7 gap-3">

            <!-- Tabel Riwayat Impor File Presensi -->
            <div class="lg:col-span-5 bg-white/20 backdrop-blur-2xl p-3 rounded-lg h-auto lg:min-h-[calc(100vh-210px)]">
                <livewire:manajemen.presensi.tabel-riwayat-import-presensi />
            </div>

            <!-- Upload File Presensi -->
            <div class="lg:col-span-2 bg-white/20 backdrop-blur-2xl p-3 rounded-lg h-auto lg:min-h-[calc(100vh-210px)]">
                <livewire:manajemen.presensi.import-presensi />
            </div>
        </div>
    </div>
@endsection