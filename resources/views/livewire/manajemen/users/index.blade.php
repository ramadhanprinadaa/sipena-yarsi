<div class="flex flex-col space-y-4 h-[calc(100vh-280px)]">

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
    <div class="table-container relative">
        <!-- Loading -->
        <div wire:loading>
            <div class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper">
            <table class="table">
                <!-- Header -->
                <thead class="table-header">
                    <tr>
                        <th
                            wire:click="sortBy('username')"
                            scope="col" class="px-4 py-3 font-medium w-30"
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
                        <th scope="col" class="px-4 py-3 font-medium w-40">Nama</th>
                        <th scope="col" class="px-4 py-3 font-medium w-30">NIP</th>
                        <th scope="col" class="px-4 py-3 font-medium w-40">Email</th>
                        <th scope="col" class="px-4 py-3 font-medium w-30">Role</th>
                        <th scope="col" class="px-4 py-3 font-medium w-20">Status</th>
                        <th scope="col" class="px-4 py-3 font-medium w-20">Aksi</th>
                    </tr>
                </thead>
                <!-- Body -->
                <tbody>
                    @foreach ($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="table-row">

                        <!-- Username -->
                        <td class="px-4 py-2 font-medium text-heading truncate">
                            {{ $user->username }}
                        </td>

                        <!-- Nama -->
                        <td class="px-4 py-2 font-medium truncate"
                            title="{{ $user->pegawai->nama ?? 'N/A' }}">
                            {{ $user->pegawai->nama ?? '-' }}
                        </td>

                        <!-- NIP -->
                        <td class="px-4 py-2 truncate">
                            {{ $user->pegawai->nip ?? '-' }}
                        </td>

                        <!-- Email -->
                        <td class="px-4 py-2 truncate">
                            {{ $user->email }}
                        </td>

                        <!-- Role -->
                        <td class="px-4 py-2">
                            @php
                                $role = $user->role->name ?? '';
                                $roleColor = $roleColors[$role] ?? $roleColors['default'];
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-medium rounded-md {{ $roleColor }}">
                                {{ $role }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-2">
                            @if ($user->status == 'active')
                                <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-green-100 text-green-700">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-red-100 text-red-700">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-2">
                            <button wire:click="openDetail({{ $user->id }})" class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition cursor-pointer">
                                Lihat
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer & Pagination -->
        <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                    Menampilkan
                    <span class="font-semibold text-heading">{{ $users->firstItem() }}-{{ $users->lastItem() }}</span> dari
                    <span class="font-semibold text-heading">{{ $users->total() }} pengguna</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button
                            wire:click="gotoPage(1)"
                            @disabled($users->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                            >
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button
                            wire:click="previousPage"
                            @disabled($users->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $users->currentPage() - 3);
                        $i <= min($users->lastPage(), $users->currentPage() + 3);
                        $i++)
                        <li>
                            <button
                                wire:click="gotoPage({{ $i }})"
                                class="w-9 {{ $users->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button
                            wire:click="nextPage"
                            @disabled(!$users->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button
                        wire:click="gotoPage({{ $users->lastPage() }})"
                        @disabled($users->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                        >
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>
</div>