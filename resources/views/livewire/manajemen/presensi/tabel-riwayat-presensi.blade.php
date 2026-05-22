<div class="flex flex-col h-full lg:h-[calc(100vh-180px)] space-y-4 px-2 py-3">
    <!-- Header -->
    <div class="flex justify-between">
        <div class="flex-none flex gap-2 font-poppins">
            <h1 class="text-2xl font-semibold">Riwayat Presensi Harian</h1>
        </div>

        <!-- Search and Export Data -->
        <div class="flex gap-2">

            <!-- Button Export Data -->
            <button @click=""
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
            </div>


        </div>
    </div>

    <!-- Filter -->
    <div class="flex-none flex flex-wrap items-center justify-between gap-3">

        <!-- Filter Dropdown -->
        <div class="flex flex-wrap items-center gap-2">
            @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                <!-- Filter Unit Kerja -->
                <div class="relative w-44" x-data="{ open: false, selected: 'Semua Unit Kerja' }">
                    <button @click="open = !open" class="filter-dropdown" wire:model.live="selectedUnitKerja"
                        type="button">
                        <span x-text="selected" class="truncate"></span>
                        <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Menu -->
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="dropdown-menu h-[calc(100vh-340px)] overflow-auto">
                        <ul class="p-2 text-sm text-body font-medium">
                            <li>
                                <button @click="selected='Semua Unit Kerja'; open=false"
                                    wire:click="$set('selectedUnitKerja', null)" class="dropdown-item">
                                    Semua Unit Kerja
                                </button>
                            </li>
                            @if (auth()->user()->HasRole('SDM Universitas'))
                                @foreach ($unit_kerja_universitas as $unit)
                                    <li>
                                        <button @click="selected='{{ $unit->name }}'; open=false"
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
                                        <button @click="selected='{{ $unit->name }}'; open=false"
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

                <!-- Jenis Pegawai -->
                <div class="relative w-50" x-data="{ open: false, selected: 'Semua Jenis Pegawai' }">
                    <button @click="open = !open" class="filter-dropdown" wire:model.live="selectedJenisPegawai"
                        type="button">
                        <span x-text="selected" class="truncate"></span>
                        <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Menu -->
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="dropdown-menu h-[calc(100vh-340px)] overflow-auto">
                        <ul class="p-2 text-sm text-body font-medium">
                            <li>
                                <button @click="selected='Semua Jenis Pegawai'; open=false"
                                    wire:click="$set('selectedJenisPegawai', null)" class="dropdown-item">
                                    Semua Unit Kerja
                                </button>
                            </li>
                            @foreach ($jenis_pegawai as $jenis)
                                <li>
                                    <button @click="selected='{{ $jenis }}'; open=false"
                                        wire:click="$set('selected', '{{ $jenis }}')" class="dropdown-item">
                                        {{ $jenis }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Status Kehadiran -->
            <div class="relative w-54" x-data="{ open: false, selected: 'Semua Status Kehadiran' }">
                <button @click="open = !open" class="filter-dropdown" wire:model.live="selectedStatusKehadiran"
                    type="button">
                    <span x-text="selected" class="truncate"></span>
                    <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
                    </svg>
                </button>
                <!-- Menu -->
                <div x-show="open" @click.outside="open = false" x-transition
                    class="dropdown-menu h-[calc(100vh-340px)] overflow-auto">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="selected='Semua Status Kehadiran'; open=false"
                                wire:click="$set('selectedStatusKehadiran', null)" class="dropdown-item">
                                Semua Status Kehadiran
                            </button>
                        </li>
                        @foreach ($status_kehadiran as $status)
                            <li>
                                <button @click="selected='{{ $status->status }}'; open=false"
                                    wire:click="$set('selected', '{{ $status->id }}')" class="dropdown-item">
                                    {{ $status->status }}
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
            <div class="relative w-50">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                </div>
                <input type="text" x-data x-ref="picker" x-init="const picker = new Datepicker($refs.picker, {
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    language: 'id'
                });
                $refs.picker.addEventListener('changeDate', () => {
                    $wire.set('selectedDate', $refs.picker.value);
                });"
                    placeholder="Pilih Periode Tanggal"
                    class="w-full h-9 pl-9 pr-3 text-sm border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-3 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition"
                    placeholder="Tanggal Mulai" />
            </div>

            <!-- Periode Selesai -->
            <div class="relative w-50">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                </div>
                <input type="text" x-data x-ref="picker" x-init="const picker = new Datepicker($refs.picker, {
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    language: 'id'
                });
                $refs.picker.addEventListener('changeDate', () => {
                    $wire.set('selectedDate', $refs.picker.value);
                });"
                    placeholder="Pilih Periode Selesai"
                    class="w-full h-9 pl-9 pr-3 text-sm border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-3 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition"
                    placeholder="Tanggal Selesai" />
            </div>
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
        <div class="table-wrapper overflow-x-auto">
            <table class="table w-full table-fixed">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium w-12 text-center">#</th>
                        <th scope="col" class="px-4 py-3 font-medium w-44 text-left">
                            Nama Pegawai
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-40 text-center">
                            Tanggal Presensi
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-28 text-center">
                            Jam Masuk
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-28 text-center">
                            Jam Pulang
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-24 text-center">
                            Total Jam
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-40 text-center">
                            Status Kehadiran
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-24 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $dummyEmployees = [
                            ['nama' => 'Rafly Eryan Azis', 'nip' => '199801012022031001'],
                            ['nama' => 'Andi Wijaya, S.Kom., M.T.', 'nip' => '199505122020121002'],
                            ['nama' => 'Siti Aminah Rahmawati, M.Pd.', 'nip' => '199010102015012003'],
                            ['nama' => 'Budi Santoso Herlambang', 'nip' => '198502152010031004'],
                            ['nama' => 'Dr. Dewi Lestari, M.Si.', 'nip' => '199207202018082005'],
                        ];
                        $dummyStatuses = [
                            [
                                'label' => 'Hadir Normal',
                                'color' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            ],
                            ['label' => 'Hadir Kurang Jam', 'color' => 'bg-amber-100 text-amber-700 border-amber-200'],
                            ['label' => 'Tidak Hadir', 'color' => 'bg-red-100 text-red-700 border-red-200'],
                            ['label' => 'Izin', 'color' => 'bg-blue-100 text-blue-700 border-blue-200'],
                            ['label' => 'Sakit', 'color' => 'bg-orange-100 text-orange-700 border-orange-200'],
                            ['label' => 'Cuti', 'color' => 'bg-purple-100 text-purple-700 border-purple-200'],
                        ];
                    @endphp
                    @for ($i = 0; $i < 10; $i++)
                        @php
                            $emp = $dummyEmployees[$i % 5];
                            $status = $dummyStatuses[rand(0, 5)];
                        @endphp
                        <tr class="table-row">
                            <!-- No -->
                            <td class="px-3 py-2 text-center text-gray-500 text-sm">
                                {{ $i + 1 }}
                            </td>

                            <!-- Nama Pegawai -->
                            <td class="px-3 py-2">
                                <div class="flex flex-col min-w-0">
                                    <span class="truncate text-sm font-semibold text-gray-800"
                                        title="{{ $emp['nama'] }}">
                                        {{ $emp['nama'] }}
                                    </span>
                                    <span class="text-[11px] text-gray-400">NIP. {{ $emp['nip'] }}</span>
                                </div>
                            </td>

                            <!-- Tanggal Presensi -->
                            <td class="px-3 py-2 text-sm text-center text-gray-600">
                                {{ now()->subDays($i)->format('d/m/Y') }}
                            </td>

                            <!-- Jam Masuk -->
                            <td class="px-3 py-2 text-center text-sm text-gray-600">07:55:12</td>

                            <!-- Jam Pulang -->
                            <td class="px-3 py-2 text-center text-gray-600">16:30:45</td>

                            <!-- Total Jam -->
                            <td class="px-3 py-2 text-center text-sm text-gray-600 font-medium">08:35</td>

                            <!-- Status Kehadiran -->
                            <td class="px-3 py-2 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-3 py-2 text-center">
                                <button
                                    class="bg-indigo-500 hover:bg-indigo-600 text-white text-[11px] font-semibold px-2 py-1 rounded-md shadow-sm transition-all duration-200 cursor-pointer">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Footer & Pagination -->
        <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2"
                aria-label="Table navigation">
                <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                    Menampilkan
                    <span class="font-semibold text-heading">10</span> dari
                    <span class="font-semibold text-heading">100 file</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click=""
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click=""
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = 1; $i <= 5; $i++)
                        <li>
                            <button wire:click="" class="w-9 table-pagination-btn">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button wire:click=""
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click=""
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>
</div>
