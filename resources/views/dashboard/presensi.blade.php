@extends('layouts.app')

@section('title', 'SIPENA | Presensi')

@section('breadcrumb')
    <div class="flex flex-row gap-2 items-center">
        <span class="text-gray-500 font-bold text-sm">></span>
        <a href="{{ route('presensi') }}" class="text-gray-600 text-md font-bold">Presensi</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Presensi</h1>
    <p>Selamat datang di halaman presensi. Di sini Anda dapat melihat dan mengelola kehadiran Anda.</p>
@endsection

