<div class="flex flex-col space-y-4 h-[calc(100vh-280px)]">

    <!-- Filter & Search -->
    <div class="flex items-center justify-between gap-2">

        <!-- Filter -->
        <div class="flex gap-2">

            @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                <!-- Filter Unit Kerja -->
                <div class="relative w-50" x-data="{ open: false }">
                    <button @click="open = !open" class="filter-dropdown" type="button">
                        <span x-text="$wire.selectedUnitKerja ?? 'Semua Unit Kerja'" class="truncate"></span>
                        <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Menu -->
                    <div x-show="open" x-cloak @click.outside="open = false" x-transition
                        class="dropdown-menu h-[calc(100vh-380px)] overflow-auto">
                        <ul class="p-2 text-sm text-body font-medium">
                            <li>
                                <button @click="$wire.set('selectedUnitKerja', null); open=false" class="dropdown-item">
                                    Semua Unit Kerja
                                </button>
                            </li>
                            @if (auth()->user()->HasRole('SDM Universitas'))
                                @foreach ($unitKerjaUniversitas as $unit)
                                    <li wire:key="unit-{{ $loop->index }}">
                                        <button
                                            @click="$wire.set('selectedUnitKerja', '{{ $unit }}'); open=false"
                                            class="dropdown-item">
                                            {{ $unit }}
                                        </button>
                                    </li>
                                @endforeach
                            @endif
                            @if (auth()->user()->HasRole(['Admin', 'SDM Yayasan']))
                                @foreach ($unitKerja as $unit)
                                    <li wire:key="unit-{{ $loop->index }}">
                                        <button
                                            @click="$wire.set('selectedUnitKerja', '{{ $unit }}'); open=false"
                                            class="dropdown-item">
                                            {{ $unit }}
                                        </button>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Filter Jenis Pegawai -->
            <div class="relative w-50" x-data="{ open: false }">
                <button @click="open = !open" class="filter-dropdown" type="button">
                    <span x-text="$wire.selectedJenisPegawai ?? 'Semua Jenis Pegawai'" class="truncate"></span>
                    <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                    class="dropdown-menu max-h-[calc(100vh-380px)] overflow-auto">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="$wire.set('selectedJenisPegawai', null); open=false" class="dropdown-item">
                                Semua Jenis Pegawai
                            </button>
                        </li>
                        @foreach ($jenisPegawai as $jenis)
                            <li wire:key="jenis-{{ $loop->index }}">
                                <button @click="$wire.set('selectedJenisPegawai', '{{ $jenis }}'); open=false"
                                    class="dropdown-item">
                                    {{ $jenis }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Filter Jabatan -->
            @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                <div class="relative w-40" x-data="{ open: false, selected: 'Semua Jabatan' }">
                    <button @click="open = !open" wire.model.live="selectedJabatan" class="filter-dropdown"
                        type="button">
                        <span x-text="selected" class="truncate"></span>
                        <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Menu -->
                    <div x-show="open" x-cloak @click.outside="open = false" x-transition class="dropdown-menu">
                        <ul class="p-2 text-sm text-body font-medium">
                            <li>
                                <button @click="selected='Semua Jabatan'; open=false" class="dropdown-item"
                                    wire:click="$set('selectedJabatan', null)">
                                    Semua Jabatan
                                </button>
                            </li>
                            <li>
                                <button @click="selected='Pimpinan'; open=false" class="dropdown-item"
                                    wire:click="$set('selectedJabatan', 'Pimpinan')">
                                    Pimpinan
                                </button>
                            </li>
                            <li>
                                <button @click="selected='Pegawai'; open=false" class="dropdown-item"
                                    wire:click="$set('selectedJabatan', 'Pegawai')">
                                    Pegawai
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Filter Status Pegawai -->
            <div class="relative w-40" x-data="{ open: false }">
                <button @click="open = !open" class="filter-dropdown" type="button">
                    <span x-text="$wire.selectedStatusPegawai ?? 'Status Pegawai'" class="truncate"></span>
                    <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                    class="dropdown-menu max-h-[calc(100vh-380px)] overflow-auto">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="$wire.set('selectedStatusPegawai', null); open=false"
                                class="dropdown-item">
                                Status Pegawai
                            </button>
                        </li>
                        @foreach ($statusPegawai as $status)
                            <li wire:key="status-{{ $loop->index }}">
                                <button @click="$wire.set('selectedStatusPegawai', '{{ $status }}'); open=false"
                                    class="dropdown-item">
                                    {{ $status }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Filter Status Aktif -->
            <div class="relative w-40" x-data="{ open: false }">
                <button @click="open = !open" class="filter-dropdown" type="button">
                    <span class="truncate">
                        {{ $selectedStatusAktif === 'active' ? 'Aktif' : ($selectedStatusAktif === 'inactive' ? 'Non Aktif' : 'Status Aktif') }}
                    </span>
                    <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                    class="dropdown-menu max-h-[calc(100vh-380px)] overflow-auto">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="$wire.set('selectedStatusAktif', null); open=false" class="dropdown-item">
                                Status Aktif
                            </button>
                        </li>
                        <li>
                            <button @click="$wire.set('selectedStatusAktif', 'active'); open=false"
                                class="dropdown-item">
                                Aktif
                            </button>
                        </li>
                        <li>
                            <button @click="$wire.set('selectedStatusAktif', 'inactive'); open=false"
                                class="dropdown-item">
                                Non Aktif
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="relative w-77">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="text" class="input-search" wire:model.live.debounce.300ms="search"
                placeholder="Cari Nama atau NIP ...">
            <!-- Clear Button -->
            <button type="button" wire:click="$set('search', '')" x-show="$wire.search"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container relative">

        <!-- Loading -->
        <div wire:loading>
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper">
            <table class="table w-full">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-semibold w-[4%] text-center">#</th>

                        <th scope="col" class="px-4 py-3 font-semibold w-[20%]">
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate">Nama dan NIK Pegawai</span>
                                <div>
                                    <button wire:click="sortBy('pegawai.nama')"
                                        class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                        <i class="fa-solid {{ $this->sortIcon('pegawai.nama') }}"></i>
                                    </button>
                                </div>
                            </div>
                        </th>

                        @if (auth()->user()->HasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                            <th scope="col" class="px-4 py-3 font-semibold w-[15%]">
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

                            <th scope="col" class="px-4 py-3 font-semibold w-[8%] text-center">
                                Jabatan
                            </th>
                        @endif

                        @if (auth()->user()->HasRole(['Pimpinan']))
                            <th scope="col" class="px-4 py-3 font-semibold w-[13%]">
                                Jenis Pegawai
                            </th>
                        @endif

                        <th scope="col" class="px-4 py-3 font-semibold truncate text-center w-[12%]">
                            Status Pegawai
                        </th>

                        <th scope="col" class="px-4 py-3 font-semibold w-[13%]">
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

                        <th scope="col" class="px-4 py-x font-semibold w-[13%]">
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate" title="Tanggal Berakhir Masa Kerja / Pensiun">Tanggal
                                    Berakhir...</span>
                                <div>
                                    <button wire:click="sortBy('tanggal_berakhir')"
                                        class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                        <i class="fa-solid {{ $this->sortIcon('tanggal_berakhir') }}"></i>
                                    </button>
                                </div>
                            </div>
                        </th>

                        <th scope="col" class="px-4 py-3 font-semibold text-center truncate w-[10%]">
                            Status Aktif
                        </th>

                        <th scope="col" class="px-4 py-3 font-semibold text-center w-[6%]">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->pegawai as $p)
                        @php
                            $isWarningTanggal = false;
                            if ($p->status_pegawai?->status === 'Kontrak' && $p->tanggal_habis_kontrak) {
                                $isWarningTanggal =
                                    $p->tanggal_habis_kontrak > now() &&
                                    $p->tanggal_habis_kontrak <= now()->addYears(2);
                            } elseif ($p->tanggal_pensiun) {
                                $isWarningTanggal =
                                    $p->tanggal_pensiun > now() && $p->tanggal_pensiun <= now()->addYears(5);
                            }
                            $isExpired =
                                ($p->tanggal_habis_kontrak && $p->tanggal_habis_kontrak < now()) ||
                                ($p->tanggal_pensiun && $p->tanggal_pensiun < now());
                        @endphp

                        <tr class="table-row hover:bg-gray-50 transition">
                            <td class="px-3 py-2 text-center text-gray-400 text-sm">
                                {{ $this->pegawai->firstItem() + $loop->index }}
                            </td>

                            <td class="px-3 py-2">
                                <div class="flex flex-col leading-tight">
                                    <span class="font-semibold text-gray-800 truncate">
                                        {{ $p->nama_gelar }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $p->nip }}
                                    </span>
                                </div>
                            </td>

                            @if (auth()->user()->HasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                                <td class="px-3 py-2 text-sm text-gray-700 truncate">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            @php
                                                $nama = explode(' ', $p->unit_kerja->name);
                                            @endphp
                                            <span class="text-blue-600 text-xs font-semibold">
                                                {{ strtoupper(substr($nama[0], 0, 1) . ($nama[1][0] ?? '') . ($nama[2][0] ?? '')) }}
                                            </span>
                                        </div>
                                        <span class="truncate" title="{{ $p->unit_kerja->name }}">
                                            {{ $p->unit_kerja->name }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-3 py-2 text-center">
                                    @if ($p->memimpin_unit)
                                        <span
                                            class="px-2.5 py-1 text-xs font-medium rounded-md bg-amber-100 text-amber-600 whitespace-nowrap">
                                            Pimpinan
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 text-xs font-medium rounded-md bg-indigo-100 text-indigo-600 whitespace-nowrap">
                                            Pegawai
                                        </span>
                                    @endif
                                </td>
                            @endif

                            @if (auth()->user()->HasRole(['Pimpinan']))
                                <td class="px-3 py-2">
                                    @php
                                        $namaJenis = $p->jenis_pegawai?->jenis ?? '-';

                                        $colorClass = match (true) {
                                            str_contains(strtolower($namaJenis), 'tenaga non kependidikan') => 'bg-slate-100 text-slate-700',
                                            str_contains(strtolower($namaJenis), 'tenaga kependidikan') => 'bg-sky-100 text-sky-700',
                                            str_contains(strtolower($namaJenis), 'tenaga pendidik') => 'bg-emerald-100 text-emerald-700',
                                            str_contains(strtolower($namaJenis), 'guru') => 'bg-violet-100 text-violet-700',
                                            str_contains(strtolower($namaJenis), 'dokter') => 'bg-red-100 text-red-700',
                                            str_contains(strtolower($namaJenis), 'perawat') => 'bg-pink-100 text-pink-700',
                                            str_contains(strtolower($namaJenis), 'helper') => 'bg-amber-100 text-amber-700',
                                            $namaJenis === '-' => 'bg-gray-100 text-gray-500',
                                            default => 'bg-blue-100 text-blue-700',
                                        };
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 text-xs font-medium rounded-md whitespace-nowrap {{ $colorClass }}">
                                        {{ $namaJenis }}
                                    </span>
                                </td>
                            @endif

                            <td class="px-3 py-2 text-center">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-md whitespace-nowrap {{ $p->status_pegawai->status == 'Kontrak' ? 'bg-amber-100 text-amber-600' : 'bg-green-100 text-green-600' }}">
                                    {{ $p->status_pegawai->status }}
                                </span>
                            </td>

                            <td class="px-4 py-2 truncate">
                                {{ $p->tanggal_bergabung->translatedFormat('d M Y') }}
                            </td>

                            <td
                                class="px-4 py-2 truncate {{ $isWarningTanggal || $isExpired ? ' text-red-600 font-semibold' : '' }}">
                                {{ $p->status_pegawai?->status === 'Kontrak'
                                    ? $p->tanggal_habis_kontrak?->translatedFormat('d M Y') ?? '-'
                                    : $p->tanggal_pensiun?->translatedFormat('d M Y') ?? '-' }}
                            </td>

                            <td class="px-3 py-2 text-center">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-md whitespace-nowrap {{ $p->status == 'active' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $p->status == 'active' ? 'Aktif' : 'Non Aktif' }}
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center">
                                <a wire:navigate href="{{ route('manajemen-pegawai-detail', $p->id) }}"
                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-white bg-indigo-500 hover:bg-indigo-600 rounded-md transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer & Pagination -->
        <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2"
                aria-label="Table navigation">
                <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                    Menampilkan
                    <span
                        class="font-semibold text-heading">{{ $this->pegawai->firstItem() }}-{{ $this->pegawai->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-heading">{{ $this->pegawai->total() }} pegawai</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click="gotoPage(1)" @disabled($this->pegawai->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click="previousPage" @disabled($this->pegawai->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $this->pegawai->currentPage() - 3); $i <= min($this->pegawai->lastPage(), $this->pegawai->currentPage() + 3); $i++)
                        <li>
                            <button wire:click="gotoPage({{ $i }})"
                                class="w-9 {{ $this->pegawai->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button wire:click="nextPage" @disabled(!$this->pegawai->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click="gotoPage({{ $this->pegawai->lastPage() }})" @disabled($this->pegawai->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>
</div>
