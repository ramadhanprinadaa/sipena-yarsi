<div>
    @if($open)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
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
                        wire:click="close"
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
                                wire:model="username"
                                placeholder="Masukkan username"
                                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md
                                focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                >
                                @error('username')
                                    <small class="text-red-500 block px-2">
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
                                    wire:model="email"
                                    placeholder="Masukkan email"
                                    class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                                        focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                >
                                @error('email')
                                    <small class="text-red-500 block px-2">
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
                                        wire:model="password"
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
                                @error('password')
                                    <small class="text-red-500 block px-2">
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
                                    wire:model.live="pegawaiSearch"
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
                                @error('pegawai_id')
                                    <small class="text-red-500 block px-2">
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
                                    wire:model="role"
                                    class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                                        focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer text-gray-400"
                                >
                                    <option value="">Pilih Role</option>
                                    @foreach($roleList as $role)
                                        <option value="{{ $role->id }}" class="cursor-pointer">
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <small class="text-red-500 block px-2">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="flex justify-end gap-2 pt-6 mt-6 border-t">
                        <button
                            type="button"
                            wire:click="close"
                            class="px-4 py-2 text-sm border border-gray-200 rounded-lg
                                hover:bg-gray-100 cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark mr-1"></i>
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm text-white bg-indigo-500 
                                hover:bg-indigo-600 rounded-lg cursor-pointer"
                        >
                            <i class="fa-solid fa-floppy-disk mr-1"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>