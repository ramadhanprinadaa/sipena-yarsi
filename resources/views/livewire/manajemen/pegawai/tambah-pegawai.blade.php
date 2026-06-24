<div
    x-data="{
        steps: [
            { key: 'pegawai', label: 'Data Pegawai' },
            { key: 'biodata', label: 'Biodata' },
            { key: 'alamat', label: 'Kontak & Alamat' }
        ],
        current: @entangle('currentTab').live,
        get tab() { return this.steps[this.current].key },
        get label() { return this.steps[this.current].label }
    }"
    class="bg-white min-w-[45vw] min-h-[88vh] mx-auto rounded-xl shadow-2xl flex flex-col overflow-hidden"
>
    <!-- Header -->
    <header class="flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-indigo-50 via-white to-blue-50 mb-2">

        <!-- Decoration -->
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full blur-3xl"></div>
        </div>

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                <i class="fa-solid fa-user-plus text-base text-indigo-500"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    Form Tambah Pegawai
                </h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    <span>
                        Isi data pegawai baru secara lengkap
                    </span>
                </div>
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
    <div class="px-6 py-2 border-b border-gray-100 shrink-0">
        <div class="flex items-center gap-0">
            <template x-for="(step, index) in steps" :key="step.key">
                <div class="flex items-center" :class="index < steps.length - 1 ? 'flex-1' : ''">

                    <!-- Step Node -->
                    <button
                        type="button"
                        @click="if (index <= current) {
                            current = index
                        }"
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
                            x-text="step.label">
                        </span>

                    </button>

                    <!-- Connector -->
                    <template x-if="index < steps.length - 1">
                        <div
                            class="flex-1 h-px mx-2 mb-4 transition-colors duration-200"
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
        <div class="flex-1 overflow-y-auto px-6 py-4">

            <!-- Tab: Data Pegawai -->
            <div x-show="tab === 'pegawai'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mb-4">Identitas Pegawai</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 space-y-3">
                    <!-- Nama -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Nama Lengkap Pegawai <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                            wire:model.live.blur="form.nama" placeholder="cth: Fadil Jaidi">
                        @error('form.nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- NIK -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Nomor KTP / NIK <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                            wire:model.live.blur="form.ktp" placeholder="cth: 317203xxxxxxxxxxx">
                        @error('form.ktp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- NIP -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Nomor Induk Pegawai / NIP <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                            wire:model.live.blur="form.nip" placeholder="cth: 1502023xxxxxx">
                        @error('form.nip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- NPWP -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Nomor NPWP
                        </label>
                        <input type="text"
                            class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                            wire:model.live.blur="form.npwp" placeholder="cth: 317203xxxxxxxxxxx">
                        @error('form.npwp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mt-3 mb-4">Informasi Kepegawaian</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 space-y-3">

                    <!-- Unit Kerja -->
                    <div
                        x-data="{
                            show: false,
                            selected: @entangle('form.unit_kerja_id').live,
                            focus: false,
                            get selectedLabel() {
                                let data = {{ Js::from($unitKerja) }};
                                let found = data.find(u => u.id == this.selected);
                                return found ? found.name : '';
                            }
                        }"
                        @keydown.escape.window="show = false"
                        class="relative input-wrapper col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Unit Kerja <span class="text-red-500">*</span>
                        </label>
                        <button
                            type="button"
                            x-on:click="show = !show"
                            class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer"
                        >
                            <span :class="selectedLabel ? 'text-gray-900' : 'text-gray-500/90'" x-text="selectedLabel || 'Pilih Unit Kerja'"></span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400/90"></i>
                        </button>

                        @error('form.unit_kerja_id')
                            <div class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                        <div
                            x-show="show" @click.outside="show = false" x-transition
                            class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">
                            <ul class="text-sm py-1">
                                @foreach ($unitKerja as $unit)
                                    <li>
                                        <button type="button"
                                            @click="selected = {{ $unit->id }}; show=false"
                                            class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                            {{ $unit->name }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Jenis Pegawai -->
                    <div
                        x-data="{
                            show: false,
                            selected: @entangle('form.jenis_pegawai_id').live,
                            focus: false,
                            get selectedLabel() {
                                let data = {{ Js::from($jenisPegawai) }};
                                let found = data.find(u => u.id == this.selected);
                                return found ? found.jenis : '';
                            }
                        }"
                        @keydown.escape.window="show = false"
                        class="relative input-wrapper">
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Jenis Pegawai <span class="text-red-500">*</span>
                        </label>

                        <button
                            type="button"
                            x-on:click="show = !show"
                            class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer"
                        >
                            <span :class="selectedLabel ? 'text-gray-900' : 'text-gray-500/90'" x-text="selectedLabel || 'Pilih Jenis Pegawai'"></span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400/90"></i>
                        </button>

                        @error('form.jenis_pegawai_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                        <div
                            x-show="show" @click.outside="show = false" x-transition
                            class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">
                            <ul class="text-sm py-1">
                                @foreach ($jenisPegawai as $jenis)
                                    <li>
                                        <button type="button"
                                            @click="selected = {{ $jenis->id }};
                                            show=false"
                                            class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                            {{ $jenis->jenis }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Status Pegawai -->
                    <div
                        x-data="{
                            show: false,
                            selected: @entangle('form.status_pegawai_id').live,
                            focus: false,
                            get selectedLabel() {
                                let data = {{ Js::from($statusPegawai) }};
                                let found = data.find(u => u.id == this.selected);
                                return found ? found.status : '';
                            }
                        }"
                        @keydown.escape.window="show = false"
                        class="relative input-wrapper">
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Status Pegawai <span class="text-red-500">*</span>
                        </label>

                        <button
                            type="button"
                            x-on:click="show = !show"
                            class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer"
                        >
                            <span :class="selectedLabel ? 'text-gray-900' : 'text-gray-500/90 italic'" x-text="selectedLabel || 'Pilih Status Pegawai'"></span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400/90"></i>
                        </button>

                        @error('form.status_pegawai_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                        <div
                            x-show="show" @click.outside="show = false" x-transition
                            class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">
                            <ul class="text-sm py-1">
                                @foreach ($statusPegawai as $status)
                                    <li>
                                        <button type="button"
                                            @click="selected = {{ $status->id }};
                                            show=false"
                                            class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                            {{ $status->status }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Biodata Pegawai  -->
            <div x-show="tab === 'biodata'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mb-4">Data Pribadi</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 space-y-3">
                    <!-- Gelar Depan -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Gelar Depan
                        </label>
                        <input wire:model.live.blur="form.gelar_depan" type="text" id="floating_gelar_depan" class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400" placeholder="cth: Prof. Dr.">
                        @error('form.gelar_depan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <!-- Gelar Belakang -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Gelar Depan
                        </label>
                        <input wire:model.live.blur="form.gelar_belakang" type="text" id="floating_gelar_belakang" class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400" placeholder="cth: S.Kom, M.Kom">
                        @error('form.gelar_belakang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <!-- Tempat Lahir -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Tempat Lahir <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.live.blur="form.tempat_lahir" type="text" id="floating_tempat_lahir" class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400" placeholder="cth: Jakarta">
                        @error('form.tempat_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <!-- Tanggal Lahir -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <div
                            x-data="{ picker: null }"
                            x-init="picker = new Datepicker($refs.input, {
                                format: 'dd/mm/yyyy',
                                autohide: true,
                                language: 'id'
                            });

                            $refs.input.addEventListener('changeDate', () => {
                                $wire.set('form.tanggal_lahir', $refs.input.value);
                            });"
                            class="relative"
                        >
                            <!-- Icon -->
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                            </div>
                            <!-- Input -->
                            <input
                                wire:model.defer="form.tanggal_lahir"
                                type="text"
                                x-ref="input"
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
                        @error('form.tanggal_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <!-- Jenis Kelamin -->
                    <div
                        x-data="{
                            show: false,
                            selected: @entangle('form.jenis_kelamin').live,
                            focus: false,
                            get label() {
                                return this.selected === 'L'
                                    ? 'Laki-Laki'
                                    : this.selected === 'P'
                                        ? 'Perempuan'
                                        : '';
                            }
                        }"
                        @keydown.escape.window="show = false"
                        class="relative input-wrapper">

                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>

                        <button
                            type="button"
                            x-on:click="show = !show"
                            class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer">

                            <span :class="label ? 'text-gray-900' : 'text-gray-500/90'" x-text="label || 'Pilih Jenis Kelamin'"></span>

                            <i class="fa-solid fa-chevron-down text-xs text-gray-400/90"></i>
                        </button>

                        @error('form.jenis_kelamin')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror

                        <div
                            x-show="show"
                            @click.outside="show = false"
                            x-transition
                            class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">

                            <ul class="text-sm py-1">

                                <li>
                                    <button type="button"
                                        @click="selected = 'L'; show = false"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700">
                                        Laki-Laki
                                    </button>
                                </li>

                                <li>
                                    <button type="button"
                                        @click="selected = 'P'; show = false"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700">
                                        Perempuan
                                    </button>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>

                <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mt-3 mb-4">Masa Kerja</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                    <!-- Tanggal Bergabung -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Tanggal Bergabung <span class="text-red-500">*</span>
                        </label>
                        <div x-data="{ picker: null }"
                            x-init="picker = new Datepicker($refs.input, {
                                format: 'dd/mm/yyyy',
                                autohide: true,
                                language: 'id'
                            });

                            $refs.input.addEventListener('changeDate', () => {
                                $wire.set('form.tanggal_bergabung', $refs.input.value);
                            });"
                            class="relative"
                        >
                            <!-- Icon -->
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                            </div>
                            <!-- Input -->
                            <input
                                wire:model.defer="form.tanggal_bergabung"
                                type="text"
                                x-ref="input"
                                placeholder="Pilih Tanggal Bergabung"
                                class="w-full h-9 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-0 transition-colors duration-200 placeholder:italic" />
                            <button type="button" x-show="$wire.form.tanggal_bergabung"
                                @click="
                                $wire.set('form.tanggal_bergabung', null);
                                picker.setDate({ clear: true });
                                $refs.input.value = '';"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>

                        </div>
                        @error('form.tanggal_bergabung') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @switch($form['status_pegawai_id'])
                        @case(1)
                            <!-- Tanggal Pensiun -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Tanggal Pensiun <span class="text-red-500">*</span>
                                </label>
                                <div
                                    x-data="{ picker: null }"
                                    x-init="picker = new Datepicker($refs.input, {
                                        format: 'dd/mm/yyyy',
                                        autohide: true,
                                        language: 'id'
                                    });

                                    $refs.input.addEventListener('changeDate', () => {
                                        $wire.set('form.tanggal_pensiun', $refs.input.value);
                                    });"
                                    class="relative"
                                >
                                    <!-- Icon -->
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                                    </div>
                                    <!-- Input -->
                                    <input
                                        wire:model.defer="form.tanggal_pensiun"
                                        type="text"
                                        x-ref="input"
                                        placeholder="Pilih Tanggal Pensiun"
                                        class="w-full h-9 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-0 transition-colors duration-200 placeholder:italic" />
                                    <button type="button" x-show="$wire.form.tanggal_pensiun"
                                        @click="
                                        $wire.set('form.tanggal_pensiun', null);
                                        picker.setDate({ clear: true });
                                        $refs.input.value = '';"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                                        <i class="fa-solid fa-xmark text-sm"></i>
                                    </button>

                                </div>
                                @error('form.tanggal_pensiun') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @break
                        @case(2)
                            <!-- Tanggal Habis Kontrak -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Tanggal Habis Kontrak <span class="text-red-500">*</span>
                                </label>
                                <div
                                    x-data="{ picker: null }"
                                    x-init="picker = new Datepicker($refs.input, {
                                        format: 'dd/mm/yyyy',
                                        autohide: true,
                                        language: 'id'
                                    });

                                    $refs.input.addEventListener('changeDate', () => {
                                        $wire.set('form.tanggal_habis_kontrak', $refs.input.value);
                                    });"
                                    class="relative"
                                >
                                    <!-- Icon -->
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                                    </div>
                                    <!-- Input -->
                                    <input
                                        wire:model.defer="form.tanggal_habis_kontrak"
                                        type="text"
                                        x-ref="input"
                                        placeholder="Pilih Tanggal Habis Kontrak"
                                        class="w-full h-9 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-0 transition-colors duration-200 placeholder:italic" />
                                    <button type="button" x-show="$wire.form.tanggal_habis_kontrak"
                                        @click="
                                        $wire.set('form.tanggal_habis_kontrak', null);
                                        picker.setDate({ clear: true });
                                        $refs.input.value = '';"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                                        <i class="fa-solid fa-xmark text-sm"></i>
                                    </button>
                                </div>
                                @error('form.tanggal_habis_kontrak') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @break
                    @endswitch
                </div>
            </div>

            <!-- Tab: Alamat Pegawai   -->
            <div x-show="tab === 'alamat'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mb-4">Kontak</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 space-y-3">

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Nomor Telepon
                        </label>
                        <input
                            wire:model.live.blur="form.no_telpon"
                            type="text" id="no_telpon"
                            class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400" placeholder="cth: 0812xxxxxxxx">
                        @error('form.no_telpon') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Email Yarsi
                        </label>
                        <input wire:model.live.blur="form.email_yarsi" type="text" id="email_yarsi" class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400" placeholder="cth: budi@yarsi.ac.id">
                        @error('form.email_yarsi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mt-3 mb-4">Alamat</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 space-y-3">

                    <!-- Alamat  -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Alamat Lengkap
                        </label>
                        <input wire:model.live.blur="form.alamat_ktp" type="text" id="floating_alamat" class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400" placeholder="cth: Jl. Ngawi Selatan No. 123">
                        @error('form.alamat_ktp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Alamat Domisili
                        </label>
                        <input wire:model.live.blur="form.alamat_domisili" type="text" id="alamat_domisili" class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400" placeholder="cth: Jl. Ngawi Timur No. 135">
                        @error('form.alamat_domisili') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
                <template x-if="current < steps.length - 1">
                    <button
                        type="button"
                        wire:click="nextStep"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-indigo-500 hover:bg-indigo-600 text-white rounded-md transition cursor-pointer">

                        <!-- Loading -->
                        <svg aria-hidden="true" wire:loading wire:target="nextStep"
                            class="w-4 h-4 self-center text-neutral-quaternary animate-spin fill-brand" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>
                        <span class="sr-only">Loading...</span>

                        Selanjutnya
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </template>

                <!-- Submit -->
                <template x-if="current === steps.length - 1">
                    <button
                        type="submit"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-emerald-500 hover:bg-emerald-600 text-white rounded-md transition cursor-pointer">

                        <!-- Loading -->
                        <svg aria-hidden="true"
                            wire:loading wire:target="save" class="w-4 h-4 text-neutral-quaternary animate-spin fill-brand" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Loading...</span>

                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        Simpan Data
                    </button>
                </template>
            </div>
        </div>
    </form>
</div>
