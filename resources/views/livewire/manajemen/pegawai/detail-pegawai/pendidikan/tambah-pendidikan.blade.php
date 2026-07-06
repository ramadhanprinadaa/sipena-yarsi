<div class="relative bg-white min-w-2xl min-h-[78vh] mx-auto rounded-2xl shadow-2xl flex flex-col overflow-hidden">
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
                <i class="fa-solid fa-graduation-cap text-lg text-white"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    Form Tambah Riwayat Pendidikan
                </h2>

                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    <p class="text-xs text-gray-400 mt-0.5">
                        Tambah Data Riwayat Pendidikan {{ $pegawai->nama }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Close Button -->
        <div class="relative flex items-center gap-2">
            <button type="button" @click="$dispatch('close-add-pendidikan-modal')" wire:loading.attr="disabled"
                class="w-10 h-10 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <div class="relative flex-1 overflow-y-auto overflow-x-hidden p-6">
        <div class="grid grid-cols-2 gap-x-6 gap-y-4">
            <!-- Field Jenjang Pendidikan -->
            <div
                x-data="{ show: false, selected: @entangle('form.jenjang_pendidikan_id') }"
                class="relative col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Jenjang Pendidikan <span class="text-red-500">*</span>
                </label>
                <button type="button" x-on:click="show = !show"
                    class="w-full flex items-center justify-between h-10 px-1 border-b-2 border-gray-300 hover:border-indigo-400 focus-within:border-indigo-500 text-sm transition cursor-pointer">

                    <span class="truncate" :class="selected ? 'text-gray-900' : 'text-gray-400 italic'">
                        {{ $jenjangPendidikan[$form['jenjang_pendidikan_id']] ?? 'Pilih Jenjang Pendidikan' }}
                    </span>

                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                        :class="{ 'rotate-180': show }">
                    </i>
                </button>
                @error('form.jenjang_pendidikan_id')
                    <span class="mt-1 block text-xs text-red-600">
                        {{ $message }}
                    </span>
                @enderror
                <!-- Dropdown -->
                <div x-show="show" x-cloak x-on:click.outside="show = false" x-transition
                    class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg overflow-hidden">
                    <ul class="max-h-60 overflow-y-auto py-1 text-sm">
                        @foreach ($jenjangPendidikan as $id => $jenjang)
                            <li>
                                <button type="button"
                                    x-on:click="$wire.set('form.jenjang_pendidikan_id', {{ $id }}); show = false;"
                                    class="w-full text-left px-3 py-2 hover:bg-indigo-50">
                                    {{ $jenjang }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Field Tahun Masuk & Tahun Lulus -->
            <div class="grid grid-cols-2 gap-4 col-span-2">
                <div>
                    <label for="tahun_masuk" class="block text-xs font-medium text-gray-600 mb-1">
                        Tahun Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="tahun_masuk"
                    wire:model.live.blur="form.tahun_masuk" maxlength="4"
                        inputmode="numeric" placeholder="Contoh: 2020"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400">
                    @error('form.tahun_masuk')
                        <span class="mt-1 block text-xs text-red-600">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div>
                    <label for="tahun_lulus" class="block text-xs font-medium text-gray-600 mb-1">
                        Tahun Lulus <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="tahun_lulus"
                    wire:model.live.blur="form.tahun_lulus" maxlength="4"
                        inputmode="numeric" placeholder="Contoh: 2024"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400">
                    @error('form.tahun_lulus')
                        <span class="mt-1 block text-xs text-red-600">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <!-- Field Upload File Ijazah -->
            <div class="col-span-2">
                <label for="file_ijazah" class="block text-xs font-medium text-gray-600 mb-1">
                    File Ijazah <span class="text-red-500">*</span>
                </label>
                <input type="file" id="file_ijazah" wire:model.live.blur="form.file_ijazah" accept=".pdf,.doc,.docx"
                    class="w-full border-2 rounded-md shadow-none focus:ring-0 border-gray-300 focus:border-indigo-500 focus:outline-none  text-sm placeholder:italic placeholder-gray-400">
                <p class="mt-1 text-xs text-gray-400">Format PDF, DOC, atau DOCX. Maksimal 10 MB.</p>

                <!-- Indikator sedang mengunggah file -->
                <div wire:loading wire:target="form.file_ijazah" class="mt-1 text-xs text-blue-500">
                    Mengunggah file...
                </div>

                @error('form.file_ijazah')
                    <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="flex justify-end gap-3 p-4 border-t border-gray-200">
        <button type="button" @click="$dispatch('close-add-pendidikan-modal')"
            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md cursor-pointer transition-colors duration-150">
            Batal
        </button>
        <button type="button"
            x-on:click="showLoading = true; $wire.save().finally(() => setTimeout(() => showLoading = false, 500))"
             @disabled(blank($form['jenjang_pendidikan_id']) || blank($form['tahun_masuk']) || blank($form['tahun_lulus']) || blank($form['file_ijazah']) || $errors->any())
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
