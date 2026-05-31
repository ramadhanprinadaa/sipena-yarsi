<div class="flex flex-col h-full lg:h-[calc(100vh-150px)] space-y-4 px-2 py-3">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-3">

        @php
            $user = auth()->user();
            $unitName = null;

            if ($user->hasRole('SDM Universitas')) {
                $unitName = $user->pegawai?->unit_kerja?->unitSdm?->name;
            } elseif ($user->hasRole('Pimpinan')) {
                $unitName = $user->pegawai?->memimpin_unit?->name;
            }
        @endphp

        <!-- Judul Header -->
        <div class="flex-none font-poppins">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-semibold text-slate-800 tracking-tight">
                    Riwayat Presensi
                </h1>

                @if ($unitName)
                    <div class="flex items-center gap-2 md:gap-3 text-slate-600">
                        <span class="hidden md:inline text-xl text-slate-500">|</span>
                        <span class="text-lg font-medium">
                            {{ $unitName }}
                        </span>
                    </div>
                @endif
            </div>
            <p class="text-sm text-slate-600 mt-1 hidden md:block">
                Pantau presensi harian dan jam kerja pegawai dalam satu periode.
            </p>
        </div>

        <!-- Search and Export Data -->
        <div class="flex gap-2">

            <!-- Button Export Data -->
            <button
                x-on:click="openExport = true;"
                class="flex items-center px-3 h-9 self-center justify-center cursor-pointer bg-emerald-600/90 hover:bg-emerald-700 text-white text-sm rounded-md transition">
                <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i>
                Export Excel
            </button>

            <!-- Search -->
            <div class="relative w-67">
                <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" class="input-search placeholder-gray-400" wire:model.live.debounce.300ms="search"
                    placeholder="Cari Nama / NIP Pegawai ..." />
                <!-- Clear Button -->
                <button type="button" wire:click="$set('search', '')" x-show="$wire.search"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="flex-none flex flex-wrap items-center justify-between gap-3">

        <!-- Filter Dropdown -->
        <div class="flex flex-wrap items-center gap-2">

            @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))

                <!-- Filter Unit Kerja -->
                <div class="relative w-54" x-data="{ open: false }">
                    <button @click="open = !open" class="filter-dropdown" type="button">
                        <span x-text="$wire.selectedUnitKerja ?? 'Semua Unit Kerja'" class="truncate"></span>
                        <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="dropdown-menu h-[calc(100vh-340px)] overflow-auto">
                        <ul class="p-2 text-sm text-body font-medium">
                            <li>
                                <button @click="$wire.set('selectedUnitKerja', null); open=false" class="dropdown-item">
                                    Semua Unit Kerja
                                </button>
                            </li>
                            @foreach ($unitKerja as $unit)
                                <li wire:key="unit-{{ $loop->index }}">
                                    <button @click="$wire.set('selectedUnitKerja', '{{ $unit }}'); open=false"
                                        class="dropdown-item">
                                        {{ $unit }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Jenis Pegawai -->
                <div class="relative w-50" x-data="{ open: false }">
                    <button @click="open = !open" class="filter-dropdown" type="button">
                        <span x-text="$wire.selectedJenisPegawai ?? 'Semua Jenis Pegawai'" class="truncate"></span>
                        <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="dropdown-menu h-[calc(100vh-340px)] overflow-auto">
                        <ul class="p-2 text-sm text-body font-medium">
                            <li>
                                <button @click="$wire.set('selectedJenisPegawai', null); open=false"
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
            @endif

            <!-- Status Kehadiran -->
            <div class="relative w-54" x-data="{ open: false }">
                <button @click="open = !open" class="filter-dropdown" type="button">
                    <span x-text="$wire.selectedStatusKehadiran ?? 'Semua Status Kehadiran'" class="truncate"></span>
                    <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="dropdown-menu h-[calc(100vh-340px)] overflow-auto">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="$wire.set('selectedStatusKehadiran', null); open=false"
                                class="dropdown-item">
                                Semua Status Kehadiran
                            </button>
                        </li>
                        @foreach ($statusKehadiran as $status)
                            <li wire:key="status-{{ $loop->index }}">
                                <button
                                    @click="$wire.set('selectedStatusKehadiran', '{{ $status }}'); open=false"
                                    class="dropdown-item">
                                    {{ $status }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Filter Tanggal -->
        <div class="flex flex-wrap items-center gap-2">

            <!-- Periode Tanggal -->
            <div x-data="{ picker: null }" x-init="picker = new Datepicker($refs.input, {
                format: 'dd/mm/yyyy',
                autohide: true,
                language: 'id'
            });

            $refs.input.addEventListener('changeDate', () => {
                $wire.set('selectedPeriodeMulai', $refs.input.value);
            });" class="relative w-50">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                </div>
                <input type="text" x-ref="input" wire:model.live="selectedPeriodeMulai"
                    placeholder="Pilih Periode Tanggal"
                    class="w-full h-9 pl-9 pr-3 text-sm border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-3 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition" />
                <button type="button" x-show="$wire.selectedPeriodeMulai"
                    @click="
                    $wire.set('selectedPeriodeMulai', null);
                    picker.setDate({ clear: true });
                    $refs.input.value = '';"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Periode Selesai -->
            <div x-data="{ picker: null }" x-init="picker = new Datepicker($refs.input, {
                format: 'dd/mm/yyyy',
                autohide: true,
                language: 'id'
            });

            $refs.input.addEventListener('changeDate', () => {
                $wire.set('selectedPeriodeSelesai', $refs.input.value);
            });" class="relative w-50">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                </div>
                <input type="text" x-ref="input" wire:model.live="selectedPeriodeSelesai"
                    placeholder="Pilih Periode Selesai"
                    class="w-full h-9 pl-9 pr-3 text-sm border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-3 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition" />
                <button type="button" x-show="$wire.selectedPeriodeSelesai"
                    @click="
                    $wire.set('selectedPeriodeSelesai', null);
                    picker.setDate({ clear: true });
                    $refs.input.value = '';"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

    </div>

    <!-- Table -->
    <div class="table-container relative">
        <!-- Loading -->
        <div wire:loading
            wire:target="search, selectedJenisPegawai, selectedUnitKerja, selectedStatusKehadiran, selectedPeriodeMulai, selectedPeriodeSelesai">
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper overflow-x-auto">
            @if ($this->riwayatPresensi->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-calendar-minus text-2xl text-indigo-500"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">
                        {{ $this->emptyStateMessage }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm">
                        Silahkan upload file presensi / Hubungi Administrator.
                    </p>
                </div>
            @else
                <table class="table w-full table-fixed">
                    <thead class="table-header">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium w-[5%] text-center">#</th>
                            <th scope="col" class="px-4 py-3 font-medium w-[17%] text-left">
                                Nama Pegawai
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[17%] text-left">
                                Tanggal Presensi
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[13%] text-center">
                                Jam Masuk
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[13%] text-center">
                                Jam Pulang
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[15%] text-center">
                                Total Jam
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[15%] text-center">
                                Status Kehadiran
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[10%] text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->riwayatPresensi as $riwayat)
                            <tr class="table-row">
                                <!-- No -->
                                <td class="px-3 py-2 text-center text-gray-500 text-sm">
                                    {{ $this->riwayatPresensi->firstItem() + $loop->index }}
                                </td>

                                <!-- Nama Pegawai -->
                                <td class="px-3 py-2">
                                    <div class="flex flex-col min-w-0">
                                        <span class="truncate text-sm font-semibold text-gray-800"
                                            title="{{ $riwayat->pegawai?->nama ?? $riwayat->pegawai_nip }}">
                                            {{ $riwayat->pegawai?->nama ?? $riwayat->pegawai_nip }}
                                        </span>
                                        <span class="text-[11px] text-gray-400">NIP.
                                            {{ $riwayat->pegawai_nip }}</span>
                                    </div>
                                </td>

                                <!-- Tanggal Presensi -->
                                <td class="px-3 py-2 text-sm text-gray-600 truncate">
                                    {{ $riwayat->tanggal?->translatedFormat('l, d F Y') ?? '-' }}
                                </td>

                                <!-- Jam Masuk -->
                                <td class="px-3 py-2 text-center text-sm text-gray-600">
                                    {{ $riwayat->jam_masuk?->format('H:i:s') ?? '-' }}
                                </td>

                                <!-- Jam Pulang -->
                                <td class="px-3 py-2 text-center text-gray-600">
                                    {{ $riwayat->jam_keluar?->format('H:i:s') ?? '-' }}</td>

                                <!-- Total Jam -->
                                <td class="px-3 py-2 text-center text-gray-600 font-medium truncate">
                                    {{ $riwayat->total_jam_kerja ?? '-' }}</td>

                                <!-- Status Kehadiran -->
                                <td class="px-3 py-2 text-center truncate">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium"
                                        style="{{ $riwayat->statusKehadiran?->badge_style }}">
                                        {{ $riwayat->statusKehadiran?->status }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="px-3 py-2 text-center">
                                    <button @click="$dispatch('open-loading-detail-riwayat')"
                                        wire:click="showDetailRiwayat({{ $riwayat->id }})"
                                        class="bg-indigo-500 hover:bg-indigo-600 text-white text-[11px] font-semibold px-2 py-1 rounded-md shadow-sm transition-all duration-200 cursor-pointer">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Footer & Pagination -->
        <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2"
                aria-label="Table navigation">
                <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                    Menampilkan
                    <span
                        class="font-semibold text-heading">{{ $this->riwayatPresensi->firstItem() }}-{{ $this->riwayatPresensi->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-heading">{{ $this->riwayatPresensi->total() }} data</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click="gotoPage(1)" @disabled($this->riwayatPresensi->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click="previousPage" @disabled($this->riwayatPresensi->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $this->riwayatPresensi->currentPage() - 3); $i <= min($this->riwayatPresensi->lastPage(), $this->riwayatPresensi->currentPage() + 3); $i++)
                        <li>
                            <button wire:click="gotoPage({{ $i }})"
                                class="w-9 {{ $this->riwayatPresensi->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button wire:click="nextPage" @disabled(!$this->riwayatPresensi->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click="gotoPage({{ $this->riwayatPresensi->lastPage() }})"
                        @disabled($this->riwayatPresensi->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>
</div>
