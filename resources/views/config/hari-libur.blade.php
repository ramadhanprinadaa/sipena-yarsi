@extends('layouts.app')

@section('title', 'SIPENA | Hari Libur')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Konfigurasi</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a href="{{ route('konfigurasi-hari-libur') }}" class="text-indigo-600 hover:text-indigo-500">Hari Libur</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Hari Libur</h1>
@endsection