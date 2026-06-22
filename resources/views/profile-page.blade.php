@extends('layouts.app')

@section('title', 'SIPENA | Profile')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
      <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('profile') }}" class="text-indigo-600 hover:text-indigo-500">Profile</a>
    </div>
@endsection

@section('content')
  <livewire:profile />
@endsection
