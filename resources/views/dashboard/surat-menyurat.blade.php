@extends('layouts.app')

@section('title', 'SIPENA | Surat Menyurat')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('surat-menyurat') }}" class="text-indigo-400">Surat Menyurat</a>
    </div>
@endsection

@section('content')
    @include('errors.under-construction')
@endsection