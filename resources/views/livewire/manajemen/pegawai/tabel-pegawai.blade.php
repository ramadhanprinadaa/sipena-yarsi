<div class="flex flex-col space-y-4 h-[calc(100vh-280px)]">

    <!-- Filter & Search -->
    <div class="flex items-center justify-between gap-2">

        <!-- Filter -->
        <div class="flex gap-2">

            @if(auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                <!-- Filter Unit Kerja -->
                <div class="relative w-44" x-data="{ open: false, selected: 'Semua Unit Kerja' }">
                    <button
                        @click="open = !open"
                        class="filter-dropdown"
                        wire.model.live="selectedUnitKerja"
                        type="button">
                            <span x-text="selected" class="truncate"></span>
                            <svg
                                class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                            </svg>
                    </button>
                    <!-- Menu -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="dropdown-menu">
                        <ul class="p-2 text-sm text-body font-medium">
                            <li>
                                <button
                                    @click="selected='Semua Unit Kerja'; open=false"
                                    wire:click="$set('selectedUnitKerja', null)"
                                    class="dropdown-item">
                                    Semua Unit Kerja
                                </button>
                            </li>
                            @if (auth()->user()->HasRole('SDM Universitas'))
                                @foreach ($unit_kerja_universitas as $unit)
                                    <li>
                                        <button
                                            @click="selected='{{$unit->name}}'; open=false"
                                            wire:click="$set('selectedUnitKerja', '{{ $unit->id }}')"
                                            class="dropdown-item">
                                            {{ $unit->name }}
                                        </button>
                                    </li>
                                @endforeach
                            @endif
                            @if (auth()->user()->HasRole(['Admin', 'SDM Yayasan']))
                                @foreach ($unit_kerja as $unit)
                                    <li>
                                        <button
                                            @click="selected='{{$unit->name}}'; open=false"
                                            wire:click="$set('selectedUnitKerja', '{{ $unit->id }}')"
                                            class="dropdown-item">
                                            {{ $unit->name }}
                                        </button>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Filter Jabatan -->
                <div class="relative w-40" x-data="{ open: false, selected: 'Semua Jabatan' }">
                    <button
                        @click="open = !open"
                        wire.model.live="selectedJabatan"
                        class="filter-dropdown"
                        type="button">
                            <span x-text="selected" class="truncate"></span>
                            <svg
                                class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                            </svg>
                    </button>
                    <!-- Menu -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="dropdown-menu">
                        <ul class="p-2 text-sm text-body font-medium">
                            <li>
                                <button @click="selected='Semua Jabatan'; open=false" class="dropdown-item" wire:click="$set('selectedJabatan', null)">
                                    Semua Jabatan
                                </button>
                            </li>
                            <li>
                                <button @click="selected='Pimpinan'; open=false" class="dropdown-item" wire:click="$set('selectedJabatan', 'Pimpinan')">
                                    Pimpinan
                                </button>
                            </li>
                            <li>
                                <button @click="selected='Pegawai'; open=false" class="dropdown-item" wire:click="$set('selectedJabatan', 'Pegawai')">
                                    Pegawai
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Filter Gelar -->
            <div class="relative w-34" x-data="{ open: false, selected: 'Semua Gelar' }">
                <button
                    @click="open = !open"
                    wire.model.live="selectedGelar"
                    class="filter-dropdown"
                    type="button">
                        <span x-text="selected" class="truncate"></span>
                        <svg
                            class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                        </svg>
                </button>
                <!-- Menu -->
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="dropdown-menu">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="selected='Semua Gelar'; open=false" class="dropdown-item" wire:click="$set('selectedGelar', null)">
                                Semua Gelar
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Sarjana'; open=false" class="dropdown-item" wire:click="$set('selectedGelar','Sarjana')">
                                Sarjana
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Magister'; open=false" class="dropdown-item" wire:click="$set('selectedGelar','Magister')">
                                Magister
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Doktor'; open=false" class="dropdown-item" wire:click="$set('selectedGelar','Doktor')">
                                Doktor
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Professor'; open=false" class="dropdown-item" wire:click="$set('selectedGelar','Professor')">
                                Professor
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Filter Status -->
            <div class="relative w-36" x-data="{ open: false, selected: 'Semua Status' }">
                <button
                    @click="open = !open"
                    wire:model.live="selectedStatus"
                    class="filter-dropdown"
                    type="button">
                        <span x-text="selected" class="truncate"></span>
                        <svg
                            class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                        </svg>
                </button>
                <!-- Menu -->
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="dropdown-menu">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="selected='Semua Status'; open=false" class="dropdown-item" wire:click="$set('selectedStatus', null)">
                                Semua Status
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Aktif'; open=false" class="dropdown-item" wire:click="$set('selectedStatus', 'active')">
                                Aktif
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Nonaktif'; open=false" class="dropdown-item" wire:click="$set('selectedStatus', 'inactive')">
                                Nonaktif
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Filter Rentang Masa Kerja -->
            <div class="relative w-46" x-data="{ open: false, selected: 'Semua Masa Kerja' }">
                <button
                    @click="open = !open"
                    wire:model.live="selectedMasaKerja"
                    class="filter-dropdown"
                    type="button">
                        <span x-text="selected" class="truncate"></span>
                        <svg
                            class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                        </svg>
                </button>
                <!-- Menu -->
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="dropdown-menu">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="selected='Semua Masa Kerja'; open=false" class="dropdown-item" wire:click="$set('selectedMasaKerja', null)">
                                Semua Masa Kerja
                            </button>
                        </li>
                        <li>
                            <button @click="selected='0-2 Tahun'; open=false" class="dropdown-item" wire:click="$set('selectedMasaKerja', '0-2 Tahun')">
                                0-2 Tahun
                            </button>
                        </li>
                        <li>
                            <button @click="selected='1-5 Tahun'; open=false" class="dropdown-item" wire:click="$set('selectedMasaKerja', '2-5 Tahun')">
                                2-5 Tahun
                            </button>
                        </li>
                        <li>
                            <button @click="selected='5-10 Tahun'; open=false" class="dropdown-item" wire:click="$set('selectedMasaKerja', '5-10 Tahun')">
                                5-10 Tahun
                            </button>
                        </li>
                        <li>
                            <button @click="selected='&gt; 10 Tahun'; open=false" class="dropdown-item" wire:click="$set('selectedMasaKerja', '> 10 Tahun')">
                                &gt; 10 Tahun
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="relative w-77">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            </div>
            <input type="text" class="input-search"
                wire:model.live.debounce.300ms="search" placeholder="Cari Nama atau NIP ...">
        </div>
    </div>

    <!-- Table -->
    <div class="table-container relative">

        <!-- Loading -->
        <div wire:loading>
            <div class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium w-36">
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate">Nama Pegawai</span>
                                <div>
                                    <button wire:click="sortBy('pegawai.nama')"
                                        class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                        <i class="fa-solid {{ $this->sortIcon('pegawai.nama') }}"></i>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-28">
                            <span class="truncate">NIK Pegawai</span>
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-22">
                            <div class="flex items-center justify-between gap-2">
                                <span>Usia</span>
                                <div>
                                    <button wire:click="sortBy('pegawai.tanggal_lahir')"
                                        class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                        <i class="fa-solid {{ $this->sortIcon('pegawai.tanggal_lahir') }}"></i>
                                    </button>
                                </div>
                            </div>
                        </th>
                        @if (auth()->user()->HasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                            <th scope="col" class="px-4 py-3 font-medium w-32">
                                <div class="flex items-center justify-between gap-2">
                                    <span>Unit Kerja</span>
                                    <div>
                                        <button wire:click="sortBy('unit_kerja')"
                                            class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                            <i class="fa-solid {{ $this->sortIcon('unit_kerja') }}"></i>
                                        </button>
                                    </div>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-24 text-center">
                                Jabatan
                            </th>
                        @endif
                        <th scope="col" class="px-4 py-3 font-medium w-40">
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate">Tanggal Bergabung</span>
                                <div>
                                    <button wire:click="sortBy('pegawai.tanggal_bergabung')"
                                        class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                        <i class="fa-solid {{ $this->sortIcon('pegawai.tanggal_bergabung') }}"></i>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-36">
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate">Tanggal Pensiun</span>
                                <div>
                                    <button wire:click="sortBy('pegawai.tanggal_pensiun')"
                                        class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                        <i class="fa-solid {{ $this->sortIcon('pegawai.tanggal_pensiun') }}"></i>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center w-18">
                            Status
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center w-18">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pegawai as $p)
                        <tr class="table-row">
                            <th scope="row" class="px-4 py-2 font-medium text-heading truncate">
                                {{ $p->nama }}
                            </th>
                            <td class="px-4 py-2 truncate">
                                {{ $p->nip }}
                            </td>
                            <td class="px-4 py-2 truncate">
                                {{ $p->age }} Tahun
                            </td>
                            @if (auth()->user()->HasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                                <td class="px-4 py-2 truncate">
                                    {{ $p->unit_kerja->name }}
                                </td>
                                <td class="px-4 py-2 truncate text-center">
                                    @if( $p->memimpin_unit != null)
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-amber-100 text-amber-600">
                                            Pimpinan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-violet-100 text-violet-600">
                                            Pegawai
                                        </span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-4 py-2 truncate">
                                {{ $p->tanggal_bergabung->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-4 py-2 truncate">
                                {{ $p->tanggal_pensiun->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if ($p->status == 'active')
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-green-100 text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-red-100 text-red-700">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">
                                <a
                                    wire:navigate href="{{route('manajemen-pegawai-detail', $p->id)}}"
                                    class="px-2 py-1 text-xs text-white rounded-md bg-blue-600 hover:bg-blue-700 transition">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer & Pagination -->
        <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                    Menampilkan
                    <span class="font-semibold text-heading">{{ $pegawai->firstItem() }}-{{ $pegawai->lastItem() }}</span> dari
                    <span class="font-semibold text-heading">{{ $pegawai->total() }} pegawai</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button
                            wire:click="gotoPage(1)"
                            @disabled($pegawai->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                            >
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button
                            wire:click="previousPage"
                            @disabled($pegawai->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $pegawai->currentPage() - 3);
                        $i <= min($pegawai->lastPage(), $pegawai->currentPage() + 3);
                        $i++)
                        <li>
                            <button
                                wire:click="gotoPage({{ $i }})"
                                class="w-9 {{ $pegawai->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button
                            wire:click="nextPage"
                            @disabled(!$pegawai->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button
                        wire:click="gotoPage({{ $pegawai->lastPage() }})"
                        @disabled($pegawai->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                        >
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>
</div>
