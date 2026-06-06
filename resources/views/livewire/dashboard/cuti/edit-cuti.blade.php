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
                                        <input type="file" wire:model="dokumen_pendukung"
                                            class="hidden" 
                                            @change="handleFilePengajuan($event, 'pengajuan')"
                                            accept=".pdf,.jpg,.jpeg,.png"
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
                                    @if($dokumen_lama)
                                        <p class="text-xs text-gray-500 mt-2">Dokumen saat ini: {{ basename($dokumen_lama) }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-2">Format: PDF, JPG, JPEG, PNG. Maksimal 2 MB.</p>
                                    @error('dokumen_pendukung') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

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
