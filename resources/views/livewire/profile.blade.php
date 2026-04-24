<div
    class="max-w-6xl mx-auto "
    x-data="{ editing: false,
              show: false,
            }"
    x-on:profile-saved.window="editing = false"
    >

  <!-- Header -->
  <div class="flex items-center justify-between mb-5 bg-white/80 backdrop-blur-md rounded-xl shadow-xl px-5 py-3">

    <!-- Left Content -->
    <div class="flex items-center gap-3">
      <div class="w-13 h-13 rounded-full bg-gray-500 flex items-center justify-center">
        <i class="fa-solid fa-user text-2xl text-white"></i>
      </div>
      <div>
        <h1 class="text-xl font-semibold text-gray-800">Informasi Profil</h1>
        <p class="text-sm text-gray-500">Kelola informasi akun anda</p>
      </div>
    </div>

    <!-- Right Content -->
    <div class="flex items-center gap-2">

      <!-- Message Updated -->
      @if (session()->has('success') || session()->has('error'))
        <div
            x-data="{
                show: true,
                type: '{{ session()->has('success') ? 'success' : 'error' }}'
            }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3000)"
            class="px-4 py-2 text-sm rounded-md flex items-center"
            :class="{
                'bg-green-200 text-green-800': type === 'success',
                'bg-red-200 text-red-800': type === 'error'
            }"
        >
            <i
                class="mr-2"
                :class="{
                    'fa-solid fa-circle-check': type === 'success',
                    'fa-solid fa-circle-xmark': type === 'error'
                }"
            ></i>
            <span>
                {{ session('success') ?? session('error') }}
            </span>
        </div>
      @endif

      <!-- Edit Button -->
      <div class="flex items-end gap-2">
        <button
          x-show="!editing"
          @click="editing = true"
          class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 cursor-pointer gap-2 flex items-center justify-center"
        >
          <i class="fa-solid fa-pen-to-square text-lg"></i>
          <span class="text-sm">Edit</span>
        </button>
      </div>

      <!-- Save and Cancel Button -->
      <div
          class="flex justify-end gap-3"
          x-show="editing">

        <!-- Batal -->
        <button
            x-show="editing"
            @click="$wire.cancel(); editing = false"
            class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-sm cursor-pointer"
        >
            Batal
        </button>

        <!-- Simpan -->
        @if ($this->isDirty && !$errors->any())
          <button
              wire:click="save"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm cursor-pointer border border-gray-300"
          >
              Simpan
          </button>
        @endif
      </div>
    </div>
  </div>

  <!-- Body Content -->
  <div class="grid grid-cols-1 md:grid-cols-2 auto-rows-fr gap-3 gap-x-10 bg-white/80 backdrop-blur-md rounded-xl shadow-xl py-5 px-5">

    <!-- Left Side -->
    <div class="space-y-3">
      <!-- Nama Lengkap -->
      <div>
        <label class="flex items-center gap-2 text-md px-2">
          <i class="fa-solid fa-user-tie mr-1"></i>
          Nama Lengkap
        </label>
        <div class="flex gap-2">
          <input
            type="text"
            value="{{ $user->pegawai?->nama ?? '-'}}"
            disabled
            class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-200 disabled:text-gray-600"
          />
        </div>
      </div>

      <!-- Nomor Induk Pegawai -->
      <div>
        <label class="flex items-center gap-2 text-md px-2">
          <i class="fa-solid fa-id-card mr-1"></i>
          Nomor Induk Pegawai
        </label>
        <div class="flex gap-2">
          <input
            type="text"
            value="{{  $user->pegawai?->nip ?? '-' }}"
            disabled
            class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-200 disabled:text-gray-600"
          />
        </div>
      </div>

      <!-- Email Yarsi -->
      <div>
        <label class="flex items-center gap-2 text-md px-2">
          <i class="fa-solid fa-envelope-circle-check mr-1"></i>
          Email Yarsi
        </label>

        <div class="flex gap-2">
          <input
            type="text"
            value="{{ $user->pegawai?->email_yarsi ?? '-' }}"
            disabled
            class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-200 disabled:text-gray-600"
          />
        </div>
      </div>

      <!-- Kontak -->
      <div>
        <label class="flex items-center gap-2 text-md px-2">
          <i class="fa-solid fa-phone mr-1"></i>
          Kontak
        </label>

        <div class="flex gap-2">
          <input
            type="text"
            wire:model.live="form.no_telpon"
            placeholder="-"
            :disabled="!editing || !{{ $user->pegawai ? 'true' : 'false' }}"
            class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white text-gray-800 disabled:bg-gray-200 disabled:text-gray-600 enabled:border border-gray-200"
          />
        </div>

        @error('form.no_telpon')
          <p class="text-xs text-red-500 mt-1">
            {{ $message }}
          </p>
        @enderror
      </div>

    </div>

    <!-- Right Side -->
    <div class="space-y-3">

      <!-- Username -->
      <div>
        <label class="flex items-center gap-2 text-md px-2">
          <i class="fa-solid fa-user mr-1"></i>
          Username
        </label>

        <div class="flex gap-2">
          <input
            type="text"
            value="{{ $user->username }}"
            disabled
            class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-200 disabled:text-gray-600"
          />
        </div>
      </div>

      <!-- Role -->
      <div>
        <label class="flex items-center gap-2 text-md px-2">
          <i class="fa-solid fa-user-shield mr-1"></i>
          Role
        </label>

        <div class="flex gap-2">
          <input
            type="text"
            value="{{ $user->role->name }}"
            disabled
            class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-200 disabled:text-gray-600"
          />
        </div>
      </div>

      <!-- Email -->
      <div>
        <label class="flex items-center gap-2 text-md px-2">
          <i class="fa-solid fa-at mr-1"></i>
          Email
        </label>

        <div class="flex gap-2">
          <input
            type="text"
            wire:model.live="form.email"
            :disabled="!editing"
            class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white/90 text-gray-800 disabled:bg-gray-200 disabled:text-gray-600 enabled:border border-gray-200"
          />
        </div>

        @error('form.email')
          <p class="relative text-red-500 px-1 text-xs mt-1">
              {{ $message }}
          </p>
        @enderror
      </div>

      <!-- Jenis Kelamin -->
      <div>
        <label class="flex items-center gap-2 text-md px-2">
          <i class="fa-solid fa-mars mr-1"></i>
          Jenis Kelamin
        </label>
        <div class="relative">
          <select
            wire:model.live="form.jenis_kelamin"
            :disabled="!editing || !{{ $user->pegawai ? 'true' : 'false' }}"
            class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white text-gray-800 disabled:bg-gray-200 disabled:text-gray-600 enabled:border border-gray-200 appearance-none cursor-pointer"
          >
            <option value="">- Pilih -</option>
            <option value="L">Laki - Laki</option>
            <option value="P">Perempuan</option>
          </select>

          <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
          </svg>
        </div>
      </div>

    </div>

  </div>
</div>
