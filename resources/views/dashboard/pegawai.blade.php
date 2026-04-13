@extends('layouts.app')

@section('title', 'SIPENA | Kepegawaian')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('kepegawaian') }}" class="text-indigo-400">Kepegawaian</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Kepegawaian</h1>
    <p>Selamat datang di halaman kepegawaian. Di sini Anda dapat melihat dan mengelola informasi kepegawaian Anda.</p>
@endsection