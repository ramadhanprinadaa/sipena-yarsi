<div class="relative bg-white min-w-4xl max-h-[88vh] mx-auto rounded-2xl shadow-2xl flex flex-col overflow-hidden">

    <!-- Header -->
    <header class="relative flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-indigo-50 via-white to-blue-50">

        <!-- Decoration -->
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full blur-3xl"></div>
        </div>

        <!-- Header Information -->
        <div class="relative flex items-center gap-4">
            <div class="w-11 h-11 rounded-2xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-200">
                <i class="fa-solid fa-id-card-clip text-lg text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    Form Edit Biodata
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    Edit Biodata {{ $nama_pegawai }} - {{ $nik_pegawai }}
                </p>
            </div>
        </div>

        <div class="relative flex items-center gap-2">
            <!-- Close Button -->
            <button type="button" @click="$dispatch('close-edit-biodata-modal')" wire:loading.attr="disabled"
                class="w-10 h-10 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </header>

    <!-- Form -->
    <form wire:submit.prevent="save" class="flex flex-col flex-1 min-h-0">

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto px-6 py-4">

            <!-- Data Tidak Dapat Diubah -->
            <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mb-4">Identitas Pegawai (Tidak Dapat Diubah)</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 mb-3">
                <!-- Nama (read-only) -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nama Lengkap Pegawai</label>
                    <input type="text" value="{{ $nama_pegawai }}" disabled
                        class="w-full border-0 rounded-none shadow-none border-b-2 border-gray-200 bg-transparent py-2 px-0 text-sm text-gray-500 cursor-not-allowed">
                </div>

                <!-- NIP (read-only) -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Induk Pegawai / NIP</label>
                    <input type="text" value="{{ $nik_pegawai }}" disabled
                        class="w-full border-0 rounded-none shadow-none border-b-2 border-gray-200 bg-transparent py-2 px-0 text-sm text-gray-500 cursor-not-allowed">
                </div>
            </div>

            <!-- Data Identitas Lain -->
            <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mt-3 mb-4">Identitas Lainnya</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 space-y-3">
                <!-- NIK / KTP -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nomor KTP / NIK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model.live.blur="form.ktp"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: 317203xxxxxxxxxxx">
                    @error('form.ktp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- NPWP -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nomor NPWP</label>
                    <input type="text" wire:model.live.blur="form.npwp"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: 317203xxxxxxxxxxx">
                    @error('form.npwp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Gelar Depan -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Gelar Depan</label>
                    <input type="text" wire:model.live.blur="form.gelar_depan"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: Dr.">
                    @error('form.gelar_depan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Gelar Belakang -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Gelar Belakang</label>
                    <input type="text" wire:model.live.blur="form.gelar_belakang"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: S.Kom., M.T.">
                    @error('form.gelar_belakang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Informasi Kepegawaian -->
            <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mt-3 mb-4">Informasi Kepegawaian</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 space-y-3">

                <!-- Unit Kerja -->
                <div
                    x-data="{
                        show: false,
                        selected: @entangle('form.unit_kerja_id').live,
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
                    <button type="button" x-on:click="show = !show"
                        class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer">
                        <span :class="selectedLabel ? 'text-gray-900' : 'text-gray-500/90'" x-text="selectedLabel || 'Pilih Unit Kerja'"></span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400/90"></i>
                    </button>
                    @error('form.unit_kerja_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div x-show="show" @click.outside="show = false" x-transition
                        class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">
                        <ul class="text-sm py-1">
                            @foreach ($unitKerja as $unit)
                                <li>
                                    <button type="button" @click="selected = {{ $unit->id }}; show = false"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        {{ $unit->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Unit / Bagian -->
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Unit / Bagian</label>
                    <input type="text" wire:model.live.blur="form.unit_bagian"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: Bagian Akademik">
                    @error('form.unit_bagian') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jenis Pegawai -->
                <div
                    x-data="{
                        show: false,
                        selected: @entangle('form.jenis_pegawai_id').live,
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
                    <button type="button" x-on:click="show = !show"
                        class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer">
                        <span :class="selectedLabel ? 'text-gray-900' : 'text-gray-500/90'" x-text="selectedLabel || 'Pilih Jenis Pegawai'"></span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400/90"></i>
                    </button>
                    @error('form.jenis_pegawai_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div x-show="show" @click.outside="show = false" x-transition
                        class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">
                        <ul class="text-sm py-1">
                            @foreach ($jenisPegawai as $jenis)
                                <li>
                                    <button type="button" @click="selected = {{ $jenis->id }}; show = false"
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
                    <button type="button" x-on:click="show = !show"
                        class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer">
                        <span :class="selectedLabel ? 'text-gray-900' : 'text-gray-500/90'" x-text="selectedLabel || 'Pilih Status Pegawai'"></span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400/90"></i>
                    </button>
                    @error('form.status_pegawai_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div x-show="show" @click.outside="show = false" x-transition
                        class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">
                        <ul class="text-sm py-1">
                            @foreach ($statusPegawai as $status)
                                <li>
                                    <button type="button" @click="selected = {{ $status->id }}; show = false"
                                        class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        {{ $status->status }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div
                    x-data="{
                        show: false,
                        selected: @entangle('form.jenis_kelamin').live,
                        get selectedLabel() {
                            return this.selected === 'L' ? 'Laki-Laki' : (this.selected === 'P' ? 'Perempuan' : '');
                        }
                    }"
                    @keydown.escape.window="show = false"
                    class="relative input-wrapper">
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <button type="button" x-on:click="show = !show"
                        class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer">
                        <span :class="selectedLabel ? 'text-gray-900' : 'text-gray-500/90'" x-text="selectedLabel || 'Pilih Jenis Kelamin'"></span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400/90"></i>
                    </button>
                    @error('form.jenis_kelamin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div x-show="show" @click.outside="show = false" x-transition
                        class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">
                        <ul class="text-sm py-1">
                            <li>
                                <button type="button" @click="selected = 'L'; show = false"
                                    class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700">
                                    Laki-Laki
                                </button>
                            </li>
                            <li>
                                <button type="button" @click="selected = 'P'; show = false"
                                    class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700">
                                    Perempuan
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Tempat Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model.live.blur="form.tempat_lahir"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: Jakarta">
                    @error('form.tempat_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <div x-data="{ picker: null }"
                        x-init="picker = new Datepicker($refs.input, { format: 'dd/mm/yyyy', autohide: true, language: 'id' });
                            $refs.input.addEventListener('changeDate', () => { $wire.set('form.tanggal_lahir', $refs.input.value); });"
                        class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                        </div>
                        <input wire:model="form.tanggal_lahir" type="text" x-ref="input" placeholder="Pilih Tanggal Lahir"
                            class="w-full h-9 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-0 transition-colors duration-200 placeholder:italic">
                    </div>
                    @error('form.tanggal_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Masa Kerja -->
            <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mt-3 mb-4">Masa Kerja</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                <!-- Tanggal Bergabung -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Tanggal Bergabung <span class="text-red-500">*</span>
                    </label>
                    <div x-data="{ picker: null }"
                        x-init="picker = new Datepicker($refs.input, { format: 'dd/mm/yyyy', autohide: true, language: 'id' });
                            $refs.input.addEventListener('changeDate', () => { $wire.set('form.tanggal_bergabung', $refs.input.value); });"
                        class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                        </div>
                        <input wire:model="form.tanggal_bergabung" type="text" x-ref="input" placeholder="Pilih Tanggal Bergabung"
                            class="w-full h-9 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-0 transition-colors duration-200 placeholder:italic">
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
                            <div x-data="{ picker: null }"
                                x-init="picker = new Datepicker($refs.input, { format: 'dd/mm/yyyy', autohide: true, language: 'id' });
                                    $refs.input.addEventListener('changeDate', () => { $wire.set('form.tanggal_pensiun', $refs.input.value); });"
                                class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                                </div>
                                <input wire:model="form.tanggal_pensiun" type="text" x-ref="input" placeholder="Pilih Tanggal Pensiun"
                                    class="w-full h-9 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-0 transition-colors duration-200 placeholder:italic">
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
                            <div x-data="{ picker: null }"
                                x-init="picker = new Datepicker($refs.input, { format: 'dd/mm/yyyy', autohide: true, language: 'id' });
                                    $refs.input.addEventListener('changeDate', () => { $wire.set('form.tanggal_habis_kontrak', $refs.input.value); });"
                                class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                                </div>
                                <input wire:model="form.tanggal_habis_kontrak" type="text" x-ref="input" placeholder="Pilih Tanggal Habis Kontrak"
                                    class="w-full h-9 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-0 transition-colors duration-200 placeholder:italic">
                            </div>
                            @error('form.tanggal_habis_kontrak') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        @break
                @endswitch
            </div>

            <!-- Kontak & Alamat -->
            <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mt-3 mb-4">Kontak</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 space-y-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Telepon</label>
                    <input type="text" wire:model.live.blur="form.no_telpon"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: 0812xxxxxxxx">
                    @error('form.no_telpon') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email Yarsi</label>
                    <input type="text" wire:model.live.blur="form.email_yarsi"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: budi@yarsi.ac.id">
                    @error('form.email_yarsi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mt-3 mb-4">Alamat</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 space-y-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Alamat Lengkap (KTP)</label>
                    <input type="text" wire:model.live.blur="form.alamat_ktp"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: Jl. Ngawi Selatan No. 123">
                    @error('form.alamat_ktp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Alamat Domisili</label>
                    <input type="text" wire:model.live.blur="form.alamat_domisili"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                        placeholder="cth: Jl. Ngawi Timur No. 135">
                    @error('form.alamat_domisili') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Status Akun -->
            <p class="text-xs font-medium text-indigo-400 uppercase tracking-wider mt-3 mb-4">Status Akun</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Status Database Pegawai <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="form.status"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm bg-transparent">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                    @error('form.status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100 shrink-0 bg-gray-50/60">

            <!-- Indikator perubahan belum tersimpan -->
            @if($this->isDirty)
                <span class="mr-auto text-xs text-amber-600 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Ada perubahan yang belum disimpan
                </span>
            @endif

            <!-- Batal -->
            <button type="button" @click="$dispatch('close-edit-biodata-modal')"
                class="px-4 py-2 text-sm font-medium text-white bg-red-400 hover:bg-red-500 rounded-md transition cursor-pointer">
                Batal
            </button>

            <!-- Simpan (disabled ketika form belum berubah) -->
            <button type="button"
                x-on:click="showLoading = true; $wire.save().finally(() => setTimeout(() => showLoading = false, 500))"
                @disabled($errors->any() || !$this->isDirty)
                wire:loading.attr="disabled"
                wire:target="save"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-emerald-500 hover:bg-emerald-600 text-white rounded-md transition cursor-pointer disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-emerald-500">

                <!-- Loading spinner -->
                <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
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
    </form>
</div>