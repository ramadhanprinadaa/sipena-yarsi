<div
    x-data="{
        steps: ['biodata', 'alamat', 'keluarga', 'rekening'],
        stepLabels: ['Data Biodata', 'Data Alamat', 'Data Keluarga', 'Data Rekening'],
        current: 0,
        get tab() { return this.steps[this.current] }
    }"
    class="bg-white w-[720px] max-w-[95vw] h-[88vh] mx-auto rounded-xl shadow-2xl flex flex-col overflow-hidden"
>

    <!-- Header -->
    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                <i class="fa-solid fa-user-plus text-base text-indigo-500"></i>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-800 leading-tight">Form Tambah Pegawai</h2>
                <p class="text-xs text-gray-400 leading-tight">Isi data pegawai baru secara lengkap</p>
            </div>
        </div>
        <button
            type="button"
            @click="$dispatch('close-add-modal')"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </header>

    <!-- Step Indicator -->
    <div class="px-6 py-4 border-b border-gray-100 shrink-0">
        <div class="flex items-center gap-0">
            <template x-for="(label, index) in stepLabels" :key="index">
                <div class="flex items-center" :class="index < stepLabels.length - 1 ? 'flex-1' : ''">
                    <!-- Step Node -->
                    <button
                        type="button"
                        @click="current = index"
                        class="flex flex-col items-center gap-1 group cursor-pointer">
                        <div
                            class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold border-2 transition-all duration-200"
                            :class="{
                                'bg-indigo-500 border-indigo-500 text-white': current === index,
                                'bg-white border-indigo-200 text-indigo-400': current > index,
                                'bg-white border-gray-200 text-gray-400': current < index
                            }">
                            <template x-if="current > index">
                                <i class="fa-solid fa-check text-[10px] text-indigo-400"></i>
                            </template>
                            <template x-if="current <= index">
                                <span x-text="index + 1"></span>
                            </template>
                        </div>
                        <span
                            class="text-[10px] font-medium whitespace-nowrap transition-colors"
                            :class="current === index ? 'text-indigo-600' : 'text-gray-400'"
                            x-text="label">
                        </span>
                    </button>
                    <!-- Connector Line -->
                    <template x-if="index < stepLabels.length - 1">
                        <div class="flex-1 h-px mx-2 mb-4 transition-colors duration-200"
                            :class="current > index ? 'bg-indigo-300' : 'bg-gray-200'">
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="save" class="flex flex-col flex-1 min-h-0">

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto px-6 py-5">

            <!-- ===================== -->
            <!-- Tab: Biodata          -->
            <!-- ===================== -->
            <div x-show="tab === 'biodata'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Informasi Dasar</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                    <!-- Nama (full width) -->
                    <div class="md:col-span-2">
                        <div class="input-wrapper group">
                            <input wire:model="form.nama" type="text" id="floating_nama" class="input-field peer" placeholder=" ">
                            <label for="floating_nama" class="input-label">Nama Lengkap Pegawai</label>
                        </div>
                        @error('form.nama') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <!-- Unit Kerja (dropdown) -->
                    <div
                        x-data="{ show: false, selected: '', focus: false }"
                        @keydown.escape.window="show = false"
                        class="relative input-wrapper md:col-span-2">

                        <button
                            x-ref="trigger"
                            type="button"
                            @click="show = !show"
                            @focus="focus = true"
                            @blur="focus = false"
                            class="input-field flex justify-between items-center cursor-pointer">
                            <span :class="selected ? 'text-gray-900' : 'text-gray-500/90'" x-text="selected || ' '"></span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-150" :class="show ? 'rotate-180' : ''"></i>
                        </button>

                        <label
                            @click="$refs.trigger.click(); $refs.trigger.focus()"
                            class="input-label-btn"
                            :class="{ 'input-label-btn-selected': focus || selected, 'input-label-btn-focus': focus }">
                            Unit Kerja
                        </label>

                        @error('form.unit_kerja') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                        <div
                            x-show="show"
                            @click.outside="show = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg z-50 max-h-44 overflow-y-auto">
                            <ul class="text-sm py-1">
                                @for($i = 0; $i <= 10; $i++)
                                    <li>
                                        <button type="button"
                                            @click="selected='Fakultas Ekonomi'; show=false"
                                            class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                            Fakultas Ekonomi
                                        </button>
                                    </li>
                                @endfor
                            </ul>
                        </div>
                    </div>

                    <!-- NIK -->
                    <div>
                        <div class="input-wrapper group">
                            <input wire:model="form.nik" type="text" id="floating_nik" class="input-field peer" placeholder=" ">
                            <label for="floating_nik" class="input-label">Nomor KTP / NIK</label>
                        </div>
                        @error('form.nik') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <!-- Gelar Depan -->
                    <div>
                        <div class="input-wrapper group">
                            <input wire:model="form.gelar_depan" type="text" id="floating_gelar_depan" class="input-field peer" placeholder=" ">
                            <label for="floating_gelar_depan" class="input-label">Gelar Depan</label>
                        </div>
                        @error('form.gelar_depan') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <!-- NIP -->
                    <div>
                        <div class="input-wrapper group">
                            <input wire:model="form.nip" type="text" id="floating_nip" class="input-field peer" placeholder=" ">
                            <label for="floating_nip" class="input-label">Nomor Induk Pegawai / NIP</label>
                        </div>
                        @error('form.nip') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <!-- Gelar Belakang -->
                    <div>
                        <div class="input-wrapper group">
                            <input wire:model="form.gelar_belakang" type="text" id="floating_gelar_belakang" class="input-field peer" placeholder=" ">
                            <label for="floating_gelar_belakang" class="input-label">Gelar Belakang</label>
                        </div>
                        @error('form.gelar_belakang') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <!-- NPWP -->
                    <div>
                        <div class="input-wrapper group">
                            <input wire:model="form.npwp" type="text" id="floating_npwp" class="input-field peer" placeholder=" ">
                            <label for="floating_npwp" class="input-label">Nomor NPWP</label>
                        </div>
                        @error('form.npwp') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <div class="input-wrapper group">
                            <input wire:model="form.tempat_lahir" type="text" id="floating_tempat_lahir" class="input-field peer" placeholder=" ">
                            <label for="floating_tempat_lahir" class="input-label">Tempat Lahir</label>
                        </div>
                        @error('form.tempat_lahir') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <div class="input-wrapper group">
                            <input wire:model="form.tanggal_lahir" type="date" id="floating_tanggal_lahir" class="input-field peer" placeholder=" ">
                            <label for="floating_tanggal_lahir" class="input-label">Tanggal Lahir</label>
                        </div>
                        @error('form.tanggal_lahir') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Pensiun -->
                    <div>
                        <div class="input-wrapper group">
                            <input wire:model="form.tanggal_pensiun" type="date" id="floating_tanggal_pensiun" class="input-field peer" placeholder=" ">
                            <label for="floating_tanggal_pensiun" class="input-label">Tanggal Pensiun</label>
                        </div>
                        @error('form.tanggal_pensiun') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div
                        x-data="{ show: false, selected: '', focus: false }"
                        @keydown.escape.window="show = false"
                        class="relative input-wrapper">

                        <button
                            x-ref="trigger"
                            type="button"
                            @click="show = !show"
                            @focus="focus = true"
                            @blur="focus = false"
                            class="input-field flex justify-between items-center cursor-pointer">
                            <span :class="selected ? 'text-gray-900' : 'text-gray-500/90'" x-text="selected || ' '"></span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-150" :class="show ? 'rotate-180' : ''"></i>
                        </button>

                        <label
                            @click="$refs.trigger.click(); $refs.trigger.focus()"
                            class="input-label-btn"
                            :class="{ 'input-label-btn-selected': focus || selected, 'input-label-btn-focus': focus }">
                            Jenis Kelamin
                        </label>
                        @error('form.jenis_kelamin') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror

                        <div
                            x-show="show"
                            @click.outside="show = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg z-50 max-h-44 overflow-y-auto">
                            <ul class="text-sm py-1">
                                <li>
                                    <button type="button"
                                        @click="selected='Laki-Laki'; show=false"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        Laki-Laki
                                    </button>
                                </li>
                                <li>
                                    <button type="button"
                                        @click="selected='Perempuan'; show=false"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        Perempuan
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tipe Pegawai -->
                    <div x-data="{ show: false, selected: '', focus: false }"
                        @keydown.escape.window="show = false"
                        class="relative input-wrapper">

                        <button
                            x-ref="trigger"
                            type="button"
                            @click="show = !show"
                            @focus="focus = true"
                            @blur="focus = false"
                            class="input-field flex justify-between items-center cursor-pointer">
                            <span :class="selected ? 'text-gray-900' : 'text-gray-500/90'" x-text="selected || ' '"></span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-150" :class="show ? 'rotate-180' : ''"></i>
                        </button>

                        <label
                            @click="$refs.trigger.click(); $refs.trigger.focus()"
                            class="input-label-btn"
                            :class="{ 'input-label-btn-selected': focus || selected, 'input-label-btn-focus': focus }">
                            Tipe Pegawai
                        </label>
                        @error('form.tipe_pegawai') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror

                        <div
                            x-show="show"
                            @click.outside="show = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg z-50 max-h-44 overflow-y-auto">
                            <ul class="text-sm py-1">
                                <li>
                                    <button type="button"
                                        @click="selected='Dosen'; show=false"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        Dosen
                                    </button>
                                </li>
                                <li>
                                    <button type="button"
                                        @click="selected='Staff'; show=false"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        Staff
                                    </button>
                                </li>
                                <li>
                                    <button type="button"
                                        @click="selected='Tendik'; show=false"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        Tendik
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== -->
            <!-- Tab: Alamat           -->
            <!-- ===================== -->
            <div x-show="tab === 'alamat'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Informasi Alamat</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                    <!-- Alamat (full width) -->
                    <div class="md:col-span-2">
                        <div class="input-wrapper group">
                            <input wire:model="form.alamat" type="text" id="floating_alamat" class="input-field peer" placeholder=" ">
                            <label for="floating_alamat" class="input-label">Alamat Lengkap</label>
                        </div>
                        @error('form.alamat') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <div class="input-wrapper group">
                            <input wire:model="form.alamat_domisili" type="text" id="alamat_domisili" class="input-field peer" placeholder=" ">
                            <label for="alamat_domisili" class="input-label">Alamat Domisili</label>
                        </div>
                        @error('form.alamat_domisili') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <div class="input-wrapper group">
                            <input wire:model="form.no_telpon" type="text" id="no_telpon" class="input-field peer" placeholder=" ">
                            <label for="no_telpon" class="input-label">Nomor Telpon</label>
                        </div>
                        @error('form.no_telpon') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <div class="input-wrapper group">
                            <input wire:model="form.email_yarsi" type="text" id="email_yarsi" class="input-field peer" placeholder=" ">
                            <label for="email_yarsi" class="input-label">Email Yarsi</label>
                        </div>
                        @error('form.email_yarsi') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- ===================== -->
            <!-- Tab: Keluarga         -->
            <!-- ===================== -->
            <div x-show="tab === 'keluarga'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Informasi Keluarga</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                    <div class="md:col-span-2">
                        <div class="input-wrapper group">
                            <input type="text" id="keluarga_nama" class="input-field peer" placeholder=" ">
                            <label for="keluarga_nama" class="input-label">Nama Kepala Keluarga</label>
                        </div>
                    </div>
                    <div>
                        <div class="input-wrapper group">
                            <input type="text" id="keluarga_status" class="input-field peer" placeholder=" ">
                            <label for="keluarga_status" class="input-label">Status Pernikahan</label>
                        </div>
                    </div>
                    <div>
                        <div class="input-wrapper group">
                            <input type="text" id="keluarga_jumlah_anak" class="input-field peer" placeholder=" ">
                            <label for="keluarga_jumlah_anak" class="input-label">Jumlah Anak</label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ===================== -->
            <!-- Tab: Rekening         -->
            <!-- ===================== -->
            <div x-show="tab === 'rekening'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Informasi Rekening</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                    <div class="md:col-span-2">
                        <div class="input-wrapper group">
                            <input type="text" id="rekening_nama_bank" class="input-field peer" placeholder=" ">
                            <label for="rekening_nama_bank" class="input-label">Nama Bank</label>
                        </div>
                    </div>
                    <div>
                        <div class="input-wrapper group">
                            <input type="text" id="rekening_no" class="input-field peer" placeholder=" ">
                            <label for="rekening_no" class="input-label">Nomor Rekening</label>
                        </div>
                    </div>
                    <div>
                        <div class="input-wrapper group">
                            <input type="text" id="rekening_nama_pemilik" class="input-field peer" placeholder=" ">
                            <label for="rekening_nama_pemilik" class="input-label">Nama Pemilik Rekening</label>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 shrink-0 bg-gray-50/60">

            <!-- Back button -->
            <button
                type="button"
                x-show="current > 0"
                x-transition
                @click="current--"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-sky-500 hover:bg-sky-600 rounded-md transition cursor-pointer">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Sebelumnya
            </button>
            <div x-show="current === 0" class="w-px"></div>

            <div class="flex items-center gap-2">
                <!-- Cancel -->
                <button
                    type="button"
                    @click="$dispatch('close-add-modal')"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-400 hover:bg-red-500 rounded-md transition cursor-pointer">
                    Batal
                </button>

                <!-- Next -->
                <button
                    type="button"
                    x-show="current < steps.length - 1"
                    x-transition
                    @click="current++"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-indigo-500 hover:bg-indigo-600 text-white rounded-md transition cursor-pointer">
                    Selanjutnya
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                <!-- Submit -->
                <button
                    type="submit"
                    x-show="current === steps.length - 1"
                    x-transition
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-emerald-500 hover:bg-emerald-600 text-white rounded-md transition cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Data
                </button>
            </div>
        </div>

    </form>
</div>
