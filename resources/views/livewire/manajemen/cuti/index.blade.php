<div class="flex-1 flex flex-col gap-4 min-h-0">
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
                        $allowedTabs = ['riwayat', 'rekap'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'SDM Universitas':
                        $allowedTabs = ['riwayat', 'rekap'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'Pimpinan':
                        $allowedTabs = ['riwayat', 'rekap'];
                        $defaultTab = 'riwayat';
                        break;
                    default:
                        $allowedTabs = [];
                        $defaultTab = 'riwayat';
                }
            @endphp
            
            <div x-data="{ 
                activeTab: '{{ $defaultTab }}',
                showModal: false,
                activeButtonWidth: 0,
                activeButtonLeft: 0,
                updateUnderline() {
                    const buttons = {
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
                        {{-- Riwayat Cuti - All allowed roles --}}
                        @if(in_array('riwayat', $allowedTabs))
                            <button 
                                x-ref="btnRiwayat"
                                @click="activeTab = 'riwayat'; $nextTick(() => updateUnderline())" 
                                :class="activeTab === 'riwayat' ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-500'" 
                                class="px-4 py-2 font-medium transition-colors duration-300 cursor-pointer">
                                <i class="fa-solid fa-history mr-2"></i>Riwayat Pengajuan Cuti
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

                {{-- TAB 1: Riwayat Cuti - All allowed roles --}}
                @if(in_array('riwayat', $allowedTabs))
                <div x-show="activeTab === 'riwayat'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Search -->
                    <div class="flex gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-start w-full md:w-auto">

                            <!-- Search -->
                            <div class="flex items-center w-full md:w-60 border border-gray-200 rounded-[10px] bg-white px-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z"/>
                                </svg>
                                <input type="text" wire:model.live="filterRiwayatSearch" placeholder="Cari Nama atau NIP..." class="w-full h-10 px-2 text-sm outline-none focus:ring-0 focus:border-transparent border-0 focus:outline-none focus:shadow-none">
                            </div>

                            <!-- Date Filter -->
                            <div class="flex flex-wrap gap-3">
                                <div class="relative w-48">
                                    <input type="date" wire:model.live="filterRiwayatDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                </div>
                            </div>
                        </div>
                            
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-end w-full md:w-auto">
                             
                            <div class="relative w-48">
                                <select wire:model.live="filterRiwayatJenis" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
                                    <option value="">Jenis Cuti</option>
                                    @foreach($jenisCutiList as $jenis)
                                        <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative w-48">
                                <select wire:model.live="filterRiwayatStatus" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
                                    <option value="">Semua Status</option>
                                    <option value="pending_atasan">Menunggu Pimpinan</option>
                                    <option value="pending_rektor">Menunggu Rektor</option>
                                    <option value="pending_sdm_universitas">Menunggu SDM Universitas</option>
                                    <option value="pending_sdm_yayasan">Menunggu SDM Yayasan</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>

                            <button wire:click="exportRiwayatExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
                                <i class="fa-solid fa-download mr-2"></i> Export Excel
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-[#F5F7FA]/50 border border-gray-200 rounded-[20px] shadow-sm">
                        <div class="flex-1 overflow-y-auto no-scrollbar">
                            <table class="min-w-full text-sm">
                                <!-- Header -->
                                <thead class="bg-[#F5F7FA] text-gray-600 text-xs uppercase tracking-wider sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Nama Pegawai</th>
                                        <th class="px-4 py-3 text-center font-semibold">NIP</th>
                                        <th class="px-4 py-3 text-center font-semibold">Tanggal Pengajuan</th>
                                        <th class="px-4 py-3 text-center font-semibold">Tanggal Mulai - Selesai</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jenis Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jumlah Hari Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Sisa Saldo Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($cutiList as $cuti)
                                        <tr class="hover:bg-[#F5F7FA]/50 transition">
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
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $cuti->sisa_saldo_cuti ?? 0 }} hari</td>
                                            <td class="px-4 py-4 text-center">
                                                @php
                                                    $statusClass = 'bg-gray-100 text-gray-700';
                                                    if (in_array($cuti->status, ['disetujui'])) {
                                                        $statusClass = 'bg-green-100 text-green-700';
                                                    } elseif (str_contains($cuti->status, 'pending')) {
                                                        $statusClass = 'bg-yellow-100 text-yellow-700';
                                                    } elseif ($cuti->status === 'ditolak') {
                                                        $statusClass = 'bg-red-100 text-red-700';
                                                    }
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $cuti->status)) }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                @if($this->canApprove($cuti))
                                                    <button wire:click="openApprovalConfirmation('approve', {{ $cuti->id }})" class="px-3 py-1.5 text-xs font-medium text-blue-600 border border-blue-600 rounded-[10px] hover:bg-blue-600/10 transition mr-1 cursor-pointer">Setujui</button>
                                                    <button wire:click="openApprovalConfirmation('reject', {{ $cuti->id }})" class="px-3 py-1.5 text-xs font-medium text-red-600 border border-red-600 rounded-[10px] hover:bg-red-600/10 transition cursor-pointer">Tolak</button>
                                                    <button  type="button" wire:click="$dispatch('openDetailModal', {{ $cuti->id }})" class="px-3 py-1.5 text-xs font-medium text-pink-500 border border-pink-500 rounded-[10px] hover:bg-pink-600/10 transition cursor-pointer">Detail </button>
                                                @else
                                                    <button  type="button" wire:click="$dispatch('openDetailModal', { cutiId: {{ $cuti->id }} })" class="px-3 py-1.5 text-xs font-medium text-pink-500 border border-pink-500 rounded-[10px] hover:bg-pink-600/10 transition cursor-pointer">Detail </button>
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
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            Menampilkan {{ $cutiList->count() }} pengajuan cuti
                        </div>
                    </div>
                </div>
                @endif

                {{-- TAB 2: Rekapitulasi Cuti - All allowed roles --}}
                @if(in_array('rekap', $allowedTabs))
                <div x-show="activeTab === 'rekap'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-start w-full md:w-auto">
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRekapStartDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRekapEndDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                        </div>
                        
                        <button wire:click="exportRekapExcel" class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
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
                                        <th class="px-4 py-3 text-left font-semibold">Nama Pegawai</th>
                                        <th class="px-4 py-3 text-left font-semibold">NIP</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jumlah Cuti Digunakan</th>
                                        <th class="px-4 py-3 text-center font-semibold">Sisa Saldo Cuti</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($rekapList as $item)
                                        <tr class="hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $item['nama'] }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $item['nip'] }}</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $item['jumlah_cuti'] }} hari</td>
                                            <td class="px-4 py-4 text-center text-gray-600">{{ $item['sisa_saldo_cuti'] ?? 0 }} hari</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data rekap cuti.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            Menampilkan {{ $rekapList->count() }} pegawai dalam rekap.
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

                <livewire:manajemen.cuti.detail-cuti />
            </div>
</div>