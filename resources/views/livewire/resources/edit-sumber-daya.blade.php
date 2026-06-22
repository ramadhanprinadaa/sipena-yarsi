<div class="bg-white min-w-[42vw] h-[70vh] mx-auto rounded-xl shadow-2xl flex flex-col overflow-hidden">

    <!-- Header -->
    <header class="flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-amber-50 via-white to-orange-50">

        <!-- Header Information -->
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full flex items-center justify-center bg-amber-500">
                <i class="fa-solid fa-pen-to-square text-white text-base"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">Edit Sumber Daya</h2>
                <span class="text-sm text-slate-500">Perbarui judul atau ganti file dokumen</span>
            </div>
        </div>

        <!-- Close Button -->
        <button type="button" @click="$dispatch('close-edit-modal')"
            class="w-10 h-10 flex items-center justify-center rounded-full text-gray-400 hover:text-red-500 hover:bg-red-100/70 transition-all cursor-pointer">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </header>

    <!-- Main Content -->
    <div class="p-6 flex flex-col flex-1 overflow-y-auto">
        <form wire:submit="save" class="flex flex-col flex-1 space-y-4">

            <!-- Judul -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Judul Dokumen <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model="judul"
                    placeholder="cth: Manual Book Pengguna SIPENA"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-amber-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400">
                @error('judul')
                    <span class="text-sm text-red-500 mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- Upload File -->
            <div class="flex-1 flex flex-col h-full">
                <!-- Label -->
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Upload File Baru (Opsional)
                </label>
                <!-- File Lama -->
                @if($existingFileName && !$file)
                    <div class="mb-3 text-sm text-slate-600 bg-slate-100 p-2.5 rounded-md flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-slate-400"></i>
                        <span>File saat ini: <strong>{{ $existingFileName }}</strong></span>
                    </div>
                @endif
                <!-- Dropzone File -->
                <div class="mt-1 flex-1 flex flex-col h-full justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition-all"
                    x-data="{ isUploading: false, progress: 0 }"
                    x-on:livewire-upload-start="isUploading = true"
                    x-on:livewire-upload-finish="isUploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress">
                    <div class="space-y-1 text-center">
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-amber-600 hover:text-amber-500 hover:underline focus-within:outline-none">
                                <span>Klik disini</span>
                                <input type="file" wire:model="file" class="sr-only" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                            </label>
                            <p class="pl-1">untuk pilih file baru</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah file saat ini.</p>

                        <div x-show="isUploading" class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                            <div class="bg-amber-500 h-2.5 rounded-full" x-bind:style="'width: ' + progress + '%'"></div>
                        </div>
                    </div>
                </div>
                @if($file)
                    <div class="mt-2 text-sm text-emerald-600 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> File baru: {{ $file->getClientOriginalName() }}
                    </div>
                @endif
                @error('file')
                    <span class="text-sm text-red-500 mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="px-5 py-4 border-t border-slate-200 bg-gray-50 flex justify-end gap-2 rounded-b-2xl">
        <button
            type="button"
            @click="$dispatch('close-edit-modal')"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md cursor-pointer transition-colors duration-150 hover:bg-gray-50">
            Batal
        </button>
        <button
            type="button"
            x-on:click="showLoading = true; $wire.save().finally(() => setTimeout(() => showLoading = false, 500))"
            @disabled($errors->any() || blank($judul) || ($judul === $originalJudul && blank($file)))
            wire:click="save"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-md transition-colors duration-150 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-amber-500">
            <!-- Loading spinner -->
            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                </path>
            </svg>
            <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
    </div>
</div>