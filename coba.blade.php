<div class="relative w-50" x-data="{ open: false }">
    <button @click="open = !open" class="filter-dropdown" type="button">
        <span x-text="$wire.selectedJenisPegawai ?? 'Semua Jenis Pegawai'" class="truncate"></span>
        <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
        </svg>
    </button>
    <div x-show="open" @click.outside="open = false" x-transition class="dropdown-menu h-[calc(100vh-340px)] overflow-auto">
        <ul class="p-2 text-sm text-body font-medium">
            <li>
                <button
                    @click="$wire.set('selectedJenisPegawai', null); open=false"
                    class="dropdown-item">
                    Semua Jenis Pegawai
                </button>
            </li>
            @foreach ($jenisPegawai as $jenis)
                <li wire:key="jenis-{{ $loop->index }}">
                    <button
                        @click="$wire.set('selectedJenisPegawai', '{{ $jenis }}'); open=false"
                        class="dropdown-item">
                        {{ $jenis }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>