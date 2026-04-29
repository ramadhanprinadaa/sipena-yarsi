@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Pegawai')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a wire:navigate href="{{ route('manajemen-pegawai') }}" class="text-indigo-400">Pegawai</a>
    </div>
@endsection

@section('content')

    <div class="flex flex-col h-full min-h-0">
        <div class="flex items-end justify-between mb-4">
            <div class="flex flex-col gap-2 font-poppins">
                <h1 class="text-2xl font-semibold">Database Pegawai</h1>
                <span class="text-sm font-medium">Kelola dan pantau data pegawai di organisasi anda.</span>
            </div>

            @php
                $user = auth()->user();
                $unitName = null;
                if ($user->hasRole('SDM Universitas')) {
                    $unitName = $user->pegawai?->unit_kerja?->unitSdm?->name;
                }
                if ($user->hasRole('Pimpinan')) {
                    $unitName = $user->pegawai?->unit_kerja?->name;
                }
            @endphp

            @if ($unitName)
                <div class="flex items-center gap-4 px-4 py-3 bg-gray-50 rounded-xl border border-gray-200 w-fit">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-teal-50">
                        <i class="fa-solid fa-building-user text-teal text-lg"></i>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-xs text-gray-500 font-medium">
                            Unit Kerja
                        </span>
                        <h1 class="text-xl font-semibold font-poppins text-gray-800">
                            {{ $unitName }}
                        </h1>
                    </div>
                </div>
            @endif

            @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan']))
                <div class="flex gap-2">
                    <button class="flex items-center px-3 h-10 justify-center cursor-pointer bg-emerald-600 hover:bg-emerald-800 text-white text-sm rounded-md transition">
                        <i class="fa-solid fa-upload mr-2"></i>
                        Import Excel
                    </button>
                    <button class="flex items-center px-3 h-10 justify-center cursor-pointer bg-indigo-500 hover:bg-indigo-700 text-white text-sm rounded-md transition">
                        <i class="fa-solid fa-user-plus mr-2"></i>
                        Tambah Pegawai
                    </button>
                </div>
            @endif
        </div>

        <!-- Table Pegawai -->
        <div class="flex-1">
            <livewire:manajemen.pegawai.tabel-pegawai />
        </div>

    </div>

@endsection