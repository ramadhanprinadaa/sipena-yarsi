@extends('layouts.app')

@section('title', 'SIPENA | Cuti')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('cuti') }}" class="text-indigo-400">Cuti</a>
    </div>
@endsection

@section('content')
    <div class="flex flex-col h-full min-h-0">

        {{-- <div class="flex justify-between mb-8">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold">Cuti</h1>
                <p  class="text-sm text-gray-800">Selamat datang di halaman cuti. Di sini Anda dapat melihat dan mengelola informasi cuti Anda.</p>
            </div>
        </div> --}}

        <div class="flex-1 flex flex-col gap-4 min-h-0">

            <livewire:dashboard.cuti.index/>
            <livewire:dashboard.cuti.add-cuti/>
            <livewire:dashboard.cuti.edit-cuti/>
            <livewire:dashboard.cuti.delete-cuti/>

        </div>
    </div>
@endsection
