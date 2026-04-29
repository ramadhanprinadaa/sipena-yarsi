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
    <div class="flex flex-col h-full min-h-0">
        <div class="flex items-end justify-between mb-4">
            <div class="flex flex-col gap-2 font-poppins">
                <h1 class="text-2xl font-semibold">Unit Kerja</h1>
                <p class="text-sm font-medium">Kelola daftar unit kerja dalam organisasi Anda.</p>
            </div>

            <button class="flex items-center px-3 h-10 justify-center cursor-pointer bg-indigo-500 hover:bg-indigo-700 text-white text-sm rounded-md transition">
                <i class="fa-solid fa-plus text-sm mr-2"></i>
                Tambah Unit Kerja
            </button>
        </div>

        <div class="flex-1">
            <livewire:config.unit-kerja.tabel-unit-kerja/>
        </div>
    </div>
@endsection