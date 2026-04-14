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
    <h1 class="text-2xl font-bold mb-4">Manajemen Pengguna</h1>
    <p>Selamat datang di halaman manajemen pengguna. Di sini Anda dapat melihat dan mengelola informasi pengguna Anda.</p>
@endsection