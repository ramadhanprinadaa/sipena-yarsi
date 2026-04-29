<div>
  @section('title', 'SIPENA | Detail Pegawai')

  @section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a wire:navigate href="{{ route('manajemen-pegawai') }}" class="hover:text-indigo-500">Pegawai</a>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
        <a wire:navigate href="{{ route('manajemen-pegawai-detail', $pegawai) }}" class="text-indigo-600 hover:text-indigo-500">Detail Pegawai</a>
    </div>
  @endsection

  @section('content')
    <div>
      <h1>Detail Pegawai</h1>
      <p>Nama: {{ $pegawai->nama }}</p>

    </div>
  @endsection

</div>