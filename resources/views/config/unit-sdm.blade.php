@extends('layouts.app')

@section('title', 'SIPENA | Unit SDM')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Konfigurasi</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a href="{{ route('konfigurasi-unit-sdm') }}" class="text-indigo-600 hover:text-indigo-500">Unit SDM</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Unit SDM</h1>
@endsection