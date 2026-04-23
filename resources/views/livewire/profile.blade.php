<div x-data="{ editing:false }" class="max-w-5xl mx-auto pt-3">

  <!-- Header -->
  <div class="flex items-center justify-between mb-5 px-3">
    <div>
      <h1 class="text-xl font-semibold text-gray-800">Informasi Profil</h1>
      <p class="text-sm text-gray-500">Kelola informasi akun anda</p>
    </div>

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
  </div>

  <!-- Content -->
  <div class="grid grid-cols-2 gap-3 gap-x-10">

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
          class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-300 disabled:text-gray-600"
        />
      </div>
    </div>

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
          class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-300 disabled:text-gray-600"
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
          class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-300 disabled:text-gray-600"
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
          class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-300 disabled:text-gray-600"
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
          class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-300 disabled:text-gray-600"
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
          wire:model.defer="email"
          :disabled="!editing"
          class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white text-gray-800 disabled:bg-gray-300 disabled:text-gray-600"
        />
      </div>

      @error('email')
        <p class="text-red-500 text-xs mt-1">
            {{ $message }}
        </p>
      @enderror
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
          wire:model.defer="no_telpon"
          :disabled="!editing"
          class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white text-gray-800 disabled:bg-gray-300 disabled:text-gray-600"
        />
      </div>
    </div>

    <!-- Jenis Kelamin -->
    <div>
      <label class="flex items-center gap-2 text-md px-2">
        <i class="fa-solid fa-mars mr-1"></i>
        Jenis Kelamin
      </label>
      <select
        wire:model.defer="jenis_kelamin"
        :disabled="!editing"
        class="mt-1 px-3 py-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white text-gray-800 disabled:bg-gray-300 disabled:text-gray-600"
      >
        <option value="">- Pilih -</option>
        <option value="L">Laki - laki</option>
        <option value="P">Perempuan</option>
      </select>
    </div>
  </div>

  <!-- Footer -->
  <div class="mt-5 flex justify-end gap-3">
    <!-- Batal -->
    <button
        x-show="editing"
        @click="editing = false; $wire.resetForm()"
        class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-sm cursor-pointer"
    >
        Batal
    </button>
    <!-- Simpan -->
    <button
        x-show="editing"
        @click="$wire.save()"
        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm cursor-pointer"
    >
        Simpan
    </button>
  </div>
</div>
