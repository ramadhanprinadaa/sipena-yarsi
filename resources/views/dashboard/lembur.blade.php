@extends('layouts.app')

@section('title', 'SIPENA | Lembur')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
        <a href="{{ route('lembur') }}" class="text-indigo-600">Lembur</a>
    </div>
@endsection

@section('content')
    <div class="flex flex-col h-full min-h-0">

        <div class="flex justify-between mb-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold">Lembur</h1>
                <p class="text-sm text-gray-800">Kelola pengajuan lembur, surat perintah, dan rekapitulasi Anda</p>
            </div>
        </div>

        <div class="flex-1 flex flex-col gap-4 min-h-0">
            {{-- Tabs Navigation --}}
            <div x-data="{
                activeTab: 'spl',
                showModalDetail: false,
                activeButtonWidth: 0,
                activeButtonLeft: 0,
                updateUnderline() {
                    const buttons = {
                        'spl': this.$refs.btnSpl,
                        'riwayat': this.$refs.btnRiwayat,
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
                                <input type="date" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
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
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">SPL/001/2026</td>
                                        <td class="px-4 py-4 text-gray-600">30 April 2026</td>
                                        <td class="px-4 py-4 text-gray-600">Proyek Sistem Informasi</td>
                                        <td class="px-4 py-4 text-gray-600">18:00 - 21:00</td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Diterbitkan</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">SPL/002/2026</td>
                                        <td class="px-4 py-4 text-gray-600">28 April 2026</td>
                                        <td class="px-4 py-4 text-gray-600">Dokumentasi Sistem</td>
                                        <td class="px-4 py-4 text-gray-600">19:00 - 22:00</td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Pending</span>
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

                {{-- TAB 2: Riwayat Lembur --}}
                <div x-show="activeTab === 'riwayat'" class="flex flex-col space-y-4 h-full min-h-0">

                    <!-- Filter & Search -->
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

                        <!-- Filter -->
                        <div class="flex flex-wrap gap-3">
                            <div class="relative w-48">
                                <input type="date" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400">
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
                                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jenis Hari</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jam Mulai</th>
                                        <th class="px-4 py-3 text-left font-semibold">Jam Selesai</th>
                                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <!-- Body -->
                                <tbody class="divide-y divide-[#878787]/30">
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">30 April 2026</td>
                                        <td class="px-4 py-4 text-gray-600">Hari Biasa</td>
                                        <td class="px-4 py-4 text-gray-600">18:00</td>
                                        <td class="px-4 py-4 text-gray-600">21:00</td>
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Disetujui</span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button @click="showModalDetail = true" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">28 April 2026</td>
                                        <td class="px-4 py-4 text-gray-600">Hari Minggu</td>
                                        <td class="px-4 py-4 text-gray-600">19:00</td>
                                        <td class="px-4 py-4 text-gray-600">22:00</td>
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Pending</span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button @click="showModalDetail = true" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                                Detail
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

                {{-- TAB 3: Rekapitulasi Lembur --}}
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

                    <!-- DETAIL MODAL -->
                    <div
                        x-show="showModalDetail"
                        x-transition.opacity
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
                    >
                        <!-- Modal Box -->
                        <div
                            @click.away="showModalDetail = false"
                            x-transition.scale
                            class="w-full max-w-4xl max-h-[80vh] bg-white rounded-[20px] shadow-2xl border border-gray-200 flex flex-col"
                        >
                            <!-- Header -->
                            <div class="flex justify-between items-center px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-[#F5F7FA] to-white rounded-t-[20px] flex-shrink-0">
                                <h2 class="text-2xl font-bold text-[#2B76FF]">
                                    Detail Lembur
                                </h2>
                                <button @click="showModalDetail = false" class="text-gray-400 hover:text-gray-600 cursor-pointer transition">
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
                                                <input type="text" readonly value="SPL/2023/X/089"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Unit Kerja</label>
                                                <input type="text" readonly value="Fakultas"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
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
                                                <input type="date" readonly value="2023-10-01"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jam Mulai</label>
                                                <input type="time" readonly value="08:00"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jam Selesai</label>
                                                <input type="time" readonly value="17:00"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
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
                                            <input type="text" readonly value="Menyelesaikan Fitur SIPENA"
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition mb-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-2">Deskripsi Tugas</label>
                                            <textarea readonly
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none transition h-24 resize-none scrollbar-thin scrollbar-thumb-[#2B76FF]/40 scrollbar-track-gray-50">Membuat Design SIPENA Lembur dan Cuti</textarea>
                                        </div>
                                    </div>


                                </div>
                            </div>


                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection

@section('formCards')
    <div x-data="{
        showModalPengajuan: false,
        showModalLaporan: false,
        filePengajuan: null,
        fileLaporan: null,
        dragOverPengajuan: false,
        dragOverLaporan: false,
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },
        getFileIcon(fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            const iconMap = {
                'pdf': 'fa-file-pdf text-red-500',
                'doc': 'fa-file-word text-blue-500',
                'docx': 'fa-file-word text-blue-500',
                'xls': 'fa-file-excel text-green-500',
                'xlsx': 'fa-file-excel text-green-500',
                'ppt': 'fa-file-powerpoint text-orange-500',
                'pptx': 'fa-file-powerpoint text-orange-500',
                'jpg': 'fa-file-image text-purple-500',
                'jpeg': 'fa-file-image text-purple-500',
                'png': 'fa-file-image text-purple-500',
                'gif': 'fa-file-image text-purple-500',
                'zip': 'fa-file-archive text-yellow-600',
                'rar': 'fa-file-archive text-yellow-600',
                'txt': 'fa-file-lines text-gray-500'
            };
            return iconMap[ext] || 'fa-file text-gray-500';
        },
        handleFilePengajuan(e, form = 'pengajuan') {
            e.preventDefault();
            e.stopPropagation();
            if (form === 'pengajuan') this.dragOverPengajuan = false;
            if (form === 'laporan') this.dragOverLaporan = false;

            const files = e.dataTransfer?.files || e.target?.files;
            if (files && files.length > 0) {
                const file = files[0];
                if (form === 'pengajuan') this.filePengajuan = file;
                if (form === 'laporan') this.fileLaporan = file;
            }
        },
        removeFile(form = 'pengajuan') {
            if (form === 'pengajuan') this.filePengajuan = null;
            if (form === 'laporan') this.fileLaporan = null;
        }
    }" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-full">

        {{-- Form Pengajuan Lembur Card --}}
        <div class="bg-[linear-gradient(135deg,_#8187FF_0%,_#7DB5FF_50%,_#D1A6FF_100%)] rounded-[20px] p-8 flex flex-col justify-center min-h-48 gap-8 shadow-lg hover:shadow-xl transition">
            <h2 class="text-[45px] font-bold bg-[linear-gradient(90deg,_#FFA58E_0%,_#DBFFEE_50%,_#70FFEE_100%)] bg-clip-text text-transparent mb-2 max-w-[420px]">Form Pengajuan Lembur</h2>
            <button
                @click="showModalPengajuan = true"
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
                @click="showModalLaporan = true"
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

        {{-- MODAL: Form Pengajuan Lembur --}}
        <div
            x-show="showModalPengajuan"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
        >
            <!-- Modal Box -->
            <div
                @click.away="showModalPengajuan = false"
                x-transition.scale
                class="w-full max-w-2xl max-h-[85vh] bg-white rounded-[20px] shadow-2xl border border-gray-200 flex flex-col"
            >
                <!-- Header -->
                <div class="flex justify-between items-center px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-[#F5F7FA] to-white rounded-t-[20px] flex-shrink-0">
                    <h2 class="text-lg font-bold text-[#2B76FF]">
                        Form Pengajuan Lembur
                    </h2>
                    <button @click="showModalPengajuan = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Content Scrollable -->
                <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                    <div class="p-6 space-y-4">

                        <!-- Surat Perintah Lembur -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Surat Perintah Lembur</label>
                            <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                <option>SPL/2023/X/012 - Akreditasi Prodi</option>
                                <option>SPL/2023/X/013 - Dokumentasi Sistem</option>
                            </select>
                        </div>

                        {{-- Row: Estimasi Jam & Tanggal --}}
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Estimasi Jam -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Estimasi Jam</label>
                                <input type="time" placeholder="17:00 - 19:00"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                                <input type="date"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Upload Dokumen -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Dokumen</label>

                            <!-- Drag & Drop Area (Before File Selected) -->
                            <div x-show="!filePengajuan"
                                @dragover.prevent="dragOverPengajuan = true"
                                @dragleave.prevent="dragOverPengajuan = false"
                                @drop.prevent="handleFilePengajuan($event, 'pengajuan')"
                                :class="dragOverPengajuan ? 'border-[#2B76FF] bg-[#2B76FF]/10 shadow-lg' : 'border-gray-300 hover:border-[#2B76FF] hover:bg-[#2B76FF]/5'"
                                class="border-2 border-dashed rounded-lg p-8 text-center transition cursor-pointer">
                                <input type="file"
                                    class="hidden"
                                    @change="handleFilePengajuan($event, 'pengajuan')"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    x-ref="inputPengajuan">
                                <div @click="$refs.inputPengajuan.click()" class="cursor-pointer">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl mb-2 block transition" :class="dragOverPengajuan ? 'text-[#2B76FF] scale-110' : 'text-gray-400'"></i>
                                    <p class="text-sm font-medium" :class="dragOverPengajuan ? 'text-[#2B76FF]' : 'text-gray-500'">Klik atau drag file kesini</p>
                                </div>
                            </div>

                            <!-- File Selected Display -->
                            <div x-show="filePengajuan" class="border-2 border-green-200 bg-green-50 rounded-lg p-4 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 flex-1">
                                        <!-- File Icon -->
                                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <i class="fa-solid" :class="getFileIcon(filePengajuan?.name || '')"></i>
                                        </div>
                                        <!-- File Info -->
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate" x-text="filePengajuan?.name"></p>
                                            <p class="text-xs text-gray-600" x-text="formatFileSize(filePengajuan?.size || 0)"></p>
                                        </div>
                                    </div>
                                    <!-- Remove Button -->
                                    <button @click="removeFile('pengajuan')"
                                        class="ml-2 p-2 text-red-500 hover:bg-red-100 cursor-pointer rounded-lg transition flex-shrink-0">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </div>
                                <!-- Progress Bar -->
                                <div class="mt-3 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-[#2B76FF] to-[#7B61FF] rounded-full w-full animation-pulse"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Kegiatan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kegiatan</label>
                            <textarea placeholder="Persiapan Dokumen Akreditasi"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition h-24 resize-none scrollbar-thin scrollbar-thumb-[#2B76FF]/40 scrollbar-track-gray-50"></textarea>
                        </div>

                    </div>
                </div>

                <!-- Footer Button -->
                <div class="px-6 py-4 bg-white border-t border-gray-200 rounded-b-[20px] flex-shrink-0">
                    <button
                        class="w-full py-3 rounded-lg text-white font-semibold
                            bg-gradient-to-r from-[#2B76FF] via-[#7B61FF] to-[#FF00CC]
                            hover:shadow-lg cursor-pointer transition duration-300">
                        Ajukan Lembur
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL: Form Laporan Hasil Lembur --}}
        <div
            x-show="showModalLaporan"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
        >
            <!-- Modal Box -->
            <div
                @click.away="showModalLaporan = false"
                x-transition.scale
                class="w-full max-w-2xl max-h-[85vh] bg-white rounded-[20px] shadow-2xl border border-gray-200 flex flex-col"
            >
                <!-- Header -->
                <div class="flex justify-between items-center px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-[#F5F7FA] to-white rounded-t-[20px] flex-shrink-0">
                    <h2 class="text-lg font-bold text-[#2B76FF]">
                        Form Laporan Hasil Lembur
                    </h2>
                    <button @click="showModalLaporan = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Content Scrollable -->
                <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                    <div class="p-6 space-y-4">

                        {{-- Row: Jam Mulai & Jam Selesai --}}
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Jam Mulai -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai</label>
                                <input type="time" placeholder="17:00"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                            </div>

                            <!-- Jam Selesai -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai</label>
                                <input type="time" placeholder="20:00"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Upload Dokumen (Laporan) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Dokumen (Opsional)</label>

                            <!-- Drag & Drop Area (Before File Selected) -->
                            <div x-show="!fileLaporan"
                                @dragover.prevent="dragOverLaporan = true"
                                @dragleave.prevent="dragOverLaporan = false"
                                @drop.prevent="handleFilePengajuan($event, 'laporan')"
                                :class="dragOverLaporan ? 'border-[#2B76FF] bg-[#2B76FF]/10 shadow-lg' : 'border-gray-300 hover:border-[#2B76FF] hover:bg-[#2B76FF]/5'"
                                class="border-2 border-dashed rounded-lg p-6 text-center transition cursor-pointer mb-4">
                                <input type="file"
                                    class="hidden"
                                    @change="handleFilePengajuan($event, 'laporan')"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    x-ref="inputLaporan">
                                <div @click="$refs.inputLaporan.click()" class="cursor-pointer">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl mb-2 block transition" :class="dragOverLaporan ? 'text-[#2B76FF] scale-110' : 'text-gray-400'"></i>
                                    <p class="text-sm font-medium" :class="dragOverLaporan ? 'text-[#2B76FF]' : 'text-gray-500'">Klik atau drag file kesini</p>
                                </div>
                            </div>

                            <!-- File Selected Display -->
                            <div x-show="fileLaporan" class="border-2 border-green-200 bg-green-50 rounded-lg p-4 transition mb-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <i class="fa-solid" :class="getFileIcon(fileLaporan?.name || '')"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate" x-text="fileLaporan?.name"></p>
                                            <p class="text-xs text-gray-600" x-text="formatFileSize(fileLaporan?.size || 0)"></p>
                                        </div>
                                    </div>
                                    <button @click="removeFile('laporan')"
                                        class="ml-2 p-2 text-red-500 hover:bg-red-100 cursor-pointer rounded-lg transition flex-shrink-0">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Hasil Pekerjaan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Hasil Pekerjaan</label>
                            <textarea placeholder="Jelaskan detail pekerjaan yang dilakukan..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition h-40 resize-none scrollbar-thin scrollbar-thumb-[#2B76FF]/40 scrollbar-track-gray-50"></textarea>
                        </div>

                    </div>
                </div>

                <!-- Footer Button -->
                <div class="px-6 py-4 bg-white border-t border-gray-200 rounded-b-[20px] flex-shrink-0">
                    <button
                        class="w-full py-3 rounded-lg text-white font-semibold
                            bg-gradient-to-r from-[#2B76FF] via-[#7B61FF] to-[#FF00CC]
                            hover:shadow-lg cursor-pointer transition duration-300">
                        Kirim Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection