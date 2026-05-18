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
                <div x-show="activeTab === 'spl'" class="flex flex-col space-y-4 h-full min-h-0">

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
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-[#F5F7FA]/50 border border-gray-200 rounded-[20px] shadow-sm">
                        <div class="flex-1 overflow-y-auto no-scrollbar">
                            <table class="min-w-full text-sm">
                                <!-- Header -->
                                <thead class="bg-[#F5F7FA] text-gray-600 text-xs uppercase tracking-wider sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Nomor Surat</th>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Lembur</th>
                                        <th class="px-4 py-3 text-left font-semibold">Kegiatan</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jam Lembur</th>
                                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($spls as $spl)
                                        <tr class="hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $spl->nomor_surat }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($spl->tanggal_lembur)->format('d M Y') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $spl->nama_kegiatan }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $spl->jam_mulai }} - {{ $spl->jam_selesai }}</td>
                                            <td class="px-4 py-4 text-center">
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">{{ $spl->status }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada SPL yang tersedia</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            No data available
                        </div>
                    </div>
                </div>

                {{-- TAB 2: Riwayat Lembur --}}
                <div x-show="activeTab === 'riwayat'" class="flex flex-col space-y-4 h-full min-h-0">

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
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-[#F5F7FA]/50 border border-gray-200 rounded-[20px] shadow-sm">
                        <div class="flex-1 overflow-y-auto no-scrollbar">
                            <table class="min-w-full text-sm">
                                <!-- Header -->
                                <thead class="bg-[#F5F7FA] text-gray-600 text-xs uppercase tracking-wider sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Lembur</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jenis Hari</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jam Mulai</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jam Selesai</th>
                                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                                        <th class="px-4 py-3 text-left font-semibold">Disetujui Oleh</th>
                                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($lemburList as $lembur)
                                        <tr class="hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->jenis_hari }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->jam_mulai }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->jam_selesai }}</td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $this->getStatusLembur($lembur) }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApproverLabel($lembur, 'pengajuan') }}</td>
                                            <td class="px-4 py-4 text-center">
                                                <button type="button" wire:click="showDetail({{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">Tidak ada riwayat lembur</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            No data available
                        </div>
                    </div>
                </div>

                {{-- TAB 3: Laporan Lembur --}}
                <div x-show="activeTab === 'laporan'" class="flex flex-col space-y-4 h-full min-h-0">

                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-[#F5F7FA]/50 border border-gray-200 rounded-[20px] shadow-sm">
                        <div class="flex-1 overflow-y-auto no-scrollbar">
                            <table class="min-w-full text-sm">
                                <thead class="bg-[#F5F7FA] text-gray-600 text-xs uppercase tracking-wider sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Lembur</th>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal & Jam Aktual</th>
                                        <th class="px-4 py-3 text-left font-semibold">Hasil Pekerjaan</th>
                                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                                        <th class="px-4 py-3 text-left font-semibold">Catatan</th>
                                        <th class="px-4 py-3 text-left font-semibold">Disetujui Oleh</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($laporanList as $lembur)
                                        <tr class="hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }} | {{ $lembur->laporanHasilLembur->jam_mulai ?? '-' }} - {{ $lembur->laporanHasilLembur->jam_selesai ?? '-' }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->laporanHasilLembur->hasil_pekerjaan ?? '-' }}</td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $this->getLaporanStatus($lembur) }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApprovalCatatan($lembur, 'laporan') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApproverLabel($lembur, 'laporan') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada laporan lembur</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            No data available
                        </div>
                    </div>
                </div>

                {{-- TAB 4: Rekapitulasi Lembur --}}
                <div x-show="activeTab === 'rekap'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <!-- Icon -->
                                <i class="fa-regular fa-calendar text-[16px] absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <!-- Input -->
                                <input 
                                    type="text" 
                                    id="dateRange"
                                    placeholder="Periode Cut Off"
                                    class="w-full h-10 pl-3 pr-10 text-sm bg-white border border-gray-400 rounded-[10px] 
                                        focus:outline-none focus:ring-2 focus:ring-indigo-400 
                                        placeholder:text-gray-500"
                                >
                            </div>
                        </div>

                        <!-- Export Button -->
                        <button class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
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
                                        <th class="px-4 py-3 text-left font-semibold">Hari (Jumlah)</th>
                                        <th class="px-4 py-3 text-left font-semibold">Total Jam</th>
                                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">5 hari</td>
                                        <td class="px-4 py-4 text-gray-600">15 jam</td>
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Tercatat</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            No data available
                        </div>
                    </div>
                </div>

                <!-- Detail Modal -->
                @include('livewire.dashboard.lembur.detail-lembur')

                    
            </div>
</div>

    
@section('formCards')
    <div  class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-full">
        
        {{-- Form Pengajuan Lembur Card --}}
        <div class="bg-[linear-gradient(135deg,_#8187FF_0%,_#7DB5FF_50%,_#D1A6FF_100%)] rounded-[20px] p-8 flex flex-col justify-center min-h-48 gap-8 shadow-lg hover:shadow-xl transition">
            <h2 class="text-[45px] font-bold bg-[linear-gradient(90deg,_#FFA58E_0%,_#DBFFEE_50%,_#70FFEE_100%)] bg-clip-text text-transparent mb-2 max-w-[420px]">Form Pengajuan Lembur</h2>
            <button 
                @click="$dispatch('open-add-pengajuan-lembur')"
                class="flex items-center justify-between gap-3 px-6 py-3 border-2 border-white/75 rounded-lg text-white font-semibold hover:bg-white/20 cursor-pointer transition duration-300">
                <span>Ajukan Lembur</span>
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

        {{-- Form Laporan Lembur Card --}}
        <div class="bg-[linear-gradient(135deg,_#8187FF_0%,_#7DB5FF_50%,_#D1A6FF_100%)] rounded-[20px] p-8 flex flex-col justify-center min-h-48 gap-8 shadow-lg hover:shadow-xl transition">
            <h2 class="text-[45px] font-bold bg-[linear-gradient(90deg,_#FFFDCE_0%,_#EDA3FF_40%,_#9CFFFA_85%)] bg-clip-text text-transparent mb-2 max-w-[420px]">Form Laporan Lembur</h2>
            <button 
                @click="$dispatch('open-add-laporan-lembur')"
                class="flex items-center justify-between gap-3 px-6 py-3 border-2 border-white/75 rounded-lg text-white font-semibold hover:bg-white/20 cursor-pointer transition duration-300">
                <span>Ajukan Laporan</span>
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
