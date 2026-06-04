<div class="space-y-6">
    <div class="flex items-center justify-between bg-white rounded-md shadow-md border border-indigo-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-md bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i class="fa-solid fa-address-card text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Biodata Pegawai</h2>
                <p class="text-xs text-gray-500">Informasi personal dan identitas kependudukan</p>
            </div>
        </div>
        <button class="px-4 py-2 text-sm font-medium bg-indigo-50 text-indigo-600 rounded-md hover:bg-indigo-600 hover:text-white transition-colors duration-200 shadow-sm cursor-pointer">
            <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit Biodata
        </button>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- 1. KARTU INFORMASI PRIBADI (Tema Biru) --}}
        <div class="bg-white rounded-md shadow-sm border border-gray-100 border-t-4 border-t-blue-500 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3 class="font-semibold text-gray-800 text-lg">Informasi Pribadi</h3>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Nama Lengkap & Gelar</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-user-tag text-blue-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->nama_gelar ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Jenis Kelamin</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-venus-mars text-blue-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->jenis_kelamin ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Tempat Lahir</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-location-dot text-blue-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->tempat_lahir ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Tanggal Lahir & Umur</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-calendar-day text-blue-400 mt-0.5 w-4 text-center"></i>
                        <span>
                            {{ $pegawai->tanggal_lahir_formatted ?: 'N/A' }}
                            <span class="text-gray-400 font-normal">({{ $pegawai->tanggal_lahir ? $pegawai->age . ' Tahun' : 'N/A' }})</span>
                        </span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Email YARSI</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-envelope text-blue-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->email_yarsi ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">No. Telepon / WhatsApp</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-phone text-blue-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->no_telpon ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. KARTU IDENTITAS & ALAMAT (Tema Emerald/Hijau) --}}
        <div class="bg-white rounded-md shadow-sm border border-gray-100 border-t-4 border-t-emerald-500 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-id-card-clip"></i>
                </div>
                <h3 class="font-semibold text-gray-800 text-lg">Identitas & Domisili</h3>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Nomor KTP (NIK)</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-id-card text-emerald-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->ktp ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">NPWP</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-file-invoice-dollar text-emerald-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->npwp ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <span class="block text-xs font-medium text-gray-500 mb-1">Alamat Sesuai KTP</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-map-location-dot text-emerald-400 mt-0.5 w-4 text-center"></i>
                        <span class="leading-relaxed">{{ $pegawai->alamat_ktp ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <span class="block text-xs font-medium text-gray-500 mb-1">Alamat Domisili (Saat ini)</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-house-user text-emerald-400 mt-0.5 w-4 text-center"></i>
                        <span class="leading-relaxed">{{ $pegawai->alamat_domisili ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. KARTU RINGKASAN PEKERJAAN (Tema Ungu/Purple) --}}
        <div class="bg-white rounded-md shadow-sm border border-gray-100 border-t-4 border-t-purple-500 overflow-hidden xl:col-span-2">
            <div class="p-5 border-b border-gray-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-purple-50 flex items-center justify-center text-purple-600">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <h3 class="font-semibold text-gray-800 text-lg">Ringkasan Status Pekerjaan</h3>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-5">
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">NIP (Nomor Induk Pegawai)</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-fingerprint text-purple-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->nip ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Unit Kerja</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-building text-purple-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->unit_kerja?->name ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Status Pegawai</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-user-check text-purple-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->status_pegawai?->status ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Jenis Pegawai</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-users-gear text-purple-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->jenis_pegawai?->jenis ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Tanggal Bergabung</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-calendar-check text-purple-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->tanggal_bergabung_formatted ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Masa Kerja</span>
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-clock-rotate-left text-purple-400 mt-0.5 w-4 text-center"></i>
                        <span>{{ $pegawai->masa_kerja ?: 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    @if($pegawai->tanggal_habis_kontrak)
                        <span class="block text-xs font-medium text-gray-500 mb-1">Habis Kontrak</span>
                    @elseif($pegawai->tanggal_pensiun)
                        <span class="block text-xs font-medium text-gray-500 mb-1">Pensiun</span>
                    @endif
                    <div class="flex gap-2 text-sm text-gray-900 font-medium">
                        <i class="fa-solid fa-calendar-xmark text-purple-400 mt-0.5 w-4 text-center"></i>
                        <span>
                            @if($pegawai->tanggal_habis_kontrak)
                                {{ $pegawai->tanggal_habis_kontrak_formatted }}
                            @elseif($pegawai->tanggal_pensiun)
                                {{ $pegawai->tanggal_pensiun_formatted }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Status Akun Database</span>
                    <div class="flex items-center gap-2 text-sm font-medium">
                        <i class="fa-solid fa-circle-dot {{ $pegawai->status === 'active' ? 'text-green-500' : 'text-red-500' }} mt-0.5 w-4 text-center text-[10px]"></i>
                        <span class="{{ $pegawai->status === 'active' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $pegawai->status_label ?: 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>