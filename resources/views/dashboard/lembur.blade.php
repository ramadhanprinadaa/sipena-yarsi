@extends('layouts.app')

@section('title', 'SIPENA | Lembur')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('lembur') }}" class="text-indigo-400">Lembur</a>
    </div>
@endsection

@section('content')

    <div class="flex flex-col h-full min-h-0">

        <div class="flex justify-between mb-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold">Lembur</h1>
                <p class="text-sm text-gray-800">Kelola pengajuan lembur, surat perintah, dan rekapitulasi Anda</p>
            </div>
        </div>

        <livewire:dashboard.lembur.index />
        <livewire:dashboard.lembur.add-lembur />
        <livewire:dashboard.lembur.add-laporan />

    </div>

@endsection

