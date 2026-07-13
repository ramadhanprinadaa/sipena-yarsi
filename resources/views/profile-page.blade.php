@extends('layouts.app')

@section('title', 'SIPENA | Profile')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
      <span>Profil</span>
      <i class="fa-solid fa-chevron-right text-xs"></i>
      <a href="{{ route('profile') }}" class="text-indigo-600 hover:text-indigo-500 transition">Profil</a>
    </div>
@endsection

@section('content')
  <livewire:profile />
@endsection
