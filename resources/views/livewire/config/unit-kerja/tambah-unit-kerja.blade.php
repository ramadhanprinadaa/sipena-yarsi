<div class="bg-white w-2xl mx-auto p-6 rounded-xl shadow-lg">

  <!-- Header -->
  <header class="flex items-center justify-between pb-4 mb-5 border-b border-gray-300">
    <div class="flex items-center gap-3">
        <i class="fa-solid fa-building-circle-check text-2xl text-indigo-500"></i>
        <h2 class="text-lg font-semibold text-gray-700">
            Form Tambah Unit Kerja
        </h2>
    </div>

    <!-- CLOSE -->
    <button
        type="button"
        @click="$dispatch('close-modal')"
        class="w-7 h-7 flex items-center justify-center rounded-md bg-red-400 hover:bg-red-500 text-white cursor-pointer">
        <i class="fa-solid fa-xmark text-sm"></i>
    </button>
  </header>

  <!-- Form -->
  <form wire:submit.prevent="save" class="space-y-5">

    <!-- Nama -->
    <div>
        <input type="text"
            class="w-full border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 text-sm"
            wire:model.live.debounce.500ms="form.nama_unit"
            placeholder="Nama Unit Kerja">
        @error('form.nama_unit')
            <div class="text-xs text-red-500 mt-1">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Unit SDM -->
    <div x-data="{ show: false, selected: @entangle('form.unit_sdm_id').live }" class="relative">
        <button
            type="button"
            @click="show = !show"
            wire:model.live="form.unit_sdm_id"
            class="w-full flex justify-between items-center py-2 border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none text-sm cursor-pointer">
            <span
                :class="selected ? 'text-gray-900' : 'text-gray-500/90'"
                x-text="
                    selected == 1 ? 'SDM Yayasan' :
                    selected == 2 ? 'SDM Universitas' :
                    'Unit SDM'
                ">
            </span>
            <i class="fa-solid fa-chevron-down text-xs text-gray-500/90"></i>
        </button>
        @error('form.unit_sdm_id')
            <div class="text-xs text-red-500 mt-1">
                {{ $message }}
            </div>
        @enderror

        <!-- Menu Dropdown -->
        <div
            x-show="show"
            @click.outside="show = false"
            x-transition
            class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50">

            <ul class="text-sm">
                <li>
                    <button type="button"
                        @click="selected='1'; show=false"
                        class="w-full text-left px-3 py-2 hover:bg-gray-100">
                        SDM Yayasan
                    </button>
                </li>
                <li>
                    <button type="button"
                        @click="selected='2'; show=false"
                        class="w-full text-left px-3 py-2 hover:bg-gray-100">
                        SDM Universitas
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Pimpinan -->
    <div class="relative">
        <div class="flex gap-2">
          <input type="text"
              class="w-full border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 text-sm"
              wire:model.live.debounce.500ms="pimpinanSearch"
              placeholder="Pimpinan Unit">
          @if($form['pimpinan_id'])
              <button
                  wire:click="removePimpinan"
                  type="button"
                  class="mt-1 px-3 hover:bg-red-50 rounded border border-red-200 cursor-pointer"
              >
                  <i class="fa-solid fa-trash text-red-500"></i>
              </button>
          @endif
        </div>

        @error('form.pimpinan_id')
            <div class="text-xs text-red-500 mt-1">
                {{ $message }}
            </div>
        @enderror

        @if($pimpinanSearch && count($pimpinanResults) > 0)
            <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg w-full max-h-48 overflow-y-auto z-50">
                @foreach($pimpinanResults as $pimpinan)
                    <button
                        type="button"
                        wire:click="selectPimpinan({{ $pimpinan->id }})"
                        class="w-full text-left px-3 py-2 hover:bg-indigo-50
                            transition border-b last:border-b-0"
                    >
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

    <!-- Unit Induk -->
    <div class="relative">
        <div class="flex gap-2">
            <input type="text"
                class="w-full border-b-2 border-gray-300 focus:border-indigo-500 focus:outline-none py-2 text-sm"
                wire:model.live.debounce.400ms="unitIndukSearch"
                placeholder="Unit Induk">
            @if($form['unit_induk_id'])
              <button
                  wire:click="removeUnitInduk"
                  type="button"
                  class="mt-1 px-3 hover:bg-red-50 rounded border border-red-200 cursor-pointer"
              >
                  <i class="fa-solid fa-trash text-red-500"></i>
              </button>
          @endif
        </div>
        @error('form.unit_induk_id')
            <div class="text-xs text-red-500 mt-1">
                {{ $message }}
            </div>
        @enderror
        @if($unitIndukSearch && count($unitIndukResults) > 0)
            <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg w-full max-h-48 overflow-y-auto z-50">
                @foreach($unitIndukResults as $unitInduk)
                    <button
                        type="button"
                        wire:click="selectUnitInduk({{ $unitInduk->id }})"
                        class="w-full text-left px-3 py-2 hover:bg-indigo-50
                            transition border-b last:border-b-0"
                    >
                        <div class="text-sm font-medium text-gray-700">
                            {{ $unitInduk->name }}
                        </div>
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="flex justify-end gap-3 pt-6 border-t border-gray-300">
        <button
            type="button"
            @click="$dispatch('close-modal')"
            class="px-4 py-2 text-sm bg-red-400 hover:bg-red-500 text-white rounded-md cursor-pointer">
            Batal
        </button>

        <button
            type="submit"
            class="px-4 py-2 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-md cursor-pointer">
            Simpan
        </button>
    </div>

  </form>
</div>