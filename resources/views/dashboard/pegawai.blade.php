@extends('layouts.app')

@section('title', 'SIPENA | Detail Pegawai')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-400 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>

        <a href="{{ route('kepegawaian') }}" class="text-indigo-600 hover:text-indigo-500">
            Kepegawaian
        </a>

        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>

        <span class="text-gray-600">Detail Pegawai</span>
    </div>
@endsection

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

                    <div class="flex items-center gap-5">

                        <div class="w-24 h-24 rounded-2xl bg-white/20 flex items-center justify-center text-white text-4xl">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div class="text-white">

                            <h1 class="text-3xl font-bold">
                                Dr. Ahmad Ramadhan, S.Kom., M.Kom
                            </h1>

                            <div class="mt-2 flex flex-wrap items-center gap-3 text-sm">

                                <span class="bg-white/20 px-3 py-1 rounded-full">
                                    NIP: 198912312023011001
                                </span>

                                <span class="bg-emerald-500 px-3 py-1 rounded-full">
                                    Pegawai Tetap
                                </span>

                                <span class="bg-yellow-400 text-gray-800 px-3 py-1 rounded-full">
                                    Aktif
                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="flex gap-3">

                        <button
                            class="text-white bg-white/20 hover:bg-white/30 transition px-5 py-2.5 rounded-xl">
                            <i class="fa-solid fa-print mr-2"></i>
                            Cetak
                        </button>

                        <button
                            class="text-indigo-600 bg-white hover:bg-gray-100 transition px-5 py-2.5 rounded-xl font-medium">
                            <i class="fa-solid fa-pen-to-square mr-2"></i>
                            Edit
                        </button>

                    </div>

                </div>
            </div>

        </div>


        {{-- GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- INFORMASI PRIBADI --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">

                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-id-card text-indigo-600"></i>
                            Informasi Pribadi
                        </h2>
                    </div>

                    <div class="p-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div>
                                <label class="text-sm text-gray-500">Nama Lengkap</label>
                                <p class="font-medium text-gray-800">
                                    Dr. Ahmad Ramadhan, S.Kom., M.Kom
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">Jenis Kelamin</label>
                                <p class="font-medium text-gray-800">
                                    Laki-Laki
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">Tempat Lahir</label>
                                <p class="font-medium text-gray-800">
                                    Jakarta
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">Tanggal Lahir</label>
                                <p class="font-medium text-gray-800">
                                    31 Desember 1989
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">KTP</label>
                                <p class="font-medium text-gray-800">
                                    3201010101010001
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">NPWP</label>
                                <p class="font-medium text-gray-800">
                                    09.888.777.6-123.000
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">No Telepon</label>
                                <p class="font-medium text-gray-800">
                                    081234567890
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">Email YARSI</label>
                                <p class="font-medium text-gray-800">
                                    ahmad.ramadhan@yarsi.ac.id
                                </p>
                            </div>

                        </div>

                    </div>

                </div>


                {{-- INFORMASI KEPEGAWAIAN --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">

                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-briefcase text-indigo-600"></i>
                            Informasi Kepegawaian
                        </h2>
                    </div>

                    <div class="p-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div>
                                <label class="text-sm text-gray-500">Unit Kerja</label>
                                <p class="font-medium text-gray-800">
                                    Fakultas Teknologi Informasi
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">Jenis Pegawai</label>
                                <p class="font-medium text-gray-800">
                                    Pegawai Tetap
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">Status Pegawai</label>
                                <p class="font-medium text-gray-800">
                                    Aktif
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">Tanggal Bergabung</label>
                                <p class="font-medium text-gray-800">
                                    01 Januari 2023
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">Tanggal Habis Kontrak</label>
                                <p class="font-medium text-gray-800">
                                    -
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-500">Tanggal Pensiun</label>
                                <p class="font-medium text-gray-800">
                                    31 Desember 2045
                                </p>
                            </div>

                        </div>

                    </div>

                </div>


                {{-- ALAMAT --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">

                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-indigo-600"></i>
                            Informasi Alamat
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">

                        <div>
                            <label class="text-sm text-gray-500">Alamat KTP</label>

                            <div class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-4 text-gray-700">
                                Jl. Melati Indah No. 88, Jakarta Selatan, DKI Jakarta
                            </div>
                        </div>

                        <div>
                            <label class="text-sm text-gray-500">Alamat Domisili</label>

                            <div class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-4 text-gray-700">
                                Jl. Mawar Residence Blok A2 No. 10, Depok, Jawa Barat
                            </div>
                        </div>

                    </div>

                </div>


                {{-- DATA KELUARGA --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">

                    <div class="border-b border-gray-100 px-6 py-4 flex justify-between items-center">

                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-people-roof text-indigo-600"></i>
                            Data Keluarga
                        </h2>

                        <button
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-xl transition">
                            <i class="fa-solid fa-plus mr-1"></i>
                            Tambah
                        </button>

                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full text-sm text-left text-gray-600">

                            <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                                <tr>
                                    <th class="px-6 py-4">No</th>
                                    <th class="px-6 py-4">Nama</th>
                                    <th class="px-6 py-4">Hubungan</th>
                                    <th class="px-6 py-4">Jenis Kelamin</th>
                                    <th class="px-6 py-4">Tanggal Lahir</th>
                                    <th class="px-6 py-4">Pekerjaan</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        Siti Nurhaliza
                                    </td>
                                    <td class="px-6 py-4">
                                        Istri
                                    </td>
                                    <td class="px-6 py-4">
                                        Perempuan
                                    </td>
                                    <td class="px-6 py-4">
                                        12 Mei 1992
                                    </td>
                                    <td class="px-6 py-4">
                                        Dosen
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2">

                                            <button
                                                class="w-9 h-9 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>

                                            <button
                                                class="w-9 h-9 rounded-lg bg-red-100 text-red-600 hover:bg-red-200">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>


                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-6 py-4">2</td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        Muhammad Raihan
                                    </td>
                                    <td class="px-6 py-4">
                                        Anak
                                    </td>
                                    <td class="px-6 py-4">
                                        Laki-Laki
                                    </td>
                                    <td class="px-6 py-4">
                                        20 Februari 2018
                                    </td>
                                    <td class="px-6 py-4">
                                        Pelajar
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2">

                                            <button
                                                class="w-9 h-9 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>

                                            <button
                                                class="w-9 h-9 rounded-lg bg-red-100 text-red-600 hover:bg-red-200">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- RIGHT --}}
            <div class="space-y-6">

                {{-- STATUS --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">

                    <h2 class="text-lg font-semibold text-gray-800 mb-5">
                        Status Pegawai
                    </h2>

                    <div class="space-y-4">

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">
                                Status
                            </span>

                            <span
                                class="bg-emerald-100 text-emerald-700 text-sm px-3 py-1 rounded-full font-medium">
                                Aktif
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">
                                Jenis
                            </span>

                            <span
                                class="bg-indigo-100 text-indigo-700 text-sm px-3 py-1 rounded-full font-medium">
                                Tetap
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">
                                Masa Kerja
                            </span>

                            <span class="font-medium text-gray-800">
                                3 Tahun
                            </span>
                        </div>

                    </div>

                </div>


                {{-- QUICK INFO --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">

                    <h2 class="text-lg font-semibold text-gray-800 mb-5">
                        Informasi Tambahan
                    </h2>

                    <div class="space-y-5">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <i class="fa-solid fa-envelope"></i>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">
                                    Email
                                </p>

                                <p class="font-medium text-gray-800">
                                    ahmad.ramadhan@yarsi.ac.id
                                </p>
                            </div>

                        </div>


                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                                <i class="fa-solid fa-phone"></i>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">
                                    Telepon
                                </p>

                                <p class="font-medium text-gray-800">
                                    081234567890
                                </p>
                            </div>

                        </div>


                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                                <i class="fa-solid fa-calendar"></i>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">
                                    Bergabung
                                </p>

                                <p class="font-medium text-gray-800">
                                    01 Januari 2023
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection