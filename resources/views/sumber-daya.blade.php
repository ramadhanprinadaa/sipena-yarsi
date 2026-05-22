@extends('layouts.app')

@section('title', 'SIPENA | Cuti')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Sumber Daya</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('sumber-daya') }}" class="text-indigo-400">Sumber Daya</a>
    </div>
@endsection

@section('content')
    <div class="bg-white/20 backdrop-blur-sm shadow-md rounded-xl p-6 min-h-[calc(100vh-157px)]">
        <h1 class="text-bold text-xl">Kelola Sumber Daya</h1>
    </div>
@endsection