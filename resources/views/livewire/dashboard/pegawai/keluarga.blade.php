<div class="flex flex-col space-y-6 h-[calc(100vh-250px)]">
    <!-- Header -->
    <div
        class="flex items-center justify-between bg-white rounded-md shadow-md border border-indigo-100 border-t-4 border-t-indigo-500 p-5">

        <!-- Header Information -->
        <div>
            <h3 class="text-xl font-bold text-gray-800">Data Keluarga</h3>
            <p class="text-sm text-gray-500 mt-0.5">
                <i class="fa-solid fa-people-roof mr-1.5 text-indigo-500"></i>
                Informasi daftar anggota keluarga
            </p>
        </div>

        <!-- Filter dan Button Tambah Data Keluarga-->
        <div class="flex flex-wrap items-center gap-3">

            <!-- Search -->
            <div class="relative w-75">
                <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" class="input-search bg-gray-50 border border-gray-300"
                    wire:model.live.debounce.300ms="search" placeholder="Cari Nama Anggota Keluarga ...">
                <!-- Clear Button -->
                <button type="button" wire:click="$set('search', '')" x-show="$wire.search"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Filter Hubungan Keluarga -->
            <div class="relative w-55 bg-gray-50" x-data="{ open: false }">
                <button @click="open = !open" class="filter-dropdown" type="button">
                    <span x-text="$wire.selectedHubungan ?? 'Hubungan Keluarga'" class="truncate"></span>
                    <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                    class="dropdown-menu max-h-[calc(100vh-480px)] overflow-auto">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="$wire.set('selectedHubungan', null); open=false" class="dropdown-item">
                                Hubungan Keluarga
                            </button>
                        </li>
                        @foreach ($hubunganKeluarga as $hubungan)
                            <li wire:key="status-{{ $loop->index }}">
                                <button @click="$wire.set('selectedHubungan', '{{ $hubungan }}'); open=false"
                                    class="dropdown-item">
                                    {{ $hubungan }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container relative rounded-md shadow-md border border-slate-100 border-t-4 border-t-slate-500">

        <!-- Loading -->
        <div wire:loading wire:target="">
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper">
            @if ($this->keluarga->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-people-roof text-2xl text-slate-600"></i>
                    </div>
                    <h4 class="text-base font-semibold text-slate-700">Data Keluarga Belum Tersedia</h4>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm">
                        {{ $this->emptyStateMessage }}
                    </p>
                </div>
            @else
                <table class="table w-full">
                    <thead class="table-header text-xs bg-slate-50/80 text-gray-700">
                        <tr>
                            <!-- Header Index-->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[4%] uppercase">#</th>

                            <!-- Nama Lengkap -->
                            <th scope="col" class="px-4 py-3 font-semibold w-[18%] uppercase">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="truncate">Nama Lengkap</span>
                                    <div>
                                        <button wire:click="sortBy('nama')"
                                            class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                            <i class="fa-solid {{ $this->sortIcon('nama') }}"></i>
                                        </button>
                                    </div>
                                </div>
                            </th>

                            <!-- Hubungan -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[16%] uppercase">Hubungan
                            </th>

                            <!-- Tempat, Tanggal Lahir -->
                            <th scope="col" class="px-4 py-3 font-semibold w-[18%] uppercase">
                                <div class="flex items-center justify-between gap-2">
                                    <span>Tempat, Tanggal Lahir</span>
                                    <div>
                                        <button wire:click="sortBy('tanggal_lahir')"
                                            class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                            <i class="fa-solid {{ $this->sortIcon('tanggal_lahir') }}"></i>
                                        </button>
                                    </div>
                                </div>
                            </th>

                            <!-- Pekerjaan -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[16%] uppercase">Pekerjaan
                            </th>

                            <!-- No. Telpon -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[16%] uppercase">No. Telpon
                            </th>

                            <!-- Aksi -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[10%] uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->keluarga as $k)
                            <tr class="table-row hover:bg-gray-50 transition">
                                <!-- Index -->
                                <td class="px-4 py-3 text-center text-gray-400 text-sm">
                                    {{ $this->keluarga->firstItem() + $loop->index }}
                                </td>

                                <!-- Nama Lengkap -->
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $k->nama }}
                                </td>

                                <!-- Hubungan -->
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $jenis = $k->jenisKeluarga->jenis ?? '-';
                                        $badgeClass = match ($jenis) {
                                            'Suami', 'Istri' => 'bg-pink-100 text-pink-700 border-pink-200',
                                            'Anak' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                            'Ayah', 'Ibu' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'Mertua' => 'bg-teal-100 text-teal-700 border-teal-200',
                                            'Saudara Laki-Laki',
                                            'Saudara Perempuan'
                                                => 'bg-amber-100 text-amber-700 border-amber-200',
                                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                                        };
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-md border {{ $badgeClass }}">
                                        {{ $jenis }}
                                    </span>
                                </td>

                                <!-- Tempat. Tanggal Lahir -->
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $k->tempat_lahir }},
                                    <span
                                        class="">{{ \Carbon\Carbon::parse($k->tanggal_lahir)->translatedFormat('d M Y') }}</span>
                                </td>

                                <!-- Pekerjaan -->
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    {{ $k->pekerjaan ?? '-' }}
                                </td>

                                <!-- No. Telpon -->
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    {{ $k->no_telpon ?? '-' }}
                                </td>

                                <!-- Aksi -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            x-on:click="openLoadingDetail = true; $wire.selectKeluarga({{ $k->id }})"
                                            class="p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition cursor-pointer"
                                            title="Edit Data">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button
                                            x-on:click="openDeleteModal = true; $wire.confirmDelete({{ $k->id }})"
                                            class="p-1.5 text-red-600 bg-red-50 hover:bg-red-100 rounded transition cursor-pointer"
                                            title="Hapus Data">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Footer & Pagination -->
        <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2"
                aria-label="Table navigation">
                <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                    Menampilkan
                    <span
                        class="font-semibold text-heading">{{ $this->keluarga->firstItem() }}-{{ $this->keluarga->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-heading">{{ $this->keluarga->total() }} keluarga</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click="gotoPage(1)" @disabled($this->keluarga->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click="previousPage" @disabled($this->keluarga->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $this->keluarga->currentPage() - 3); $i <= min($this->keluarga->lastPage(), $this->keluarga->currentPage() + 3); $i++)
                        <li>
                            <button wire:click="gotoPage({{ $i }})"
                                class="w-9 {{ $this->keluarga->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button wire:click="nextPage" @disabled(!$this->keluarga->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click="gotoPage({{ $this->keluarga->lastPage() }})" @disabled($this->keluarga->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>
</div>
