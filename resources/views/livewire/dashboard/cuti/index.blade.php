            {{-- Tabs Navigation --}}
            <div x-data="{ 
                activeTab: 'riwayat',
                activeButtonWidth: 0,
                activeButtonLeft: 0,
                updateUnderline() {
                    const buttons = {
                        'riwayat': this.$refs.btnRiwayat,
                        'rekapitulasi': this.$refs.btnRekapitulasi,
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
                        <!-- Riwayat Cuti -->
                        <button 
                            x-ref="btnRiwayat"
                            @click="activeTab = 'riwayat'; $nextTick(() => updateUnderline())" 
                            :class="activeTab === 'riwayat' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'" 
                            class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                            <i class="fa-solid fa-history mr-2"></i>Riwayat Cuti
                        </button>
                        <!-- Rekapitulasi -->
                        <button 
                            x-ref="btnRekapitulasi"
                            @click="activeTab = 'rekapitulasi'; $nextTick(() => updateUnderline())" 
                            :class="activeTab === 'rekapitulasi' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'" 
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

                {{-- TAB 1: Riwayat Cuti --}}
                <div x-show="activeTab === 'riwayat'" class="flex flex-col space-y-4 min-h-[65vh]">

                    <!-- Filter  -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-end">

                            <!-- Date Filter  -->
                            <div class="flex flex-wrap gap-3">
                                <div class="relative w-48">
                                    <input type="date" wire:model.live="filterRiwayatDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                </div>
                            </div>

                            <div class="relative w-48">
                                <select wire:model.live="filterRiwayatStatus" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
                                    <option value="">Status Pengajuan</option>
                                    <option value="pending_atasan">Menunggu Pimpinan</option>
                                    <option value="pending_rektor">Menunggu Rektor</option>
                                    <option value="pending_sdm_universitas">Menunggu SDM Universitas</option>
                                    <option value="pending_sdm_yayasan">Menunggu SDM Yayasan</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>

                        </div>

                        <!-- Button  -->
                        <div class="flex items-center gap-3">

                            <!-- Ajukan Cuti Button -->
                            <button @click="$dispatch('open-modal-add')" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-blue-500 hover:bg-blue-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
                                <!-- Icon -->
                                <i class="fa-solid fa-add mr-2"></i> Ajukan Cuti
                            </button>

                            <!-- Export Button -->
                            <button wire:click="exportRiwayatExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
                                <!-- Icon -->
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
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="truncate">Tanggal Mulai</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-between gap-2">
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
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Status</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-4 font-medium w-42">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="truncate">Keterangan</span>
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
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ \Carbon\Carbon::parse($cuti->tanggal_pengajuan)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->jenisCuti->nama ?? '-' }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">
                                                {{ $cuti->jenisCuti?->dihitung_per_jam ? (($cuti->jumlah_jam ?? 0) . ' Jam') : (($cuti->jumlah_hari_cuti ?? 0) . ' Hari') }}
                                            </td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->saldo_cuti_sesudah ?? '-' }} Hari</td>
                                            <td class="px-4 py-4 text-center">
                                                <span class="px-3 py-1 {{ $this->statusBadgeClass($cuti->status) }} rounded-full text-xs font-semibold">{{ $this->statusLabel($cuti->status) }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->keterangan }}</td>
                                            <td class="px-4 py-4 text-center">
                                                @if(!in_array($cuti->status, ['disetujui', 'ditolak']))
                                                    <button wire:click="$dispatch('openModalEdit', { id: {{ $cuti->id }} })" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                                        Edit
                                                    </button>
                                                    <button wire:click="$dispatch('openModalDelete', { id: {{ $cuti->id }} })" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition cursor-pointer">
                                                        Hapus
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
                                        <span class="font-semibold text-heading">{{ $cutiList->firstItem() }}-{{ $cutiList->lastItem() }}</span> dari
                                        <span class="font-semibold text-heading">{{ $cutiList->total() }} Lembur</span>
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
                <div x-show="activeTab === 'rekapitulasi'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Export -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                        <div class="flex flex-col gap-3 md:flex-row md:items-center">
                            <div class="flex flex-wrap gap-3">
                                <input type="date" wire:model.live="filterRekapStartDate" class="w-48 h-10 px-3 text-sm bg-white border-2 border-indigo-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition cursor-pointer hover:border-indigo-400">
                                <input type="date" wire:model.live="filterRekapEndDate" class="w-48 h-10 px-3 text-sm bg-white border-2 border-indigo-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition cursor-pointer hover:border-indigo-400">
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
                                                <span><i class="fa-solid fa-chart-line mr-1.5 text-emerald-400"></i>Sisa Saldo: {{ $cuti->saldo_cuti_sesudah ?? '-' }} Hari</span>
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
                                <div class="bg-blue-400 h-2 rounded-full" style="width: {{ ($rekapSummary['cuti_terpakai_tahunan']) > 0 ? min(100, ($rekapSummary['cuti_terpakai_tahunan'] / 12) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        
                        {{-- Cuti Besar --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-white text-xs font-medium">Cuti Besar</p>
                                <span class="text-white text-xs font-bold">{{ $rekapSummary['cuti_terpakai_besar'] }}/66 Hari</span>
                            </div>
                            <div class="w-full bg-white/30 rounded-full h-2">
                                <div class="bg-red-400 h-2 rounded-full" style="width: {{ ($rekapSummary['cuti_terpakai_besar']) > 0 ? min(100, ($rekapSummary['cuti_terpakai_besar'] / 66) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        {{-- Cuti Melahirkan --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-white text-xs font-medium">Cuti Melahirkan</p>
                                <span class="text-white text-xs font-bold">{{ $rekapSummary['cuti_terpakai_melahirkan'] }}/90 Hari</span>
                            </div>
                            <div class="w-full bg-white/30 rounded-full h-2">
                                <div class="bg-pink-400 h-2 rounded-full" style="width: {{ ($rekapSummary['cuti_terpakai_melahirkan']) > 0 ? min(100, ($rekapSummary['cuti_terpakai_melahirkan'] / 90) * 100) : 0 }}%"></div>
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
