<div>
    @if($show)
      	<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
			<div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl"
				x-data="{
					edit:{},
					dirty:false
				}"
			>

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-300">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user text-indigo-500"></i>
                        <h2 class="text-lg font-semibold text-gray-700">
                            Detail Pengguna
                        </h2>
                    </div>

                    <button
                        wire:click="close"
                        class="text-gray-400 hover:text-gray-600 cursor-pointer"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <!-- LEFT -->
                        <div class="space-y-4">

                            <!-- Username -->
                            <div>
                                <label class="text-sm text-gray-600 px-2">
                                    <i class="fa-solid fa-user mr-1 text-gray-400"></i>
                                    Username
                                </label>

                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        wire:model="username"
                                        :disabled="!edit.username"
                                        readonly
                                        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                                        focus:outline-none focus:ring-2 focus:ring-indigo-400
                                        disabled:bg-gray-100 disabled:text-gray-500"
                                    >
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="text-sm text-gray-600 px-2">
                                    <i class="fa-solid fa-envelope mr-1 text-gray-400"></i>
                                    Email
                                </label>

                                <div class="flex gap-2 mb-1">
                                    <input
                                        type="email"
                                        wire:model.live="formEdit.email"
                                        placeholder="{{ $user->email ?? '-'}}"
                                        :disabled="!edit.email"
                                        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                                        focus:outline-none focus:ring-2 focus:ring-indigo-400
                                        disabled:bg-gray-100 disabled:text-gray-500"
                                    >
                                    <button
                                        @click="edit.email = true"
                                        class="mt-1 px-3 hover:bg-gray-100 rounded border border-gray-200 cursor-pointer"
                                    >
                                        <i class="fa-solid fa-pen text-gray-500"></i>
                                    </button>
                                </div>
								@error('formEdit.email')
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

                                <div class="flex gap-2">

                                    <div class="relative w-full">
                                        <select
                                            wire:model.live="formEdit.role_id"
                                            :disabled="!edit.role"
                                            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                                            focus:outline-none focus:ring-2 focus:ring-indigo-400
                                            disabled:bg-gray-100 disabled:text-gray-400 enabled:cursor-pointer appearance-none"
                                        >
                                            @foreach($roles as $r)
                                                <option value="{{ $r->id }}">
                                                    {{ $r->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 text-gray-400 pointer-events-none"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>

                                    <button
                                        @click="edit.role = true"
                                        class="mt-1 px-3 hover:bg-gray-100 rounded border border-gray-200 cursor-pointer"
                                    >
                                        <i class="fa-solid fa-pen text-gray-500"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT -->
                        <div class="space-y-4">

                            <!-- Pegawai -->
                            <div class="relative">
                                <label class="text-sm text-gray-600 px-2">
                                    <i class="fa-solid fa-id-card mr-1 text-gray-400"></i>
                                    Nama Pegawai
                                </label>

                                <div class="flex gap-2">
                                    <input
                                        type="text"
										wire:model.live.debounce.400ms="pegawaiSearch"
                                        wire:blur="restorePegawai"
                                        :disabled="!edit.pegawai"
                                        placeholder="{{ $user->pegawai->nama ?? '-' }}"
                                        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                                        focus:outline-none focus:ring-2 focus:ring-indigo-400
                                        disabled:bg-gray-100 disabled:text-gray-500"
                                    >
                                    <!-- Tombol Hapus -->
                                    <button
                                        x-show="edit.pegawai && @js($formEdit['pegawai_id'])"
                                        x-transition
                                        wire:click="removePegawai"
                                        type="button"
                                        class="mt-1 px-3 hover:bg-red-50 rounded border border-red-200 cursor-pointer"
                                    >
                                        <i class="fa-solid fa-trash text-red-500"></i>
                                    </button>
									<button
										@click="edit.pegawai = true"
										class="mt-1 px-3 hover:bg-gray-100 rounded border border-gray-200 cursor-pointer"
									>
										<i class="fa-solid fa-pen text-gray-500"></i>
									</button>
                                </div>
                                @if($pegawaiSearch && count($pegawaiResults) > 0)
                                    <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-200
                                                rounded-md shadow-lg max-h-48 overflow-y-auto z-50">
                                        @foreach($pegawaiResults as $pegawai)
                                            <button
                                                type="button"
                                                wire:click="selectPegawai({{ $pegawai->id }})"
                                                class="w-full text-left px-3 py-2 hover:bg-indigo-50
                                                    transition border-b border-gray-200 last:border-b-0 cursor-pointer"
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
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="text-sm text-gray-600 px-2">
                                    <i class="fa-solid fa-toggle-on mr-1 text-gray-400"></i>
                                    Status
                                </label>

                                <div class="flex gap-2">
                                    <div class="relative w-full">
                                        <select
                                            wire:model.live="formEdit.status"
                                            :disabled="!edit.status"
                                            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-md
                                            focus:outline-none focus:ring-2 focus:ring-indigo-400
                                            disabled:bg-gray-100 disabled:text-gray-500 enabled:cursor-pointer appearance-none"
                                        >
                                            <option value="active">Aktif</option>
                                            <option value="inactive">Nonaktif</option>
                                        </select>
                                        <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 text-gray-400 pointer-events-none"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                    <button
                                        @click="edit.status = true"
                                        class="mt-1 px-3 border border-gray-200 rounded hover:bg-gray-100 cursor-pointer"
                                    >
                                        <i class="fa-solid fa-pen text-gray-500"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-300 w-[95%] mx-auto" >

                <!-- Footer -->
                <div class="flex justify-end gap-3 px-4 py-3 bg-gray-50 rounded-b-lg">

                    <button
                        wire:click="close"
                        class="px-4 py-2 text-sm border border-gray-200 rounded hover:bg-gray-100 cursor-pointer"
                    >
                        Tutup
                    </button>

                    @if ($this->isDirty)
                        <button
                            wire:click="save"
                            @disabled(!$this->isDirty)
                            class="px-4 py-2 text-sm text-white bg-indigo-500
                            hover:bg-indigo-600 rounded cursor-pointer"
                        >
                            <i class="fa-solid fa-floppy-disk mr-1"></i>
                            Simpan Perubahan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>