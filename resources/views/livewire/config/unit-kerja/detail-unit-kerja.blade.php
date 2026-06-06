<div x-data="{ showLoading: false }"
    class="relative bg-white min-w-2xl min-h-[68vh] mx-auto rounded-2xl shadow-2xl flex flex-col overflow-hidden">

    {{-- ═══════════════════════════ HEADER ════════════════════════════════ --}}
    <header
        class="flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-indigo-50 via-white to-blue-50">

        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full blur-3xl"></div>
        </div>

        {{-- Header Information --}}
        <div class="relative flex items-center gap-4">
            <div
                class="w-11 h-11 rounded-2xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-200">
                <i class="fa-solid fa-building-circle-check text-lg text-white"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    Detail Unit Kerja
                </h2>

                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    @if ($unit)
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $editMode ? 'Mode Edit — ubah data lalu simpan' : 'Informasi unit kerja' }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="relative flex items-center gap-2">

            {{-- Button Edit dan Cancel --}}
            @if ($unit)

                @if (!$editMode)
                    <button type="button"
                        x-on:click="showLoading = true; $wire.enterEditMode().finally(() => setTimeout(() => showLoading = false, 500))"
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

            {{-- Close Button --}}
            <button type="button" @click="$dispatch('close-detail')" wire:loading.attr="disabled"
                class="w-10 h-10 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </header>

    {{-- Content --}}
    <div class="relative flex-1 overflow-y-auto overflow-x-hidden p-6">
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
        </div>

        @if (!$unit)
            {{-- Skeleton fallback — tidak seharusnya tampil karena modal baru
                 terbuka setelah data siap, namun dipertahankan sebagai guard. --}}
            <div class="space-y-4 animate-pulse">
                <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-100 rounded w-1/2"></div>
                <div class="h-4 bg-gray-100 rounded w-2/3"></div>
            </div>
        @elseif (!$editMode)
            {{-- ─────────────────── MODE READ ─────────────────── --}}
            <div class="space-y-4">

                {{-- Nama Unit Kerja --}}
                <div class="flex gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        @php
                            $initials = collect(explode(' ', $unit->name))
                                ->take(3)
                                ->map(fn($w) => strtoupper($w[0]))
                                ->implode('');
                        @endphp
                        <span class="text-indigo-600 text-xs font-bold">{{ $initials }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Nama Unit Kerja</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $unit->name }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Unit SDM --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Unit SDM</p>
                        @if ($unit->unit_sdm_id == 1)
                            <span
                                class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-violet-100 text-violet-700 rounded-md">
                                SDM Yayasan
                            </span>
                        @elseif ($unit->unit_sdm_id == 2)
                            <span
                                class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-md">
                                SDM Universitas
                            </span>
                        @else
                            <span class="text-sm text-gray-400 italic">—</span>
                        @endif
                    </div>

                    {{-- Unit Induk --}}
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Unit Induk</p>
                        @if ($unit->parent)
                            <p class="text-sm text-gray-700">{{ $unit->parent->name }}</p>
                        @else
                            <p class="text-sm text-gray-400 italic">Tidak ada unit induk</p>
                        @endif
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Pimpinan --}}
                <div>
                    <p class="text-xs text-gray-400 mb-2">Pimpinan Unit</p>
                    @if ($unit->pimpinan)
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                @php $pNama = explode(' ', $unit->pimpinan->nama); @endphp
                                <span class="text-amber-600 text-xs font-bold">
                                    {{ strtoupper(substr($pNama[0], 0, 1) . ($pNama[1][0] ?? '')) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $unit->pimpinan->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $unit->pimpinan->nip }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Belum ditentukan</p>
                    @endif
                </div>
            </div>
        @else
            {{-- ─────────────────── MODE EDIT ─────────────────── --}}
            <div class="space-y-4">

                {{-- Nama Unit Kerja --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Unit Kerja <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model.live="form.name" placeholder="Nama Unit Kerja"
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm bg-transparent transition-colors duration-150
                               @error('form.name') border-red-400 @enderror">
                    @error('form.nama_unit')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Unit SDM --}}
                <div x-data="{ show: false, selected: @entangle('form.unit_sdm_id').live }" class="relative">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Unit SDM <span class="text-red-500">*</span>
                    </label>
                    <button type="button" @click="show = !show"
                        class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer transition-colors duration-150
                               @error('form.unit_sdm_id') border-red-400 @enderror">
                        <span :class="selected ? 'text-gray-900' : 'text-gray-500/90'"
                            x-text="
                                selected == 1 ? 'SDM Yayasan' :
                                selected == 2 ? 'SDM Universitas' :
                                'Pilih Unit SDM'
                            ">
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400" :class="show ? 'rotate-180' : ''"
                            style="transition: transform 0.15s"></i>
                    </button>
                    @error('form.unit_sdm_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    <div x-show="show" @click.outside="show = false" x-transition
                        class="absolute mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-50">
                        <ul class="text-sm py-1">
                            <li>
                                <button type="button" @click="selected = '1'; show = false"
                                    wire:click="$set('form.unit_sdm_id', 1)"
                                    class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                    SDM Yayasan
                                </button>
                            </li>
                            <li>
                                <button type="button" @click="selected = '2'; show = false"
                                    wire:click="$set('form.unit_sdm_id', 2)"
                                    class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                    SDM Universitas
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Pimpinan --}}
                <div class="relative">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Pimpinan Unit
                    </label>
                    <div class="flex gap-2">
                        <input type="text" wire:model.live.debounce.500ms="pimpinanSearch"
                            wire:blur="restorePimpinan" placeholder="Cari dan Pilih dari Daftar Pegawai yang Tersedia"
                            class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400
                                   @error('form.pimpinan_id') border-red-400 @enderror">
                        @if ($form['pimpinan_id'])
                            <button wire:click="removePimpinan" type="button"
                                class="mt-1 px-3 hover:bg-red-50 rounded border border-red-200 cursor-pointer">
                                <i class="fa-solid fa-trash text-red-500"></i>
                            </button>
                        @endif
                    </div>
                    @error('form.pimpinan_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    @if ($pimpinanSearch && count($pimpinanResults) > 0)
                        <div
                            class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg w-full max-h-48 overflow-y-auto z-50">
                            @foreach ($pimpinanResults as $pimpinan)
                                <button type="button" wire:mousedown.prevent="selectPimpinan({{ $pimpinan->id }})"
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

                {{-- Unit Induk --}}
                <div class="relative">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Unit Induk
                    </label>
                    <div class="flex gap-2">
                        <input type="text" wire:model.live.debounce.500ms="unitIndukSearch"
                            wire:blur="restoreUnitInduk" placeholder="Cari dan Pilih dari Unit Induk yang Tersedia"
                            class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm placeholder:italic placeholder-gray-400
                                   @error('form.unit_induk_id') border-red-400 @enderror">
                        @if ($form['parent_id'])
                            <button wire:click="removeUnitInduk" type="button"
                                class="mt-1 px-3 hover:bg-red-50 rounded border border-red-200 cursor-pointer">
                                <i class="fa-solid fa-trash text-red-500"></i>
                            </button>
                        @endif
                    </div>
                    @error('form.parent_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    @if ($unitIndukSearch && count($unitIndukResults) > 0)
                        <div
                            class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto z-50">
                            @foreach ($unitIndukResults as $unitInduk)
                                <button type="button" wire:mousedown.prevent="selectUnitInduk({{ $unitInduk->id }})"
                                    class="w-full text-left px-3 py-2 hover:bg-indigo-50 transition-colors border-b last:border-b-0">
                                    <p class="text-sm font-medium text-gray-700">{{ $unitInduk->name }}</p>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Footer Form --}}
    @if ($editMode)
        <div class="flex justify-end gap-3 p-4 border-t border-gray-200">
            <button type="button" wire:click="cancelEdit"
                class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md cursor-pointer transition-colors duration-150">
                Batal
            </button>
            <button type="button"
                x-on:click="showLoading = true; $wire.save().finally(() => setTimeout(() => showLoading = false, 500))"
                wire:loading.attr="disabled" wire:target="save" @disabled(!$this->isDirty)
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-md transition-colors duration-150
                    {{ $this->isDirty
                        ? 'bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 cursor-pointer'
                        : 'bg-indigo-300 cursor-not-allowed' }}
                    disabled:opacity-60 disabled:cursor-not-allowed">
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
    @endif
</div>
