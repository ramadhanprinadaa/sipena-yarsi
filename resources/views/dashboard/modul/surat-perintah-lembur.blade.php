@extends('layouts.app')

@section('title', 'SIPENA | Surat Perintah Lembur')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-1 text-sm text-gray-500 font-medium">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="m14.413 10.663-6.25 6.25a.939.939 0 1 1-1.328-1.328L12.42 10 6.836 4.413a.939.939 0 1 1 1.328-1.328l6.25 6.25a.94.94 0 0 1-.001 1.328" fill="#CBD5E1"/>
        </svg>

        <a href="#">Modul</a>
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.784 15.68 11.46 4.13h1.75L8.534 15.68z" fill="#CBD5E1"/>
        </svg>
        <a href="{{ route('modul.surat-perintah-lembur') }}" class="text-cyan-500">Surat Perintah Lembur</a>
    </div>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Surat Perintah Lembur</h1>
    <p>Selamat datang di halaman surat perintah lembur. Di sini Anda dapat melihat dan mengelola informasi surat perintah lembur Anda.</p>
@endsection