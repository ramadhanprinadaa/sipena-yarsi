<div>
    @section('breadcrumb')
        <div class="flex flex-wrap items-center gap-x-2 text-sm text-gray-500 font-medium">
            <span class="text-gray-400">Konfigurasi</span>
            <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
            <a wire:navigate href="{{ route('konfigurasi-unit-kerja-livewire') }}"
                class="text-indigo-600 hover:text-indigo-500 transition-colors duration-150">
                Unit Kerja
            </a>
        </div>
    @endsection

    @section('content')
        <div class="min-h-screen bg-gray-50/60 py-8 px-4 sm:px-6 lg:px-8">

            {{-- Page Header --}}
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Unit Kerja</h1>
                        <p class="mt-1 text-sm text-gray-500">Kelola daftar unit kerja dalam organisasi Anda.</p>
                    </div>
                    <button wire:click="openCreate"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-medium rounded-xl shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Tambah Unit Kerja
                    </button>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Unit</p>
                    <p class="mt-1.5 text-2xl font-semibold text-gray-900">24</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Aktif</p>
                    <p class="mt-1.5 text-2xl font-semibold text-emerald-600">21</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Non-Aktif</p>
                    <p class="mt-1.5 text-2xl font-semibold text-rose-500">3</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Pegawai</p>
                    <p class="mt-1.5 text-2xl font-semibold text-gray-900">312</p>
                </div>
            </div>

            {{-- Main Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                {{-- Toolbar --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                    {{-- Search --}}
                    <div class="relative w-full sm:w-80">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Cari nama atau kode unit..."
                            class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all duration-150" />
                    </div>

                    {{-- Right controls --}}
                    <div class="flex items-center gap-2">
                        <select
                            class="text-sm bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all duration-150">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                        <button
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-150"
                            title="Ekspor">
                            <i class="fa-solid fa-arrow-down-to-bracket text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/60">
                                <th scope="col"
                                    class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-8">
                                    #
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <button wire:click="sortBy('kode')" class="inline-flex items-center gap-1 hover:text-gray-700 transition-colors">
                                        Kode
                                        <span class="text-gray-300">
                                            @if ($sortField === 'kode')
                                                <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-indigo-400"></i>
                                            @else
                                                <i class="fa-solid fa-sort"></i>
                                            @endif
                                        </span>
                                    </button>
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <button wire:click="sortBy('nama')" class="inline-flex items-center gap-1 hover:text-gray-700 transition-colors">
                                        Nama Unit Kerja
                                        <span class="text-gray-300">
                                            @if ($sortField === 'nama')
                                                <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-indigo-400"></i>
                                            @else
                                                <i class="fa-solid fa-sort"></i>
                                            @endif
                                        </span>
                                    </button>
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Singkatan
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Kepala Unit
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Pegawai
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 pr-6 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            {{-- Data rows — ganti dengan @foreach ($unitKerjas as $index => $item) --}}
                            @php
                                $dummyData = [
                                    ['id' => 1, 'kode' => 'UK-001', 'nama' => 'Bagian Sumber Daya Manusia', 'singkatan' => 'SDM', 'kepala' => 'Dr. Andi Wijaya, M.M.', 'pegawai' => 18, 'status' => 'aktif'],
                                    ['id' => 2, 'kode' => 'UK-002', 'nama' => 'Bagian Keuangan dan Akuntansi', 'singkatan' => 'KEU', 'kepala' => 'Siti Rahayu, S.E., M.Si.', 'pegawai' => 12, 'status' => 'aktif'],
                                    ['id' => 3, 'kode' => 'UK-003', 'nama' => 'Bagian Teknologi Informasi', 'singkatan' => 'TI', 'kepala' => 'Budi Santoso, S.Kom.', 'pegawai' => 9, 'status' => 'aktif'],
                                    ['id' => 4, 'kode' => 'UK-004', 'nama' => 'Bagian Pengadaan Barang dan Jasa', 'singkatan' => 'PBJ', 'kepala' => '—', 'pegawai' => 6, 'status' => 'nonaktif'],
                                    ['id' => 5, 'kode' => 'UK-005', 'nama' => 'Bagian Hukum dan Kepatuhan', 'singkatan' => 'HUK', 'kepala' => 'Rini Kurniasih, S.H., M.H.', 'pegawai' => 5, 'status' => 'aktif'],
                                ];
                            @endphp

                            @forelse ($dummyData as $index => $item)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-100 group">
                                    <td class="py-3.5 pl-6 pr-3 text-sm text-gray-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-xs font-mono font-medium">
                                            {{ $item['kode'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <p class="text-sm font-medium text-gray-900">{{ $item['nama'] }}</p>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="text-sm text-gray-600">{{ $item['singkatan'] }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2">
                                            @if ($item['kepala'] !== '—')
                                                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-indigo-600 text-xs font-semibold">
                                                        {{ strtoupper(substr($item['kepala'], 0, 1)) }}
                                                    </span>
                                                </div>
                                                <span class="text-sm text-gray-700 truncate max-w-[160px]">{{ $item['kepala'] }}</span>
                                            @else
                                                <span class="text-sm text-gray-400 italic">Belum ditentukan</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="text-sm font-medium text-gray-700">{{ $item['pegawai'] }}</span>
                                        <span class="text-xs text-gray-400 ml-0.5">org</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if ($item['status'] === 'aktif')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-600 ring-1 ring-rose-200/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 pr-6 text-right">
                                        <div class="inline-flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                            <button wire:click="openEdit({{ $item['id'] }})"
                                                class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-150"
                                                title="Edit">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            </button>
                                            <button wire:click="confirmDelete({{ $item['id'] }})"
                                                class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all duration-150"
                                                title="Hapus">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                                                <i class="fa-solid fa-building text-gray-400 text-lg"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500">Belum ada data unit kerja</p>
                                            <p class="text-xs text-gray-400">Mulai dengan menambahkan unit kerja baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span class="font-medium text-gray-700">1–5</span> dari <span class="font-medium text-gray-700">24</span> unit kerja
                    </p>
                    {{-- {{ $unitKerjas->links() }} --}}
                    <div class="flex items-center gap-1">
                        <button class="px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-100 rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                            <i class="fa-solid fa-chevron-left mr-1"></i> Prev
                        </button>
                        <button class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-lg font-medium">1</button>
                        <button class="px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">2</button>
                        <button class="px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">3</button>
                        <button class="px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                            Next <i class="fa-solid fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== MODAL: Form Tambah / Edit ==================== --}}
        @if ($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
                x-data x-init="$el.classList.add('animate-fadeIn')">

                {{-- Backdrop --}}
                <div wire:click="$set('showModal', false)"
                    class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

                {{-- Modal Panel --}}
                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">
                                {{ $editingId ? 'Edit Unit Kerja' : 'Tambah Unit Kerja' }}
                            </h2>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $editingId ? 'Perbarui informasi unit kerja yang dipilih.' : 'Isi data untuk membuat unit kerja baru.' }}
                            </p>
                        </div>
                        <button wire:click="$set('showModal', false)"
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-150">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5 space-y-5">

                        {{-- Kode & Singkatan --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Kode Unit <span class="text-rose-500">*</span>
                                </label>
                                <input wire:model="kode" type="text" placeholder="UK-001"
                                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 focus:bg-white transition-all duration-150 font-mono @error('kode') border-rose-400 bg-rose-50 @enderror" />
                                @error('kode')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Singkatan <span class="text-rose-500">*</span>
                                </label>
                                <input wire:model="singkatan" type="text" placeholder="SDM"
                                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 focus:bg-white transition-all duration-150 @error('singkatan') border-rose-400 bg-rose-50 @enderror" />
                                @error('singkatan')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Nama --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Nama Unit Kerja <span class="text-rose-500">*</span>
                            </label>
                            <input wire:model="nama" type="text" placeholder="Bagian Sumber Daya Manusia"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 focus:bg-white transition-all duration-150 @error('nama') border-rose-400 bg-rose-50 @enderror" />
                            @error('nama')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kepala Unit --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Kepala Unit
                                <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <input wire:model="kepala" type="text" placeholder="Nama kepala unit kerja"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 focus:bg-white transition-all duration-150" />
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input wire:model="status" type="radio" value="aktif"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" />
                                    <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">Aktif</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input wire:model="status" type="radio" value="nonaktif"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" />
                                    <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">Non-Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50/60 border-t border-gray-100">
                        <button wire:click="$set('showModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-all duration-150">
                            Batal
                        </button>
                        <button wire:click="save" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-medium rounded-xl shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="save">
                                <i class="fa-solid fa-floppy-disk text-xs mr-1"></i>
                                {{ $editingId ? 'Perbarui' : 'Simpan' }}
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ==================== MODAL: Konfirmasi Hapus ==================== --}}
        @if ($showDeleteModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div wire:click="$set('showDeleteModal', false)"
                    class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

                <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 pt-6 pb-2 text-center">
                        <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg"></i>
                        </div>
                        <h2 class="text-base font-semibold text-gray-900">Hapus Unit Kerja?</h2>
                        <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                            Tindakan ini tidak dapat dibatalkan. Seluruh data yang terkait dengan unit kerja ini akan ikut terhapus.
                        </p>
                    </div>
                    <div class="flex gap-3 px-6 py-5">
                        <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-150">
                            Batal
                        </button>
                        <button wire:click="delete" wire:loading.attr="disabled"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-xl transition-all duration-150 disabled:opacity-60">
                            <span wire:loading.remove wire:target="delete">
                                <i class="fa-solid fa-trash text-xs mr-1"></i> Hapus
                            </span>
                            <span wire:loading wire:target="delete">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

    @endsection
</div>