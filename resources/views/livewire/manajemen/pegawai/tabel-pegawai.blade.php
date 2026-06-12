<div x-data="{ showLoading: false, openExport: false }" x-on:open-export="showLoading = false; openExport = true;"
    class="flex flex-col space-y-4 h-[calc(100vh-280px)]">

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

                <!-- Filter Jenjang Pendidikan -->
                <div class="relative w-48" x-data="{ open: false }">
                    <button @click="open = !open" class="filter-dropdown" type="button">
                        <span x-text="$wire.selectedJenjangPendidikan ?? 'Jenjang Pendidikan'" class="truncate"></span>
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
                                <button @click="$wire.set('selectedJenjangPendidikan', null); open=false"
                                    class="dropdown-item">
                                    Jenjang Pendidikan
                                </button>
                            </li>
                            @foreach ($jenjangPendidikan as $jenjang)
                                <li wire:key="jenjang-{{ $loop->index }}">
                                    <button
                                        @click="$wire.set('selectedJenjangPendidikan', '{{ $jenjang }}'); open=false"
                                        class="dropdown-item">
                                        {{ $jenjang }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Filter Jenis Pegawai -->
            @if (auth()->user()->hasRole('Pimpinan'))
                <div class="relative w-50" x-data="{ open: false }">
                    <button @click="open = !open" class="filter-dropdown" type="button">
                        <span x-text="$wire.selectedJenisPegawai ?? 'Semua Jenis Pegawai'" class="truncate"></span>
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

            <!-- Filter Jabatan -->
            @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                <div class="relative w-40" x-data="{ open: false, selected: 'Semua Jabatan' }">
                    <button @click="open = !open" wire.model.live="selectedJabatan" class="filter-dropdown"
                        type="button">
                        <span x-text="selected" class="truncate"></span>
                        <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m19 9-7 7-7-7" />
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
        <div wire:loading wire:target="">
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper">
            @if ($this->pegawai->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-people-roof text-2xl text-indigo-500"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">
                        {{ $this->emptyStateMessage }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm">
                        Silahkan Hubungi Administrator.
                    </p>
                </div>
            @else
                <table class="table w-full">
                    <thead class="table-header">
                        <tr>
                            <!-- Header Index-->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[3%]">#</th>

                            <!-- Header Nama dan NIP -->
                            <th scope="col" class="px-4 py-3 font-semibold w-[16%]">
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
                                <!-- Header Unit Kerja -->
                                <th scope="col" class="px-4 py-3 font-semibold w-[14%]">
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

                                <!-- Header Pendidikan -->
                                <th scope="col" class="px-4 py-3 font-semibold text-center truncate w-[12%]">
                                    Pendidikan Terakhir
                                </th>

                                <!-- Header Jabatan-->
                                <th scope="col" class="px-4 py-3 font-semibold text-center w-[10%]">
                                    Jabatan
                                </th>
                            @endif

                            <!-- Header Jenis Pegawai -->
                            @if (auth()->user()->HasRole(['Pimpinan']))
                                <th scope="col" class="px-4 py-3 font-semibold w-[10%]">
                                    Jenis Pegawai
                                </th>
                            @endif

                            <!-- Header Status Pegawai-->
                            <th scope="col" class="px-4 py-3 font-semibold truncate text-center w-[10%]">
                                Status Pegawai
                            </th>

                            <!-- Header Tanggal Bergabung-->
                            <th scope="col" class="px-4 py-3 font-semibold w-[10%]">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="truncate">Tanggal Bergabung</span>
                                    <div>
                                        <button wire:click="sortBy('pegawai.tanggal_bergabung')"
                                            class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                            <i
                                                class="fa-solid {{ $this->sortIcon('pegawai.tanggal_bergabung') }}"></i>
                                        </button>
                                    </div>
                                </div>
                            </th>

                            <!-- Header Tanggal Berakhir -->
                            <th scope="col" class="px-4 py-x font-semibold w-[10%]">
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

                            <!-- Header Status Aktif -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center truncate w-[9%]">
                                Status Aktif
                            </th>

                            <!-- Header Aksi -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[6%]">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->pegawai as $p)
                            <tr class="table-row hover:bg-gray-50 transition">

                                <!-- Index -->
                                <td class="px-3 py-2 text-center text-gray-400 text-sm">
                                    {{ $this->pegawai->firstItem() + $loop->index }}
                                </td>

                                <!-- Nama dan NIP -->
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
                                    <!-- Unit Kerja -->
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

                                    <!-- Pendidikan -->
                                    <td class="px-3 py-2 text-sm text-gray-700 truncate text-center">
                                        @php
                                            $pendidikanTerakhir = $p->riwayatPendidikan
                                                ->sortByDesc(function ($riwayat) {
                                                    return $riwayat->jenjangPendidikan->urutan ?? 0;
                                                })
                                                ->first();

                                            $kodePendidikan = $pendidikanTerakhir?->jenjangPendidikan?->kode;
                                        @endphp

                                        @if ($kodePendidikan)
                                            @php
                                                $badgeColor = match (true) {
                                                    $kodePendidikan === 'SD' => 'bg-slate-100 text-slate-700',
                                                    $kodePendidikan === 'SMP' => 'bg-zinc-100 text-zinc-700',
                                                    in_array($kodePendidikan, ['SMA', 'SMK'])
                                                        => 'bg-sky-100 text-sky-700',
                                                    in_array($kodePendidikan, ['D1', 'D2'])
                                                        => 'bg-cyan-100 text-cyan-700',
                                                    in_array($kodePendidikan, ['D3', 'D4'])
                                                        => 'bg-violet-100 text-violet-700',
                                                    $kodePendidikan === 'S1' => 'bg-emerald-100 text-emerald-700',
                                                    $kodePendidikan === 'S2' => 'bg-amber-100 text-amber-700',
                                                    $kodePendidikan === 'S3' => 'bg-rose-100 text-rose-700',
                                                    default => 'bg-gray-100 text-gray-700', // Warna Default
                                                };
                                            @endphp

                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $badgeColor }}">
                                                {{ $kodePendidikan }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">
                                                Belum ada data
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Jabatan -->
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

                                <!-- Jenis Pegawai -->
                                @if (auth()->user()->HasRole(['Pimpinan']))
                                    <td class="px-3 py-2">
                                        @php
                                            $namaJenis = $p->jenis_pegawai?->jenis ?? '-';

                                            $colorClass = match (true) {
                                                str_contains(strtolower($namaJenis), 'tenaga non kependidikan')
                                                    => 'bg-slate-200 text-slate-700',
                                                str_contains(strtolower($namaJenis), 'tenaga kependidikan')
                                                    => 'bg-sky-100 text-sky-700',
                                                str_contains(strtolower($namaJenis), 'tenaga pendidik')
                                                    => 'bg-emerald-100 text-emerald-700',
                                                str_contains(strtolower($namaJenis), 'guru')
                                                    => 'bg-violet-100 text-violet-700',
                                                str_contains(strtolower($namaJenis), 'dokter')
                                                    => 'bg-red-100 text-red-700',
                                                str_contains(strtolower($namaJenis), 'perawat')
                                                    => 'bg-pink-100 text-pink-700',
                                                str_contains(strtolower($namaJenis), 'helper')
                                                    => 'bg-amber-100 text-amber-700',
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

                                <!-- Status Pegawai -->
                                <td class="px-3 py-2 text-center">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-md whitespace-nowrap {{ $p->status_pegawai->status == 'Kontrak' ? 'bg-amber-100 text-amber-600' : 'bg-green-100 text-green-600' }}">
                                        {{ $p->status_pegawai->status }}
                                    </span>
                                </td>

                                <!-- Tanggal Bergabung -->
                                <td class="px-4 py-2 truncate">
                                    {{ $p->tanggal_bergabung->translatedFormat('d M Y') }}
                                </td>

                                <!-- Tanggal Berakhir -->
                                @php
                                    $now = now();
                                    $status = $p->status_pegawai?->status;
                                    $isWarningTanggal = false;

                                    if ($status === 'Kontrak' && $p->tanggal_habis_kontrak) {
                                        $isWarningTanggal =
                                            $p->tanggal_habis_kontrak >= $now &&
                                            $p->tanggal_habis_kontrak <= $now->copy()->addYears(2);
                                    }

                                    if ($status === 'Tetap' && $p->tanggal_pensiun) {
                                        $isWarningTanggal =
                                            $p->tanggal_pensiun >= $now &&
                                            $p->tanggal_pensiun <= $now->copy()->addYears(5);
                                    }

                                    $isExpired =
                                        ($status === 'Kontrak' &&
                                            $p->tanggal_habis_kontrak &&
                                            $p->tanggal_habis_kontrak < $now) ||
                                        ($status === 'Tetap' && $p->tanggal_pensiun && $p->tanggal_pensiun < $now);
                                @endphp
                                <td
                                    class="px-4 py-2 truncate {{ $isExpired ? 'text-red-700 font-bold' : ($isWarningTanggal ? 'text-red-600 font-semibold' : '') }}">

                                    {{ $p->tanggal_habis_kontrak
                                        ? $p->tanggal_habis_kontrak->translatedFormat('d M Y')
                                        : ($p->tanggal_pensiun
                                            ? $p->tanggal_pensiun->translatedFormat('d M Y')
                                            : '-') }}
                                </td>

                                <!-- Status Aktif -->
                                <td class="px-3 py-2 text-center">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-md whitespace-nowrap {{ $p->status == 'active' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                                        {{ $p->status == 'active' ? 'Aktif' : 'Non Aktif' }}
                                    </span>
                                </td>

                                <!-- Button Detail -->
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
            @endif
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

    <!-- Modal Confirmation for Export File -->
    <template x-teleport="body">
        <div x-show="showLoading || openExport" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click.self="openExport = false"
            @keydown.escape.window="openExport = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
            style="display: none;">

            <div x-show="showLoading" class="flex flex-col items-center gap-4">
                <div class="w-10 h-10 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                <div class="text-sm font-medium tracking-wide text-white">
                    Memuat Data...
                </div>
            </div>

            <div x-show="openExport" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.stop>

                @php $preview = $this->exportPreviewData; @endphp

                <x-modal.confirmation title="Export Data Pegawai"
                    subTitle="Konfirmasi data export pegawai di bawah ini." icon="fa-solid fa-file-export"
                    iconBg="bg-emerald-500" iconShadow="shadow-emerald-200" closeAction="openExport = false">

                    <div>
                        @if ($preview['isEmpty'])
                            <div
                                class="flex flex-col items-center justify-center min-h-[calc(100vh-450px)] p-6 text-center bg-red-50/50 border border-red-100 rounded-xl">
                                <div
                                    class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mb-4 shadow-sm shadow-red-100">
                                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-500"></i>
                                </div>
                                <h3 class="text-base font-bold text-red-800">Tidak Ada Data Untuk Diexport</h3>
                                <p class="text-sm text-red-600 mt-1.5 max-w-[70vw] leading-relaxed mx-auto">
                                    {{ $this->emptyStateMessage }}
                                </p>
                            </div>
                        @else
                            <div class="space-y-4">
                                <p class="text-sm text-slate-500 leading-relaxed">
                                    Silakan periksa kembali parameter filter di bawah ini sebelum mengunduh berkas
                                    data pegawai.
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @php
                                        $filters = [
                                            [
                                                'label' => 'Unit Kerja',
                                                'value' => $preview['unit'],
                                                'icon' => 'fa-regular fa-building',
                                                'bg' => 'bg-indigo-500/10',
                                                'text' => 'text-indigo-600',
                                            ],
                                            [
                                                'label' => 'Jenjang Pendidikan',
                                                'value' => $preview['pendidikan'],
                                                'icon' => 'fa-solid fa-graduation-cap',
                                                'bg' => 'bg-sky-500/10',
                                                'text' => 'text-sky-600',
                                            ],
                                            [
                                                'label' => 'Jabatan',
                                                'value' => $preview['jabatan'],
                                                'icon' => 'fa-solid fa-briefcase',
                                                'bg' => 'bg-amber-500/10',
                                                'text' => 'text-amber-600',
                                            ],
                                            [
                                                'label' => 'Jenis Pegawai',
                                                'value' => $preview['jenis'],
                                                'icon' => 'fa-solid fa-user-check',
                                                'bg' => 'bg-violet-500/10',
                                                'text' => 'text-violet-600',
                                            ],
                                            [
                                                'label' => 'Status Pegawai',
                                                'value' => $preview['status_pegawai'],
                                                'icon' => 'fa-solid fa-id-badge',
                                                'bg' => 'bg-emerald-500/10',
                                                'text' => 'text-emerald-600',
                                            ],
                                            [
                                                'label' => 'Status Aktif',
                                                'value' => $preview['status_aktif'],
                                                'icon' => 'fa-solid fa-toggle-on',
                                                'bg' => 'bg-rose-500/10',
                                                'text' => 'text-rose-600',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($filters as $item)
                                        <div
                                            class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-xl flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-lg {{ $item['bg'] }} flex items-center justify-center {{ $item['text'] }} shrink-0">
                                                <i class="{{ $item['icon'] }} text-base"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4
                                                    class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                                    {{ $item['label'] }}
                                                </h4>
                                                <p class="text-sm font-semibold text-slate-700 mt-0.5 truncate"
                                                    title="{{ $item['value'] }}">
                                                    {{ $item['value'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <x-slot:footer>
                        <button type="button" @click="openExport = false"
                            class="px-5 py-2 rounded-md border border-slate-200 bg-red-400/90 hover:bg-red-500 text-white transition cursor-pointer">
                            Batal
                        </button>

                        <button type="button"
                            @if (!$preview['isEmpty']) wire:click="exportData"
                                x-on:click="await $wire.exportData(); setTimeout(() => openExport = false, 800)" @endif
                            @disabled($preview['isEmpty']) wire:loading.attr="disabled" wire:target="exportData"
                            wire:loading.class="opacity-70 !cursor-wait"
                            class="px-5 py-2 rounded-md border border-slate-200 transition flex items-center justify-center
                                {{ $preview['isEmpty']
                                    ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                                    : 'bg-emerald-600/90 hover:bg-emerald-700 text-white cursor-pointer shadow-sm' }}">

                            <i wire:loading.remove wire:target="exportData"
                                class="fa-solid fa-arrow-up-right-from-square mr-2">
                            </i>

                            <!-- Loading spinner -->
                            <svg wire:loading wire:target="exportData" class="w-4 h-4 animate-spin mr-2"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                </path>
                            </svg>

                            <span wire:loading.remove wire:target="exportData">Export Data</span>
                            <span wire:loading wire:target="exportData">Mengekspor...</span>
                        </button>
                    </x-slot:footer>

                </x-modal.confirmation>
            </div>
        </div>
    </template>
</div>
