<div class="relative bg-white w-[40vw] max-h-[68vh] mx-auto rounded-2xl shadow-2xl flex flex-col overflow-hidden">

    <!-- Header Modal -->
    <header
        class="relative flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-indigo-50 via-white to-blue-50">

        <!-- Decoration -->
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full blur-3xl"></div>
        </div>

        <!-- Header Information -->
        <div class="relative flex items-center gap-4">
            <div
                class="w-11 h-11 rounded-2xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-200">
                <i class="fa-solid fa-building-columns text-lg text-white"></i>
            </div>
            <div>
                <!-- Judul dinamis: berbeda untuk mode tambah dan mode edit -->
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    {{ $this->isEditMode ? 'Form Edit Rekening' : 'Form Tambah Rekening' }}
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $this->isEditMode ? 'Perbarui data rekening pegawai' : 'Lengkapi data rekening pegawai' }}
                </p>
            </div>
        </div>

        <!-- Tombol Tutup -->
        <div class="relative flex items-center gap-2">
            <button
                type="button"
                @click="$dispatch('close-rekening-modal')"
                wire:loading.attr="disabled"
                class="w-10 h-10 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </header>

    <!-- Form -->
    <form wire:submit.prevent="save" class="flex flex-col flex-1 min-h-0">

        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3">

            <!-- Nama Bank (Dropdown) -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Nama Bank <span class="text-red-500">*</span>
                </label>
                <select wire:model.live="form.nama_bank"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm bg-transparent">
                    <option value="" disabled>Pilih Bank</option>
                    @foreach ($bankOptions as $bank)
                        <option value="{{ $bank }}">{{ $bank }}</option>
                    @endforeach
                </select>
                @error('form.nama_bank')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor Rekening -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Nomor Rekening <span class="text-red-500">*</span>
                </label>
                <input type="text" inputmode="numeric"
                    wire:model.live.debounce.500ms="form.nomor_rekening"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                    placeholder="cth: 1234567890">
                @error('form.nomor_rekening')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Pemilik Rekening -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Nama Pemilik Rekening <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.live="form.nama_rekening"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400"
                    placeholder="cth: Ahmad Budiono">
                @error('form.nama_rekening')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan -->
            <div class="text-[11px] text-amber-600 bg-amber-50 rounded p-3 border border-amber-100 flex gap-2">
                <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                <p>Nama pemilik rekening harus sesuai dengan yang tertera pada buku tabungan / kartu ATM untuk
                    menghindari kegagalan transfer.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100 shrink-0 bg-gray-50/60">

            <!-- Indikator perubahan belum tersimpan -->
            @if ($this->isDirty)
                <span class="mr-auto text-xs text-amber-600 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Ada perubahan yang belum disimpan
                </span>
            @endif

            <!-- Batal -->
            <button
                type="button"
                @click="$dispatch('close-rekening-modal')"
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
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                    </path>
                </svg>
                <span wire:loading.remove wire:target="save">
                    {{ $this->isEditMode ? 'Simpan Perubahan' : 'Tambah Data' }}
                </span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
