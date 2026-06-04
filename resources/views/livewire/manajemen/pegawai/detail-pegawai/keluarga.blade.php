<div class="space-y-6">

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Data Keluarga</h3>
            <p class="text-sm text-gray-500 mt-0.5">
                <i class="fa-solid fa-people-roof mr-1.5 text-indigo-500"></i>
                Informasi daftar anggota keluarga inti
            </p>
        </div>

        {{-- Filter & Button Tambah --}}
        <div class="flex flex-wrap items-center gap-3">

            {{-- Search Bar --}}
            <div class="relative w-full sm:w-auto min-w-[200px]">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full ps-10 p-2.5 transition-colors"
                    placeholder="Cari nama...">
            </div>

            {{-- Filter Hubungan --}}
            <div class="relative w-full sm:w-auto">
                <select wire:model.live="filterHubungan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 transition-colors cursor-pointer appearance-none pr-8">
                    <option value="">Semua Hubungan</option>
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                    <option value="Orang Tua">Orang Tua</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            {{-- Button Tambah Data --}}
            <button wire:click="$dispatch('open-modal-tambah-keluarga')"
                class="w-full sm:w-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg px-4 p-2.5 transition-colors shadow-sm focus:ring-4 focus:ring-indigo-200">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Data</span>
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-slate-50/80 text-xs text-gray-700 uppercase border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-4 w-12 text-center whitespace-nowrap">No</th>
                        <th scope="col" class="px-5 py-4 whitespace-nowrap">Nama Lengkap</th>
                        <th scope="col" class="px-5 py-4 whitespace-nowrap">Hubungan</th>
                        <th scope="col" class="px-5 py-4 whitespace-nowrap">Tempat, Tgl Lahir</th>
                        <th scope="col" class="px-5 py-4 whitespace-nowrap">Pekerjaan</th>
                        <th scope="col" class="px-5 py-4 whitespace-nowrap">No. Telp</th>
                        <th scope="col" class="px-5 py-4 text-center whitespace-nowrap w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    {{-- Ganti $keluargas dengan variabel data dari Livewire Anda --}}
                    @forelse ($keluargas ?? [] as $index => $keluarga)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4 text-center font-medium text-gray-500">
                                {{ ($keluargas->currentPage() - 1) * $keluargas->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-gray-800">
                                {{ $keluarga->nama }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    // Pewarnaan badge berdasarkan hubungan
                                    $badgeColor = match(strtolower($keluarga->hubungan)) {
                                        'suami', 'istri' => 'bg-pink-50 text-pink-700 border-pink-200',
                                        'anak' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'orang tua' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }} inline-block">
                                    {{ $keluarga->hubungan }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-800">{{ $keluarga->tempat_lahir ?: '-' }}</span>
                                    <span class="text-xs text-gray-500">
                                        {{ $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->translatedFormat('d M Y') : '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                {{ $keluarga->pekerjaan ?: '-' }}
                            </td>
                            <td class="px-5 py-4 font-medium">
                                {{ $keluarga->no_telpon ?: '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Tombol Edit --}}
                                    <button wire:click="edit({{ $keluarga->id }})"
                                        class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors"
                                        title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    {{-- Tombol Hapus --}}
                                    <button wire:click="delete({{ $keluarga->id }})"
                                        class="w-8 h-8 rounded-md bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-colors"
                                        title="Hapus Data">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-folder-open text-2xl text-indigo-300"></i>
                                    </div>
                                    <h4 class="text-gray-800 font-semibold text-base mb-1">Belum Ada Data Keluarga</h4>
                                    <p class="text-gray-500 text-sm max-w-sm mx-auto">
                                        Pegawai ini belum memiliki data keluarga yang terdaftar, atau kata kunci pencarian tidak ditemukan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($keluargas) && $keluargas->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $keluargas->links() }}
            </div>
        @endif
    </div>

</div>