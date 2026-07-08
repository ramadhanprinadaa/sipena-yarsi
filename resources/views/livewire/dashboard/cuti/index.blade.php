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

                {{-- TAB 1: Riwayat Cuti --}}
                <div x-show="activeTab === 'riwayat'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter  -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-end">

                            <!-- Date Filter  -->
                            <div class="flex flex-wrap gap-3">
                                <div class="relative w-48">
                                    <input type="date" wire:model.live="filterRiwayatDate" class="filter-dropdown border border-gray-200">
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
                                            <button @click="selected='Menunggu Verifikasi Pimpinan'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Menunggu Verifikasi Pimpinan')">
                                                Menunggu Verifikasi Pimpinan
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
                                            <button @click="selected='Disetujui'; open=false" class="dropdown-item" wire:click="$set('filterRiwayatStatus','Disetujui')">
                                                Disetujui
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

                        </div>

                        <!-- Button  -->
                        <div class="flex items-center gap-3">

                            <!-- Ajukan Cuti Button -->
                            <button @click="$dispatch('open-modal-add')" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 hover:shadow-xl text-white text-sm font-semibold rounded-[10px] transition duration-300 ease-in-out transform hover:scale-105">
                                <!-- Icon -->
                                <i class="fa-solid fa-add mr-2"></i> Ajukan Cuti
                            </button>

                            <!-- Export Button -->
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
                                                <span class="truncate">Tanggal Pengajuan</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Tanggal Mulai</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Tanggal Selesai</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Jenis Cuti</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Jumlah Hari</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Sisa Saldo Cuti</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-65">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Status</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-50">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Keterangan</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-55">
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
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ \Carbon\Carbon::parse($cuti->tanggal_pengajuan)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->jenisCuti->nama ?? '-' }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">
                                                {{ $cuti->jenisCuti?->dihitung_per_jam ? (($cuti->jumlah_jam ?? 0) . ' Jam') : (($cuti->jumlah_hari_cuti ?? 0) . ' Hari') }}
                                            </td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $this->displaySaldoCutiSesudah($cuti) }} Hari</td>
                                            <td class="px-4 py-4 text-center">
                                                <span class="px-3 py-1 {{ $this->statusBadgeClass($cuti->status) }} rounded-full text-xs font-semibold">{{ $this->statusLabel($cuti->status) }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->keterangan }}</td>
                                            <td class="flex px-4 py-4 items-center justify-center gap-2">
                                                @if(!in_array($cuti->status, ['disetujui', 'ditolak']))
                                                    <button wire:click="$dispatch('openModalEdit', { id: {{ $cuti->id }} })" class="flex px-3 py-2 text-white bg-amber-500 hover:bg-amber-100 hover:text-amber-500 rounded-md transition-colors cursor-pointer">
                                                        <i class="fa-solid fa-pen-to-square mr-1"></i>
                                                        <span class="text-xs">
                                                            Edit
                                                        </span>
                                                    </button>
                                                    <button wire:click="$dispatch('openModalDelete', { id: {{ $cuti->id }} })" class="flex px-3 py-2 text-white bg-red-500 hover:bg-red-100 hover:text-red-500 rounded-md transition-colors cursor-pointer">
                                                        <i class="fa-solid fa-trash-can mr-1"></i>
                                                        <span class="text-xs">
                                                            Hapus
                                                        </span>
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-400">Tidak ada aksi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">No data available</td>
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
                                        <span class="font-semibold text-heading">{{ $cutiList->firstItem() ?? 0 }}-{{ $cutiList->lastItem() ?? 0 }}</span> dari
                                        <span class="font-semibold text-heading">{{ $cutiList->total() ?? 0 }} Cuti</span>
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

                {{-- TAB 2: Rekapitulasi Cuti --}}
                <div x-show="activeTab === 'rekapitulasi'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Export -->
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

                        <!-- Button -->
                        <button wire:click="exportRekapExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 hover:shadow-xl text-white text-sm font-semibold rounded-[10px] transition duration-300 ease-in-out transform hover:scale-105">
                            <!-- Icon -->
                            <i class="fa-solid fa-download mr-2"></i> Export Excel
                        </button>

                    </div>

                    <!-- Data Cards List -->
                    <div class="flex-1 min-h-0 overflow-y-auto no-scrollbar space-y-3">
                        @forelse($rekapList as $cuti)
                            <div class="relative group bg-gradient-to-r from-white via-indigo-50/30 to-white border-2 border-indigo-100 hover:border-indigo-400 rounded-[16px] p-5 shadow-md hover:shadow-xl transition duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/0 via-indigo-500/5 to-indigo-500/0 opacity-0 group-hover:opacity-100 transition duration-300 pointer-events-none"></div>
                                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-3.5 text-white flex-shrink-0 shadow-lg group-hover:scale-110 transition duration-300">
                                            <i class="fa-solid {{ $cuti->jenisCuti?->dihitung_per_jam ? 'fa-clock' : 'fa-plane-departure' }} text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('d M Y') }}</span>
                                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-wider rounded-md">{{ $cuti->jenisCuti->nama ?? '-' }}</span>
                                            </div>
                                            <div class="mt-1 flex items-center gap-4 text-xs font-medium text-gray-500">
                                                <span><i class="fa-solid fa-file-signature mr-1.5 text-indigo-400"></i>Diajukan: {{ \Carbon\Carbon::parse($cuti->tanggal_pengajuan)->translatedFormat('d M Y') }}</span>
                                                <span><i class="fa-solid fa-chart-line mr-1.5 text-emerald-400"></i>Sisa Saldo: {{ $this->displaySaldoCutiSesudah($cuti) ?? '-' }} Hari</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right hidden md:block">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">Durasi Cuti</p>
                                            <p class="text-xl font-black text-indigo-600">{{ $cuti->jenisCuti?->dihitung_per_jam ? (($cuti->jumlah_jam ?? 0) . ' Jam') : (($cuti->jumlah_hari_cuti ?? 0) . ' Hari') }}</p>
                                        </div>
                                        <div class="h-10 w-[1px] bg-gray-200 hidden md:block mx-2"></div>
                                        <span class="px-4 py-2 {{ $cuti->status === 'ditolak' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200' }} rounded-full text-xs font-bold shadow-sm border">
                                            <i class="fa-solid {{ $cuti->status === 'ditolak' ? 'fa-circle-xmark' : 'fa-circle-check' }} mr-1.5"></i>{{ $this->statusLabel($cuti->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4 pl-16">
                                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 group-hover:bg-white transition duration-300">
                                        <p class="text-sm text-gray-600 italic"><i class="fa-solid fa-quote-left mr-2 text-indigo-300"></i>{{ $cuti->keterangan }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-[16px] p-12 text-center">
                                <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-folder-open text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 font-bold">Tidak ada data rekapitulasi cuti dalam periode ini</p>
                                <p class="text-gray-400 text-xs mt-1">Coba ubah filter tanggal untuk melihat data lainnya</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

@section('CardsCuti')

    {{-- Saldo Cuti Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Card 1: Sisa Saldo Cuti --}}
                <div class="bg-gradient-to-br from-blue-400 via-blue-500 to-indigo-600 rounded-[20px] p-6 shadow-lg">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-blue-100 text-sm font-medium mb-1">{{$rekapSummary['label_saldo_cuti']}}</p>
                            <h3 class="text-4xl font-bold text-white">{{ $rekapSummary['sisa_saldo'] }}</h3>
                            <p class="text-blue-200 text-xs mt-1">Hari</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fa-solid fa-calendar-check text-2xl text-white"></i>
                        </div>
                    </div>
                    <div class="border-t border-white/20 pt-4 mt-4">
                        <p class="text-blue-100 text-xs">Berakhir pada <span class="font-semibold">31 Desember {{ now()->year }}</span></p>
                    </div>
                </div>

                {{-- Card 2: Saldo Per Jenis Cuti --}}
                <div class="bg-gradient-to-br from-purple-400 via-pink-400 to-red-400 rounded-[20px] p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-white text-sm font-bold">Ringkasan Cuti</p>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fa-solid fa-chart-pie text-lg text-white"></i>
                        </div>
                    </div>
                    <div class="space-y-3">

                        {{-- Cuti Tahunan --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-white text-xs font-medium">Cuti Tahunan</p>
                                <span class="text-white text-xs font-bold">{{ $rekapSummary['cuti_terpakai_tahunan'] }}/12 Hari</span>
                            </div>
                            <div class="w-full bg-white/30 rounded-full h-2">
                                <div class="bg-blue-400 h-2 rounded-full" style="<?php echo 'width: ' . e($this->cutiTahunanProgress()) . '%'; ?>"></div>
                            </div>
                        </div>

                        {{-- Cuti Besar --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-white text-xs font-medium">Cuti Besar</p>
                                <span class="text-white text-xs font-bold">{{ $rekapSummary['cuti_terpakai_besar'] }}/{{ $cutiBesarQuota }} Hari</span>
                            </div>
                            <div class="w-full bg-white/30 rounded-full h-2">
                                <div class="bg-red-400 h-2 rounded-full" style="<?php echo 'width: ' . e($this->cutiBesarProgress()) . '%'; ?>"></div>
                            </div>
                        </div>

                        {{-- Cuti Melahirkan --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-white text-xs font-medium">Cuti Melahirkan</p>
                                <span class="text-white text-xs font-bold">{{ $rekapSummary['cuti_terpakai_melahirkan'] }}/90 Hari</span>
                            </div>
                            <div class="w-full bg-white/30 rounded-full h-2">
                                <div class="bg-pink-400 h-2 rounded-full" style="<?php echo 'width: ' . e($this->cutiMelahirkanProgress()) . '%'; ?>"></div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Card 3: Cuti Terpakai --}}
                <div class="bg-gradient-to-br from-green-400 via-emerald-500 to-teal-600 rounded-[20px] p-6 shadow-lg">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-green-100 text-sm font-medium mb-1">Cuti Terpakai</p>
                            <h3 class="text-4xl font-bold text-white">{{ $rekapSummary['cuti_terpakai'] }}</h3>
                            <p class="text-green-200 text-xs mt-1">Hari</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fa-solid fa-person-hiking text-2xl text-white"></i>
                        </div>
                    </div>
                    <div class="border-t border-white/20 pt-4 mt-4">
                        <p class="text-green-100 text-xs"><span class="font-semibold">Perhatikan Cuti Anda!</span></p>
                    </div>
                </div>

            </div>
@endsection
