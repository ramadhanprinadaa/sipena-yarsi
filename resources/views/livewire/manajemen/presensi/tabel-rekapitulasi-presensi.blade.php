<div x-data="{ showLoading: false, openExport: false }" x-on:open-export="showLoading = false; openExport = true;"
    class="flex flex-col h-full lg:h-[calc(100vh-150px)] px-2 py-3">
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

        <div class="flex-none font-poppins">
            <div class="flex flex-wrap items-center gap-2 md:gap-3">
                <h1 class="text-2xl font-semibold text-slate-800 tracking-tight">
                    Rekapitulasi Presensi
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
                Pantau ringkasan kehadiran dan jam kerja pegawai dalam satu periode.
            </p>
        </div>

        <div
            class="flex-none flex gap-3 px-3 py-2 bg-white border border-slate-200/80 rounded-xl shadow-sm items-start w-100">
            <div class="flex items-center justify-center w-9 h-9 bg-indigo-50 text-indigo-600 rounded-lg">
                <i class="fa-solid fa-calendar-week text-md"></i>
            </div>
            <div class="flex flex-col gap-3">
                <span class="text-xs font-bold text-slate-400 uppercase">
                    Periode Rekapitulasi
                </span>
                <span class="text-sm font-semibold text-slate-800 leading-none">
                    {{ $this->infoPeriodeAktif }}
                </span>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="flex-none flex flex-wrap items-center justify-between gap-3">

        <!-- Left Filter -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Periode Mulai -->
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
                    placeholder="Pilih Periode Mulai"
                    class="w-full h-10 pl-9 pr-3 text-sm border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-3 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition" />
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
                    class="w-full h-10 pl-9 pr-3 text-sm border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-3 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition" />
                <button type="button" x-show="$wire.selectedPeriodeSelesai"
                    @click="
                    $wire.set('selectedPeriodeSelesai', null);
                    picker.setDate({ clear: true });
                    $refs.input.value = '';"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Filter Unit Kerja -->
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

        </div>

        <!-- Search and Export Data -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Button Export Data -->
            <button
                x-on:click="showLoading = true; $wire.openExportPreview().finally(() => setTimeout(() => showLoading = false, 500))"
                class="flex items-center px-3 h-10 justify-center cursor-pointer bg-emerald-600/90 hover:bg-emerald-700 text-white text-sm rounded-md transition whitespace-nowrap">
                <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i>
                Export Excel
            </button>

            <!-- Search -->
            <div class="relative w-65">
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

    <!-- Table -->
    <div class="table-container relative mt-4">
        <!-- Loading -->
        <div wire:loading wire:target="search, selectedUnitKerja, selectedPeriodeMulai, selectedPeriodeSelesai">
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper overflow-x-auto">
            @if (!$this->baseQuery()->exists() || !$this->hasPresensiData)
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-calendar-minus text-2xl text-indigo-500"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700 max-w-lg">
                        {{ $this->emptyStateMessage }}
                    </h3>
                    @if ($user->hasRole('SDM Universitas') || $user->hasRole('Pimpinan'))
                        <p class="text-sm text-gray-500 mt-1 max-w-sm">
                            Silahkan Hubungi Administrator.
                        </p>
                    @else
                        <p class="text-sm text-gray-500 mt-1 max-w-sm">
                            Silahkan Upload File Presensi.
                        </p>
                    @endif
                </div>
            @else
                <table class="table">
                    <thead class="table-header">
                        <tr class="text-xs uppercase">
                            <th scope="col" class="px-4 py-3 font-medium w-10 text-center">#</th>
                            <th scope="col" class="px-4 py-3 font-medium w-50 text-left">
                                <div class="truncate">Nama Pegawai</div>
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-20 text-center">
                                <div class="truncate">Hadir</div>
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-24 text-center">
                                <div class="truncate">Tidak Hadir</div>
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-20 text-center">
                                <div class="truncate">Lembur</div>
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-20 text-center">
                                <div class="truncate">Cuti</div>
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-20 text-center">
                                <div class="truncate">Izin</div>
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-20 text-center">
                                <div class="truncate">Sakit</div>
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-32 text-center">
                                <div class="truncate">Total Jam Kerja</div>
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-34 text-center">
                                <div class="truncate">Total Jam Lembur</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->rekapitulasiPresensi as $index => $pegawai)
                            <tr class="table-row">
                                <!-- No -->
                                <td class="px-3 py-2 text-center text-gray-500 text-sm">
                                    {{ $this->rekapitulasiPresensi->firstItem() + $index }}
                                </td>

                                <!-- Nama Pegawai -->
                                <td class="px-4 py-2">
                                    <div class="flex flex-col min-w-0">
                                        <span class="truncate text-sm font-semibold text-gray-800"
                                            title="{{ $pegawai->nama }}">
                                            {{ $pegawai->nama }}
                                        </span>
                                        <span class="text-[11px] text-gray-400 truncate">NIP.
                                            {{ $pegawai->nip }}</span>
                                    </div>
                                </td>

                                <!-- Hadir -->
                                <td class="px-3 py-2 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100/60 text-emerald-700 border border-emerald-100">
                                        {{ $pegawai->rekap['hadir'] }}
                                    </span>
                                </td>

                                <!-- Tidak Hadir -->
                                <td class="px-3 py-2 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100/60 text-red-700 border border-red-100">{{ $pegawai->rekap['tidak_hadir'] }}</span>
                                </td>

                                <!-- Lembur -->
                                <td class="px-3 py-2 text-center text-sm font-medium text-blue-600">
                                    {{ $pegawai->rekap['lembur'] }}
                                </td>

                                <!-- Cuti -->
                                <td class="px-3 py-2 text-center text-sm text-gray-500">
                                    {{ $pegawai->rekap['cuti'] }}
                                </td>

                                <!-- Izin -->
                                <td class="px-3 py-2 text-center text-sm text-gray-500">
                                    {{ $pegawai->rekap['izin'] }}
                                </td>

                                <!-- Sakit -->
                                <td class="px-3 py-2 text-center text-sm text-gray-500">
                                    {{ $pegawai->rekap['sakit'] }}
                                </td>

                                <!-- Total Kerja -->
                                <td class="px-3 py-2 text-center">
                                    <span class="text-sm font-bold text-gray-700">
                                        {{ $pegawai->rekap['total_jam_kerja'] }}
                                    </span>
                                </td>

                                <!-- Total Lembur -->
                                <td class="px-3 py-2 text-center">
                                    <span class="text-sm font-bold text-indigo-600">
                                        {{ $pegawai->rekap['total_jam_lembur'] }}
                                    </span>
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
                        class="font-semibold text-heading">
                        {{ $this->hasPresensiData ? $this->rekapitulasiPresensi->firstItem() : '' }}
                        -
                        {{ $this->hasPresensiData ? $this->rekapitulasiPresensi->lastItem() : '' }}
                    </span>
                    dari
                    <span class="font-semibold text-heading">
                        {{ $this->hasPresensiData ? $this->rekapitulasiPresensi->total() : 0 }}
                        data
                    </span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click="gotoPage(1)"
                            @disabled($this->rekapitulasiPresensi->onFirstPage() || !$this->hasPresensiData)
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click="previousPage" @disabled($this->rekapitulasiPresensi->onFirstPage() || !$this->hasPresensiData)
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @if(!$this->hasPresensiData)
                        <li>
                            <button class="table-pagination-btn-active px-3">
                                1
                            </button>
                        </li>
                    @else
                        @for ($i = max(1, $this->rekapitulasiPresensi->currentPage() - 3); $i <= min($this->rekapitulasiPresensi->lastPage(), $this->rekapitulasiPresensi->currentPage() + 3); $i++)
                            <li>
                                <button wire:click="gotoPage({{ $i }})"
                                    class="w-9 {{ $this->rekapitulasiPresensi->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                    {{ $i }}
                                </button>
                            </li>
                        @endfor
                    @endif
                    <li>
                        <button wire:click="nextPage" @disabled(!$this->rekapitulasiPresensi->hasMorePages() || !$this->hasPresensiData)
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click="gotoPage({{ $this->rekapitulasiPresensi->lastPage() }})"
                        @disabled($this->rekapitulasiPresensi->onLastPage() || !$this->hasPresensiData)
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

                <x-modal.confirmation title="Export Data Rekapitulasi Presensi"
                    subTitle="Konfirmasi data export rekapitulasi presensi di bawah ini."
                    icon="fa-solid fa-file-export" iconBg="bg-emerald-500" iconShadow="shadow-emerald-200"
                    closeAction="openExport = false">

                    <div>
                        {{-- TAMPILAN JIKA DATA KOSONG --}}
                        @if ($preview['isEmpty'])
                            <div
                                class="flex flex-col items-center justify-center min-h-[calc(100vh-450px)] p-6 text-center bg-red-50/50 border border-red-100 rounded-xl">
                                <div
                                    class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mb-4 shadow-sm shadow-red-100">
                                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-500"></i>
                                </div>
                                <h3 class="text-base font-bold text-red-800">Tidak Ada Data Untuk Diexport</h3>
                                <p class="text-sm text-red-600 mt-1.5 max-w-md leading-relaxed mx-auto">
                                    {{ $this->emptyStateMessage }}
                                </p>
                            </div>
                        @else
                            <div class="space-y-4">
                                <p class="text-sm text-slate-500 leading-relaxed">
                                    Silakan periksa kembali parameter filter di bawah ini sebelum mengunduh berkas
                                    rekapitulasi presensi.
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                    <div
                                        class="sm:col-span-2 p-3.5 bg-slate-50 border border-slate-200/70 rounded-xl flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-600 shrink-0">
                                            <i class="fa-regular fa-calendar-days text-base"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                                Periode Presensi</h4>
                                            <p class="text-sm font-bold text-slate-700 mt-0.5">
                                                {{ $preview['periode'] }}</p>
                                        </div>
                                    </div>

                                    <div
                                        class="sm:col-span-2 p-3.5 bg-slate-50 border border-slate-200/70 rounded-xl flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                                            <i class="fa-solid fa-building text-base"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                                Unit Kerja</h4>
                                            <p class="text-sm font-semibold text-slate-700 mt-0.5 truncate"
                                                title="{{ $preview['unit'] }}">{{ $preview['unit'] }}</p>
                                        </div>
                                    </div>

                                    @if (!empty($preview['singleEmployee']))
                                        <div
                                            class="sm:col-span-2 p-3.5 bg-indigo-50/70 border border-indigo-100 rounded-xl flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold shrink-0 text-xs shadow-sm uppercase">
                                                {{ substr($preview['singleEmployee'], 0, 2) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h4
                                                    class="text-xs font-semibold text-indigo-500 uppercase tracking-wider">
                                                    Pegawai Terpilih</h4>
                                                <p class="text-sm font-bold text-slate-800 mt-0.5 truncate"
                                                    title="{{ $preview['singleEmployee'] }}">
                                                    {{ $preview['singleEmployee'] }}</p>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        @endif
                    </div>

                    <x-slot:footer>
                        <button type="button" @click="openExport = false"
                            class="px-5 py-2 rounded-md border border-slate-200 bg-red-400/90 hover:bg-red-500 text-white transition cursor-pointer">
                            Batal
                        </button>

                        <button type="button" {{-- Gunakan x-on:click Alpine agar bisa menunggu download & menutup modal --}}
                            @if (!$preview['isEmpty']) x-on:click="await $wire.exportData(); openExport = false;" @endif
                            @disabled($preview['isEmpty']) wire:loading.attr="disabled" wire:target="exportData"
                            wire:loading.class="opacity-70 !cursor-wait scale-95"
                            class="px-5 py-2 rounded-md border border-slate-200 transition-all duration-200 flex items-center justify-center active:scale-95
                    {{ $preview['isEmpty']
                        ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                        : 'bg-emerald-600/90 hover:bg-emerald-700 text-white cursor-pointer shadow-sm' }}">

                            <i wire:loading.remove wire:target="exportData"
                                class="fa-solid fa-arrow-up-right-from-square mr-2">
                            </i>

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
