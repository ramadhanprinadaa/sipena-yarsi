@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Lembur')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('manajemen-lembur') }}" class="text-indigo-400">Pengajuan Lembur</a>
    </div>
@endsection

@section('content')
    <div class="flex flex-col h-full min-h-0">

        <div class="flex justify-between mb-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold">Manajemen Pengajuan Lembur</h1>
                <p class="text-sm text-gray-800">Kelola pengajuan lembur, surat perintah, dan rekapitulasi</p>
            </div>
        </div>
        
        <livewire:manajemen.lembur.index />
        <livewire:manajemen.lembur.add-spl />
        <livewire:manajemen.lembur.edit-spl />
        
    </div>
@endsection