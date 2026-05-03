@extends('layouts.app')

@section('title', 'SIPENA | Cuti')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('cuti') }}" class="text-indigo-400">Cuti</a>
    </div>
@endsection

@section('content')
    <div class="flex flex-col h-full min-h-0">

        <div class="flex justify-between mb-8">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold">Cuti</h1>
                <p  class="text-sm text-gray-800">Selamat datang di halaman cuti. Di sini Anda dapat melihat dan mengelola informasi cuti Anda.</p>
            </div>
        </div>

        <div class="flex-1 flex flex-col gap-4 min-h-0">

            {{-- Tabs Navigation --}}
            @php
                $userRole = auth()->user()->role->name ?? null;
                $allowedTabs = [];
                $defaultTab = 'riwayat';

                switch($userRole) {
                    case 'Staff':
                        $allowedTabs = ['riwayat'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'Tendik':
                        $allowedTabs = ['riwayat'];
                        $defaultTab = 'riwayat';
                        break;
                    case 'Dosen':
                        $allowedTabs = ['riwayat'];
                        $defaultTab = 'riwayat';
                        break;
                }
            @endphp

            <div x-data="{
                activeTab: '{{ $defaultTab }}',
                showModalEdit: false,
                showModalDelete: false,
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

                            <div class="relative w-48">
                                    <select class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
                                        <option value="">Status Pengajuan</option>
                                        <option value="pending">Disetujui</option>
                                        <option value="approved">Ditolak</option>
                                        <option value="rejected">Menunggu Persetujuan</option>
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
                                    <tr class="hover:bg-[#F5F7FA]/50 transition">
                                        <td class="px-4 py-4 font-medium text-gray-700">31 Oktober 2026</td>
                                        <td class="px-4 py-4 text-center text-gray-600">7 Novemeber 2026</td>
                                        <td class="px-4 py-4 text-center text-gray-600">15 November 2026</td>
                                        <td class="px-4 py-4 text-center text-gray-600">Cuti Tahunan</td>
                                        <td class="px-4 py-4 text-center text-gray-600">7 Hari</td>
                                        <td class="px-4 py-4 text-center text-gray-600">12 Hari</td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Disetujui</span>
                                        </td>
                                        <td class="px-4 py-4 text-center text-gray-600">Holiday</td>
                                        <td class="px-4 py-4 text-center">
                                            <button @click="showModalEdit = true" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                                Edit
                                            </button>
                                            <button @click="showModalDelete = true" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition cursor-pointer">
                                                Hapus
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
                @endif

                {{-- MODAL DELETE CUTI --}}
                <div
                    x-show="showModalDelete"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
                >

                    <!-- Modal Box -->
                    <div
                        @click.away="showModalDelete = false"
                        x-transition.scale
                        class="w-full max-w-xl bg-white/90 rounded-2xl shadow-2xl shadow-red-500/15 overflow-hidden"
                    >

                        <!-- HEADER -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h2 class="text-md font-bold text-red-600">
                                Konfirmasi Hapus
                            </h2>
                            <button @click="showModalDelete = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>

                        <!-- CONTENT -->
                        <div class="px-6 py-12 text-center">

                            <!-- Icon Warning -->
                            <div class="mx-auto mb-10 flex items-center justify-center w-20 h-20 rounded-full bg-red-100">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-10 h-10 text-red-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67
                                        1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77
                                        1.33.19 3 1.73 3z"/>
                                </svg>
                            </div>

                            <!-- Text -->
                            <p class="text-md text-gray-700 leading-relaxed">
                                Apakah Anda yakin ingin menghapus data cuti ini?
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                Tindakan ini tidak dapat dibatalkan.
                            </p>

                        </div>

                        <!-- FOOTER -->
                        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">

                            <!-- Cancel -->
                            <button
                                @click="showModalDelete = false"
                                class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 cursor-pointer rounded-lg transition"
                            >
                                Batal
                            </button>

                            <!-- Delete -->
                            <button
                                @click="handleDelete()"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 cursor-pointer rounded-lg shadow-sm transition"
                            >
                                Hapus
                            </button>

                        </div>

                    </div>
                </div>

                {{-- MODAL EDIT CUTI --}}
                <div
                    x-show="showModalEdit"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
                >
                    <!-- Modal Box -->
                    <div
                        @click.away="showModalEdit = false"
                        x-transition.scale
                        class="w-full max-w-2xl max-h-[85vh] bg-white rounded-[20px] shadow-2xl border border-gray-200 flex flex-col"
                    >
                        <!-- Header -->
                        <div class="flex justify-between items-center px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-[#F5F7FA] to-white rounded-t-[20px] flex-shrink-0">
                            <h2 class="text-lg font-bold text-[#2B76FF]">
                                Edit Cuti
                            </h2>
                            <button @click="showModalEdit = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>

                        <!-- Content Scrollable -->
                        <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                            <div class="p-6 space-y-4">

                                {{-- Row: Tanggal Mulai & Selesai --}}
                                <div class="grid grid-cols-3 gap-4">
                                    <!-- Jenis Cuti -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Cuti</label>
                                        <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            <option>Cuti Tahunan</option>
                                            <option>Cuti Sakit</option>
                                            <option>Cuti Melahirkan</option>
                                        </select>
                                    </div>
                                    <!-- Tanggal Mulai -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                                        <input type="date"
                                            value="2026-11-07"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                    </div>

                                    <!-- Tanggal Selesai-->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                                        <input type="date"
                                            value="2026-11-07"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                    </div>
                                </div>

                                <!-- Upload Dokumen -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Dokumen</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-[#2B76FF] hover:bg-[#2B76FF]/5 transition cursor-pointer">
                                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2 block"></i>
                                        <p class="text-sm text-gray-500">Klik atau drag file kesini</p>
                                    </div>
                                </div>

                                <!-- Kegiatan -->
                               <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Cuti</label>
                                    <textarea
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition h-24 resize-none scrollbar-thin scrollbar-thumb-[#2B76FF]/40 scrollbar-track-gray-50">Melahirkan</textarea>
                                </div>

                            </div>
                        </div>

                        <!-- Footer Button -->
                        <div class="px-6 py-4 bg-white border-t border-gray-200 rounded-b-[20px] flex-shrink-0">
                            <button
                                class="w-full py-3 rounded-lg text-white font-semibold
                                    bg-gradient-to-r from-[#2B76FF] via-[#7B61FF] to-[#FF00CC]
                                    hover:shadow-lg cursor-pointer transition duration-300">
                                Edit Cuti
                            </button>
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
        showModalLaporan: false
    }" class="grid grid-cols-1 gap-4 max-w-full">

        {{-- Form Pengajuan Cuti Card --}}
        <div class="bg-[linear-gradient(135deg,_#8187FF_0%,_#7DB5FF_50%,_#D1A6FF_100%)] rounded-[20px] p-8 flex flex-col min-h-24 gap-8 shadow-lg hover:shadow-xl transition">
            <h2 class="text-[45px] font-bold bg-[linear-gradient(90deg,_#FFA58E_0%,_#DBFFEE_50%,_#70FFEE_100%)] bg-clip-text text-transparent mb-2 max-w-[360px]">Form Pengajuan Cuti</h2>
            <button
                @click="showModalPengajuan = true"
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

        {{-- MODAL: Form Pengajuan Cuti --}}
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
                        Form Pengajuan Cuti
                    </h2>
                    <button @click="showModalPengajuan = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Content Scrollable -->
                <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                    <div class="p-6 space-y-4">



                        {{-- Row: Tanggal Mulai & Selesai --}}
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Jenis Cuti -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Cuti</label>
                                <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                    <option>Cuti Tahunan</option>
                                    <option>Cuti Sakit</option>
                                    <option>Cuti Melahirkan</option>
                                </select>
                            </div>
                            <!-- Tanggal Mulai -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                                <input type="date"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                            </div>

                            <!-- Tanggal Selesai-->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                                <input type="date"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Upload Dokumen -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Dokumen</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-[#2B76FF] hover:bg-[#2B76FF]/5 transition cursor-pointer">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2 block"></i>
                                <p class="text-sm text-gray-500">Klik atau drag file kesini</p>
                            </div>
                        </div>

                        <!-- Kegiatan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Cuti</label>
                            <textarea placeholder="Jelaskan alasan cuti Anda secara singkat..."
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
                        Ajukan Cuti
                    </button>
                </div>
            </div>
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
                            <h3 class="text-4xl font-bold text-white">12</h3>
                            <p class="text-blue-200 text-xs mt-1">Hari</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fa-solid fa-calendar-check text-2xl text-white"></i>
                        </div>
                    </div>
                    <div class="border-t border-white/20 pt-4 mt-4">
                        <p class="text-blue-100 text-xs">Berakhir pada <span class="font-semibold">31 Oktober 2026</span></p>
                    </div>
                </div>

                {{-- Card 2: Saldo Per Jenis Cuti --}}
                <div class="bg-gradient-to-br from-purple-400 via-pink-400 to-red-400 rounded-[20px] p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-white text-sm font-bold">Saldo Perjenis Cuti</p>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fa-solid fa-chart-pie text-lg text-white"></i>
                        </div>
                    </div>
                    <div class="space-y-3">

                        {{-- Cuti Tahunan --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-white text-xs font-medium">Cuti Tahunan</p>
                                <span class="text-white text-xs font-bold">10/12 Hari</span>
                            </div>
                            <div class="w-full bg-white/30 rounded-full h-2">
                                <div class="bg-blue-400 h-2 rounded-full" style="width: 83%"></div>
                            </div>
                        </div>

                        {{-- Cuti Besar --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-white text-xs font-medium">Cuti Besar</p>
                                <span class="text-white text-xs font-bold">2/6 Hari</span>
                            </div>
                            <div class="w-full bg-white/30 rounded-full h-2">
                                <div class="bg-red-400 h-2 rounded-full" style="width: 33%"></div>
                            </div>
                        </div>

                        {{-- Cuti Melahirkan --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-white text-xs font-medium">Cuti Melahirkan</p>
                                <span class="text-white text-xs font-bold">30/90 Hari</span>
                            </div>
                            <div class="w-full bg-white/30 rounded-full h-2">
                                <div class="bg-pink-400 h-2 rounded-full" style="width: 33%"></div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Card 3: Cuti Terpakai --}}
                <div class="bg-gradient-to-br from-green-400 via-emerald-500 to-teal-600 rounded-[20px] p-6 shadow-lg">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-green-100 text-sm font-medium mb-1">Cuti Terpakai</p>
                            <h3 class="text-4xl font-bold text-white">7</h3>
                            <p class="text-green-200 text-xs mt-1">Hari</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fa-solid fa-person-hiking text-2xl text-white"></i>
                        </div>
                    </div>
                    <div class="border-t border-white/20 pt-4 mt-4">
                        <p class="text-green-100 text-xs">✓ <span class="font-semibold">Perhatikan Cuti Anda !</span></p>
                    </div>
                </div>

            </div>
@endsection