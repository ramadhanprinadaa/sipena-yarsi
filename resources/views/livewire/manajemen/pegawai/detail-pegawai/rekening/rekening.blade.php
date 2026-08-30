<div x-data="{ openFormModal: false }"
    @open-rekening-modal.window="openFormModal = true"
    @close-rekening-modal.window="openFormModal = false"
    class="flex flex-col min-h-[calc(100vh-380px)]">

    <!-- Header -->
    <div class="flex items-center justify-between bg-white rounded-md shadow-md border border-indigo-100 border-t-4 border-t-indigo-500 p-5">

        <!-- Header Information -->
        <div>
            <h3 class="text-xl font-bold text-gray-800">Data Rekening</h3>
            <p class="text-sm text-gray-500 mt-0.5">
                <i class="fa-solid fa-building-columns mr-1.5 text-indigo-500"></i>
                Informasi detail akun bank dan pencatatan riwayat modifikasi data.
            </p>
        </div>

        <!-- Button Trigger Add / Edit Rekening -->
        <button
            type="button"
            @click="openFormModal = true; $dispatch('open-rekening-modal')"
            class="flex items-center px-3 h-10 justify-center cursor-pointer bg-indigo-600 text-indigo-50 hover:bg-indigo-50 hover:text-indigo-600 text-sm rounded-md transition">
            <i class="fa-solid {{ $currentRekening ? 'fa-pen-to-square' : 'fa-plus' }} mr-2"></i>
            {{ $currentRekening ? 'Edit Rekening' : 'Tambah Rekening' }}
        </button>
    </div>

    <!-- Main Content -->
    <div class="flex-1 relative rounded-md shadow-md border border-slate-100 border-t-4 border-t-slate-500 p-4 flex mt-6">

        @if ($currentRekening)
            <div class="grid grid-cols-1 lg:grid-cols-10 gap-8 items-center flex-1">

                <!-- Card ATM -->
                <div class="lg:col-span-5 flex justify-center">

                    <div
                        class="relative w-full max-w-sm h-52 bg-gradient-to-br from-slate-800 via-indigo-950 to-slate-900 rounded-2xl shadow-xl p-4 text-white overflow-hidden transition-transform duration-300 hover:scale-[1.01]">
                        <div class="absolute inset-0 opacity-10 pointer-events-none">
                            <div class="absolute -top-10 -right-10 w-36 h-36 bg-white rounded-full blur-2xl"></div>
                            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-indigo-400 rounded-full blur-3xl">
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div
                                class="w-11 h-8 bg-gradient-to-br from-yellow-100 to-amber-400 rounded-md opacity-90 shadow-inner flex flex-col justify-between p-1">
                                <div class="border-b border-amber-600/20 h-0.5"></div>
                                <div class="border-b border-amber-600/20 h-0.5"></div>
                                <div class="border-b border-amber-600/20 h-0.5"></div>
                            </div>
                            <span
                                class="text-lg font-black tracking-wider uppercase bg-clip-text text-transparent bg-gradient-to-r from-gray-50 to-slate-300">
                                {{ $currentRekening['nama_bank'] }}
                            </span>
                        </div>

                        <div class="mt-8">
                            <p class="text-[10px] tracking-widest text-slate-400 uppercase font-mono">Nomor Rekening</p>
                            <h4 class="text-xl font-semibold tracking-widest font-mono mt-0.5 text-slate-100">
                                {{ chunk_split($currentRekening['nomor_rekening'], 4, ' ') }}
                            </h4>
                        </div>

                        <div class="absolute bottom-5 left-6 right-6">
                            <p class="text-[9px] tracking-widest text-slate-400 uppercase font-mono">Nama Pemilik</p>
                            <p class="text-sm font-medium uppercase tracking-wide truncate mt-0.5">
                                {{ $currentRekening['nama_rekening'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Data Rekening -->
                <div class="lg:col-span-5 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-8 space-y-3">
                    <div>
                        <h4 class="text-sm font-semibold text-slate-700 mb-2">Detail Informasi Akun</h4>
                        <dl class="grid grid-cols-3 gap-y-2 text-xs leading-relaxed">
                            <dt class="text-slate-400">Pegawai Terkait</dt>
                            <dd class="text-slate-700 col-span-2 font-medium">: {{ $nama_pegawai }}</dd>

                            <dt class="text-slate-400">Nama di Rekening</dt>
                            <dd class="text-slate-700 col-span-2">: {{ $currentRekening['nama_rekening'] }}</dd>
                        </dl>
                    </div>

                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-100 flex items-start gap-3.5">
                        <div
                            class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center shrink-0 text-xs">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <div class="space-y-0.5">
                            <h5 class="text-xs font-semibold text-slate-700">Terakhir Diperbarui Oleh:</h5>
                            <p class="text-xs font-medium text-indigo-600">
                                {{ $currentRekening['editor_name'] ?? 'Sistem / Migrasi Awal' }}
                            </p>
                            <p class="text-[10px] text-slate-400">
                                Pada tanggal:
                                {{ \Carbon\Carbon::parse($currentRekening['updated_at'])->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                            </p>
                        </div>
                    </div>

                    <div class="text-[11px] text-amber-600 bg-amber-50 rounded p-3 border border-amber-100 flex gap-2">
                        <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                        <p>Pastikan nomor rekening ini valid. Kesalahan penulisan dapat mengakibatkan kegagalan atau
                            hambatan pengiriman dana remunerasi kerja bulanan pegawai.</p>
                    </div>
                </div>

            </div>
        @else
            <div class="flex flex-col items-center text-center max-w-sm mx-auto py-12">
                <div
                    class="w-14 h-14 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mb-4 border border-dashed border-slate-200">
                    <i class="fa-solid fa-credit-card text-xl"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-700">Data Rekening Belum Tersedia</h4>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    Pegawai ini belum memiliki relasi data nomor rekening terdaftar. Hubungi administrator atau perbarui
                    data menggunakan tombol aksi.
                </p>
            </div>
        @endif
    </div>

    <!-- Modal Form Rekening -->
    <template x-teleport="body">
        <div x-show="openFormModal" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="openFormModal = false"
            @keydown.escape.window="openFormModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
            style="display: none;">

            <div x-show="openFormModal" x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.stop>
                <livewire:manajemen.pegawai.detail-pegawai.rekening.rekening-form :pegawai_id="$pegawai_id" />
            </div>
        </div>
    </template>
</div>
