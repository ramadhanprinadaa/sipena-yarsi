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
                <div x-show="activeTab === 'riwayat'" class="flex flex-col space-y-4 h-full min-h-0">

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

                        <!-- Button -->
                        <button wire:click="exportRiwayatExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
                            <!-- Icon -->
                            <i class="fa-solid fa-download mr-2"></i> Export Excel
                        </button>

                    </div>

                    <!-- Table -->
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-[#F5F7FA]/50 border border-gray-200 rounded-[20px] shadow-sm">
                        <div class="flex-1 overflow-y-auto no-scrollbar">
                            <table class="min-w-full text-sm">
                                <!-- Header -->
                                <thead class="bg-[#F5F7FA] text-gray-600 text-xs uppercase tracking-wider sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Pengajuan</th>
                                        <th class="px-4 py-3 text-center font-semibold">Tanggal Mulai</th>
                                        <th class="px-4 py-3 text-center font-semibold">Tanggal Selesai</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jenis Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jumlah Hari Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Sisa Saldo Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                                        <th class="px-4 py-3 text-center font-semibold">Keterangan</th>
                                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($cutiList as $cuti)
                                        <tr class="hover:bg-[#F5F7FA]/50 transition">
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
                                                @if($cuti->status !== 'disetujui')
                                                    <button wire:click="$dispatch('openModalEdit', { id: {{ $cuti->id }} })" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                                        Edit
                                                    </button>
                                                    <button wire:click="$dispatch('openModalDelete', { id: {{ $cuti->id }} })" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition cursor-pointer">
                                                        Hapus
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
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
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            {{ count($cutiList) }} data
                        </div>
                    </div>
                </div>

                {{-- TAB 2: Rekapitulasi Cuti --}}
                <div x-show="activeTab === 'rekapitulasi'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter  -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-end">
                            
                            <div class="flex flex-wrap gap-3">
                                <input type="date" wire:model.live="filterRekapStartDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <input type="date" wire:model.live="filterRekapEndDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>

                            <div class="relative w-48">
                                <div class="w-48 h-10 px-3 text-sm bg-white border border-gray-200 rounded-[10px] flex items-center text-gray-500">
                                    {{ $filterRekapStartDate || $filterRekapEndDate ? 'Periode aktif' : 'Semua periode' }}
                                </div>
                            </div>
                        </div>

                        <!-- Button -->
                        <button wire:click="exportRekapExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
                            <!-- Icon -->
                            <i class="fa-solid fa-download mr-2"></i> Export Excel
                        </button>

                    </div>

                    <!-- Table -->
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-[#F5F7FA]/50 border border-gray-200 rounded-[20px] shadow-sm">
                        <div class="flex-1 overflow-y-auto no-scrollbar">
                            <table class="min-w-full text-sm">
                                <!-- Header -->
                                <thead class="bg-[#F5F7FA] text-gray-600 text-xs uppercase tracking-wider sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Pengajuan</th>
                                        <th class="px-4 py-3 text-center font-semibold">Tanggal Mulai</th>
                                        <th class="px-4 py-3 text-center font-semibold">Tanggal Selesai</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jenis Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jumlah Hari Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Sisa Saldo Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                                        <th class="px-4 py-3 text-center font-semibold">Keterangan</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($rekapList as $cuti)
                                        <tr class="hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ \Carbon\Carbon::parse($cuti->tanggal_pengajuan)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->jenisCuti->nama ?? '-' }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->jenisCuti?->dihitung_per_jam ? (($cuti->jumlah_jam ?? 0) . ' Jam') : (($cuti->jumlah_hari_cuti ?? 0) . ' Hari') }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->saldo_cuti_sesudah ?? '-' }} Hari</td>
                                            <td class="px-4 py-4 text-center">
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Disetujui</span>
                                            </td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->keterangan }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            {{ count($rekapList) }} data
                        </div>
                    </div>
                </div>      
            </div>

@section('formCards')
    <div x-data="{ 
    }" class="grid grid-cols-1 gap-4 max-w-full">
        
        {{-- Form Pengajuan Cuti Card --}}
        <div class="bg-[linear-gradient(135deg,_#8187FF_0%,_#7DB5FF_50%,_#D1A6FF_100%)] rounded-[20px] p-8 flex flex-col min-h-24 gap-8 shadow-lg hover:shadow-xl transition">
            <h2 class="text-[45px] font-bold bg-[linear-gradient(90deg,_#FFA58E_0%,_#DBFFEE_50%,_#70FFEE_100%)] bg-clip-text text-transparent mb-2 max-w-[360px]">Form Pengajuan Cuti</h2>
            <button 
                @click="$dispatch('open-modal-add')"
                class="flex items-center justify-between gap-3 px-6 py-3 border-2 border-white/75 rounded-lg text-white font-semibold hover:bg-white/20 cursor-pointer transition duration-300">
                <span>Ajukan Cuti</span>
                <!-- Arrow Gradient -->
                <svg width="120" height="20" viewBox="0 0 120 20" fill="none">
                    <defs>
                        <linearGradient id="gradArrow" x1="0" y1="0" x2="120" y2="0" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#59FFE3"/>
                            <stop offset="44%" stop-color="#C3FFF5"/>
                        </linearGradient>
                    </defs>
                    <!-- Line -->
                    <line x1="0" y1="10" x2="100" y2="10" stroke="url(#gradArrow)" stroke-width="3" stroke-linecap="round"/>
                    <!-- Arrow Head -->
                    <path d="M95 3 L105 10 L95 17" stroke="url(#gradArrow)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>            
            </button>
        </div>

        
    </div>
@endsection

@section('CardsCuti')

    {{-- Saldo Cuti Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                {{-- Card 1: Sisa Saldo Cuti --}}
                <div class="bg-gradient-to-br from-blue-400 via-blue-500 to-indigo-600 rounded-[20px] p-6 shadow-lg">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-blue-100 text-sm font-medium mb-1">Sisa Saldo Cuti</p>
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
                            <h3 class="text-4xl font-bold text-white">{{ ($rekapSummary['cuti_terpakai'] + $rekapSummary['cuti_terpakai_besar'] + $rekapSummary['cuti_terpakai_melahirkan']) }}</h3>
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
