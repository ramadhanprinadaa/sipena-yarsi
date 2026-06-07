<div>
@if($open)                
                {{-- MODAL DELETE CUTI --}}
                <div 
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
                >

                    <!-- Modal Box -->
                    <div    
                        class="w-full max-w-xl bg-white/90 rounded-2xl shadow-2xl shadow-red-500/15 overflow-hidden"
                    >

                        <!-- HEADER -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h2 class="text-md font-bold text-red-600">
                                Konfirmasi Hapus
                            </h2>
                            <button wire:click="close" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>

                        <!-- CONTENT -->
                        <div class="px-6 py-12 text-center">
                            
                            <!-- Icon Warning -->
                            <div class="mx-auto mb-10 flex items-center justify-center w-20 h-20 rounded-full bg-red-100">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    class="w-10 h-10 text-red-600" 
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 
                                        1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 
                                        1.33.19 3 1.73 3z"/>
                                </svg>
                            </div>

                            <!-- Text -->
                            <p class="text-md text-gray-700 leading-relaxed">
                                Apakah Anda yakin ingin menghapus data cuti ini?
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                Tindakan ini tidak dapat dibatalkan.
                            </p>
                            @error('cuti') <p class="text-sm text-red-500 mt-3">{{ $message }}</p> @enderror

                        </div>

                        <!-- FOOTER -->
                        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">
                            
                            <!-- Cancel -->
                            <button 
                                wire:click="close"
                                class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 cursor-pointer rounded-lg transition"
                            >
                                Batal
                            </button>

                            <!-- Delete -->
                            <button wire:click="delete" wire:loading.attr="disabled" wire:target="delete"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 cursor-pointer rounded-lg shadow-sm transition"
                            >
                                <span wire:loading.remove wire:target="delete">Hapus</span>
                                <span wire:loading wire:target="delete">Menghapus...</span>
                            </button>

                        </div>

                    </div>
                </div>
@endif
</div>
