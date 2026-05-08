@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Pengguna')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a href="{{ route('manajemen-pegawai') }}" class="text-indigo-600 hover:text-indigo-500">Pengguna</a>
    </div>
@endsection

@section('content')
    <div class="flex flex-col h-full min-h-0">

        <div class="flex justify-between mb-2">
            <div class="flex flex-col gap-2 font-poppins">
                <h1 class="text-2xl font-semibold">Daftar Pengguna</h1>
                <p class="text-sm font-medium">Kelola akun pengguna dan hak akses</p>
            </div>
            <div
                x-data="{ show: false, message: '', type: 'success' }"
                x-on:notify.window="
                    show = true;
                    message = $event.detail.message;
                    type = $event.detail.type;
                    setTimeout(() => show = false, 10000)
                "
                x-show="show"
                x-transition
                class="mb-4 p-4 text-sm rounded-lg"
                :class="{
                    'bg-green-200 text-green-800': type === 'success',
                    'bg-red-200 text-red-800': type === 'error'
                }"
            >
                <i class="fa-solid fa-circle-check mr-2"></i>
                <span x-text="message"></span>
            </div>
        </div>

        <div class="flex-1">
            <livewire:manajemen.users.index />
            <livewire:manajemen.users.add-user />
            <livewire:manajemen.users.detail-user />
        </div>
    </div>
@endsection