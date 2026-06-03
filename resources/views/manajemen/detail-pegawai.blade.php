@extends('layouts.app')

@section('title', 'SIPENA | Detail Pegawai')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
        <a wire:navigate href="{{ route('manajemen-pegawai') }}" class="hover:text-indigo-500">Pegawai</a>
        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
        <a wire:navigate href="{{ route('manajemen-pegawai-detail', $pegawai->id) }}"
            class="text-indigo-600 hover:text-indigo-500">Detail Pegawai</a>
    </div>
@endsection

@section('content')
    <div class="flex flex-col gap-6 bg-white/30 backdrop-blur-2xl p-3 rounded-lg min-h-[calc(100vh-157px)]">
        <h1>Detail Pegawai</h1>
        <p>{{ $pegawai->nama }}</p>
    </div>
@endsection
