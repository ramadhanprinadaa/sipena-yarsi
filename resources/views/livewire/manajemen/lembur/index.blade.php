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

            <div x-data="{
                activeTab: '{{ $defaultTab }}',
                selectedEmployees: [],
                activeButtonWidth: 0,
                activeButtonLeft: 0,
                isEmployeeSelected(nip) {
                    return this.selectedEmployees.some(emp => emp.nip === nip);
                },
                toggleEmployeeSelection(employee) {
                    const index = this.selectedEmployees.findIndex(emp => emp.nip === employee.nip);
                    if (index > -1) {
                        this.selectedEmployees.splice(index, 1);
                    } else {
                        this.selectedEmployees.push(employee);
                    }
                },
                updateUnderline() {
                    const buttons = {
                        'spl': this.$refs.btnSpl,
                        'riwayat': this.$refs.btnRiwayat,
                        'rekap': this.$refs.btnRekap,
                        'verifikasi': this.$refs.btnVerifikasi
                    };
                    const activeBtn = buttons[this.activeTab];
                    if (activeBtn) {
                        this.activeButtonWidth = activeBtn.offsetWidth;
                        this.activeButtonLeft = activeBtn.offsetLeft;
                    }
                }
            }"
            @load="updateUnderline()"
            class="flex flex-col gap-4 min-h-0">
                <div class="relative border-b border-gray-200">
                    <div class="flex gap-2">
                        {{-- Surat Perintah Lembur (SPL) - Only Pimpinan --}}
                        @if(in_array('spl', $allowedTabs))
                            <button
                                x-ref="btnSpl"
                                @click="activeTab = 'spl'; $nextTick(() => updateUnderline())"
                                :class="activeTab === 'spl' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'"
                                class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                                <i class="fa-solid fa-file-lines mr-2"></i>Surat Perintah Lembur
                            </button>
                        @endif

                        {{-- Riwayat Lembur - All allowed roles --}}
                        @if(in_array('riwayat', $allowedTabs))
                            <button
                                x-ref="btnRiwayat"
                                @click="activeTab = 'riwayat'; $nextTick(() => updateUnderline())"
                                :class="activeTab === 'riwayat' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'"
                                class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                                <i class="fa-solid fa-history mr-2"></i>Riwayat Pengajuan Lembur
                            </button>
                        @endif

                        {{-- Persetujuan & Verifikasi - SDM Yayasan, SDM Universitas, Pimpinan --}}
                        @if(in_array('verifikasi', $allowedTabs))
                            <button
                                x-ref="btnVerifikasi"
                                @click="activeTab = 'verifikasi'; $nextTick(() => updateUnderline())"
                                :class="activeTab === 'verifikasi' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'"
                                class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                                <i class="fa-solid fa-check-double mr-2"></i>Persetujuan & Verifikasi Laporan Lembur
                            </button>
                        @endif

                        {{-- Rekapitulasi - Admin & SDM Yayasan --}}
                        @if(in_array('rekap', $allowedTabs))
                            <button
                                x-ref="btnRekap"
                                @click="activeTab = 'rekap'; $nextTick(() => updateUnderline())"
                                :class="activeTab === 'rekap' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'"
                                class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                                <i class="fa-solid fa-chart-bar mr-2"></i>Rekapitulasi
                            </button>
                        @endif
                    </div>
                    <!-- Smooth Underline Indicator -->
                    <div
                        :style="{ left: activeButtonLeft + 'px', width: activeButtonWidth + 'px' }"
                        class="absolute bottom-0 h-0.5 translate-y-0.5 bg-indigo-600 transition-all duration-500 ease-out"
                    ></div>
                </div>

                {{-- TAB 1: Surat Perintah Lembur (SPL) - Only Pimpinan --}}
                @if(in_array('spl', $allowedTabs))
                <div x-show="activeTab === 'spl'" class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterSplDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                        </div>

                        <!-- Button -->
                        <button wire:click="$dispatch('open-add-spl')" class="flex items-center w-48 h-10 justify-center cursor-pointer bg-[#2B76FF] hover:shadow-lg text-white text-sm rounded-[10px] transition">
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
                                        <th scope="col" class="px-4 py-4 font-medium w-18 text-center">
                                            <span class="truncate">Aksi</span>
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
                                            <button wire:click="$dispatch('open-edit-spl', { id: {{ $spl->id }} })" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                                Edit
                                            </button>
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
                <div x-show="activeTab === 'riwayat'" class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter (Left Side) -->
                        <div class="flex flex-wrap gap-3">
                            <!-- Status Filter -->
                            <div class="relative w-48">
                                <select wire:model.live="filterRiwayatStatus" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
                                    <option value="">Semua Status</option>
                                    <option value="Menunggu Verifikasi Atasan">Menunggu Verifikasi Atasan</option>
                                    <option value="Menunggu Verifikasi Rektor">Menunggu Verifikasi Rektor</option>
                                    <option value="Menunggu Verifikasi SDM Universitas">Menunggu Verifikasi SDM Universitas</option>
                                    <option value="Menunggu Verifikasi SDM Yayasan">Menunggu Verifikasi SDM Yayasan</option>
                                    <option value="Menunggu Pelaksanaan">Menunggu Pelaksanaan</option>
                                    <option value="Menunggu Laporan">Menunggu Laporan</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            </div>

                            <!-- Date Filter -->
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRiwayatDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                        </div>

                        <!-- Search & Button (Right Side) -->
                        <div class="flex flex-wrap gap-3">
                            <!-- Search -->
                            <div class="flex items-center w-full md:w-72 border border-gray-200 rounded-[10px] bg-white px-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z"/>
                                </svg>
                                <input type="text" wire:model.live="filterRiwayatSearch" placeholder="Cari Nama atau NIP..." class="w-full h-10 px-2 text-sm outline-none focus:ring-0 focus:border-transparent border-0 focus:outline-none focus:shadow-none">
                            </div>

                            <!-- Button -->
                            <button wire:click="exportRiwayatExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
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
                <div x-show="activeTab === 'rekap'" class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRekapStartDate" placeholder="Start Date" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRekapEndDate" placeholder="End Date" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                        </div>

                        <!-- Button -->
                        <button wire:click="exportRekapExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
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
                <div x-show="activeTab === 'verifikasi'" class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterLaporanDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="flex items-center w-full md:w-72 border border-gray-200 rounded-[10px] bg-white px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                            <input type="text" wire:model.live="filterLaporanSearch" placeholder="Cari Nama atau NIP..." class="w-full h-10 px-2 text-sm outline-none focus:ring-0 focus:border-transparent border-0 focus:outline-none focus:shadow-none">
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
                                        <th scope="col" class="px-4 py-4 font-medium w-25">
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
                                        <th scope="col" class="px-4 py-4 text-center font-medium w-18">
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
                                            <td class="flex px-4 py-4 items-center justify-center">
                                                @if($this->canApproveLaporan($lembur))
                                                    <button wire:click="openApprovalConfirmation('laporan', 'approve', {{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-blue-500 border border-blue-500 rounded-[10px] hover:bg-blue-600/10 transition cursor-pointer">
                                                        Setujui
                                                    </button>
                                                    <button wire:click="openApprovalConfirmation('laporan', 'reject', {{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-red-500 border border-red-500 rounded-[10px] hover:bg-red-600/10 transition cursor-pointer">
                                                        Tolak
                                                    </button>
                                                @endif
                                                <button wire:click="showDetailLaporan({{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-pink-500 border border-pink-500 rounded-[10px] hover:bg-pink-600/10 transition cursor-pointer">
                                                    Detail
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

                {{-- Detail Laporan Modal --}}
                @if($openDetailLaporan && $selectedLemburDetail)
                    <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"></div>
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="relative w-full max-w-4xl max-h-full bg-white rounded-3xl shadow-2xl overflow-hidden">
                            {{-- Header --}}
                            <div class="relative bg-gradient-to-br from-indigo-600 via-blue-500 to-sky-400 px-8 pt-8 pb-14 overflow-hidden">
                                <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/10"></div>
                                <div class="absolute -bottom-14 left-16 w-56 h-56 rounded-full bg-white/5"></div>
                                <div class="relative z-10 flex items-start justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border border-white/25">
                                            <i class="fa-solid fa-business-time text-white text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold tracking-widest uppercase text-white/70 mb-1">Laporan Lembur</p>
                                            <h2 class="text-2xl font-bold text-white">Detail Laporan Lembur</h2>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="closeDetailLaporan" class="w-10 h-10 rounded-full bg-white/15 border border-white/25 text-white hover:bg-white/25 transition-colors flex items-center justify-center cursor-pointer">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <div class="relative z-10 mt-5 inline-flex items-center gap-2 bg-white/15 border border-white/30 backdrop-blur px-4 py-1.5 rounded-full">
                                    @php
                                        $statusLaporan = $this->getLaporanStatus($selectedLemburDetail);
                                        $dotColor = match(true) {
                                            $statusLaporan === 'Disetujui' => 'bg-emerald-400 shadow-emerald-400/50',
                                            $statusLaporan === 'Ditolak' => 'bg-rose-400 shadow-rose-400/50',
                                            str_contains($statusLaporan, 'Menunggu') => 'bg-amber-400 shadow-amber-400/50',
                                            default => 'bg-slate-300',
                                        };
                                    @endphp
                                    <span class="w-2 h-2 rounded-full shadow-lg {{ $dotColor }}"></span>
                                    <span class="text-sm font-semibold text-white">{{ $statusLaporan }}</span>
                                </div>
                            </div>

                            {{-- Stats Strip --}}
                            <div class="mx-8 -mt-8 grid grid-cols-3 bg-white rounded-2xl shadow-xl border border-indigo-100 overflow-hidden z-20 relative">
                                <div class="flex items-center gap-3 px-5 py-4 border-r border-slate-100">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-calendar-day text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal</p>
                                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ \Carbon\Carbon::parse($selectedLemburDetail->tanggal_lembur)->translatedFormat('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 px-5 py-4 border-r border-slate-100">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-clock text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jam Aktual</p>
                                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ $selectedLemburDetail->laporanHasilLembur->jam_mulai ?? '-' }} - {{ $selectedLemburDetail->laporanHasilLembur->jam_selesai ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 px-5 py-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-hourglass-half text-emerald-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jenis Hari</p>
                                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ $selectedLemburDetail->jenis_hari }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="grid lg:grid-cols-2 gap-5 px-8 pt-5 pb-8 overflow-y-auto max-h-[60vh]">
                                <div class="flex flex-col space-y-4 h-full">
                                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Informasi Pegawai & Laporan</p>
                                    <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200 rounded-2xl p-4">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">{{ strtoupper(substr($selectedLemburDetail->pegawai->nama ?? 'P', 0, 1)) }}</div>
                                        <div>
                                            <p class="text-base font-bold text-slate-900">{{ $selectedLemburDetail->pegawai->nama ?? '-' }}</p>
                                            <p class="text-xs font-semibold text-indigo-600 mt-0.5">{{ $selectedLemburDetail->pegawai->nip ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 col-span-2">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Kegiatan (SPL)</p>
                                            <p class="text-sm font-bold text-slate-800">{{ $selectedLemburDetail->suratPerintahLembur->nama_kegiatan ?? '-' }}</p>
                                        </div>
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 col-span-2">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Hasil Pekerjaan</p>
                                            <p class="text-sm font-semibold text-slate-800">{{ $selectedLemburDetail->laporanHasilLembur->hasil_pekerjaan ?? '-' }}</p>
                                        </div>
                                    </div>

                                    {{-- Approved By --}}
                                    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-xl p-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-user-check text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Disetujui Oleh</p>
                                            <p class="text-sm font-bold text-emerald-800">
                                                {{ $selectedLemburDetail->laporanHasilLembur->persetujuan->sortByDesc('approved_at')->first()?->approver?->pegawai?->nama ?? '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mt-2">Riwayat Persetujuan Laporan</p>
                                    @if($selectedLemburDetail->laporanHasilLembur->persetujuan->count())
                                        <div class="space-y-2">
                                            @foreach($selectedLemburDetail->laporanHasilLembur->persetujuan->sortByDesc('approved_at') as $approval)
                                                <div class="flex items-start gap-3 {{ $approval->status === 'Disetujui' ? 'bg-emerald-50 border-emerald-200' : ($approval->status === 'Ditolak' ? 'bg-rose-50 border-rose-200' : 'bg-blue-50 border-blue-200') }} border rounded-xl px-4 py-2">
                                                    <div class="w-9 h-9 rounded-xl {{ $approval->status === 'Disetujui' ? 'bg-emerald-500' : ($approval->status === 'Ditolak' ? 'bg-rose-500' : 'bg-blue-500') }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        <i class="fa-solid {{ $approval->status === 'Disetujui' ? 'fa-check' : ($approval->status === 'Ditolak' ? 'fa-xmark' : 'fa-clock') }} text-white text-sm"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-bold {{ $approval->status === 'Disetujui' ? 'text-emerald-900' : ($approval->status === 'Ditolak' ? 'text-rose-900' : 'text-blue-900') }} truncate">{{ $approval->approver->pegawai->nama ?? $approval->approver->name }}</p>
                                                        <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $approval->role_approval }} · {{ $approval->status }}</p>
                                                        @if($approval->catatan)<p class="text-xs text-slate-600 mt-1 italic">"{{ $approval->catatan }}"</p>@endif
                                                    </div>
                                                    <p class="text-xs text-slate-400 mt-0.5">{{ $approval->approved_at ? \Carbon\Carbon::parse($approval->approved_at)->translatedFormat('d M Y, H:i') : '-' }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex h-full bg-slate-50 border border-dashed border-slate-300 rounded-xl p-4 text-center justify-center items-center">
                                            <p class="text-sm text-slate-500">Belum ada riwayat persetujuan laporan.</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-4 flex flex-col">
                                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">File Laporan</p>
                                    @if($selectedLemburDetail->laporanHasilLembur->file_laporan)
                                        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 border border-amber-200 rounded-2xl p-4 flex-1 flex flex-col gap-4">
                                            <div class="w-full h-full">
                                                @if(Str::endsWith($selectedLemburDetail->laporanHasilLembur->file_laporan, ['.jpg', '.jpeg', '.png']))
                                                    <img src="{{ asset('storage/' . $selectedLemburDetail->laporanHasilLembur->file_laporan) }}" class="w-full h-full object-contain rounded-xl border border-amber-200 bg-white" />
                                                @else
                                                    <iframe src="{{ asset('storage/' . $selectedLemburDetail->laporanHasilLembur->file_laporan) }}" class="w-full h-full rounded-xl border border-amber-200"></iframe>
                                                @endif
                                            </div>
                                            <div class="flex gap-3 h-12">
                                                <a href="{{ asset('storage/' . $selectedLemburDetail->laporanHasilLembur->file_laporan) }}" target="_blank" class="w-full h-full rounded-xl bg-amber-400 hover:bg-amber-500 text-white text-sm font-semibold flex items-center justify-center transition-colors"><i class="fa-solid fa-eye mr-2"></i>
                                                Preview
                                                </a>
                                                <a href="{{ asset('storage/' . $selectedLemburDetail->laporanHasilLembur->file_laporan) }}" download class="w-full h-full rounded-xl bg-amber-400 hover:bg-amber-500 text-white text-sm font-semibold flex items-center justify-center transition-colors"><i class="fa-solid fa-download mr-2"></i>
                                                Unduh
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-slate-50 flex-1 flex flex-col items-center justify-center border border-dashed border-slate-300 rounded-2xl p-6 text-center">
                                            <i class="fa-solid fa-file-slash text-slate-300 text-4xl mb-3"></i>
                                            <p class="text-sm text-slate-500 font-medium">Tidak ada file laporan</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>