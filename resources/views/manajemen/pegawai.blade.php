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

    <div class="flex items-end justify-between mb-4">
        <div class="flex flex-col gap-2 font-poppins">
            <h1 class="text-2xl font-semibold">Database Pegawai</h1>
            <span class="text-sm font-medium">Kelola dan pantau data pegawai di organisasi anda.</span>
        </div>

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
    <div class="mb-4">
        <livewire:manajemen.pegawai.tabel-pegawai />
    </div>

    {{-- <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawai as $p)
                <tr>
                    <td>
                        <p class="font-medium text-gray-800">{{ $p->nama }}</p>
                    </td>
                    <td>
                        <a wire:navigate href="{{ route('manajemen-pegawai-detail', $p->id) }}" class="text-indigo-400">Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table> --}}
@endsection