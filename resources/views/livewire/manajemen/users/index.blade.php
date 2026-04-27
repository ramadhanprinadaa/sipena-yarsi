<div class="flex flex-col space-y-4 h-full">

    <!-- Filter & Search -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

      <!-- Filter -->
        <div class="flex flex-wrap gap-3">

            {{-- Role --}}
            <div class="relative w-48">
              	<select wire:model.live="selectedRole" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-md focus:outline-none focus:ring-4 focus:ring-indigo-500 cursor-pointer appearance-none">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <div class="relative w-48">
                <select wire:model.live="selectedStatus" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-md focus:outline-none focus:ring-4 focus:ring-indigo-500 cursor-pointer appearance-none">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div class="flex gap-3">
            {{-- Search --}}
            <div class="relative w-72">
                <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                </div>
                <input
                    type="text"
                    class="input-search h-10"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari Nama atau NIP ...">
            </div>

            <!-- Add User Button -->
            <button wire:click="$dispatch('open-add-user')" class="flex items-center px-3 justify-center cursor-pointer bg-indigo-500 hover:bg-indigo-700 text-white text-sm rounded-md transition">
                <i class="fa-solid fa-user-plus mr-2"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="flex-1 overflow-y-auto no-scrollbar">
            <table class="min-w-full text-sm">
                <!-- Header -->
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider sticky top-0">
                    <tr>
                        <th
                            wire:click="sortBy('username')"
                            class="px-4 py-3 text-left font-semibold cursor-pointer select-none"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <span>Username</span>
                                <span class="text-gray-500">
                                    @if(true)
                                        <i class="fa-solid fa-sort-up"></i>
                                    @else
                                        <i class="fa-solid fa-sort-down"></i>
                                    @endif
                                </span>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold">NIP</th>
                        <th class="px-4 py-3 text-left font-semibold">Email</th>
                        <th class="px-4 py-3 text-left font-semibold">Role</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <!-- Body -->
                <tbody class="divide-y divide-gray-100">
                    @foreach ($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50 transition">

                        <!-- Username -->
                        <td class="px-4 py-3 font-medium text-gray-700 max-w-40 truncate">
                            {{ $user->username }}
                        </td>

                        <!-- Nama -->
                        <td class="px-4 py-3 max-w-40 truncate"
                            title="{{ $user->pegawai->nama ?? 'N/A' }}">
                            {{ $user->pegawai->nama ?? '-' }}
                        </td>

                        <!-- NIP -->
                        <td class="px-4 py-3 text-gray-600">
                            {{ $user->pegawai->nip ?? '-' }}
                        </td>

                        <!-- Email -->
                        <td class="px-4 py-3 text-gray-600 max-w-48 truncate">
                            {{ $user->email }}
                        </td>

                        <!-- Role -->
                        <td class="px-4 py-3">
                            @php
                                $role = $user->role->name ?? '';
                                $roleColor = $roleColors[$role] ?? $roleColors['default'];
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $roleColor }}">
                                {{ $role }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-3">
                            @if ($user->status == 'active')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3">
                            <button wire:click="openDetail({{ $user->id }})" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                Lihat
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-2 bg-gray-100 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>