<div class="bg-white w-2xl mx-auto p-6 rounded-xl shadow-lg">

    {{-- ═══════════════════════════ HEADER ════════════════════════════════ --}}
    <header class="flex items-center justify-between pb-4 mb-5 border-b border-gray-200">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-building-circle-check text-2xl text-indigo-500"></i>
            <div>
                <h2 class="text-base font-semibold text-gray-800 leading-tight">
                    Detail Unit Kerja
                </h2>
                @if ($unit)
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $editMode ? 'Mode Edit — ubah data lalu simpan' : 'Informasi unit kerja' }}
                    </p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Tombol Edit / Batal Edit --}}
            @if ($unit)
                @if (! $editMode)
                    <button
                        type="button"
                        wire:click="enterEditMode"
                        class="inline-flex items-center gap-1.5 px-3 h-7 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-md transition-colors duration-150 cursor-pointer">
                        <i class="fa-solid fa-pen text-xs"></i>
                        Edit
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="cancelEdit"
                        class="inline-flex items-center gap-1.5 px-3 h-7 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-150 cursor-pointer">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        Batal Edit
                    </button>
                @endif
            @endif

            {{-- Tombol Tutup --}}
            <button
                type="button"
                wire:click="close"
                wire:loading.attr="disabled"
                class="w-7 h-7 flex items-center justify-center rounded-md bg-red-400 hover:bg-red-500 active:bg-red-600 text-white cursor-pointer transition-colors duration-150 disabled:opacity-60">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </header>

    {{-- ═══════════════════════════ BODY ══════════════════════════════════ --}}
    @if (! $unit)
        {{-- Skeleton fallback — tidak seharusnya tampil karena modal baru
             terbuka setelah data siap, namun dipertahankan sebagai guard. --}}
        <div class="space-y-4 animate-pulse">
            <div class="h-5 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-100 rounded w-1/2"></div>
            <div class="h-4 bg-gray-100 rounded w-2/3"></div>
        </div>
    @elseif (! $editMode)
        {{-- ─────────────────── MODE READ ─────────────────── --}}
        <div class="space-y-4">

            {{-- Nama Unit Kerja --}}
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    @php $initials = collect(explode(' ', $unit->name))->take(3)->map(fn($w) => strtoupper($w[0]))->implode(''); @endphp
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
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-violet-100 text-violet-700 rounded-md">
                            SDM Yayasan
                        </span>
                    @elseif ($unit->unit_sdm_id == 2)
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-md">
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
                        <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
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
        <form wire:submit.prevent="save" class="space-y-3">

            {{-- Nama Unit Kerja --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Nama Unit Kerja <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model.live="form.name"
                    placeholder="Nama Unit Kerja"
                    class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm bg-transparent transition-colors duration-150
                           @error('form.name') border-red-400 @enderror">
                @error('form.nama_unit')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Unit SDM --}}
            <div
                x-data="{ show: false, selected: @entangle('form.unit_sdm_id').live }"
                class="relative">
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Unit SDM <span class="text-red-500">*</span>
                </label>
                <button
                    type="button"
                    @click="show = !show"
                    class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer transition-colors duration-150
                           @error('form.unit_sdm_id') border-red-400 @enderror">
                    <span
                        :class="selected ? 'text-gray-900' : 'text-gray-500/90'"
                        x-text="
                            selected == 1 ? 'SDM Yayasan' :
                            selected == 2 ? 'SDM Universitas' :
                            'Pilih Unit SDM'
                        ">
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400"
                       :class="show ? 'rotate-180' : ''"
                       style="transition: transform 0.15s"></i>
                </button>
                @error('form.unit_sdm_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror

                <div
                    x-show="show"
                    @click.outside="show = false"
                    x-transition
                    class="absolute mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-50">
                    <ul class="text-sm py-1">
                        <li>
                            <button
                                type="button"
                                @click="selected = '1'; show = false"
                                wire:click="$set('form.unit_sdm_id', 1)"
                                class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                SDM Yayasan
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                @click="selected = '2'; show = false"
                                wire:click="$set('form.unit_sdm_id', 2)"
                                class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                SDM Universitas
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Pimpinan (typeahead) --}}
            <div class="relative">
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Pimpinan Unit
                </label>
                <div class="flex gap-2">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="pimpinanSearch"
                        wire:blur="restorePimpinan"
                        placeholder="Cari nama atau NIP pimpinan..."
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm bg-transparent transition-colors duration-150
                               @error('form.pimpinan_id') border-red-400 @enderror">
                    @if ($form['pimpinan_id'])
                        <button
                            type="button"
                            wire:click="removePimpinan"
                            class="flex-shrink-0 mt-1 px-3 hover:bg-red-50 rounded border border-red-200 cursor-pointer transition-colors duration-150">
                            <i class="fa-solid fa-trash text-red-400 text-xs"></i>
                        </button>
                    @endif
                </div>
                @error('form.pimpinan_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror

                @if ($pimpinanSearch && count($pimpinanResults) > 0)
                    <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto z-50">
                        @foreach ($pimpinanResults as $pimpinan)
                            <button
                                type="button"
                                wire:click="selectPimpinan({{ $pimpinan->id }})"
                                class="w-full text-left px-3 py-2 hover:bg-indigo-50 transition-colors border-b last:border-b-0">
                                <p class="text-sm font-medium text-gray-700">{{ $pimpinan->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $pimpinan->nip }}</p>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Unit Induk (typeahead) --}}
            <div class="relative">
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Unit Induk
                </label>
                <div class="flex gap-2">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="unitIndukSearch"
                        wire:blur="restoreUnitInduk"
                        placeholder="Cari nama unit induk..."
                        class="w-full border-0 rounded-none shadow-none focus:ring-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 px-0 text-sm bg-transparent transition-colors duration-150
                               @error('form.unit_induk_id') border-red-400 @enderror">
                    @if ($form['parent_id'])
                        <button
                            type="button"
                            wire:click="removeUnitInduk"
                            class="flex-shrink-0 mt-1 px-3 hover:bg-red-50 rounded border border-red-200 cursor-pointer transition-colors duration-150">
                            <i class="fa-solid fa-trash text-red-400 text-xs"></i>
                        </button>
                    @endif
                </div>
                @error('form.parent_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror

                @if ($unitIndukSearch && count($unitIndukResults) > 0)
                    <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto z-50">
                        @foreach ($unitIndukResults as $unitInduk)
                            <button
                                type="button"
                                wire:click="selectUnitInduk({{ $unitInduk->id }})"
                                class="w-full text-left px-3 py-2 hover:bg-indigo-50 transition-colors border-b last:border-b-0">
                                <p class="text-sm font-medium text-gray-700">{{ $unitInduk->name }}</p>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Footer Form --}}
            <div class="flex justify-end gap-3 pt-3 border-t border-gray-200">
                <button
                    type="button"
                    wire:click="cancelEdit"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md cursor-pointer transition-colors duration-150">
                    Batal
                </button>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    @disabled(! $this->isDirty)
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-md transition-colors duration-150
                           {{ $this->isDirty
                               ? 'bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 cursor-pointer'
                               : 'bg-indigo-300 cursor-not-allowed' }}
                           disabled:opacity-60 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="save">
                        <i class="fa-solid fa-floppy-disk text-xs mr-1"></i>
                        Simpan Perubahan
                    </span>
                    <span wire:loading wire:target="save" class="flex flex-row items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span>Menyimpan...</span>
                    </span>
                </button>
            </div>

        </form>
    @endif

</div>
