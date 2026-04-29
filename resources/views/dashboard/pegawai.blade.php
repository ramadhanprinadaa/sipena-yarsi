@extends('layouts.app')

@section('title', 'SIPENA | Kepegawaian')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a href="{{ route('kepegawaian') }}" class="text-indigo-600 hover:text-indigo-500">Kepegawaian</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Kepegawaian</h1>
    <p>Selamat datang di halaman kepegawaian. Di sini Anda dapat melihat dan mengelola informasi kepegawaian Anda.</p>
@endsection