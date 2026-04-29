<div class="flex flex-col space-y-3 h-[calc(100vh-280px)] bg-white/20 backdrop-blur-sm shadow-md rounded-md p-4">
    <!-- Filter & Search -->
    <div class="flex items-center justify-between gap-2">
        <!-- Search -->
        <div class="relative w-77">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            </div>
            <input type="text" class="input-search"
                wire:model.live.debounce.300ms="search" placeholder="Cari Nama atau Kode Unit ...">
        </div>

        <!-- Filter -->
        <div class="flex gap-2">

            <!-- Filter Unit SDM -->
            <div class="relative w-46" x-data="{ open: false, selected: 'Semua Unit SDM' }">
                <button
                    @click="open = !open"
                    wire:model.live="selectedUnitSDM"
                    class="filter-dropdown"
                    type="button">
                        <span x-text="selected" class="truncate"></span>
                        <svg
                            class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                        </svg>
                </button>
                <!-- Menu -->
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="dropdown-menu">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="selected='Semua Masa Kerja'; open=false" class="dropdown-item" wire:click="$set('selectedMasaKerja', null)">
                                Semua Unit SDM
                            </button>
                        </li>
                        <li>
                            <button @click="selected='0-2 Tahun'; open=false" class="dropdown-item" wire:click="$set('selectedMasaKerja', '0-2 Tahun')">
                                SDM Yayasan
                            </button>
                        </li>
                        <li>
                            <button @click="selected='0-2 Tahun'; open=false" class="dropdown-item" wire:click="$set('selectedMasaKerja', '0-2 Tahun')">
                                SDM Universitas
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

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
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium text-center w-15">
                            #
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center w-36">
                            Kode Unit
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-72">
                            Nama Unit Kerja
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium">
                            Pimpinan Unit
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center">
                            Status
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center">
                            Total Pegawai
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center w-20">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 10; $i++)
                        <tr class="table-row">
                            <td class="px-4 py-3 text-center">
                                {{ $i }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                FTI
                            </td>
                            <td class="px-4 py-3 truncate">
                                Fakultas Teknologi Informasi
                            </td>
                            <td class="px-4 py-3 truncate">
                                Ambatukam
                            </td>
                            <td class="px-4 py-3 text-center">
                                Aktif
                            </td>
                            <td class="px-4 py-3 text-center">
                                100
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="#" class="text-primary hover:underline">Edit</a>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Footer & Pagination -->
        <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">
            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2" aria-label="Table navigation">
                <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                    Menampilkan
                    <span class="font-semibold text-heading">1-100</span> dari
                    <span class="font-semibold text-heading">1000 pegawai</span>
                </span>
                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button
                            wire:click="gotoPage(1)"
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                            >
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button
                            wire:click="previousPage"
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    <li>
                        <button
                            wire:click="nextPage"
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button
                        wire:click="#"
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                        >
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
     </div>

</div>
