@extends('layouts.app')

@section('title', 'SIPENA | Cuti')

@section('breadcrumb')
    <div class="flex flex-row gap-2 items-center">
        <span class="text-gray-500 font-bold text-sm">></span>
        <a href="{{ route('cuti') }}" class="text-gray-600 text-md font-bold">Cuti</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Cuti</h1>
    <p>Selamat datang di halaman cuti. Di sini Anda dapat melihat dan mengelola informasi cuti Anda.</p>
@endsection