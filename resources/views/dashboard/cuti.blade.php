@extends('layouts.app')

@section('title', 'SIPENA | Cuti')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a href="{{ route('cuti') }}" class="text-indigo-600 hover:text-indigo-500">Cuti</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Cuti</h1>
    <p>Selamat datang di halaman cuti. Di sini Anda dapat melihat dan mengelola informasi cuti Anda.</p>
@endsection