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
    <div class="flex flex-col h-full min-h-[calc(100vh-157px)]">

        <div class="flex items-end justify-between mb-4 ps-3 pt-1">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold">Manajemen Pengajuan Lembur</h1>
                <p class="text-sm text-gray-800">Kelola pengajuan lembur, surat perintah, dan rekapitulasi</p>
            </div>
        </div>

        <div class="flex-1 bg-white/30 backdrop-blur-xl shadow-md rounded-xl p-4">
            <livewire:manajemen.lembur.index />
        </div>

        <livewire:manajemen.lembur.add-spl />
        <livewire:manajemen.lembur.edit-spl />

    </div>
@endsection