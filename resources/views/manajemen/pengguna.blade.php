@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Pengguna')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('manajemen-pegawai') }}" class="text-indigo-400">Pengguna</a>
    </div>
@endsection

@section('content')
    <div class="flex flex-col h-full min-h-0">

        <div class="flex flex-col mb-2">
            <h1 class="text-2xl font-bold">Daftar Pengguna</h1>
            <p class="text-sm text-gray-800">Kelola akun pengguna dan hak akses</p>
        </div>
        <div class="flex-1">
            <livewire:users.index />
            <livewire:users.add-user />
        </div>
    </div>
@endsection