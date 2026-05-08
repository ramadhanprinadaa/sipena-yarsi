@extends('layouts.app')

@section('title', 'SIPENA | Detail Pegawai')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
    <span>Beranda</span>
    <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
    <a href="{{ route('kepegawaian') }}" class="text-indigo-600 hover:text-indigo-500">Kepegawaian</a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Profil Header Card -->
    <div class="bg-white shadow-lg rounded-2xl p-6 flex flex-col md:flex-row items-center md:items-start gap-6 relative overflow-hidden">
        <!-- Dekorasi Top Bar -->
        <div class="absolute top-0 left-0 w-full h-2 bg-blue-900"></div>

        <div class="w-24 h-24 rounded-full bg-blue-50 text-blue-900 border-4 border-white shadow-md flex items-center justify-center text-4xl shrink-0">
            <i class="fa-solid fa-user-tie"></i>
        </div>

        <div class="flex-1 text-center md:text-left">
            <!-- Gelar Depan + Nama + Gelar Belakang -->
            <h1 class="text-2xl font-bold text-gray-900">Dr. Budi Santoso, S.Kom., M.T.</h1>
            <p class="text-blue-900 font-semibold mt-1">
                <i class="fa-solid fa-id-badge mr-2"></i>NIP: 198501152010121002
            </p>
            <div class="mt-3 flex flex-wrap justify-center md:justify-start gap-2">
                <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full border border-green-200">
                    <i class="fa-solid fa-circle-check mr-1"></i> Aktif
                </span>
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full border border-blue-200">
                    <i class="fa-solid fa-user-shield mr-1"></i> Pegawai Tetap
                </span>
                <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-3 py-1 rounded-full border border-purple-200">
                    <i class="fa-solid fa-building mr-1"></i> Fakultas Teknologi Informasi
                </span>
            </div>
        </div>

        <div class="mt-4 md:mt-0 flex gap-2">
            <button type="button" class="text-white bg-blue-900 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none shadow-sm transition-all">
                <i class="fa-solid fa-pen-to-square mr-2"></i>Edit Data
            </button>
        </div>
    </div>

    <!-- Grid Detail Informasi -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        <!-- Kartu Informasi Pribadi -->
        <div class="bg-white shadow-lg rounded-2xl p-6 border-t-4 border-blue-400">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
                <i class="fa-solid fa-address-card text-blue-500 mr-2"></i>Informasi Pribadi
            </h2>
            <ul class="space-y-4 text-sm">
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-regular fa-id-card w-5 text-center"></i> No. KTP</span>
                    <span class="font-medium text-gray-900">3171234567890001</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-money-check w-5 text-center"></i> NPWP</span>
                    <span class="font-medium text-gray-900">98.765.432.1-098.000</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-cake-candles w-5 text-center"></i> Tempat, Tgl Lahir</span>
                    <span class="font-medium text-gray-900">Jakarta, 15 Januari 1985</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-venus-mars w-5 text-center"></i> Jenis Kelamin</span>
                    <span class="font-medium text-gray-900">Laki-laki</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-envelope w-5 text-center"></i> Email Institusi</span>
                    <span class="font-medium text-gray-900">budi.santoso@yarsi.ac.id</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-phone w-5 text-center"></i> No. Telpon</span>
                    <span class="font-medium text-gray-900">0812-3456-7890</span>
                </li>
                <li class="flex flex-col gap-1 pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-house w-5 text-center"></i> Alamat KTP</span>
                    <span class="font-medium text-gray-900 pl-6 leading-relaxed">Jl. Letjen Suprapto No.13, RT.10/RW.5, Cemp. Putih Tim., Kec. Cemp. Putih, Jakarta Pusat 10510</span>
                </li>
                <li class="flex flex-col gap-1 pb-1">
                    <span class="text-gray-500"><i class="fa-solid fa-map-location-dot w-5 text-center"></i> Alamat Domisili</span>
                    <span class="font-medium text-gray-900 pl-6 leading-relaxed">Sama dengan alamat KTP</span>
                </li>
            </ul>
        </div>

        <!-- Kartu Informasi Kepegawaian -->
        <div class="bg-white shadow-lg rounded-2xl p-6 border-t-4 border-indigo-400">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
                <i class="fa-solid fa-briefcase text-indigo-500 mr-2"></i>Informasi Kepegawaian
            </h2>
            <ul class="space-y-4 text-sm">
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-network-wired w-5 text-center"></i> Unit Kerja</span>
                    <span class="font-medium text-gray-900">Fakultas Teknologi Informasi</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-user-tag w-5 text-center"></i> Jenis Pegawai</span>
                    <span class="font-medium text-gray-900">Dosen Tetap</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-star-half-stroke w-5 text-center"></i> Status Kepegawaian</span>
                    <span class="font-medium text-gray-900">Aktif</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-calendar-check w-5 text-center"></i> Tanggal Bergabung</span>
                    <span class="font-medium text-gray-900">01 September 2010</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-file-signature w-5 text-center"></i> Tanggal Habis Kontrak</span>
                    <span class="font-medium text-gray-400 italic">Tidak ada (Pegawai Tetap)</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-hourglass-end w-5 text-center"></i> Tanggal Pensiun</span>
                    <span class="font-medium text-gray-900">15 Januari 2045</span>
                </li>
                <li class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <span class="text-gray-500"><i class="fa-solid fa-clock-rotate-left w-5 text-center"></i> Data Dibuat</span>
                    <span class="font-medium text-gray-900">10 Mei 2023</span>
                </li>
                <li class="flex justify-between items-center pb-1">
                    <span class="text-gray-500"><i class="fa-solid fa-pen w-5 text-center"></i> Terakhir Diubah</span>
                    <span class="font-medium text-gray-900">02 November 2023</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Bagian Tabel Daftar Keluarga -->
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
            <h2 class="text-lg font-bold text-gray-900">
                <i class="fa-solid fa-people-roof text-blue-900 mr-2"></i>Daftar Keluarga
            </h2>
            <button type="button" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none flex items-center shadow-sm transition-all">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Anggota
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-sm sm:rounded-lg border border-gray-200">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-10 text-center font-bold">No</th>
                        <th scope="col" class="px-6 py-4 font-bold">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-4 font-bold">Hubungan</th>
                        <th scope="col" class="px-6 py-4 font-bold">Jenis Kelamin</th>
                        <th scope="col" class="px-6 py-4 font-bold">Tempat, Tanggal Lahir</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-center">1</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">Siti Aminah, S.E.</td>
                        <td class="px-6 py-4">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded">Istri</span>
                        </td>
                        <td class="px-6 py-4">Perempuan</td>
                        <td class="px-6 py-4">Bandung, 12 Mei 1988</td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-center">2</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">Ahmad Fathan Santoso</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Anak</span>
                        </td>
                        <td class="px-6 py-4">Laki-laki</td>
                        <td class="px-6 py-4">Jakarta, 05 Agustus 2015</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection