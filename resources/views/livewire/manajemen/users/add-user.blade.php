<div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl">
    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-300">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-indigo-500"></i>
            <h2 class="text-lg font-semibold text-gray-700">
                Form Tambah Pengguna
            </h2>
        </div>
        <button
            @click="$dispatch('close-add-modal')"
            class="text-gray-400 hover:text-gray-600 cursor-pointer"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="save" class="p-6">
        <div class="grid grid-cols-2 gap-6">
            <!-- LEFT -->
            <div class="space-y-4">
                <!-- Username -->
                <div>
                    <label class="text-sm text-gray-600 px-2">
                        <i class="fa-solid fa-user mr-1 text-gray-400"></i>
                        Username
                    </label>
                    <input
                    type="text"
                    wire:model.live.debounce.500ms="form.username"
                    placeholder="Masukkan username"
                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md
                    focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    >
                    @error('form.username')
                        <small class="text-red-500 block px-1 mt-1">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
                <!-- Email -->
                <div>
                    <label class="text-sm text-gray-600 px-2">
                        <i class="fa-solid fa-envelope mr-1 text-gray-400"></i>
                        Email
                    </label>
                    <input
                        type="email"
                        wire:model.live.debounce.500ms="form.email"
                        placeholder="Masukkan email"
                        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                            focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    >
                    @error('form.email')
                        <small class="text-red-500 block px-1 mt-1">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
                <!-- Password -->
                <div x-data="{ showPassword: false }">
                    <label class="text-sm text-gray-600 px-2">
                        <i class="fa-solid fa-lock mr-1 text-gray-400"></i>
                        Password
                    </label>
                    <div class="relative mt-1">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            wire:model.live.debounce.500ms="form.password"
                            placeholder="Masukkan password"
                            class="w-full px-3 py-2 border border-gray-200 rounded-md
                                focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"
                        >
                            <i class="fa-solid"
                            :class="showPassword ? 'fa-eye-slash' : 'fa-eye'">
                            </i>
                        </button>
                    </div>
                    @error('form.password')
                        <small class="text-red-500 block px-1 mt-1">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>

            <!-- RIGHT -->
            <div class="space-y-4">
                <div class="relative">
                    <label class="text-sm text-gray-600 px-2">
                        <i class="fa-solid fa-id-card mr-1 text-gray-400"></i>
                        Koneksi Pegawai
                    </label>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="pegawaiSearch"
                        placeholder="Cari nama atau NIP..."
                        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                            focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    >
                    @if($pegawaiSearch && count($pegawaiResults) > 0)
                        <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-200
                                    rounded-md shadow-lg max-h-48 overflow-y-auto z-50">
                            @foreach($pegawaiResults as $pegawai)
                                <button
                                    type="button"
                                    wire:click="selectPegawai({{ $pegawai->id }})"
                                    class="w-full text-left px-3 py-2 hover:bg-indigo-50
                                        transition border-b last:border-b-0"
                                >
                                    <div class="text-sm font-medium text-gray-700">
                                        {{ $pegawai->nama }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $pegawai->nip }}
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                    @error('form.pegawai_id')
                        <small class="text-red-500 block px-1 mt-1">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
                <!-- Role -->
                <div>
                    <label class="text-sm text-gray-600 px-2">
                        <i class="fa-solid fa-user-shield mr-1 text-gray-400"></i>
                        Role
                    </label>
                    <select
                        wire:model.live="form.role_id"
                        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                            focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer text-gray-500"
                    >
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" class="cursor-pointer text-black">
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('form.role_id')
                        <small class="text-red-500 block px-1 mt-1">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-2 pt-6 mt-6 border-t border-gray-300">
            <button
                type="button"
                @click="$dispatch('close-add-modal')"
                class="px-4 py-2 text-sm border border-gray-200 rounded-md bg-gray-100
                    hover:bg-gray-200 cursor-pointer"
            >
                <i class="fa-solid fa-xmark mr-1"></i>
                Batal
            </button>
            <button
                type="submit"
                :disabled="!$wire.form.username || !$wire.form.email || !$wire.form.password || !$wire.form.role_id"
                class="px-4 py-2 text-sm text-white bg-indigo-500
                    hover:bg-indigo-600 rounded-md cursor-pointer disabled:bg-indigo-500/50 disabled:cursor-not-allowed disabled:hover:bg-indigo-500/50"
            >
                <!-- Loading -->
                <svg aria-hidden="true"
                    wire:loading wire:target="save" class="w-4 h-4 text-neutral-quaternary animate-spin fill-brand" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                </svg>
                <span class="sr-only">Menyimpan...</span>
                
                <i class="fa-solid fa-floppy-disk mr-1"></i>
                Simpan
            </button>
        </div>
    </form>
</div>