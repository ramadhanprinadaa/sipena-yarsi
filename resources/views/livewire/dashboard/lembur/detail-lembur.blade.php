<div>                
                @if ($openDetail && $selectedLembur)    
                    <template x-teleport="body">
                    <!-- DETAIL MODAL -->
                    <div 
                        x-transition.opacity
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
                    >
                        <!-- Modal Box -->
                        <div 
                            x-transition.scale
                            class="w-full max-w-4xl max-h-[80vh] bg-white rounded-[20px] shadow-2xl border border-gray-200 flex flex-col"
                        >
                            <!-- Header -->
                            <div class="flex justify-between items-center px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-[#F5F7FA] to-white rounded-t-[20px] flex-shrink-0">
                                <h2 class="text-2xl font-bold text-[#2B76FF]">
                                    Detail Lembur
                                </h2>
                                <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 cursor-pointer transition">
                                    <i class="fa-solid fa-xmark text-xl"></i>
                                </button>
                            </div>

                            <!-- Content Scrollable -->
                            <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-[#2B76FF]/50 scrollbar-track-gray-100">
                                <div class="p-6 space-y-6">

                                    <!-- INFORMASI SURAT -->
                                    <div>
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Informasi Surat</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Nomor Surat</label>
                                                <input type="text" readonly value="{{ $selectedLembur?->suratPerintahLembur?->nomor_surat }}"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Unit Kerja</label>
                                                <input type="text" readonly value="{{ $selectedLembur?->suratPerintahLembur?->unitKerja?->name }}"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- WAKTU LEMBUR -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Waktu Lembur</p>
                                        </div>
                                        <div class="grid grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Tanggal Lembur</label>
                                                <input type="date" readonly value="{{ $selectedLembur?->tanggal_lembur }}"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jenis Hari</label>
                                                <input type="text" readonly value="{{ $selectedLembur?->jenis_hari }}"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jam Mulai</label>
                                                <input type="time" readonly value="{{ $selectedLembur?->jam_mulai }}"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Jam Selesai</label>
                                                <input type="time" readonly value="{{ $selectedLembur?->jam_selesai }}"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition">
                                            </div>
                                        </div>
                                    </div>


                                    <!-- DETAIL KEGIATAN -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-[#2B76FF]">
                                            <div class="w-1 h-5 bg-[#2B76FF] rounded-full"></div>
                                            <p class="text-xs font-bold text-[#2B76FF] uppercase tracking-wide">Detail Kegiatan</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Nama Kegiatan</label>
                                                <input type="text" readonly value="{{ $selectedLembur?->alasan_lembur }}"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition mb-4">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                                                <input type="text" readonly value="{{ $selectedLembur?->status }}"
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none  transition mb-4">
                                            </div>
                                        </div>
                                        
                                        @if($selectedLembur?->laporanHasilLembur)
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-2">Hasil Pekerjaan</label>
                                            <textarea readonly
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2B76FF] focus:border-transparent pointer-events-none transition h-24 resize-none scrollbar-thin scrollbar-thumb-[#2B76FF]/40 scrollbar-track-gray-50">{{ $selectedLembur->laporanHasilLembur->hasil_pekerjaan }}</textarea>
                                        </div>
                                        @endif  

                                    </div>

                            </div>
                        </div>
                    </div>
                    </template>
                @endif
</div>
