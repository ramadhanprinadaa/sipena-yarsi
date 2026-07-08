<div class="flex flex-col gap-4 min-h-0">
            {{-- Tabs Navigation --}}
            @php
                $userRole = auth()->user()->role->name ?? null;
                $allowedTabs = [];

                switch($userRole) {
                    case 'Admin':
                        $allowedTabs = ['riwayat', 'rekap'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'SDM Yayasan':
                        $allowedTabs = ['spl', 'riwayat', 'verifikasi', 'rekap'];
                        $defaultTab = 'spl';
                        break;
                    case 'SDM Universitas':
                        $allowedTabs = ['spl', 'riwayat', 'verifikasi', 'rekap'];
                        $defaultTab = 'spl';
                        break;
                    case 'Pimpinan':
                        $allowedTabs = ['spl', 'riwayat', 'verifikasi', 'rekap'];
                        $defaultTab = 'spl';
                        break;
                    case 'Rektor':
                        $allowedTabs = ['spl', 'riwayat', 'verifikasi', 'rekap'];
                        $defaultTab = 'spl';
                        break;
                    default:
                        $allowedTabs = [];
                        $defaultTab = 'riwayat';
                }
            @endphp

            {{-- ─── Tabs Navigation ─── --}}
            <div x-data="{
                    activeTab: 'spl',
                    init() { this.$nextTick(() => this.updatePill()) },
                    updatePill() {
                        const refs = {
                            spl:     this.$refs.btnSpl,
                            riwayat: this.$refs.btnRiwayat,
                            laporan: this.$refs.btnLaporan,
                            rekapitulasi:   this.$refs.btnRekapitulasi,
                        };
                        const btn = refs[this.activeTab];
                        if (!btn) return;
                        const pill = this.$refs.pill;
                        pill.style.width  = btn.offsetWidth  + 'px';
                        pill.style.left   = btn.offsetLeft   + 'px';
                    },
                    setTab(tab) {
                        this.activeTab = tab;
                        this.$nextTick(() => this.updatePill());
                    }
                }"
                class="flex flex-col gap-4 min-h-0"
            >

                {{-- Tab Bar --}}
                <div class="relative inline-flex items-center gap-1.5 p-1.5 bg-gradient-to-r from-indigo-50 via-white to-cyan-50 rounded-3xl self-start shadow-lg shadow-indigo-100/60 border border-indigo-100/80 overflow-hidden">

                    <div class="absolute -top-8 -left-8 h-24 w-24 rounded-full bg-indigo-200/25 blur-2xl pointer-events-none"></div>
                    <div class="absolute -bottom-8 -right-8 h-24 w-24 rounded-full bg-cyan-200/25 blur-2xl pointer-events-none"></div>

                    {{-- Sliding pill (background) --}}
                    <div
                        x-ref="pill"
                        class="absolute top-1.5 bottom-1.5 rounded-2xl bg-white shadow-lg shadow-indigo-200/40 border border-indigo-200/70 ring-1 ring-indigo-100/50 transition-all duration-300 ease-out pointer-events-none"
                    ></div>

                    {{-- Surat Perintah Lembur --}}
                    <button
                        x-ref="btnSpl"
                        @click="setTab('spl')"
                        :class="activeTab === 'spl' ? 'text-indigo-700 scale-[1.01]' : 'text-gray-500 hover:text-gray-700 hover:-translate-y-0.5'"
                        class="relative z-10 flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 cursor-pointer whitespace-nowrap group"
                    >
                        <i class="fa-solid fa-file-lines text-base transition-transform duration-200 group-hover:scale-110"></i>
                        <span>Surat Perintah Lembur</span>
                    </button>

                    {{-- Riwayat Lembur --}}
                    <button
                        x-ref="btnRiwayat"
                        @click="setTab('riwayat')"
                        :class="activeTab === 'riwayat' ? 'text-indigo-700 scale-[1.01]' : 'text-gray-500 hover:text-gray-700 hover:-translate-y-0.5'"
                        class="relative z-10 flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 cursor-pointer whitespace-nowrap group"
                    >
                        <i class="fa-solid fa-history text-base transition-transform duration-200 group-hover:scale-110"></i>
                        <span>Riwayat Lembur</span>
                    </button>

                    {{-- Laporan Lembur --}}
                    <button
                        x-ref="btnLaporan"
                        @click="setTab('laporan')"
                        :class="activeTab === 'laporan' ? 'text-indigo-700 scale-[1.01]' : 'text-gray-500 hover:text-gray-700 hover:-translate-y-0.5'"
                        class="relative z-10 flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 cursor-pointer whitespace-nowrap group"
                    >
                        <i class="fa-solid fa-clipboard-check text-base transition-transform duration-200 group-hover:scale-110"></i>
                        <span>Laporan Lembur</span>
                    </button>

                    {{-- Rekapitulasi --}}
                    <button
                        x-ref="btnRekapitulasi"
                        @click="setTab('rekapitulasi')"
                        :class="activeTab === 'rekapitulasi' ? 'text-indigo-700 scale-[1.01]' : 'text-gray-500 hover:text-gray-700 hover:-translate-y-0.5'"
                        class="relative z-10 flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 cursor-pointer whitespace-nowrap group"
                    >
                        <i class="fa-solid fa-chart-bar text-base transition-transform duration-200 group-hover:scale-110"></i>
                        <span>Rekapitulasi</span>
                    </button>
                </div>

                {{-- TAB 1: Surat Perintah Lembur (SPL) - Only Pimpinan --}}
                @if(in_array('spl', $allowedTabs))
                <div x-show="activeTab === 'spl'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterSplDate" class="filter-dropdown border border-gray-200">
                            </div>
                        </div>

                        <button wire:click="$dispatch('open-add-spl')" class="flex items-center w-48 h-10 justify-center cursor-pointer bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 hover:shadow-xl text-white text-sm font-semibold rounded-[10px] transition duration-300 ease-in-out transform hover:scale-105">
                            <i class="fa-solid fa-plus mr-2"></i> Buat & Terbitkan SPL
                        </button>

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

                                <!-- Header -->
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Nomor Surat</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Tanggal Lembur</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Kegiatan</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Jam Masuk</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Jam Keluar</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex justify-between gap-2">
                                                <span class="truncate items-center">Status</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex justify-center gap-2">
                                                <span class="truncate items-center">Aksi</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>

                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($spls as $spl)
                                    <tr class="table-row hover:bg-gray-50 transition">
                                        <td class="px-4 py-4 font-medium text-blue-700">{{ $spl->nomor_surat }}</td>
                                        <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($spl->tanggal_lembur)->format('d F Y') }}</td>
                                        <td class="px-4 py-4 text-gray-600">{{ $spl->nama_kegiatan }}</td>
                                        <td class="px-4 py-4 text-gray-600">{{ $spl->jam_mulai }}</td>
                                        <td class="px-4 py-4 text-gray-600">{{ $spl->jam_selesai }}</td>
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">{{ $spl->status }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @if($spl->lembur->isEmpty())
                                                <button wire:click="$dispatch('open-edit-spl', { id: {{ $spl->id }} })" class="px-3 py-1.5 text-white bg-amber-500 hover:bg-amber-100 hover:text-amber-500 rounded-md transition-colors cursor-pointer">
                                                    <i class="fa-solid fa-pen-to-square mr-1"></i>
                                                    <span class="text-xs">Edit</span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Tidak ada data SPL</td>
                                    </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                            <!-- Footer & Pagination -->
                            <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

                                <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                                    <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                                        Menampilkan
                                        <span class="font-semibold text-heading">{{ $spls->firstItem() }}-{{ $spls->lastItem() }}</span> dari
                                        <span class="font-semibold text-heading">{{ $spls->total() }} SPL</span>
                                    </span>

                                    <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                                        <li>
                                            <button
                                                wire:click="gotoPage(1)"
                                                @disabled($spls->onFirstPage())
                                                class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                <i class="fa-solid fa-angles-left text-xs"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                wire:click="previousPage"
                                                @disabled($spls->onFirstPage())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Previous
                                            </button>
                                        </li>
                                        @for ($i = max(1, $spls->currentPage() - 3);
                                            $i <= min($spls->lastPage(), $spls->currentPage() + 3);
                                            $i++)
                                            <li>
                                                <button
                                                    wire:click="gotoPage({{ $i }})"
                                                    class="w-9 {{ $spls->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                                    {{ $i }}
                                                </button>
                                            </li>
                                        @endfor
                                        <li>
                                            <button
                                                wire:click="nextPage"
                                                @disabled(!$spls->hasMorePages())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Next
                                            </button>
                                        </li>
                                        <button
                                            wire:click="gotoPage({{ $spls->lastPage() }})"
                                            @disabled($spls->onLastPage())
                                            class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                            <i class="fa-solid fa-angles-right text-xs"></i>
                                        </button>
                                    </ul>
                                </nav>
                            </div>
                    </div>
                </div>
                @endif

                {{-- TAB 2: Riwayat Lembur - All allowed roles --}}
                @if(in_array('riwayat', $allowedTabs))
                <div x-show="activeTab === 'riwayat'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter (Left Side) -->
                        <div class="flex flex-wrap gap-3">
                            <!-- Status Filter -->
                            <div class="relative w-48" x-data="{ open: false, selected: 'Semua Status' }">
                                <button
                                    @click="open = !open"
                                    wire:model.live="filterRiwayatStatus"
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
                                            <button @click="selected='Semua Status'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus', null)">
                                                Semua Status
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Menunggu Verifikasi Atasan'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Menunggu Verifikasi Atasan')">
                                                Menunggu Verifikasi Atasan
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Menunggu Verifikasi Rektor'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Menunggu Verifikasi Rektor')">
                                                Menunggu Verifikasi Rektor
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Menunggu Verifikasi SDM Universitas'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Menunggu Verifikasi SDM Universitas')">
                                                Menunggu Verifikasi SDM Universitas
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Menunggu Verifikasi SDM Yayasan'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Menunggu Verifikasi SDM Yayasan')">
                                                Menunggu Verifikasi SDM Yayasan
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Menunggu Pelaksanaan'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Menunggu Pelaksanaan')">
                                                Menunggu Pelaksanaan
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Menunggu Laporan'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Menunggu Laporan')">
                                                Menunggu Laporan
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Selesai'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Selesai')">
                                                Selesai
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Ditolak'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Ditolak')">
                                                Ditolak
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Date Filter -->
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRiwayatDate" class="filter-dropdown border border-gray-200">
                            </div>
                        </div>

                        <!-- Search & Button (Right Side) -->
                        <div class="flex flex-wrap gap-3">
                            <!-- Search -->
                            <div class="relative w-65">
                                <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                                </div>
                                <input type="text" class="input-search"
                                    wire:model.live.debounce.300ms="filterRiwayatSearch" placeholder="Cari Nama atau NIP ...">
                            </div>

                            <!-- Button -->
                            <button wire:click="exportRiwayatExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 hover:shadow-xl text-white text-sm font-semibold rounded-[10px] transition duration-300 ease-in-out transform hover:scale-105">
                                <i class="fa-solid fa-download mr-2"></i> Export Excel
                            </button>
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

                                <!-- Header -->
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Nama Pegawai</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">NIP</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Tanggal Lembur</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex justify-between gap-2">
                                                <span class="truncate items-center">Jenis Hari</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex justify-between gap-2">
                                                <span class="truncate items-center">Jam Mulai & Jam Selesai</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Status</span>
                                            </div>
                                        </th>

                                    </tr>
                                </thead>

                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($lemburList as $lembur)
                                        <tr class="table-row hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $lembur->pegawai->nama ?? '-' }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->pegawai->nip ?? '-' }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->jenis_hari ?? '-'}}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }} | {{ $lembur->jam_mulai ?? '-' }} - {{ $lembur->jam_selesai ?? '-' }}</td>
                                            <td class="flex px-4 py-4 items-center justify-center">
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">{{ $this->getStatusLembur($lembur) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada data pengajuan lembur</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                            <!-- Footer & Pagination -->
                            <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

                                <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                                    <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                                        Menampilkan
                                        <span class="font-semibold text-heading">{{ $lemburList->firstItem() }}-{{ $lemburList->lastItem() }}</span> dari
                                        <span class="font-semibold text-heading">{{ $lemburList->total() }} Lembur</span>
                                    </span>

                                    <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                                        <li>
                                            <button
                                                wire:click="gotoPage(1)"
                                                @disabled($lemburList->onFirstPage())
                                                class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                <i class="fa-solid fa-angles-left text-xs"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                wire:click="previousPage"
                                                @disabled($lemburList->onFirstPage())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Previous
                                            </button>
                                        </li>
                                        @for ($i = max(1, $lemburList->currentPage() - 3);
                                            $i <= min($lemburList->lastPage(), $lemburList->currentPage() + 3);
                                            $i++)
                                            <li>
                                                <button
                                                    wire:click="gotoPage({{ $i }})"
                                                    class="w-9 {{ $lemburList->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                                    {{ $i }}
                                                </button>
                                            </li>
                                        @endfor
                                        <li>
                                            <button
                                                wire:click="nextPage"
                                                @disabled(!$lemburList->hasMorePages())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Next
                                            </button>
                                        </li>
                                        <button
                                            wire:click="gotoPage({{ $lemburList->lastPage() }})"
                                            @disabled($lemburList->onLastPage())
                                            class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                            <i class="fa-solid fa-angles-right text-xs"></i>
                                        </button>
                                    </ul>
                                </nav>
                            </div>
                    </div>
                </div>
                @endif

                {{-- TAB 3: Rekapitulasi Lembur - Admin & SDM Yayasan --}}
                @if(in_array('rekap', $allowedTabs))
                <div x-show="activeTab === 'rekapitulasi'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRekapStartDate" placeholder="Start Date" class="filter-dropdown border border-gray-200">
                            </div>
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRekapEndDate" placeholder="End Date" class="filter-dropdown border border-gray-200">
                            </div>
                        </div>

                        <!-- Button -->
                        <button wire:click="exportRekapExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 hover:shadow-xl text-white text-sm font-semibold rounded-[10px] transition duration-300 ease-in-out transform hover:scale-105">
                            <i class="fa-solid fa-download mr-2"></i> Export Excel
                        </button>

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

                                <!-- Header -->
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Nama Pegawai</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">NIP</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Total Hari</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Total Jam</span>
                                            </div>
                                        </th>

                                    </tr>
                                </thead>

                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($rekapList as $rekap)
                                        <tr class="table-row hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $rekap->pegawai->nama }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $rekap->pegawai->nip }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $rekap->hari }} hari</td>
                                            <td class="px-4 py-4 text-gray-600">{{ number_format($rekap->total_jam, 2) }} jam</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data rekap lembur</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                            <!-- Footer & Pagination -->
                            <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

                                <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                                    <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                                        Menampilkan
                                        <span class="font-semibold text-heading">{{ $rekapList->firstItem() }}-{{ $rekapList->lastItem() }}</span> dari
                                        <span class="font-semibold text-heading">{{ $rekapList->total() }} Rekap</span>
                                    </span>

                                    <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                                        <li>
                                            <button
                                                wire:click="gotoPage(1)"
                                                @disabled($rekapList->onFirstPage())
                                                class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                <i class="fa-solid fa-angles-left text-xs"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                wire:click="previousPage"
                                                @disabled($rekapList->onFirstPage())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Previous
                                            </button>
                                        </li>
                                        @for ($i = max(1, $rekapList->currentPage() - 3);
                                            $i <= min($rekapList->lastPage(), $rekapList->currentPage() + 3);
                                            $i++)
                                            <li>
                                                <button
                                                    wire:click="gotoPage({{ $i }})"
                                                    class="w-9 {{ $rekapList->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                                    {{ $i }}
                                                </button>
                                            </li>
                                        @endfor
                                        <li>
                                            <button
                                                wire:click="nextPage"
                                                @disabled(!$rekapList->hasMorePages())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Next
                                            </button>
                                        </li>
                                        <button
                                            wire:click="gotoPage({{ $rekapList->lastPage() }})"
                                            @disabled($rekapList->onLastPage())
                                            class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                            <i class="fa-solid fa-angles-right text-xs"></i>
                                        </button>
                                    </ul>
                                </nav>
                            </div>
                    </div>
                </div>
                @endif

                {{-- TAB 4: Persetujuan & Verifikasi Laporan Lembur - SDM Yayasan, SDM Universitas, Pimpinan --}}
                @if(in_array('verifikasi', $allowedTabs))
                <div x-show="activeTab === 'laporan'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterLaporanDate" class="filter-dropdown border border-gray-200">
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="relative w-77">
                            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                            </div>
                            <input type="text" class="input-search"
                                wire:model.live.debounce.300ms="filterLaporanSearch" placeholder="Cari Nama atau NIP ...">
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

                                <!-- Header -->
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col" class="px-4 py-4 font-medium w-28">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Nama Pegawai</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-35">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Tanggal & Jam Aktual</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-25">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Hasil Pekerjaan</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-35">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Status</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-25">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Catatan</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-25">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Disetujui Oleh</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 text-center font-medium w-40">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Aksi</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>

                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($laporanList as $lembur)
                                        <tr class="table-row hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $lembur->pegawai->nama ?? '-' }}<br><span class="text-xs text-gray-400">{{ $lembur->pegawai->nip ?? '-' }}</span></td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }} | {{ $lembur->laporanHasilLembur->jam_mulai ?? '-' }} - {{ $lembur->laporanHasilLembur->jam_selesai ?? '-' }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->laporanHasilLembur->hasil_pekerjaan ?? '-' }}</td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $this->getLaporanStatus($lembur) }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApprovalCatatan($lembur, 'laporan') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApproverLabel($lembur, 'laporan') }}</td>
                                            <td class="flex px-4 py-4 items-center justify-center gap-2">
                                                @if($this->canApproveLaporan($lembur))
                                                    <button wire:click="openApprovalConfirmation('laporan', 'approve', {{ $lembur->id }})" class="flex px-3 py-2 text-white bg-green-500 hover:bg-green-100 hover:text-green-500 rounded-md transition-colors cursor-pointer">
                                                        <i class="fa-solid fa-check mr-1"></i>
                                                        <span class="text-xs">
                                                            Setujui
                                                        </span>
                                                    </button>
                                                    <button wire:click="openApprovalConfirmation('laporan', 'reject', {{ $lembur->id }})" class="flex px-3 py-2 text-white bg-red-500 hover:bg-red-100 hover:text-red-500 rounded-md transition-colors cursor-pointer">
                                                        <i class="fa-solid fa-x mr-1"></i>
                                                        <span class="text-xs">
                                                            Tolak
                                                        </span>
                                                    </button>
                                                @endif
                                                <button wire:click="$dispatch('showDetailLaporan', { id: {{ $lembur->id }} })" class="flex px-3 py-2 text-white bg-indigo-500 hover:bg-indigo-100 hover:text-indigo-500 rounded-md transition-colors cursor-pointer">
                                                    <i class="fa-solid fa-eye mr-1"></i>
                                                    <span class="text-xs">
                                                        Detail
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">Tidak ada data laporan lembur</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                            <!-- Footer & Pagination -->
                            <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

                                <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                                    <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                                        Menampilkan
                                        <span class="font-semibold text-heading">{{ $laporanList->firstItem() }}-{{ $laporanList->lastItem() }}</span> dari
                                        <span class="font-semibold text-heading">{{ $laporanList->total() }} Laporan</span>
                                    </span>

                                    <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                                        <li>
                                            <button
                                                wire:click="gotoPage(1)"
                                                @disabled($laporanList->onFirstPage())
                                                class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                <i class="fa-solid fa-angles-left text-xs"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                wire:click="previousPage"
                                                @disabled($laporanList->onFirstPage())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Previous
                                            </button>
                                        </li>
                                        @for ($i = max(1, $laporanList->currentPage() - 3);
                                            $i <= min($laporanList->lastPage(), $laporanList->currentPage() + 3);
                                            $i++)
                                            <li>
                                                <button
                                                    wire:click="gotoPage({{ $i }})"
                                                    class="w-9 {{ $laporanList->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                                    {{ $i }}
                                                </button>
                                            </li>
                                        @endfor
                                        <li>
                                            <button
                                                wire:click="nextPage"
                                                @disabled(!$laporanList->hasMorePages())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Next
                                            </button>
                                        </li>
                                        <button
                                            wire:click="gotoPage({{ $laporanList->lastPage() }})"
                                            @disabled($laporanList->onLastPage())
                                            class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                            <i class="fa-solid fa-angles-right text-xs"></i>
                                        </button>
                                    </ul>
                                </nav>
                            </div>
                    </div>
                </div>
                @endif

                @if($confirmAction)
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                        <div class="w-full max-w-md bg-white rounded-[16px] shadow-2xl border border-gray-200">
                            @if($confirmStep === 'password')
                                <div class="px-6 py-5 border-b border-gray-100">
                                    <h3 class="text-lg font-bold text-gray-800">Verifikasi Password</h3>
                                    <p class="mt-2 text-sm text-gray-600">Silakan masukkan password Anda untuk melanjutkan persetujuan.</p>
                                    <div class="mt-4">
                                        <input type="password" wire:model="password" wire:keydown.enter="verifyPassword" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition" placeholder="Masukkan password anda">
                                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="px-6 py-4 flex justify-end gap-3">
                                    <button wire:click="closeApprovalConfirmation" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-[10px] hover:bg-gray-50 transition cursor-pointer">
                                        Batal
                                    </button>
                                    <button wire:click="verifyPassword" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[10px] hover:bg-indigo-700 transition cursor-pointer">
                                        Verifikasi
                                    </button>
                                </div>
                            @else
                                <div class="px-6 py-5 border-b border-gray-100">
                                    <h3 class="text-lg font-bold text-gray-800">{{ $confirmTitle }}</h3>
                                    <p class="mt-2 text-sm text-gray-600">{{ $confirmMessage }}</p>
                                </div>
                                <div class="px-6 py-4 flex justify-end gap-3">
                                    <button wire:click="closeApprovalConfirmation" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-[10px] hover:bg-gray-50 transition cursor-pointer">
                                        Batal
                                    </button>
                                    <button wire:click="confirmApproval" class="px-4 py-2 text-sm font-medium text-white rounded-[10px] transition cursor-pointer {{ $confirmAction === 'approve' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-red-600 hover:bg-red-700' }}">
                                        Ya, {{ $confirmAction === 'approve' ? 'Setujui' : 'Tolak' }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif


            </div>
        </div>
