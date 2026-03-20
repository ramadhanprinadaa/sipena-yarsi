@extends('layouts.app')

@section('title', 'SIPENA | Kepegawaian')

@section('breadcrumb')
    <div class="flex flex-row gap-2 items-center">
        <span class="text-gray-500 font-bold text-sm">></span>
        <a href="{{ route('kepegawaian') }}" class="text-gray-600 text-md font-bold">Kepegawaian</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Kepegawaian</h1>
    <p>Selamat datang di halaman kepegawaian. Di sini Anda dapat melihat dan mengelola informasi kepegawaian Anda.</p>
@endsection