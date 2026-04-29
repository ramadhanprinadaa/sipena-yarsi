@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Lembur')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a href="{{ route('manajemen-lembur') }}" class="text-indigo-600 hover:text-indigo-500">Pengajuan Lembur</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Manajemen Pengajuan Lembur</h1>
    <p>Selamat datang di halaman manajemen pengajuan lembur. Di sini Anda dapat melihat dan mengelola informasi pengajuan lembur pegawai Anda.</p>
@endsection