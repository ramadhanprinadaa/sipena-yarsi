<div class="bg-white w-[720px] max-w-[95vw] h-[78vh] mx-auto rounded-xl shadow-2xl flex flex-col overflow-hidden">

    <!-- Header -->
    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                <i class="fa-solid fa-calendar-plus text-base text-indigo-500"></i>
            </div>
            <div>
                <h2 class="text-md font-bold text-gray-800 leading-tight tracking-tight">Form Tambah Hari Libur</h2>
                <p class="text-xs text-gray-400 leading-tight">Isi data hari libur baru secara lengkap</p>
            </div>
        </div>
        <button
            type="button"
            @click="$dispatch('close-add-modal')"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </header>

    <!-- Form -->
    <form wire:submit.prevent="save" class="flex flex-col flex-1 min-h-0 px-6 py-4 overflow-auto">

        <!-- Content -->
        <div class="flex-1 space-y-10">
            <!-- Nama Hari Libur -->
            <div>
                <div class="input-wrapper group">
                    <input wire:model.live="form.nama_hari_libur" type="text" id="floating_nama_hari_libur" class="input-field peer" placeholder=" ">
                    <label for="floating_nama_hari_libur" class="input-label">Nama Hari Libur</label>
                </div>
                @error('form.nama_hari_libur') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
            </div>

            <!-- Tanggal Hari Libur -->
            <div>
                <div class="input-wrapper group relative"
                    x-data
                    x-init="
                        const picker = document.getElementById('floating_tanggal');
                        picker.addEventListener('changeDate', () => {
                            $wire.set('form.tanggal', picker.value);
                        });
                    "
                >
                    <!-- Icon -->
                    <div class="absolute inset-y-0 flex items-center ps-1 pointer-events-none z-10">
                        <i class="fa-solid fa-calendar-days text-body text-xs"></i>
                    </div>
                    <!-- Input -->
                    <input
                        wire:model.defer="form.tanggal"
                        type="text"
                        id="floating_tanggal"
                        datepicker
                        datepicker-autohide
                        datepicker-format="dd/mm/yyyy"
                        datepicker-language="id"
                        placeholder=" "
                        class="input-field peer ps-6"
                    >
                    <!-- Label -->
                    <label
                        for="floating_tanggal"
                        class="input-label ps-6 inset-y-2 peer-focus:ps-1 peer-[:not(:placeholder-shown)]:ps-1"
                    >
                        Tanggal Hari Libur
                    </label>
                </div>
                @error('form.tanggal') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
            </div>

            <!-- Jenis Hari Libur -->
            <div>
                <div
                    x-data="{ show: false, selected: @entangle('form.jenis_hari_libur').live, focus: false,}"
                    @keydown.escape.window="show = false"
                    class="relative input-wrapper">
                    <!-- Trigger -->
                    <button
                        x-ref="trigger"
                        type="button"
                        @click="show = !show"
                        @focus="focus = true"
                        @blur="focus = false"
                        class="input-field flex justify-between items-center cursor-pointer">
                        <span
                            :class="selected ? 'text-gray-900' : 'text-gray-500/90'"
                            x-text="selected || ' '">
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-150"
                            :class="show ? 'rotate-180' : ''">
                        </i>
                    </button>

                    <!-- Label -->
                    <label
                        @click="$refs.trigger.click(); $refs.trigger.focus()"
                        class="input-label-btn"
                        :class="{
                            'input-label-btn-selected': focus || selected,
                            'input-label-btn-focus': focus
                        }">
                        Jenis Hari Libur
                    </label>
                    <!-- Dropdown -->
                    <div
                        x-show="show"
                        @click.outside="show = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg z-50 overflow-hidden">
                        <ul class="text-sm py-1">
                            @foreach ($jenisHariLibur as $item)
                                <li>
                                    <button type="button" @click="selected = '{{ $item }}'; show = false;"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        {{ $item }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @error('form.jenis_hari_libur')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Keterangan -->
            <div class="">
                <div class="input-wrapper group">
                    <input wire:model.live="form.keterangan" type="textarea" id="floating_keterangan" class="input-field peer" placeholder=" ">
                    <label for="floating_keterangan" class="input-label">Keterangan</label>
                </div>
                @error('form.keterangan') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
            </div>
        </div>
        <!-- Footer -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-300">
            <button
                type="button"
                @click="$dispatch('close-add-modal')"
                class="px-4 py-2 text-sm bg-red-400 hover:bg-red-500 text-white rounded-md cursor-pointer">
                Batal
            </button>

            <button
                type="submit"
                @disabled(
                    blank($form['nama_hari_libur']) ||
                    blank($form['tanggal']) ||
                    blank($form['jenis_hari_libur']) ||
                    $errors->any()
                )
                class="px-4 py-2 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-500">

                <!-- Loading spinner -->
                <svg wire:loading wire:target="save"
                    class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>

                <span wire:loading.remove wire:target="save">Simpan Hari Libur</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
