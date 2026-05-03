<div class="flex flex-col space-y-4 h-full">

    <!-- Filter & Search -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-gray-500">

      <!-- Filter -->
        <div class="flex flex-wrap gap-3">

            {{-- Role --}}
            <div class="relative w-48">
              	<select wire:model.live="selectedRole" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
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
                <select wire:model.live="selectedStatus" class="w-48 h-10 px-2 text-sm bg-white border border-gray-200 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer appearance-none">
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
            <!-- Search -->
            <div class="flex items-center w-full md:w-72 border border-gray-200 rounded-[10px] bg-white px-3">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 text-gray-400"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Cari Nama atau NIP..."
                    class="w-full h-10 px-2 text-sm outline-none"
                >
            </div>

            <!-- Add User Button -->
            <button wire:click="$dispatch('open-add-user')" class="flex items-center px-3 justify-center cursor-pointer bg-indigo-500 hover:bg-indigo-700 text-white text-sm rounded-[10px] transition">
                <i class="fa-solid fa-user-plus mr-2"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="flex flex-col flex-1 min-h-0 overflow-hidden bg-[#F5F7FA]/50 border border-gray-200 rounded-[20px] shadow-sm">
        <div class="flex-1 overflow-y-auto no-scrollbar">
            <table class="min-w-full text-sm">
                <!-- Header -->
                <thead class="bg-[#F5F7FA] text-gray-600 text-xs uppercase tracking-wider sticky top-0">
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
                        <th class="px-4 py-4 text-left font-semibold">Nama</th>
                        <th class="px-4 py-4 text-left font-semibold">NIP</th>
                        <th class="w-[285px] py-4 text-left font-semibold">Email</th>
                        <th class="w-[185px] py-4 text-center font-semibold">Role</th>
                        <th class="px-4 py-4 text-center font-semibold">Status</th>
                        <th class="px-4 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <!-- Body -->
                <tbody class="divide-y divide-[#878787]/30">
                    @foreach ($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-[#F5F7FA]/50 transition">

                        <!-- Username -->
                        <td class="px-4 py-4 font-medium text-gray-700 max-w-40 truncate">
                            {{ $user->username }}
                        </td>

                        <!-- Nama -->
                        <td class="px-4 py-4 max-w-40 truncate"
                            title="{{ $user->pegawai->nama ?? 'N/A' }}">
                            {{ $user->pegawai->nama ?? '-' }}
                        </td>

                        <!-- NIP -->
                        <td class="px-4 py-4 text-gray-600">
                            {{ $user->pegawai->nip ?? '-' }}
                        </td>

                        <!-- Email -->
                        <td class="w-[285px] py-4 text-gray-600">
                            {{ $user->email }}
                        </td>

                        <!-- Role -->
                        <td class="w-[185px] py-4 text-center">
                            @php
                                $role = $user->role->name ?? '';
                                $roleColor = $roleColors[$role] ?? $roleColors['default'];
                            @endphp
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $roleColor }}">
                                {{ $role }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-4 text-center">
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
                        <td class="px-4 py-4 text-center">
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