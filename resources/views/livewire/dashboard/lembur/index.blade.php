<div class="flex-1 flex flex-col gap-4 min-h-0">

    {{-- ─── Tabs Navigation ─── --}}
    <div x-data="{
            activeTab: 'spl',
            init() { this.$nextTick(() => this.updatePill()) },
            updatePill() {
                const refs = {
                    spl:     this.$refs.btnSpl,
                    riwayat: this.$refs.btnRiwayat,
                    laporan: this.$refs.btnLaporan,
                    rekap:   this.$refs.btnRekap,
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
                x-ref="btnRekap"
                @click="setTab('rekap')"
                :class="activeTab === 'rekap' ? 'text-indigo-700 scale-[1.01]' : 'text-gray-500 hover:text-gray-700 hover:-translate-y-0.5'"
                class="relative z-10 flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 cursor-pointer whitespace-nowrap group"
            >
                <i class="fa-solid fa-chart-bar text-base transition-transform duration-200 group-hover:scale-110"></i>
                <span>Rekapitulasi</span>
            </button>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 1: Surat Perintah Lembur (SPL)                    --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'spl'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="flex flex-col space-y-4 min-h-[65vh]">

            <!-- Filter & Search -->
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">
                <div class="flex flex-wrap gap-3">
                    <div class="relative w-48">
                        <input type="date" wire:model.live="filterSplDate" class="filter-dropdown border border-gray-200">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container relative">
                <div wire:loading>
                    <div class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                        <div role="status"><x-ui.spinner /></div>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Nomor Surat</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Tanggal Lembur</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex justify-between gap-2"><span class="truncate items-center">Nama Kegiatan</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex justify-between gap-2"><span class="truncate items-center">Jam Mulai & Jam Selesai</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-center gap-2"><span class="truncate">Status</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-center gap-2"><span class="truncate">Aksi</span></div></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#878787]/30">
                            @forelse($spls as $spl)
                                <tr class="table-row hover:bg-[#F5F7FA]/50 transition">
                                    <td class="px-4 py-4 font-medium text-gray-700">{{ $spl->nomor_surat }}</td>
                                    <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($spl->tanggal_lembur)->format('d M Y') }}</td>
                                    <td class="px-4 py-4 text-gray-600">{{ $spl->nama_kegiatan }}</td>
                                    <td class="px-4 py-4 text-gray-600">{{ $spl->jam_mulai }} - {{ $spl->jam_selesai }}</td>
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $hasLembur  = in_array($spl->id, $lembur_items);
                                            $statusText = $hasLembur ? 'Telah Diajukan' : 'Belum Diajukan';
                                            $statusBg   = $hasLembur ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
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
                <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">
                    <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                        <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                            Menampilkan <span class="font-semibold text-heading">{{ $spls->firstItem() }}-{{ $spls->lastItem() }}</span> dari <span class="font-semibold text-heading">{{ $spls->total() }} Lembur</span>
                        </span>
                        <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                            <li><button wire:click="gotoPage(1)" @disabled($spls->onFirstPage()) class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"><i class="fa-solid fa-angles-left text-xs"></i></button></li>
                            <li><button wire:click="previousPage" @disabled($spls->onFirstPage()) class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">Previous</button></li>
                            @for ($i = max(1, $spls->currentPage() - 3); $i <= min($spls->lastPage(), $spls->currentPage() + 3); $i++)
                                <li><button wire:click="gotoPage({{ $i }})" class="w-9 {{ $spls->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">{{ $i }}</button></li>
                            @endfor
                            <li><button wire:click="nextPage" @disabled(!$spls->hasMorePages()) class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">Next</button></li>
                            <button wire:click="gotoPage({{ $spls->lastPage() }})" @disabled($spls->onLastPage()) class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"><i class="fa-solid fa-angles-right text-xs"></i></button>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 2: Riwayat Lembur                                 --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'riwayat'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="flex flex-col space-y-4 min-h-[65vh]">

            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">
                <div class="flex flex-wrap gap-3">
                    <div class="relative w-48">
                        <input type="date" wire:model.live="filterRiwayatDate" class="filter-dropdown border border-gray-200">
                    </div>
                </div>
                <button wire:click="exportRiwayatExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 hover:shadow-xl text-white text-sm font-semibold rounded-[10px] transition duration-300 ease-in-out transform hover:scale-105">
                    <i class="fa-solid fa-download mr-2"></i> Export Excel
                </button>
            </div>

            <div class="table-container relative">
                <div wire:loading>
                    <div class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                        <div role="status"><x-ui.spinner /></div>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Tanggal Lembur</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Jenis Hari</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Jam Mulai</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Jam Selesai</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-center gap-2"><span class="truncate">Status</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-center gap-2"><span class="truncate">Aksi</span></div></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#878787]/30">
                            @forelse($lemburList as $lembur)
                                @php
                                    $tanggalLembur = \Carbon\Carbon::parse($lembur->tanggal_lembur);
                                    $now           = \Carbon\Carbon::now();
                                    $canAddLaporan = $now >= $tanggalLembur;
                                    $hasLaporan    = $lembur->laporanHasilLembur ? true : false;
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
                                            <button type="button" wire:click="showDetail({{ $lembur->id }})" class="px-3 py-1.5 text-white bg-indigo-500 hover:bg-indigo-100 hover:text-indigo-500 rounded-md transition-colors cursor-pointer">
                                                <i class="fa-solid fa-eye mr-1"></i>
                                                <span class="text-xs">
                                                    Detail
                                                </span>
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
                <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">
                    <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                        <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                            Menampilkan <span class="font-semibold text-heading">{{ $lemburList->firstItem() }}-{{ $lemburList->lastItem() }}</span> dari <span class="font-semibold text-heading">{{ $lemburList->total() }} Lembur</span>
                        </span>
                        <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                            <li><button wire:click="gotoPage(1)" @disabled($lemburList->onFirstPage()) class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"><i class="fa-solid fa-angles-left text-xs"></i></button></li>
                            <li><button wire:click="previousPage" @disabled($lemburList->onFirstPage()) class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">Previous</button></li>
                            @for ($i = max(1, $lemburList->currentPage() - 3); $i <= min($lemburList->lastPage(), $lemburList->currentPage() + 3); $i++)
                                <li><button wire:click="gotoPage({{ $i }})" class="w-9 {{ $lemburList->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">{{ $i }}</button></li>
                            @endfor
                            <li><button wire:click="nextPage" @disabled(!$lemburList->hasMorePages()) class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">Next</button></li>
                            <button wire:click="gotoPage({{ $lemburList->lastPage() }})" @disabled($lemburList->onLastPage()) class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"><i class="fa-solid fa-angles-right text-xs"></i></button>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 3: Laporan Lembur                                 --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'laporan'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="flex flex-col space-y-4 min-h-[65vh]">

            <div class="table-container relative">
                <div wire:loading>
                    <div class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                        <div role="status"><x-ui.spinner /></div>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="px-4 py-4 font-medium w-40"><div class="flex items-center justify-between gap-2"><span class="truncate">Tanggal Lembur</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Jam Aktual</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Hasil Pekerjaan</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Catatan</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-between gap-2"><span class="truncate">Disetujui Oleh</span></div></th>
                                <th scope="col" class="px-4 py-4 font-medium w-42"><div class="flex items-center justify-center gap-2"><span class="truncate">Status</span></div></th>
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
                <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">
                    <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                        <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                            Menampilkan <span class="font-semibold text-heading">{{ $laporanList->firstItem() }}-{{ $laporanList->lastItem() }}</span> dari <span class="font-semibold text-heading">{{ $laporanList->total() }} Laporan Lembur</span>
                        </span>
                        <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                            <li><button wire:click="gotoPage(1)" @disabled($laporanList->onFirstPage()) class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"><i class="fa-solid fa-angles-left text-xs"></i></button></li>
                            <li><button wire:click="previousPage" @disabled($laporanList->onFirstPage()) class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">Previous</button></li>
                            @for ($i = max(1, $laporanList->currentPage() - 3); $i <= min($laporanList->lastPage(), $laporanList->currentPage() + 3); $i++)
                                <li><button wire:click="gotoPage({{ $i }})" class="w-9 {{ $laporanList->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">{{ $i }}</button></li>
                            @endfor
                            <li><button wire:click="nextPage" @disabled(!$laporanList->hasMorePages()) class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">Next</button></li>
                            <button wire:click="gotoPage({{ $laporanList->lastPage() }})" @disabled($laporanList->onLastPage()) class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"><i class="fa-solid fa-angles-right text-xs"></i></button>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 4: Rekapitulasi Lembur                            --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'rekap'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="flex flex-col space-y-4 h-full min-h-0">

            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="flex flex-row gap-3 md:items-center">
                    <div class="flex flex-wrap gap-3">
                        <div class="relative w-48">
                            <input type="date" wire:model.live="filterRekapStartDate" class="filter-dropdown border border-gray-200">
                        </div>
                        <div class="relative w-48">
                            <input type="date" wire:model.live="filterRekapEndDate" class="filter-dropdown border border-gray-200">
                        </div>
                    </div>
                    <div class="relative w-48">
                        <div class="w-48 h-10 px-3 text-sm bg-indigo-50 border-2 border-indigo-100 rounded-[10px] flex items-center text-indigo-600 font-medium">
                            <i class="fa-solid fa-calendar-check mr-2"></i>
                            {{ $filterRekapStartDate || $filterRekapEndDate ? 'Periode aktif' : 'Semua periode' }}
                        </div>
                    </div>
                </div>
                <button wire:click="exportRekapExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 hover:shadow-xl text-white text-sm font-semibold rounded-[10px] transition duration-300 ease-in-out transform hover:scale-105">
                    <i class="fa-solid fa-download mr-2"></i> Export Excel
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="group relative bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-600 rounded-[16px] p-6 shadow-lg hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-white/90">Total Hari</span>
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl"><i class="fa-solid fa-calendar-day text-2xl text-white"></i></div>
                        </div>
                        <div class="text-4xl font-black text-white">{{ $rekapSummary['hari'] }}</div>
                        <div class="mt-2 text-sm text-white/75 font-medium">hari lembur</div>
                    </div>
                </div>
                <div class="group relative bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600 rounded-[16px] p-6 shadow-lg hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-white/90">Total Jam</span>
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl"><i class="fa-solid fa-hourglass-end text-2xl text-white"></i></div>
                        </div>
                        <div class="text-4xl font-black text-white">{{ number_format($rekapSummary['total_jam'], 2) }}</div>
                        <div class="mt-2 text-sm text-white/75 font-medium">jam tercatat</div>
                    </div>
                </div>
                <div class="group relative bg-gradient-to-br from-emerald-500 via-green-600 to-teal-600 rounded-[16px] p-6 shadow-lg hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-white/90">Selesai</span>
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl"><i class="fa-solid fa-circle-check text-2xl text-white"></i></div>
                        </div>
                        <div class="text-4xl font-black text-white">{{ $rekapSummary['selesai'] }}</div>
                        <div class="mt-2 text-sm text-white/75 font-medium">pengajuan selesai</div>
                    </div>
                </div>
                <div class="group relative bg-gradient-to-br from-amber-500 via-orange-600 to-red-600 rounded-[16px] p-6 shadow-lg hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-white/90">Dalam Proses</span>
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl"><i class="fa-solid fa-spinner text-2xl text-white"></i></div>
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