@extends('layouts.app')

@section('title', 'SIPENA | Unit Kerja')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Konfigurasi</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('konfigurasi-unit-kerja') }}" class="text-indigo-400">Unit Kerja</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Unit Kerja</h1>
@endsection