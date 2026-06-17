<div>
@if($open)
        {{-- MODAL EDIT CUTI --}}
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
        }" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4">

                    <!-- Modal Box -->
                    <div
                        class="w-full max-w-2xl max-h-[85vh] bg-white rounded-[20px] shadow-2xl border border-gray-200 flex flex-col"
                    >
                        <!-- Header -->
                        <div class="flex justify-between items-center px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-[#F5F7FA] to-white rounded-t-[20px] flex-shrink-0">
                            <h2 class="text-lg font-bold text-[#2B76FF]">
                                Edit Cuti
                            </h2>
                            <button wire:click="close" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
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
                                        <select wire:model.live="jenis_cuti_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            <option value="">Pilih Jenis Cuti</option>
                                            @foreach($jenisCutiList as $jenis)
                                                <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('jenis_cuti_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <!-- Tanggal Mulai -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                                        <input type="date" wire:model="tanggal_mulai"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                        @error('tanggal_mulai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <!-- Tanggal Selesai-->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                                        <input type="date" wire:model="tanggal_selesai"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                        @error('tanggal_selesai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                @php
                                    $selectedJenis = collect($jenisCutiList)->firstWhere('id', (int) $jenis_cuti_id);
                                @endphp
                                @if($selectedJenis?->dihitung_per_jam)
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai</label>
                                            <input type="time" wire:model="jam_mulai" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            @error('jam_mulai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai</label>
                                            <input type="time" wire:model="jam_selesai" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                            @error('jam_selesai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                @endif

                                {{-- Opsi Izin Sakit --}}
                                @if($jenis_cuti_id == 4)
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                    <label class="block text-sm font-semibold text-amber-800 mb-3">Opsi Pemotongan Izin Sakit</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="relative flex items-center p-3 border-2 rounded-xl cursor-pointer transition"
                                            :class="$wire.metode_potongan === 'potong_gaji' ? 'border-[#2B76FF] bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300'">
                                            <input type="radio" wire:model.live="metode_potongan" value="potong_gaji" class="w-4 h-4 text-[#2B76FF] focus:ring-[#2B76FF]">
                                            <div class="ml-3">
                                                <p class="text-sm font-bold text-gray-800">Potong Gaji</p>
                                                <p class="text-[10px] text-gray-500">Gaji akan dipotong sesuai ketentuan</p>
                                            </div>
                                        </label>
                                        <label class="relative flex items-center p-3 border-2 rounded-xl cursor-pointer transition"
                                            :class="$wire.metode_potongan === 'potong_cuti' ? 'border-[#2B76FF] bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300'">
                                            <input type="radio" wire:model.live="metode_potongan" value="potong_cuti" class="w-4 h-4 text-[#2B76FF] focus:ring-[#2B76FF]">
                                            <div class="ml-3">
                                                <p class="text-sm font-bold text-gray-800">Potong Saldo Cuti</p>
                                                <p class="text-[10px] text-gray-500">Memotong sisa saldo cuti anda</p>
                                            </div>
                                        </label>
                                    </div>
                                    @error('metode_potongan') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                </div>
                                @endif

                                @if($jenis_cuti_id == 4)
                                {{-- Input Dokumen Pendukung --}}
                                <div class="col-span-2 mt-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Dokumen Pendukung <span class="text-xs font-normal text-gray-500">(Opsional, Max: 2MB. Format: PDF, JPG, PNG)</span>
                                    </label>

                                    {{-- Indikator File Lama --}}
                                    @if($dokumen_lama)
                                        <div class="mb-3 flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-lg">
                                            <div class="flex items-center gap-3">
                                                <i class="fa-solid fa-file-invoice text-blue-500 text-xl"></i>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">Dokumen Saat Ini</p>
                                                    <a href="{{ Storage::url($dokumen_lama) }}" target="_blank" class="text-xs text-[#2B76FF] hover:underline font-semibold">
                                                        Lihat Dokumen
                                                    </a>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">Telah Diunggah</span>
                                        </div>
                                    @endif

                                    {{-- Area Drag & Drop File Baru --}}
                                    <div
                                        @dragover.prevent="dragOverPengajuan = true"
                                        @dragleave.prevent="dragOverPengajuan = false"
                                        @drop="handleFilePengajuan($event, 'pengajuan')"
                                        class="relative border-2 border-dashed rounded-xl p-6 transition-all duration-200"
                                        :class="{
                                            'border-[#2B76FF] bg-blue-50': dragOverPengajuan,
                                            'border-gray-300 bg-gray-50 hover:border-[#2B76FF]': !dragOverPengajuan
                                        }"
                                    >
                                        <input
                                            type="file"
                                            wire:model="dokumen_pendukung"
                                            @change="handleFilePengajuan($event, 'pengajuan')"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                        >

                                        <div class="text-center">
                                            <template x-if="!filePengajuan">
                                                <div class="space-y-2">
                                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-2 text-[#2B76FF]">
                                                        <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-700">
                                                        {{ $dokumen_lama ? 'Tarik & Lepas file baru di sini untuk menimpa' : 'Tarik & Lepas file di sini atau klik untuk memilih' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">Maksimal 2MB</p>
                                                </div>
                                            </template>

                                            <template x-if="filePengajuan">
                                                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm relative z-20">
                                                    <div class="flex items-center space-x-3 overflow-hidden">
                                                        <i :class="getFileIcon(filePengajuan.name)" class="text-2xl"></i>
                                                        <div class="text-left overflow-hidden">
                                                            <p class="text-sm font-medium text-gray-700 truncate max-w-[200px]" x-text="filePengajuan.name"></p>
                                                            <p class="text-xs text-gray-500" x-text="formatFileSize(filePengajuan.size)"></p>
                                                        </div>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        @click.prevent="removeFile('pengajuan'); $wire.set('dokumen_pendukung', null)"
                                                        class="text-red-500 hover:text-red-700 transition"
                                                    >
                                                        <i class="fa-solid fa-xmark text-lg"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>

                                        <div wire:loading wire:target="dokumen_pendukung" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center rounded-xl z-10">
                                            <i class="fa-solid fa-circle-notch fa-spin text-2xl text-[#2B76FF] mb-2"></i>
                                            <span class="text-sm font-medium text-[#2B76FF]">Mengunggah...</span>
                                        </div>
                                    </div>

                                    @error('dokumen_pendukung') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                @endif

                                <!-- Kegiatan -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Cuti</label>
                                    <textarea wire:model="keterangan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition h-24 resize-none scrollbar-thin scrollbar-thumb-[#2B76FF]/40 scrollbar-track-gray-50"></textarea>
                                    @error('keterangan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                @error('cuti') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                            </div>
                        </div>

                        <!-- Footer Button -->
                        <div class="px-6 py-4 bg-white border-t border-gray-200 rounded-b-[20px] flex-shrink-0">
                            <button wire:click="update" wire:loading.attr="disabled" wire:target="update,dokumen_pendukung"
                                class="w-full py-3 rounded-lg text-white font-semibold
                                    bg-gradient-to-r from-[#2B76FF] via-[#7B61FF] to-[#FF00CC]
                                    hover:shadow-lg cursor-pointer transition duration-300">
                                <span wire:loading.remove wire:target="update">Edit Cuti</span>
                                <span wire:loading wire:target="update">Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                </div>
@endif
</div>