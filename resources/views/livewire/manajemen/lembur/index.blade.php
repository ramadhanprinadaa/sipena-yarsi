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
                        $allowedTabs = ['riwayat', 'rekap', 'verifikasi'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'SDM Universitas':
                        $allowedTabs = ['riwayat', 'verifikasi'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'Pimpinan':
                        $allowedTabs = ['spl', 'riwayat', 'verifikasi'];
                        $defaultTab = 'spl';
                        break;
                    case 'Rektor':
                        $allowedTabs = ['spl', 'riwayat', 'verifikasi'];
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
                    </div>
                    <!-- Smooth Underline Indicator -->
                    <div 
                        :style="{ left: activeButtonLeft + 'px', width: activeButtonWidth + 'px' }"
                        class="absolute bottom-0 h-0.5 translate-y-0.5 bg-indigo-600 transition-all duration-500 ease-out"
                    ></div>
                </div>

                {{-- TAB 1: Surat Perintah Lembur (SPL) - Only Pimpinan --}}
                @if(in_array('spl', $allowedTabs))
                <div x-show="activeTab === 'spl'" class="flex flex-col space-y-4 h-full min-h-0">

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
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-[#F5F7FA]/50 border border-gray-200 rounded-[20px] shadow-sm">
                        <div class="flex-1 overflow-y-auto no-scrollbar">
                            <table class="min-w-full text-sm">
                                <!-- Header -->
                                <thead class="bg-[#F5F7FA] text-gray-600 text-xs uppercase tracking-wider sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Nomor Surat</th>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Lembur</th>
                                        <th class="px-4 py-3 text-left font-semibold">Kegiatan</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jam Masuk</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jam Keluar</th>
                                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($spls as $spl)
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">{{ $spl->nomor_surat }}</td>
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
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            No data available
                        </div>
                    </div>
                </div>
                @endif

                {{-- TAB 2: Riwayat Lembur - All allowed roles --}}
                @if(in_array('riwayat', $allowedTabs))
                <div x-show="activeTab === 'riwayat'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <select wire:model.live="filterRiwayatStatus" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
                                    <option value="">Semua Status</option>
                                    <option value="Menunggu Verifikasi Atasan">Menunggu Verifikasi Atasan</option>
                                    <option value="Menunggu Pelaksanaan">Menunggu Pelaksanaan</option>
                                    <option value="Menunggu Laporan">Menunggu Laporan</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            </div>

                            <div class="relative w-48">
                                <input type="date" wire:model.live="filterRiwayatDate" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="flex items-center w-full md:w-72 border border-gray-200 rounded-[10px] bg-white px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                            <input type="text" wire:model.live="filterRiwayatSearch" placeholder="Cari Nama atau NIP..." class="w-full h-10 px-2 text-sm outline-none focus:ring-0 focus:border-transparent border-0 focus:outline-none focus:shadow-none">
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
                                        <th class="px-4 py-3 text-left font-semibold">NIP</th>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Lembur</th>
                                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                                        <th class="px-4 py-3 text-left font-semibold">Disetujui Oleh</th>
                                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($lemburList as $lembur)
                                        <tr class="hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $lembur->pegawai->nama ?? '-' }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->pegawai->nip ?? '-' }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}</td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">{{ $this->getStatusLembur($lembur) }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApproverLabel($lembur, 'pengajuan') }}</td>
                                            <td class="px-4 py-4 text-center">
                                                @if($lembur->status === 'Menunggu Verifikasi Atasan' && !$lembur->laporanHasilLembur)
                                                    <button wire:click="openApprovalConfirmation('pengajuan', 'approve', {{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-blue-500 border border-blue-500 rounded-[10px] hover:bg-blue-600/10 transition cursor-pointer">
                                                        Setujui
                                                    </button>
                                                    <button wire:click="openApprovalConfirmation('pengajuan', 'reject', {{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-red-500 border border-red-500 rounded-[10px] hover:bg-red-600/10 transition cursor-pointer">
                                                        Tolak
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-400">Tidak ada aksi</span>
                                                @endif
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
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            No data available
                        </div>
                    </div>
                </div>
                @endif

                {{-- TAB 3: Rekapitulasi Lembur - Admin & SDM Yayasan --}}
                @if(in_array('rekap', $allowedTabs))
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

                        <!-- Button -->
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
                                        <th class="px-4 py-3 text-left font-semibold">Nama Pegawai</th>
                                        <th class="px-4 py-3 text-left font-semibold">NIP</th>
                                        <th class="px-4 py-3 text-left font-semibold">Hari (Jumlah)</th>
                                        <th class="px-4 py-3 text-left font-semibold">Total Jam</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">John Doe</td>
                                        <td class="px-4 py-4 text-gray-600">123456789</td>
                                        <td class="px-4 py-4 text-gray-600">5 hari</td>
                                        <td class="px-4 py-4 text-gray-600">15 jam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 text-sm">
                            No data available
                        </div>
                    </div>
                </div>
                @endif

                {{-- TAB 4: Persetujuan & Verifikasi Laporan Lembur - SDM Yayasan, SDM Universitas, Pimpinan --}}
                @if(in_array('verifikasi', $allowedTabs))
                <div x-show="activeTab === 'verifikasi'" class="flex flex-col space-y-4 h-full min-h-0">

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
                    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-[#F5F7FA]/50 border border-gray-200 rounded-[20px] shadow-sm">
                        <div class="flex-1 overflow-y-auto no-scrollbar">
                            <table class="min-w-full text-sm">
                                <!-- Header -->
                                <thead class="bg-[#F5F7FA] text-gray-600 text-xs uppercase tracking-wider sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Nama Pegawai</th>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Lembur</th>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal & Jam Aktual</th>
                                        <th class="px-4 py-3 text-left font-semibold">Hasil Pekerjaan</th>
                                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                                        <th class="px-4 py-3 text-left font-semibold">Catatan</th>
                                        <th class="px-4 py-3 text-left font-semibold">Disetujui Oleh</th>
                                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    @forelse($laporanList as $lembur)
                                        <tr class="hover:bg-[#F5F7FA]/50 transition">
                                            <td class="px-4 py-4 font-medium text-gray-700">{{ $lembur->pegawai->nama ?? '-' }}<br><span class="text-xs text-gray-400">{{ $lembur->pegawai->nip ?? '-' }}</span></td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }} | {{ $lembur->laporanHasilLembur->jam_mulai ?? '-' }} - {{ $lembur->laporanHasilLembur->jam_selesai ?? '-' }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $lembur->laporanHasilLembur->hasil_pekerjaan ?? '-' }}</td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $this->getLaporanStatus($lembur) }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApprovalCatatan($lembur, 'laporan') }}</td>
                                            <td class="px-4 py-4 text-gray-600">{{ $this->getApproverLabel($lembur, 'laporan') }}</td>
                                            <td class="px-4 py-4 text-center">
                                                @if($lembur->status === 'Menunggu Verifikasi Atasan')
                                                    <button wire:click="openApprovalConfirmation('laporan', 'approve', {{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-blue-500 border border-blue-500 rounded-[10px] hover:bg-blue-600/10 transition cursor-pointer">
                                                        Setujui
                                                    </button>
                                                    <button wire:click="openApprovalConfirmation('laporan', 'reject', {{ $lembur->id }})" class="px-3 py-1.5 text-xs font-medium text-red-500 border border-red-500 rounded-[10px] hover:bg-red-600/10 transition cursor-pointer">
                                                        Tolak
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-400">Tidak ada aksi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-4 text-center text-gray-500">Tidak ada data laporan lembur</td>
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

                
                
            </div>
        </div>
