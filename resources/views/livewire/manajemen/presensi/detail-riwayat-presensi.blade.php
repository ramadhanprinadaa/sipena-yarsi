<div x-data="{ showLoading: false }"
    class="relative bg-white w-3xl h-[90vh] mx-auto rounded-2xl shadow-2xl flex flex-col overflow-hidden">

    {{-- Header --}}
    <header
        class="flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-indigo-50 via-white to-blue-50 ">

        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full blur-3xl"></div>
        </div>

        {{-- Header Information --}}
        <div class="relative flex items-center gap-4">
            <div
                class="w-11 h-11 rounded-2xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-200">
                <i class="fa-solid fa-calendar-check text-white text-xl"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    Detail Presensi
                </h2>

                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    <i class="fa-regular fa-calendar"></i>

                    <span>
                        {{ $presensi?->tanggal?->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Button Edit dan Button Close --}}
        <div class="relative flex items-center gap-2">

            @php
                $user = auth()->user();
            @endphp

            @if (!$user->hasRole('SDM Universitas') && !$user->hasRole('Pimpinan'))
                @if (!$isEdit)
                    <button type="button"
                        x-on:click="showLoading = true; $wire.edit().finally(() => setTimeout(() => showLoading = false, 500))"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-all duration-200 cursor-pointer border border-indigo-100">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Edit Data
                    </button>
                @else
                    <button type="button"
                        x-on:click="showLoading = true; $wire.cancelEdit().finally(() => setTimeout(() => showLoading = false, 500))"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 cursor-pointer">
                        <i class="fa-solid fa-rotate-left"></i>
                        Batal
                    </button>
                @endif
            @endif

            <button type="button" @click="$dispatch('close-detail-riwayat')" wire:loading.attr="disabled"
                class="w-10 h-10 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200 cursor-pointer">

                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </header>


    {{-- Content --}}
    <div class="relative flex-1 overflow-y-auto overflow-x-hidden p-4">

        {{-- Loading Switch Mode --}}
        <div x-show="showLoading" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
            style="display: none;">

            <div class="bg-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3 border border-slate-100">
                <div class="w-6 h-6 border-2 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
                <span class="text-sm font-semibold text-slate-700 tracking-wide">Memuat Data...</span>
            </div>

            {{-- <div class="flex flex-col items-center gap-4">
                <div class="w-10 h-10 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                <div class="text-sm font-medium tracking-wide text-white">
                    Memuat Data...
                </div>
            </div> --}}
        </div>

        @if (!$isEdit)
            {{-- ========================= MODE DETAIL ========================= --}}
            <div class="min-h-full flex flex-col gap-3">

                <div class="flex-1 flex flex-col gap-3">
                    {{-- Pegawai + Status --}}
                    <div class="grid grid-cols-10 gap-3 flex-1">

                        <div class="col-span-6 bg-white border border-slate-200 rounded-lg p-4">
                            <p class="text-xs font-medium uppercase text-slate-400 mb-1">
                                Nama Pegawai
                            </p>

                            <h3 class="text-md font-semibold text-slate-800 leading-tight">
                                {{ $presensi?->pegawai?->nama ?? '-' }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ $presensi?->pegawai_nip }}
                            </p>
                        </div>

                        @if ($presensi?->statusKehadiran)
                            <div class="col-span-4 border rounded-lg p-4"
                                style="
                                background-color: {{ $presensi->statusKehadiran?->warna }}10;
                                border-color: {{ $presensi->statusKehadiran?->warna }}30;
                            ">

                                <p class="text-xs font-medium uppercase text-slate-500 mb-1">
                                    Status Kehadiran
                                </p>

                                <p class="text-md font-semibold"
                                    style="color: {{ $presensi->statusKehadiran?->warna }}">
                                    {{ $presensi->statusKehadiran->status }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Jam Presensi --}}
                    <div class="grid grid-cols-3 gap-3 flex-1">

                        <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                            <p class="text-xs font-medium uppercase text-green-700/70 mb-1">
                                Jam Masuk
                            </p>

                            <h3 class="text-lg font-bold text-green-800">
                                {{ $presensi?->jam_masuk?->translatedFormat('H:i') ?? '--:--' }}
                            </h3>
                        </div>

                        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                            <p class="text-xs font-medium uppercase text-red-700/70 mb-1">
                                Jam Keluar
                            </p>

                            <h3 class="text-lg font-bold text-red-800">
                                {{ $presensi?->jam_keluar?->translatedFormat('H:i') ?? '--:--' }}
                            </h3>
                        </div>

                        <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                            <p class="text-xs font-medium uppercase text-indigo-700/70 mb-1">
                                Total Jam
                            </p>

                            <h3 class="text-lg font-bold text-indigo-800">
                                {{ $presensi?->total_jam_kerja ?? '--:--' }}
                            </h3>
                        </div>
                    </div>
                </div>

                {{-- Informasi Sistem --}}
                <div class="bg-white border border-slate-200 rounded-lg p-4">

                    <h3 class="text-sm uppercase font-semibold text-slate-700 mb-3">
                        Informasi Sistem
                    </h3>

                    <div class="grid grid-cols-2 gap-3">

                        <div>
                            <p class="text-xs text-slate-400 mb-1">
                                Dibuat Pada
                            </p>

                            <p class="text-sm text-slate-700">
                                {{ $presensi?->created_at?->translatedFormat('d M Y | H:i') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400 mb-1">
                                Dibuat Oleh
                            </p>

                            <p class="text-sm text-slate-700">
                                {{ $presensi?->creator?->pegawai?->nama ?? ($presensi?->creator?->username ?? 'Sistem') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400 mb-1">
                                Diperbarui Pada
                            </p>

                            <p class="text-sm text-slate-700">
                                {{ $presensi?->updated_at?->translatedFormat('d M Y | H:i') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400 mb-1">
                                Diperbarui Oleh
                            </p>

                            <p class="text-sm text-slate-700">
                                {{ $presensi?->user?->pegawai?->nama ?? ($presensi?->user?->username ?? 'Sistem') }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <p class="text-xs text-slate-400 mb-1">
                                Keterangan Perubahan Data
                            </p>

                            <p class="text-sm leading-relaxed text-slate-600 break-all">
                                {{ $presensi?->latestLog?->keterangan ?? 'Belum ada keterangan perubahan presensi.' }}
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        @else
            {{-- ========================= MODE EDIT ========================= --}}
            <div class="h-full flex flex-col gap-4">

                {{-- Informasi Pegawai Ringkas --}}
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <p class="text-xs uppercase font-medium text-slate-400 mb-1">
                        Edit Presensi Pegawai
                    </p>

                    <h3 class="text-md font-semibold text-slate-800">
                        {{ $presensi?->pegawai?->nama ?? '-' }}
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        {{ $presensi?->tanggal?->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                {{-- Form Jam --}}
                <div class="grid grid-cols-2 gap-4 bg-white border border-slate-200 rounded-lg p-4">

                    <div>
                        <label class="block text-xs uppercase font-medium text-slate-500 mb-1">
                            Jam Masuk
                        </label>

                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <input type="time" wire:model.live="form.jam_masuk"
                                class="block w-full p-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"
                                min="00:00" max="23:59" value="00:00" required />
                            @error('form.jam_masuk')
                                <span class="text-xs text-red-500 mt-1 block">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs uppercase font-medium text-slate-500 mb-1">
                            Jam Keluar
                        </label>

                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <input type="time" wire:model.live="form.jam_keluar"
                                class="block w-full p-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"
                                min="00:00" max="23:59" value="00:00" required />
                            @error('form.jam_keluar')
                                <span class="text-xs text-red-500 mt-1 block">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                </div>

                {{-- Keterangan --}}
                <div class="bg-white border border-slate-200 rounded-lg p-4 min-h-0 flex flex-col flex-1">

                    <label class="block text-xs font-medium text-slate-500 mb-2 shrink-0">
                        <span class="uppercase mr-2">
                            Keterangan Perubahan
                        </span>
                        <span class="text-red-500 text-[11px]">
                            *Wajib diisi jika terjadi perubahan data
                        </span>
                    </label>

                    <textarea wire:model.live="form.keterangan" rows="5" placeholder="Tambahkan keterangan perubahan..."
                        class="w-full flex-1 min-h-0 resize-none rounded-md border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </textarea>

                    @error('form.keterangan')
                        <span class="text-xs text-red-500 mt-1 block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Footer --}}
                <div class="border-t border-slate-100 p-3 flex items-center justify-end gap-3 shrink-0">

                    <button
                        x-on:click="
                            showLoading = true; $wire.cancelEdit().finally(() => setTimeout(() => showLoading = false, 500))
                        "
                        class="px-5 py-2.5 rounded-md border border-slate-200 text-gray-600 bg-gray-100 hover:bg-gray-200 transition cursor-pointer">
                        Batal
                    </button>

                    <button
                        x-on:click="
                            showLoading = true; $wire.save().finally(() => setTimeout(() => showLoading = false, 500))
                        "
                        wire:loading.attr="disabled" @disabled(!$this->isDirty)
                        class="px-5 py-2.5 rounded-md text-white font-medium transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed {{ $this->isDirty
                            ? 'bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 cursor-pointer'
                            : 'bg-indigo-500/80 cursor-not-allowed' }}">
                        <i wire:loading.remove wire:target="save" class="fa-solid fa-floppy-disk text-sm mr-1"></i>

                        <!-- Loading spinner -->
                        <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                            </path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
