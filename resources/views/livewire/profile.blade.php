<div
    x-data="{ editing: false }"
    x-on:profile-saved.window="editing = false"
>
  <div class="max-w-7xl mx-auto">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-lg mb-3 overflow-hidden">
      <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-lg flex items-center justify-center border-2 border-white/30 shadow-lg">
              <i class="fa-solid fa-user text-3xl text-white"></i>
            </div>
            <div class="text-white">
              <h1 class="text-2xl md:text-3xl font-bold">Informasi Profil</h1>
              <p class="text-indigo-100 text-sm md:text-base mt-1">Kelola dan perbarui informasi akun Anda</p>
            </div>
          </div>

          <div class="flex flex-row gap-3">
            <!-- Action Buttons Header -->
            <div class="flex justify-end gap-3">
              <!-- Edit Button -->
              <button
                x-show="!editing"
                @click="editing = true"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-colors duration-200 font-medium shadow-md hover:shadow-lg cursor-pointer"
              >
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit</span>
              </button>

              <!-- Save and Cancel Buttons -->
              <div class="flex gap-3" x-show="editing">
                <button
                    @click="$wire.cancel(); editing = false"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 active:bg-gray-400 transition-colors duration-200 font-medium cursor-pointer"
                >
                    <i class="fa-solid fa-times"></i>
                    <span>Batal</span>
                </button>

                @if ($this->isDirty && !$errors->any())
                  <button
                      wire:click="save"
                      class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition-colors duration-200 font-medium shadow-md hover:shadow-lg cursor-pointer"
                  >
                      <i class="fa-solid fa-check"></i>
                      <span>Simpan</span>
                  </button>
                @endif
              </div>
            </div>

            <!-- Alert Message -->
            @if (session()->has('success') || session()->has('error'))
              <div
                  x-data="{
                      show: true,
                      type: '{{ session()->has('success') ? 'success' : 'error' }}'
                  }"
                  x-show="show"
                  x-transition
                  x-init="setTimeout(() => show = false, 3500)"
                  class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm"
                  :class="{
                      'bg-green-100 text-green-800 border border-green-300': type === 'success',
                      'bg-red-100 text-red-800 border border-red-300': type === 'error'
                  }"
              >
                  <i
                      class="text-lg"
                      :class="{
                          'fa-solid fa-circle-check': type === 'success',
                          'fa-solid fa-triangle-exclamation': type === 'error'
                      }"
                  ></i>
                  <span>{{ session('success') ?? session('error') }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden min-h-[calc(100vh-280px)]">
      <!-- Form Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 md:p-8 items-center">

        <!-- Left Column -->
        <div class="space-y-4">

          <!-- Nama Lengkap -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fa-solid fa-user-tie mr-2 text-indigo-600"></i>Nama Lengkap
            </label>
            <input
              type="text"
              value="{{ $user->pegawai?->nama ?? '-'}}"
              disabled
              class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-not-allowed"
            />
          </div>

          <!-- Nomor Induk Pegawai -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fa-solid fa-id-card mr-2 text-indigo-600"></i>Nomor Induk Pegawai
            </label>
            <input
              type="text"
              value="{{  $user->pegawai?->nip ?? '-' }}"
              disabled
              class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-not-allowed"
            />
          </div>

          <!-- Email Yarsi -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fa-solid fa-envelope-circle-check mr-2 text-indigo-600"></i>Email Yarsi
            </label>
            <input
              type="email"
              value="{{ $user->pegawai?->email_yarsi ?? '-' }}"
              disabled
              class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-not-allowed"
            />
          </div>

          <!-- Kontak -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fa-solid fa-phone mr-2 text-indigo-600"></i>Nomor Telepon
            </label>
            <input
              type="tel"
              wire:model.live="form.no_telpon"
              placeholder="Masukkan nomor telepon"
              :disabled="!editing || !{{ $user->pegawai ? 'true' : 'false' }}"
              class="w-full px-4 py-2.5 rounded-lg border-2 transition-all duration-200 focus:outline-none"
              :class="!editing || !{{ $user->pegawai ? 'true' : 'false' }}
                ? 'bg-gray-100 text-gray-700 border-gray-300 cursor-not-allowed'
                : 'bg-white text-gray-800 border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent'"
            />
            @error('form.no_telpon')
              <p class="text-sm text-red-600 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message }}
              </p>
            @enderror
          </div>

        </div>

        <!-- Right Column -->
        <div class="space-y-4">

          <!-- Username -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fa-solid fa-at mr-2 text-indigo-600"></i>Username
            </label>
            <input
              type="text"
              value="{{ $user->username }}"
              disabled
              class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-not-allowed"
            />
          </div>

          <!-- Role -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fa-solid fa-user-shield mr-2 text-indigo-600"></i>Role
            </label>
            <input
              type="text"
              value="{{ $user->role->name }}"
              disabled
              class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-not-allowed"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fa-solid fa-envelope mr-2 text-indigo-600"></i>Email
            </label>
            <input
              type="email"
              wire:model.live="form.email"
              placeholder="Masukkan email Anda"
              :disabled="!editing"
              class="w-full px-4 py-2.5 rounded-lg border-2 transition-all duration-200 focus:outline-none"
              :class="!editing
                ? 'bg-gray-100 text-gray-700 border-gray-300 cursor-not-allowed'
                : 'bg-white text-gray-800 border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent'"
            />
            @error('form.email')
              <p class="text-sm text-red-600 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message }}
              </p>
            @enderror
          </div>

          <!-- Jenis Kelamin -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fa-solid fa-venus-mars mr-2 text-indigo-600"></i>Jenis Kelamin
            </label>
            <div class="relative">
              <select
                wire:model.live="form.jenis_kelamin"
                :disabled="!editing || !{{ $user->pegawai ? 'true' : 'false' }}"
                class="w-full px-4 py-2.5 rounded-lg border-2 appearance-none transition-all duration-200 focus:outline-none"
                :class="!editing || !{{ $user->pegawai ? 'true' : 'false' }}
                  ? 'bg-gray-100 text-gray-700 border-gray-300 cursor-not-allowed'
                  : 'bg-white text-gray-800 border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer'"
              >
                <option value="">- Pilih Jenis Kelamin -</option>
                <option value="L">Laki - Laki</option>
                <option value="P">Perempuan</option>
              </select>
              <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            </div>
          </div>

        </div>

      </div>
    </div>

  </div>
</div>