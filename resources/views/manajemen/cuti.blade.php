@extends('layouts.app')

@section('title', 'SIPENA | Manajemen Cuti')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Manajemen</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('manajemen-cuti') }}" class="text-indigo-400">Pengajuan Cuti</a>
    </div>
@endsection

@section('content')
    <div class="flex flex-col h-full min-h-0">

        <div class="flex justify-between mb-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold">Manajemen Pengajuan Cuti</h1>
                <p class="text-sm text-gray-800">Kelola pengajuan cuti, surat perintah, dan rekapitulasi</p>
            </div>
        </div>

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
                        $allowedTabs = ['riwayat', 'verifikasi'];
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
                                <i class="fa-solid fa-history mr-2"></i>Riwayat Cuti
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
                                <i class="fa-solid fa-check-double mr-2"></i>Persetujuan & Verifikasi
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
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                         <!-- Search -->
                            <div class="flex items-center w-full md:w-72 border border-gray-200 rounded-[10px] bg-white px-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z"/>
                                </svg>
                                <input type="text" placeholder="Cari Nama atau NIP..." class="w-full h-10 px-2 text-sm outline-none">
                            </div>

                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-end">
                            
                            <!-- Filter -->
                            <div class="flex flex-wrap gap-3">
                                <div class="relative w-48">
                                    <input type="date" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                </div>
                            </div>

                            <div class="relative w-48">
                                    <select class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
                                        <option value="">Jenis Cuti</option>
                                        <option value="pending">Cuti Besar</option>
                                        <option value="approved">Cuti Tahunan</option>
                                        <option value="rejected">Cuti Melahirkan</option>
                                    </select>
                                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                            </div>

                            <div class="relative w-48">
                                    <select class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
                                        <option value="">Semua Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Disetujui</option>
                                        <option value="rejected">Ditolak</option>
                                    </select>
                                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
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
                                        <th class="px-4 py-3 text-left font-semibold">Nama Pegawai</th>
                                        <th class="px-4 py-3 text-center font-semibold">NIP</th>
                                        <th class="px-4 py-3 text-center font-semibold">Tanggal Pengajuan</th>
                                        <th class="px-4 py-3 text-center font-semibold">Tanggal Mulai - Selesai</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jenis Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jumlah Hari Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Sisa Saldo Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">John Doe</td>
                                        <td class="px-4 py-4 text-center text-gray-600">123456789</td>
                                        <td class="px-4 py-4 text-center text-gray-600">30 April 2026</td>
                                        <td class="px-4 py-4 text-center text-gray-600">18:00 - 21:00</td>
                                        <td class="px-4 py-4 text-center text-gray-600">Cuti Tahunan</td>
                                        <td class="px-4 py-4 text-center text-gray-600">3 hari</td>
                                        <td class="px-4 py-4 text-center text-gray-600">12 hari</td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Disetujui</span>
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
                @endif

                {{-- TAB 2: Rekapitulasi Cuti - All allowed roles --}}
                @if(in_array('rekap', $allowedTabs))
                <div x-show="activeTab === 'rekap'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
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
                        
                        <!-- Button -->
                        <button class="flex items-center w-38 h-10 justify-center cursor-pointer bg-green-500 hover:bg-green-600 hover:shadow-lg text-white text-sm rounded-[10px] transition">
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
                                        <th class="px-4 py-3 text-left font-semibold">Nama Pegawai</th>
                                        <th class="px-4 py-3 text-left font-semibold">NIP</th>
                                        <th class="px-4 py-3 text-center font-semibold">Jumlah Cuti Digunakan</th>
                                        <th class="px-4 py-3 text-center font-semibold">Sisa Saldo Cuti</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">John Doe</td>
                                        <td class="px-4 py-4 text-gray-600">123456789</td>
                                        <td class="px-4 py-4 text-center text-gray-600">5 hari</td>
                                        <td class="px-4 py-4 text-center text-gray-600">10 hari</td>
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

                {{-- TAB 3: Persetujuan & Verifikasi Cuti - SDM Yayasan, SDM Universitas, Pimpinan --}}
                @if(in_array('verifikasi', $allowedTabs))
                <div x-show="activeTab === 'verifikasi'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Search -->
                        <div class="flex items-center w-full md:w-72 border border-gray-200 rounded-[10px] bg-white px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                            <input type="text" placeholder="Cari Nama atau NIP..." class="w-full h-10 px-2 text-sm outline-none">
                        </div>

                        <!-- Filter -->
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-end">
                            <!-- Periode Cut Off -->
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
                            <!-- Unit -->
                            <div class="relative w-48">
                                <select class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
                                    <option value="">Semua Unit</option>
                                    <option value="pending">Fakultas</option>
                                    <option value="approved">DPJJ</option>
                                    <option value="rejected">Universitas</option>
                                </select>
                                <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
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
                                        <th class="px-4 pr-4 py-3 text-left font-semibold">Nama Pegawai</th>
                                        <th class="px-8 py-3 text-left font-semibold">NIP</th>
                                        <th class="w-[250px] py-3 text-left font-semibold">Tanggal Pengajuan</th>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Mulai</th>
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal Selesai</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jenis Cuti</th>
                                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 pr-4 py-4 font-medium text-gray-700">John Doe</td>
                                        <td class="px-8 py-4 text-gray-600">123456789</td>
                                        <td class="w-[250px] py-4 text-gray-600">30 April 2026</td>
                                        <td class="px-4 py-4 text-gray-600">3 Mei 2026</td>
                                        <td class="px-4 py-4 text-gray-600">10 Mei 2026</td>
                                        <td class="px-4 py-4 text-gray-600">Cuti Tahunan</td>
                                        <td class="px-4 py-4 text-center">
                                            <button class="px-3 py-1.5 text-xs font-medium text-blue-500 border border-blue-500 rounded-[10px] hover:bg-blue-600/10 transition cursor-pointer">
                                                Setujui
                                            </button>
                                            <button class="px-3 py-1.5 text-xs font-medium text-red-500 border border-red-500 rounded-[10px] hover:bg-red-600/10 transition cursor-pointer">
                                                Tolak
                                            </button>
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
                    <!-- MODAL -->
                    <div 
                        x-show="showModal"
                        x-transition.opacity
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
                    >
                        <!-- Modal Box -->
                        <div 
                            @click.away="showModal = false"
                            x-transition.scale
                            class="w-full max-w-4xl max-h-[80vh] bg-white rounded-[20px] shadow-2xl border border-gray-200 flex flex-col"
                        >
                            <!-- Header -->
                            <div class="flex justify-between items-center px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-[#F5F7FA] to-white rounded-t-[20px] flex-shrink-0">
                                <h2 class="text-2xl font-bold text-[#2B76FF]">
                                    Buat & Terbitkan SPL
                                </h2>
                                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 cursor-pointer transition">
                                    <i class="fa-solid fa-xmark text-xl"></i>
                                </button>
                            </div>

                            <!-- Content Scrollable -->
                            <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                                <div class="p-6 space-y-6">

                                    <!-- INFORMASI SURAT -->
                                    <div>
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Informasi Surat</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Nomor Surat</label>
                                                <input type="text" placeholder="SPL/2023/X/089"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Unit Kerja</label>
                                                <input type="text" placeholder="Engineering"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="block text-xs font-semibold text-gray-700 mb-2">Tanggal Dibuat</label>
                                            <input type="date" 
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                        </div>
                                    </div>

                                    <!-- DETAIL KEGIATAN -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Detail Kegiatan</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-2">Nama Kegiatan</label>
                                            <input type="text" placeholder="Contoh: Menyelesaikan Fitur SIPENA"
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition mb-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-2">Deskripsi Tugas</label>
                                            <textarea placeholder="Tuliskan rincian tugas yang harus diselesaikan..."
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition h-24 resize-none scrollbar-thin scrollbar-thumb-[#2B76FF]/40 scrollbar-track-gray-50"></textarea>
                                        </div>
                                    </div>

                                    <!-- WAKTU LEMBUR -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Waktu Lembur</p>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Tanggal Lembur</label>
                                                <input type="date" 
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jam Mulai</label>
                                                <input type="time" 
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jam Selesai</label>
                                                <input type="time" 
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PILIH PEGAWAI -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Pilih Pegawai</p>
                                        </div>
                                        
                                        <!-- Search Input -->
                                        <div class="mb-4">
                                            <div class="flex items-center border border-gray-300 rounded-lg px-3 bg-white focus-within:ring-2 focus-within:ring-[#2B76FF] focus-within:border-transparent transition">
                                                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                                                <input type="text" placeholder="Cari berdasarkan Nama, NIP, atau Jabatan..." 
                                                    class="w-full py-2.5 px-3 text-sm outline-none bg-white">
                                            </div>
                                        </div>

                                        <!-- Pegawai Table -->
                                        <div class="border border-gray-200 rounded-lg overflow-hidden max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                                            <table class="w-full text-sm">
                                                <!-- Header -->
                                                <thead class="bg-[#F5F7FA] sticky top-0 z-10">
                                                    <tr class="border-b border-gray-200">
                                                        <th class="px-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wider">Nama Pegawai</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wider">NIP</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-700 text-xs uppercase tracking-wider">Jabatan</th>
                                                        <th class="px-4 py-3 text-center font-bold text-gray-700 text-xs uppercase tracking-wider">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <!-- Body -->
                                                <tbody class="divide-y divide-gray-200">
                                                    <tr class="hover:bg-blue-50 transition cursor-pointer">
                                                        <td class="px-4 py-3 font-semibold text-gray-800">Rafly Eryan Azis</td>
                                                        <td class="px-4 py-3 text-gray-600">198804212015031002</td>
                                                        <td class="px-4 py-3 text-gray-600">Dekan</td>
                                                        <td class="px-4 py-3 text-center">
                                                            <button class="p-1.5 text-[#2B76FF] hover:bg-blue-100 rounded-lg transition">
                                                                <i class="fa-solid fa-check text-sm"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr class="hover:bg-blue-50 transition cursor-pointer">
                                                        <td class="px-4 py-3 font-semibold text-gray-800">Rafly Eryan Azis</td>
                                                        <td class="px-4 py-3 text-gray-600">198804212015031002</td>
                                                        <td class="px-4 py-3 text-gray-600">Dekan</td>
                                                        <td class="px-4 py-3 text-center">
                                                            <button class="p-1.5 text-[#2B76FF] hover:bg-blue-100 rounded-lg transition">
                                                                <i class="fa-solid fa-check text-sm"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr class="hover:bg-blue-50 transition cursor-pointer">
                                                        <td class="px-4 py-3 font-semibold text-gray-800">Rafly Eryan Azis</td>
                                                        <td class="px-4 py-3 text-gray-600">198804212015031002</td>
                                                        <td class="px-4 py-3 text-gray-600">Dekan</td>
                                                        <td class="px-4 py-3 text-center">
                                                            <button class="p-1.5 text-[#2B76FF] hover:bg-blue-100 rounded-lg transition">
                                                                <i class="fa-solid fa-check text-sm"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr class="hover:bg-blue-50 transition cursor-pointer">
                                                        <td class="px-4 py-3 font-semibold text-gray-800">Rafly Eryan Azis</td>
                                                        <td class="px-4 py-3 text-gray-600">198804212015031002</td>
                                                        <td class="px-4 py-3 text-gray-600">Dekan</td>
                                                        <td class="px-4 py-3 text-center">
                                                            <button class="p-1.5 text-[#2B76FF] hover:bg-blue-100 rounded-lg transition">
                                                                <i class="fa-solid fa-check text-sm"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Footer Button -->
                            <div class="px-6 py-4 bg-white border-t border-gray-200 rounded-b-[20px] flex-shrink-0">
                                <button 
                                    class="w-full py-3 rounded-lg text-white font-semibold 
                                        bg-gradient-to-r from-[#2B76FF] via-[#7B61FF] to-[#FF00CC]  
                                        hover:shadow-lg cursor-pointer transition duration-300">
                                    Terbitkan SPL
                                </button>
                            </div>
                            
                        </div>
                    </div>
                @endif

                

            </div>
        </div>
    </div>
    @endsection