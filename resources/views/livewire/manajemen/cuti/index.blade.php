<div class="flex-1 flex flex-col gap-4 min-h-0">
            {{-- Tabs Navigation --}}
            @php
                $userRole = auth()->user()->role->name ?? null;
                $allowedTabs = [];
                
                switch($userRole) {
                    case 'Admin':
                        $allowedTabs = ['riwayat', 'rekapitulasi'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'SDM Yayasan':
                        $allowedTabs = ['riwayat', 'rekapitulasi'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'SDM Universitas':
                        $allowedTabs = ['riwayat', 'rekapitulasi'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'Pimpinan':
                        $allowedTabs = ['riwayat', 'rekapitulasi'];
                        $defaultTab = 'riwayat';
                        break;
                    default:
                        $allowedTabs = [];
                        $defaultTab = 'riwayat';
                }
            @endphp
            
            {{-- ─── Tabs Navigation ─── --}}
            <div x-data="{
                    activeTab: 'riwayat',
                    init() { this.$nextTick(() => this.updatePill()) },
                    updatePill() {
                        const refs = {
                            riwayat: this.$refs.btnRiwayat,
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
                class="flex flex-col gap-4 min-h-0">

                {{-- Tab Bar --}}
                <div class="relative inline-flex items-center gap-1.5 p-1.5 bg-gradient-to-r from-indigo-50 via-white to-cyan-50 rounded-3xl self-start shadow-lg shadow-indigo-100/60 border border-indigo-100/80 overflow-hidden">

                    <div class="absolute -top-8 -left-8 h-24 w-24 rounded-full bg-indigo-200/25 blur-2xl pointer-events-none"></div>
                    <div class="absolute -bottom-8 -right-8 h-24 w-24 rounded-full bg-cyan-200/25 blur-2xl pointer-events-none"></div>

                    {{-- Sliding pill (background) --}}
                    <div
                        x-ref="pill"
                        class="absolute top-1.5 bottom-1.5 rounded-2xl bg-white shadow-lg shadow-indigo-200/40 border border-indigo-200/70 ring-1 ring-indigo-100/50 transition-all duration-300 ease-out pointer-events-none"
                    ></div>

                    {{-- Riwayat Cuti --}}
                    <button
                        x-ref="btnRiwayat"
                        @click="setTab('riwayat')"
                        :class="activeTab === 'riwayat' ? 'text-indigo-700 scale-[1.01]' : 'text-gray-500 hover:text-gray-700 hover:-translate-y-0.5'"
                        class="relative z-10 flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 cursor-pointer whitespace-nowrap group"
                    >
                        <i class="fa-solid fa-file-lines text-base transition-transform duration-200 group-hover:scale-110"></i>
                        <span>Riwayat Cuti</span>
                    </button>

                    {{-- Rekapitulasi Cuti --}}
                    <button
                        x-ref="btnRekapitulasi"
                        @click="setTab('rekapitulasi')"
                        :class="activeTab === 'rekapitulasi' ? 'text-indigo-700 scale-[1.01]' : 'text-gray-500 hover:text-gray-700 hover:-translate-y-0.5'"
                        class="relative z-10 flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 cursor-pointer whitespace-nowrap group"
                    >
                        <i class="fa-solid fa-history text-base transition-transform duration-200 group-hover:scale-110"></i>
                        <span>Rekapitulasi Cuti</span>
                    </button>

                </div>

                {{-- TAB 1: Riwayat Cuti - All allowed roles --}}
                @if(in_array('riwayat', $allowedTabs))
                <div x-show="activeTab === 'riwayat'" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-start w-full md:w-auto">

                            <!-- Search -->
                            <div class="relative w-65">
                                <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                                </div>
                                <input type="text" class="input-search"
                                    wire:model.live.debounce.300ms="filterRiwayatSearch" placeholder="Cari Nama atau NIP ...">
                            </div>

                            <!-- Date Filter -->
                            <div class="flex flex-wrap gap-3">
                                <div class="relative w-48">
                                    <input type="date" wire:model.live="filterRiwayatDate" class="filter-dropdown border border-gray-200">
                                </div>
                            </div>
                        </div>
                            
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-end w-full md:w-auto">

                            <div class="relative w-48" x-data="{ open: false, selected: 'Semua Jenis' }">
                                <button
                                    @click="open = !open"
                                    wire:model.live="filterRiwayatJenis"
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
                                            <button @click="selected='Semua Jenis'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatJenis', null)">
                                                Semua Jenis
                                            </button>
                                        </li>
                                        @foreach($jenisCutiList as $jenis)
                                            <li>
                                                <button @click="selected='{{ $jenis->nama }}'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatJenis', '{{ $jenis->id }}')">
                                                    {{ $jenis->nama }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

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
                                            <button @click="selected='Menunggu Verifikasi Pimpinan'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus', 'Menunggu Verifikasi Pimpinan')">
                                                Menunggu Verifikasi Pimpinan
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Menunggu Verifikasi Rektor'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus', 'Menunggu Verifikasi Rektor')">
                                                Menunggu Verifikasi Rektor
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Menunggu Verifikasi SDM Universitas'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus', 'Menunggu Verifikasi SDM Universitas')">
                                                Menunggu Verifikasi SDM Universitas
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Menunggu Verifikasi SDM Yayasan'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus', 'Menunggu Verifikasi SDM Yayasan')">
                                                Menunggu Verifikasi SDM Yayasan
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Disetujui'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus', 'Disetujui')">
                                                Disetujui
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="selected='Ditolak'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus', 'Ditolak')">
                                                Ditolak
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
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
                                                <span class="truncate">Tanggal Pengajuan</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-65">
                                            <div class="flex justify-center gap-2">
                                                <span class="truncate items-center">Tanggal Mulai & Tanggal Selesai</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex justify-center gap-2">
                                                <span class="truncate items-center">Jenis Cuti</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Jumlah Hari Cuti</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Sisa Saldo Cuti</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Status</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Aksi</span>
                                            </div>
                                        </th>
                                        
                                    </tr>
                                </thead>
                                
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($cutiList as $cuti)
                                        <tr class="table-row hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $cuti->pegawai->nama }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->pegawai->nip }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->created_at->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">
                                                {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('d F Y') }}
                                                @if($cuti->tanggal_selesai)
                                                    - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('d F Y') }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->jenisCuti->nama }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->jumlah_hari_cuti }} hari</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $this->displaySaldoCutiSesudah($cuti) ?? 0 }} hari</td>
                                            <td class="px-4 py-4 text-center">
                                                @php
                                                    $statusClass = 'bg-gray-100 text-gray-700';
                                                    if (in_array($cuti->status, ['Disetujui'])) {
                                                        $statusClass = 'bg-green-100 text-green-700';
                                                    } elseif (str_contains($cuti->status, 'Menunggu Verifikasi')) {
                                                        $statusClass = 'bg-yellow-100 text-yellow-700';
                                                    } elseif ($cuti->status === 'Ditolak') {
                                                        $statusClass = 'bg-red-100 text-red-700';
                                                    }
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $cuti->status)) }}</span>
                                            </td>
                                            <td class="flex px-4 py-4 items-center justify-center gap-2">
                                                @if($this->canApprove($cuti))
                                                    <button wire:click="openApprovalConfirmation('approve', {{ $cuti->id }})" 
                                                        class="flex px-3 py-2 text-white bg-green-500 hover:bg-green-100 hover:text-green-500 rounded-md transition-colors cursor-pointer">
                                                        <i class="fa-solid fa-check mr-1"></i>
                                                        <span class="text-xs">
                                                            Setujui
                                                        </span>
                                                    </button>
                                                    <button wire:click="openApprovalConfirmation('reject', {{ $cuti->id }})" 
                                                        class="flex px-3 py-2 text-white bg-red-500 hover:bg-red-100 hover:text-red-500 rounded-md transition-colors cursor-pointer">
                                                        <i class="fa-solid fa-x mr-1"></i>
                                                        <span class="text-xs">
                                                            Tolak
                                                        </span>
                                                    </button>
                                                    <button  type="button" wire:click="$dispatch('openDetailModal', { cutiId: {{ $cuti->id }} })" 
                                                        class="flex px-3 py-2 text-white bg-indigo-500 hover:bg-indigo-100 hover:text-indigo-500 rounded-md transition-colors cursor-pointer">
                                                        <i class="fa-solid fa-eye mr-1"></i>
                                                        <span class="text-xs">
                                                            Detail
                                                        </span>
                                                    </button>
                                                @else
                                                    <button  type="button" wire:click="$dispatch('openDetailModal', { cutiId: {{ $cuti->id }} })" 
                                                        class="flex px-3 py-2 text-white bg-indigo-500 hover:bg-indigo-100 hover:text-indigo-500 rounded-md transition-colors cursor-pointer">
                                                        <i class="fa-solid fa-eye mr-1"></i>
                                                        <span class="text-xs">
                                                            Detail
                                                        </span>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-4 py-4 text-center text-gray-500">Tidak ada data cuti yang sesuai filter.</td>
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
                                        <span class="font-semibold text-heading">{{ $cutiList->firstItem() }}-{{ $cutiList->lastItem() }}</span> dari
                                        <span class="font-semibold text-heading">{{ $cutiList->total() }} Cuti</span>
                                    </span>

                                    <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                                        <li>
                                            <button
                                                wire:click="gotoPage(1)"
                                                @disabled($cutiList->onFirstPage())
                                                class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                <i class="fa-solid fa-angles-left text-xs"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                wire:click="previousPage"
                                                @disabled($cutiList->onFirstPage())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Previous
                                            </button>
                                        </li>
                                        @for ($i = max(1, $cutiList->currentPage() - 3);
                                            $i <= min($cutiList->lastPage(), $cutiList->currentPage() + 3);
                                            $i++)
                                            <li>
                                                <button
                                                    wire:click="gotoPage({{ $i }})"
                                                    class="w-9 {{ $cutiList->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                                    {{ $i }}
                                                </button>
                                            </li>
                                        @endfor
                                        <li>
                                            <button
                                                wire:click="nextPage"
                                                @disabled(!$cutiList->hasMorePages())
                                                class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                                Next
                                            </button>
                                        </li>
                                        <button
                                            wire:click="gotoPage({{ $cutiList->lastPage() }})"
                                            @disabled($cutiList->onLastPage())
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

                {{-- TAB 2: Rekapitulasi Cuti - All allowed roles --}}
                @if(in_array('rekapitulasi', $allowedTabs))
                <div x-show="activeTab === 'rekapitulasi'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col space-y-4 h-full min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-start w-full md:w-auto">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRekapStartDate" class="filter-dropdown border border-gray-200">
                            </div>
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRekapEndDate" class="filter-dropdown border border-gray-200">
                            </div>
                        </div>
                        
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
                                                <span class="truncate">Jumlah Cuti Digunakan</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex justify-between gap-2 breaks-all">
                                                <span class="truncate items-center">Sisa Saldo Cuti</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($rekapList as $item)
                                        <tr class="table-row hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $item->pegawai->nama }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $item->pegawai->nip }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $item->jumlah_cuti }} hari</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $item->sisa_saldo ?? 0 }}  hari</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data rekap cuti.</td>
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
                                        <span class="font-semibold text-heading">{{ $rekapList->total() }} Cuti</span>
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
                
                @if($confirmAction)
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                        <div class="w-full max-w-md bg-white rounded-[16px] shadow-2xl border border-gray-200">
                            <div class="px-6 py-5 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-gray-800">{{ $confirmTitle }}</h3>
                                <p class="mt-2 text-sm text-gray-600">{{ $confirmMessage }}</p>
                                @error('approval') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="px-6 py-4 flex justify-end gap-3">
                                <button wire:click="closeApprovalConfirmation" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-[10px] hover:bg-gray-50 transition cursor-pointer">
                                    Batal
                                </button>
                                <button wire:click="confirmApproval" class="px-4 py-2 text-sm font-medium text-white rounded-[10px] transition cursor-pointer {{ $confirmAction === 'approve' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-red-600 hover:bg-red-700' }}">
                                    Ya, {{ $confirmAction === 'approve' ? 'Setujui' : 'Tolak' }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                
            </div>
</div>
