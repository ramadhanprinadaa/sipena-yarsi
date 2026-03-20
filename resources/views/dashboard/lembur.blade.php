@extends('layouts.app')

@section('title', 'SIPENA | Lembur')

@section('breadcrumb')
    <div class="flex flex-row gap-2 items-center">
        <span class="text-gray-500 font-bold text-sm">></span>
        <a href="{{ route('lembur') }}" class="text-gray-600 text-md font-bold">Lembur</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Lembur</h1>
    <p>Selamat datang di halaman lembur. Di sini Anda dapat melihat dan mengelola informasi lembur Anda.</p>
@endsection