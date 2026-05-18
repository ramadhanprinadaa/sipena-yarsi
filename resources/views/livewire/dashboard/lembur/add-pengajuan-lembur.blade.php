
<div>
    @if($open)
        {{-- MODAL: Form Pengajuan Lembur --}}
        <div 
            x-show="showModalPengajuan"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
            x-data="{ 
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
            }"
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
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Content Scrollable -->
                <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                    <div class="p-6 space-y-4">

                        <!-- Surat Perintah Lembur -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Surat Perintah Lembur</label>
                            <select wire:model.live="form.surat_perintah_lembur_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition">
                                <option value="">-- Pilih SPL --</option>
                                @foreach($availableSPLs as $spl)
                                    <option value="{{ $spl['id'] }}">{{ $spl['nomor'] }} - {{ $spl['kegiatan'] }}</option>
                                @endforeach
                            </select>
                            @error('form.surat_perintah_lembur_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Row: Estimasi Jam, Jenis Hari & Tanggal --}}
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Estimasi Jam -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai - Selesai</label>
                                <div class="flex gap-2">
                                    <input type="time" wire:model.defer="form.jam_mulai" readonly
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition bg-gray-50">
                                    <input type="time" wire:model.defer="form.jam_selesai" readonly
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition bg-gray-50">
                                </div>
                                @error('form.jam_mulai')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @error('form.jam_selesai')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Lembur</label>
                                <input type="date" wire:model.defer="form.tanggal_lembur" readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition bg-gray-50">
                                @error('form.tanggal_lembur')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jenis Hari -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Hari</label>
                                <select wire:model.defer="form.jenis_hari"
                                    disabled
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition bg-gray-50">
                                    <option value="">Pilih Jenis Hari</option>
                                    <option value="Hari Kerja Normal">Hari kerja normal</option>
                                    <option value="Hari Libur Mingguan">Hari libur mingguan</option>
                                    <option value="Hari Libur Nasional">Hari libur nasional</option>
                                </select>
                                @error('form.jenis_hari')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Kegiatan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kegiatan</label>
                            <textarea wire:model.defer="form.kegiatan" readonly
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent transition h-20 resize-none scrollbar-thin scrollbar-thumb-[#2B76FF]/40 scrollbar-track-gray-50 bg-gray-50"></textarea>
                        </div>

                    </div>
                </div>

                <!-- Footer Button -->
                <div class="px-6 py-4 bg-white border-t border-gray-200 rounded-b-[20px] flex-shrink-0">
                    <button 
                        wire:click="submit"
                        class="w-full py-3 rounded-lg text-white font-semibold 
                            bg-gradient-to-r from-[#2B76FF] via-[#7B61FF] to-[#FF00CC]  
                            hover:shadow-lg cursor-pointer transition duration-300">
                        Ajukan Lembur
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
