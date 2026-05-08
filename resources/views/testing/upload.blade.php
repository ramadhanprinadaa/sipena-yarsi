@extends('layouts.app')

@section('title', 'SIPENA | Upload File')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
      <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a href="{{ route('profile') }}" class="text-indigo-600 hover:text-indigo-500">Upload</a>
    </div>
@endsection

@section('content')
    @if(session('file'))
      <p>File berhasil diupload! <a href="{{ asset('storage/' . session('file')) }}" target="_blank">Lihat File</a></p>
    @endif

    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="file">Pilih File:</label>
        <input type="file" name="file">

        @if ($errors->has('file'))
            <span class="text-red-500">{{ $errors->first('file') }}</span>
        @endif

        <button type="submit">Upload</button>
    </form>

    @if (session('file'))
        <form action="{{ route('upload.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="file" value="{{ session('file') }}">
            <button type="submit">Hapus File</button>
        </form>
    @endif
@endsection