<div class="flex-1 flex flex-col gap-4 min-h-0">
            {{-- Tabs Navigation --}}
            <div x-data="{ 
                activeTab: 'spl',
                activeButtonWidth: 0,
                activeButtonLeft: 0,
                updateUnderline() {
                    const buttons = {
                        'spl': this.$refs.btnSpl,
                        'riwayat': this.$refs.btnRiwayat,
                        'laporan': this.$refs.btnLaporan,
                        'rekap': this.$refs.btnRekap
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
                        <!-- Surat Perintah Lembur (SPL) -->
                        <button 
                            x-ref="btnSpl"
                            @click="activeTab = 'spl'; $nextTick(() => updateUnderline())" 
                            :class="activeTab === 'spl' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'" 
                            class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                            <i class="fa-solid fa-file-lines mr-2"></i>Surat Perintah Lembur
                        </button>
                        
                        <!-- Riwayat Lembur -->
                        <button 
                            x-ref="btnRiwayat"
                            @click="activeTab = 'riwayat'; $nextTick(() => updateUnderline())" 
                            :class="activeTab === 'riwayat' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'" 
                            class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                            <i class="fa-solid fa-history mr-2"></i>Riwayat Lembur
                        </button>

                        <!-- Laporan Lembur -->
                        <button 
                            x-ref="btnLaporan"
                            @click="activeTab = 'laporan'; $nextTick(() => updateUnderline())" 
                            :class="activeTab === 'laporan' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'" 
                            class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                            <i class="fa-solid fa-clipboard-check mr-2"></i>Laporan Lembur
                        </button>
                        
                        <!-- Rekapitulasi -->
                        <button 
                            x-ref="btnRekap"
                            @click="activeTab = 'rekap'; $nextTick(() => updateUnderline())" 
                            :class="activeTab === 'rekap' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'" 
                            class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                            <i class="fa-solid fa-chart-bar mr-2"></i>Rekapitulasi
                        </button>
                    </div>
                    <!-- Smooth Underline Indicator -->
                    <div 
                        :style="{ left: activeButtonLeft + 'px', width: activeButtonWidth + 'px' }"
                        class="absolute bottom-0 h-0.5 translate-y-0.5 bg-indigo-600 transition-all duration-500 ease-out"
                    ></div>
                </div>

                {{-- TAB 1: Surat Perintah Lembur (SPL) --}}
                <div x-show="activeTab === 'spl'" class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterSplDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
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
                                                <span class="truncate">Nomor Surat</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Tanggal Lembur</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex justify-between gap-2">
                                                <span class="truncate items-center">Nama Kegiatan</span>
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
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Aksi</span>
                                            </div>
                                        </th>
                                        
                                    </tr>
                                </thead>
                                
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($spls as $spl)
                                        <tr class="table-row hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $spl->nomor_surat }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($spl->tanggal_lembur)->format('d M Y') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $spl->nama_kegiatan }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $spl->jam_mulai }} - {{ $spl->jam_selesai }}</td>
                                            <td class="px-4 py-4 text-center">
                                                @php
                                                    $hasLembur = in_array($spl->id, $lembur_items);
                                                    $statusText = $hasLembur ? 'Telah Diajukan' : 'Belum Diajukan';
                                                    $statusBg = $hasLembur ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
                                                @endphp
                                                <span class="px-3 py-1 {{ $statusBg }} rounded-full text-xs font-semibold">{{ $statusText }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                @if(!$hasLembur)
                                                    <button type="button" wire:click="openAjukanModal({{ $spl->id }})" class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition cursor-pointer">
                                                        <i class="fa-solid fa-paper-plane mr-1"></i>Ajukan
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 text-xs">Sudah Diajukan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada SPL yang tersedia</td>
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
                                        <span class="font-semibold text-heading">{{ $spls->total() }} Lembur</span>
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

                {{-- TAB 2: Riwayat Lembur --}}
                <div x-show="activeTab === 'riwayat'" class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRiwayatDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                        </div>

                        <!-- Export Button -->
                        <button wire:click="exportRiwayatExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
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
                                                <span class="truncate">Tanggal Lembur</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Jenis Hari</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Jam Mulai</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Jam Selesai</span>
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
                                    @forelse($lemburList as $lembur)
                                        @php
                                            $tanggalLembur = \Carbon\Carbon::parse($lembur->tanggal_lembur);
                                            $now = \Carbon\Carbon::now();
                                            $canAddLaporan = $now >= $tanggalLembur;
                                            $hasLaporan = $lembur->laporanHasilLembur ? true : false;
                                        @endphp
                                        <tr class="table-row hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->jenis_hari }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->jam_mulai }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->jam_selesai }}</td>
                                            <td class="flex justify-center px-4 py-4">
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $this->getStatusLembur($lembur) }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                @if($canAddLaporan && !$hasLaporan)
                                                    <button type="button" wire:click="openLaporanModal({{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition cursor-pointer">
                                                        <i class="fa-solid fa-file-export mr-1"></i>Laporan
                                                    </button>
                                                @elseif($hasLaporan)
                                                    <button type="button" wire:click="showDetail({{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                                        Detail
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 text-xs">Belum Waktunya</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada riwayat lembur</td>
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

                {{-- TAB 3: Laporan Lembur --}}
                <div x-show="activeTab === 'laporan'" class="flex flex-col space-y-4 min-h-[65vh]">

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
                                        <th scope="col" class="px-4 py-4 font-medium w-40">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Tanggal Lembur</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Jam Aktual</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Hasil Pekerjaan</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Catatan</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Disetujui Oleh</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Status</span>
                                            </div>
                                        </th>
                                        
                                    </tr>
                                </thead>
                                
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($laporanList as $lembur)
                                        <tr class="table-row hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }} | {{ $lembur->laporanHasilLembur->jam_mulai ?? '-' }} - {{ $lembur->laporanHasilLembur->jam_selesai ?? '-' }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->laporanHasilLembur->hasil_pekerjaan ?? '-' }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApprovalCatatan($lembur, 'laporan') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApproverLabel($lembur, 'laporan') }}</td>
                                            <td class="flex justify-center px-4 py-4">
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $this->getLaporanStatus($lembur) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada laporan lembur</td>
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
                                        <span class="font-semibold text-heading">{{ $laporanList->total() }} Laporan Lembur</span>
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

                {{-- TAB 4: Rekapitulasi Lembur --}}
                <div x-show="activeTab === 'rekap'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <input type="date" wire:model.live="filterRekapStartDate" class="w-48 h-10 px-3 text-sm bg-white border-2 border-indigo-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition cursor-pointer hover:border-indigo-400">
                            <input type="date" wire:model.live="filterRekapEndDate" class="w-48 h-10 px-3 text-sm bg-white border-2 border-indigo-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition cursor-pointer hover:border-indigo-400">
                        </div>

                        <!-- Export Button -->
                        <button wire:click="exportRekapExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 hover:shadow-xl text-white text-sm font-semibold rounded-[10px] transition duration-300 ease-in-out transform hover:scale-105">
                            <i class="fa-solid fa-download mr-2"></i> Export Excel
                        </button>
                    </div>

                    <!-- Stats Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Total Hari Card -->
                        <div class="group relative bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-600 rounded-[16px] p-6 shadow-lg hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-sm font-semibold text-white/90">Total Hari</span>
                                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                        <i class="fa-solid fa-calendar-day text-2xl text-white"></i>
                                    </div>
                                </div>
                                <div class="text-4xl font-black text-white">{{ $rekapSummary['hari'] }}</div>
                                <div class="mt-2 text-sm text-white/75 font-medium">hari lembur</div>
                            </div>
                        </div>

                        <!-- Total Jam Card -->
                        <div class="group relative bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600 rounded-[16px] p-6 shadow-lg hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-sm font-semibold text-white/90">Total Jam</span>
                                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                        <i class="fa-solid fa-hourglass-end text-2xl text-white"></i>
                                    </div>
                                </div>
                                <div class="text-4xl font-black text-white">{{ number_format($rekapSummary['total_jam'], 2) }}</div>
                                <div class="mt-2 text-sm text-white/75 font-medium">jam tercatat</div>
                            </div>
                        </div>

                        <!-- Selesai Card -->
                        <div class="group relative bg-gradient-to-br from-emerald-500 via-green-600 to-teal-600 rounded-[16px] p-6 shadow-lg hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-sm font-semibold text-white/90">Selesai</span>
                                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                        <i class="fa-solid fa-circle-check text-2xl text-white"></i>
                                    </div>
                                </div>
                                <div class="text-4xl font-black text-white">{{ $rekapSummary['selesai'] }}</div>
                                <div class="mt-2 text-sm text-white/75 font-medium">pengajuan selesai</div>
                            </div>
                        </div>

                        <!-- Dalam Proses Card -->
                        <div class="group relative bg-gradient-to-br from-amber-500 via-orange-600 to-red-600 rounded-[16px] p-6 shadow-lg hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-sm font-semibold text-white/90">Dalam Proses</span>
                                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                        <i class="fa-solid fa-spinner text-2xl text-white"></i>
                                    </div>
                                </div>
                                <div class="text-4xl font-black text-white">{{ $rekapSummary['menunggu'] }}</div>
                                <div class="mt-2 text-sm text-white/75 font-medium">belum final</div>
                            </div>
                        </div>
                    </div>

                    <!-- List Items -->
                    <div class="flex-1 min-h-0 overflow-y-auto no-scrollbar space-y-3">
                        @forelse($rekapList as $lembur)
                            <div class="relative group bg-gradient-to-r from-white via-indigo-50/50 to-white border-2 border-indigo-200/50 hover:border-indigo-400 rounded-[16px] p-5 shadow-md hover:shadow-xl transition duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/0 via-indigo-500/5 to-indigo-500/0 opacity-0 group-hover:opacity-100 transition duration-300 pointer-events-none"></div>
                                <div class="relative z-10">
                                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                        <div class="flex items-start gap-3">
                                            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg p-3 text-white flex-shrink-0 mt-0.5">
                                                <i class="fa-solid fa-briefcase text-lg"></i>
                                            </div>
                                            <div>
                                                <div class="text-base font-bold text-gray-800">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}</div>
                                                <div class="mt-1 text-sm text-gray-600"><i class="fa-solid fa-sun mr-2 text-amber-500"></i>{{ $lembur->jenis_hari }} | <i class="fa-solid fa-clock mr-2 text-blue-500"></i>{{ $lembur->jam_mulai }} - {{ $lembur->jam_selesai }}</div>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 rounded-full text-xs font-bold shadow-sm">
                                                <i class="fa-solid fa-hourglass-half mr-1"></i>{{ number_format(\Carbon\Carbon::parse($lembur->jam_mulai)->diffInMinutes(\Carbon\Carbon::parse($lembur->jam_selesai)) / 60, 2) }} jam
                                            </span>
                                            <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 rounded-full text-xs font-bold shadow-sm">
                                                <i class="fa-solid fa-check-circle mr-1"></i>{{ $this->getStatusLembur($lembur) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-3 pl-14 text-sm text-gray-700 border-l-2 border-indigo-300 pl-4 italic"><i class="fa-solid fa-quote-left mr-2 text-indigo-400"></i>{{ $lembur->alasan_lembur }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-gradient-to-r from-gray-100 to-gray-50 border-2 border-dashed border-gray-300 rounded-[16px] p-12 text-center">
                                <i class="fa-solid fa-inbox text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500 font-semibold">Tidak ada data rekap lembur</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Detail Modal -->
                @include('livewire.dashboard.lembur.detail-lembur')

                    
            </div>
</div>

    

