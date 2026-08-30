<div class="relative bg-white min-w-2xl min-h-[68vh] mx-auto rounded-2xl shadow-2xl flex flex-col overflow-hidden">

    <!-- Header -->
    <header
        class="flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-indigo-50 via-white to-blue-50">

        <!-- Decoration -->
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full blur-3xl"></div>
        </div>

        <!-- Header Information -->
        <div class="relative flex items-center gap-4">
            <div
                class="w-11 h-11 rounded-2xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-200">
                <i class="fa-solid fa-building-circle-check text-lg text-white"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    Form Tambah Unit Kerja
                </h2>

                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    <p class="text-xs text-gray-400 mt-0.5">
                        Tambah Data Unit Kerja
                    </p>
                </div>
            </div>
        </div>

        <div class="relative flex items-center gap-2">
            <!-- Close Button -->
            <button type="button" @click="$dispatch('close-modal')" wire:loading.attr="disabled"
                class="w-10 h-10 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <div class="relative flex-1 overflow-y-auto overflow-x-hidden p-6">
        <div class="space-y-4">
            <!-- Nama -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Nama Unit Kerja <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                    wire:model.live.debounce.500ms="form.nama_unit" placeholder="cth: Fakultas Teknologi Informasi">
                @error('form.nama_unit')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Unit SDM -->
            <div x-data="{ show: false, selected: @entangle('form.unit_sdm_id').live }" class="relative">
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Unit SDM <span class="text-red-500">*</span>
                </label>
                <button type="button" @click="show = !show" wire:model.live="form.unit_sdm_id"
                    class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer">
                    <span :class="selected ? 'text-gray-900' : 'text-gray-400 italic'"
                        x-text="
                        selected == 1 ? 'SDM Yayasan' :
                        selected == 2 ? 'SDM Universitas' :
                        'Pilih Unit SDM'
                    ">
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-500/90"></i>
                </button>
                @error('form.unit_sdm_id')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Menu Dropdown -->
                <div x-show="show" @click.outside="show = false" x-transition
                    class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">

                    <ul class="text-sm">
                        <li>
                            <button type="button" @click="selected='1'; show=false"
                                class="w-full text-left px-3 py-2 hover:bg-gray-100">
                                SDM Yayasan
                            </button>
                        </li>
                        <li>
                            <button type="button" @click="selected='2'; show=false"
                                class="w-full text-left px-3 py-2 hover:bg-gray-100">
                                SDM Universitas
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Pimpinan -->
            <div class="relative">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Pimpinan Unit
                </label>
                <div class="flex gap-2">
                    <input type="text"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        wire:model.live.debounce.500ms="pimpinanSearch"
                        placeholder="Cari dan Pilih dari Daftar Pegawai yang Tersedia">
                    @if ($form['pimpinan_id'])
                        <button wire:click="removePimpinan" type="button"
                            class="mt-1 px-3 hover:bg-red-50 rounded border border-red-200 cursor-pointer">
                            <i class="fa-solid fa-trash text-red-500"></i>
                        </button>
                    @endif
                </div>

                @error('form.pimpinan_id')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror

                @if ($pimpinanSearch && count($pimpinanResults) > 0)
                    <div
                        class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg w-full max-h-48 overflow-y-auto z-50">
                        @foreach ($pimpinanResults as $pimpinan)
                            <button type="button" wire:click="selectPimpinan({{ $pimpinan->id }})"
                                class="w-full text-left px-3 py-2 hover:bg-indigo-50
                                transition border-b last:border-b-0">
                                <div class="text-sm font-medium text-gray-700">
                                    {{ $pimpinan->nama }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $pimpinan->nip }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Unit Induk -->
            <div class="relative">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Unit Induk
                </label>
                <div class="flex gap-2">
                    <input type="text"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        wire:model.live.debounce.400ms="unitIndukSearch"
                        placeholder="Cari dan Pilih dari Unit Induk yang Tersedia">
                    @if ($form['unit_induk_id'])
                        <button wire:click="removeUnitInduk" type="button"
                            class="mt-1 px-3 hover:bg-red-50 rounded border border-red-200 cursor-pointer">
                            <i class="fa-solid fa-trash text-red-500"></i>
                        </button>
                    @endif
                </div>
                @error('form.unit_induk_id')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror
                @if ($unitIndukSearch && count($unitIndukResults) > 0)
                    <div
                        class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg w-full max-h-48 overflow-y-auto z-50">
                        @foreach ($unitIndukResults as $unitInduk)
                            <button type="button" wire:click="selectUnitInduk({{ $unitInduk->id }})"
                                class="w-full text-left px-3 py-2 hover:bg-indigo-50
                                transition border-b last:border-b-0">
                                <div class="text-sm font-medium text-gray-700">
                                    {{ $unitInduk->name }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="flex justify-end gap-3 p-4 border-t border-gray-200">
        <button type="button" @click="$dispatch('close-modal')"
            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md cursor-pointer transition-colors duration-150">
            Batal
        </button>
        <button type="button"
            x-on:click="showLoading = true; $wire.save().finally(() => setTimeout(() => showLoading = false, 500))"
            @disabled(blank($form['nama_unit']) || blank($form['unit_sdm_id']) || $errors->any())
            wire:loading.attr="disabled" wire:target="save"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-md transition-colors duration-150 bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-indigo-500">
            <!-- Loading spinner -->
            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                </path>
            </svg>
            <span wire:loading.remove wire:target="save">Simpan Data</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
    </div>
</div>
