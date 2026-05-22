@extends('layouts.app')

@section('title', 'SIPENA | Presensi')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
        <a href="{{ route('presensi') }}" class="text-indigo-600 hover:text-indigo-500">Presensi</a>
    </div>
@endsection

@section('content')
    <div class="bg-white/20 backdrop-blur-sm shadow-md rounded-xl p-6 min-h-[calc(100vh-157px)]">
        <h1 class="text-2xl font-bold mb-4">Presensi</h1>
        <p>Selamat datang di halaman presensi. Di sini Anda dapat melihat dan mengelola kehadiran Anda.</p>
    </div>
@endsection

