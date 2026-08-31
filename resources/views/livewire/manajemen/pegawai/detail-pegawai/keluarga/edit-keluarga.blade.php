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
                <i class="fa-solid fa-people-roof text-lg text-white"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    Form Edit Data Keluarga
                </h2>

                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    <p class="text-xs text-gray-400 mt-0.5">
                        Edit Data Anggota Keluarga {{ $nama_pegawai }}
                    </p>
                </div>
            </div>
        </div>

        <div class="relative flex items-center gap-2">
            <!-- Close Button -->
            <button type="button" @click="$dispatch('close-edit-modal')" wire:loading.attr="disabled"
                class="w-10 h-10 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <div class="relative flex-1 overflow-y-auto overflow-x-hidden p-6">
        <div class="grid grid-cols-2 gap-x-6 gap-y-4">
            <!-- Nama Lengkap -->
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                    wire:model.live="form.nama" placeholder="cth: Budiono Siregar">
                @error('form.nama')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Hubungan Keluarga -->
            <div x-data="{ show: false, selected: @entangle('form.jenis_keluarga_id') }" class="relative col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Hubungan Keluarga
                    <span class="text-red-500">*</span>
                </label>
                <button type="button" x-on:click="show = !show"
                    class="w-full flex items-center justify-between h-10 px-1 border-b-2 border-gray-300 hover:border-indigo-400 focus-within:border-indigo-500 text-sm transition cursor-pointer">

                    <span class="truncate" :class="selected ? 'text-gray-900' : 'text-gray-400 italic'">
                        {{ $jenisKeluarga[$form['jenis_keluarga_id']] ?? 'Pilih Hubungan Keluarga' }}
                    </span>

                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                        :class="{ 'rotate-180': show }">
                    </i>
                </button>

                @error('form.jenis_keluarga_id')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Dropdown -->
                <div x-show="show" x-cloak x-on:click.outside="show = false" x-transition
                    class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                    <ul class="max-h-60 overflow-y-auto py-1 text-sm">
                        @foreach ($jenisKeluarga as $id => $jenis)
                            <li>
                                <button type="button"
                                    x-on:click="$wire.set('form.jenis_keluarga_id', {{ $id }}); show = false;"
                                    class="w-full text-left px-3 py-2 hover:bg-indigo-50">
                                    {{ $jenis }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Tempat Lahir -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Tempat Lahir
                </label>
                <input type="text"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                    wire:model.live.debounce.500ms="form.tempat_lahir" placeholder="cth: Yogyakarta">
                @error('form.tempat_lahir')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Tanggal Lahir -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Tanggal Lahir <span class="text-red-500">*</span>
                </label>
                <div x-data="{ picker: null }" x-init="picker = new Datepicker($refs.input, {
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    language: 'id'
                });
                
                $refs.input.addEventListener('changeDate', () => {
                    $wire.set('form.tanggal_lahir', $refs.input.value);
                });" class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                    </div>
                    <input type="text" x-ref="input" wire:model.live="form.tanggal_lahir"
                        placeholder="Pilih Tanggal Lahir"
                        class="w-full h-9 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-0 transition-colors duration-200 placeholder:italic" />
                    <button type="button" x-show="$wire.form.tanggal_lahir"
                        @click="
                        $wire.set('form.tanggal_lahir', null);
                        picker.setDate({ clear: true });
                        $refs.input.value = '';"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                @error('form.tanggal_lahir')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Pekerjaan -->
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Pekerjaan
                </label>
                <input type="text"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                    wire:model.live.debounce.500ms="form.pekerjaan" placeholder="cth: Wirausaha">
                @error('form.pekerjaan')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- No. Telpon -->
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Nomor Telepon
                </label>
                <input type="text"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                    wire:model.live.debounce.500ms="form.no_telpon" placeholder="cth: 08778896514">
                @error('form.no_telpon')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Alamat -->
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Alamat
                </label>
                <input type="text"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                    wire:model.live.debounce.500ms="form.alamat" placeholder="cth: Jl. Letjen Suprapto No. 5 ...">
                @error('form.alamat')
                    <div class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="flex justify-end gap-3 p-4 border-t border-gray-200">
        <button type="button" @click="$dispatch('close-edit-modal')"
            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md cursor-pointer transition-colors duration-150">
            Batal
        </button>
        <button type="button"
            x-on:click="showLoading = true; $wire.update().finally(() => setTimeout(() => showLoading = false, 500))"
            @disabled(blank($form['nama']) ||
                    blank($form['tanggal_lahir']) ||
                    blank($form['jenis_keluarga_id']) ||
                    $errors->any() ||
                    !$this->isDirty) wire:loading.attr="disabled" wire:target="update"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-md transition-colors duration-150 bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-indigo-500">
            <!-- Loading spinner -->
            <svg wire:loading wire:target="update" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                </path>
            </svg>
            <span wire:loading.remove wire:target="save">Simpan Data</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
    </div>
</div>
