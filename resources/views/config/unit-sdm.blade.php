@extends('layouts.app')

@section('title', 'SIPENA | Unit SDM')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Konfigurasi</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('konfigurasi-unit-sdm') }}" class="text-indigo-400">Unit SDM</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Unit SDM</h1>
@endsection