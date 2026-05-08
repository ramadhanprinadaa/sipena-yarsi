@extends('layouts.app')

@section('title', 'SIPENA | Surat Menyurat')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a href="{{ route('surat-menyurat') }}" class="text-indigo-600 hover:text-indigo-500">Surat Menyurat</a>
    </div>
@endsection

@section('content')
    @include('errors.under-construction')
@endsection