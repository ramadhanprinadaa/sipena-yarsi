@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Pegawai')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('manajemen-pegawai') }}" class="text-indigo-400">Pegawai</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Manajemen Pegawai</h1>
    <p>Selamat datang di halaman manajemen pegawai. Di sini Anda dapat melihat dan mengelola informasi pegawai Anda.</p>
@endsection