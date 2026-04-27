<div class="flex flex-col space-y-4 h-[calc(100vh-280px)]">

    <!-- Filter & Search -->
    <div class="flex items-center justify-between gap-2">

        <!-- Filter -->
        <div class="flex gap-2">

            @if(auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                <!-- Filter Unit Kerja -->
                <div class="relative w-42" x-data="{ open: false, selected: 'Semua Unit Kerja' }">
                    <button
                        @click="open = !open"
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
                                <button @click="selected='Semua Unit Kerja'; open=false" class="dropdown-item">
                                    Semua Unit Kerja
                                </button>
                            </li>
                            <li>
                                <button @click="selected='Fakultas Teknologi Informasi'; open=false" class="dropdown-item">
                                    Fakultas Teknologi Informasi
                                </button>
                            </li>
                            <li>
                                <button @click="selected='Sekretariat Yayasan'; open=false" class="dropdown-item">
                                    Sekretariat Yayasan
                                </button>
                            </li>
                            <li>
                                <button @click="selected='Masjid'; open=false" class="dropdown-item">
                                    Masjid
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Filter Gelar -->
            <div class="relative w-34" x-data="{ open: false, selected: 'Semua Gelar' }">
                <button
                    @click="open = !open"
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
                            <button @click="selected='Semua Gelar'; open=false" class="dropdown-item">
                                Semua Gelar
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Sarjana'; open=false" class="dropdown-item">
                                Sarjana
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Magister'; open=false" class="dropdown-item">
                                Magister
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Doktor'; open=false" class="dropdown-item">
                                Doktor
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Professor'; open=false" class="dropdown-item">
                                Professor
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Filter Status -->
            <div class="relative w-36" x-data="{ open: false, selected: 'Semua Status' }">
                <button
                    @click="open = !open"
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
                            <button @click="selected='Semua Status'; open=false" class="dropdown-item">
                                Semua Status
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Aktif'; open=false" class="dropdown-item">
                                Aktif
                            </button>
                        </li>
                        <li>
                            <button @click="selected='Nonaktif'; open=false" class="dropdown-item">
                                Nonaktif
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Filter Rentang Masa Kerja -->
            <div class="relative w-62" x-data="{ open: false, selected: 'Semua Rentang Masa Kerja' }">
                <button
                    @click="open = !open"
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
                            <button @click="selected='Semua Rentang Masa Kerja'; open=false" class="dropdown-item">
                                Semua Rentang Masa Kerja
                            </button>
                        </li>
                        <li>
                            <button @click="selected='1-5 Tahun'; open=false" class="dropdown-item">
                                1-5 Tahun
                            </button>
                        </li>
                        <li>
                            <button @click="selected='5-10 Tahun'; open=false" class="dropdown-item">
                                5-10 Tahun
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="relative w-77">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            </div>
            <input type="text" class="input-search" placeholder="Cari Nama atau NIP ...">
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">

        <!-- Main Content -->
        <div class="table-wrapper">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium w-36">
                            Nama Pegawai
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-32">
                            NIK Pegawai
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-28">
                            Usia (Tahun)
                        </th>
                        @if (auth()->user()->HasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                            <th scope="col" class="px-4 py-3 font-medium w-36">
                                Unit Kerja
                            </th>
                        @endif
                        <th scope="col" class="px-4 py-3 font-medium w-40">
                            Tanggal Bergabung
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-36">
                            Tanggal Pensiun
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center w-20">
                            Status
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center w-20">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 10; $i++)
                        <tr class="table-row">
                            <th scope="row" class="px-4 py-2 font-medium text-heading truncate">
                                Ambatukam Rodok
                            </th>
                            <td class="px-4 py-2 truncate">
                                12345678
                            </td>
                            <td class="px-4 py-2">
                                20
                            </td>
                            <td class="px-4 py-2 truncate">
                                Fakultas Teknologi Informasi
                            </td>
                            <td class="px-4 py-2">
                                19 Agustus 2022
                            </td>
                            <td class="px-4 py-2">
                                19 Agustus 2030
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if (false)
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-green-100 text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-red-100 text-red-700">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">
                                <a
                                    wire:navigate href="{{route('manajemen-pegawai-detail', ['id' => 1])}}"
                                    class="px-2 py-1 text-xs text-white rounded-md bg-blue-600 hover:bg-blue-700 transition">
                                    Lihat
                                </a>
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
                    <span class="font-semibold text-heading">1-10</span> dari
                    <span class="font-semibold text-heading">1000</span>
                </span>

                <ul class="flex -space-x-px text-sm">
                    <li>
                        <a href="#" class="table-pagination-btn rounded-s-base text-sm px-3">Previous</a>
                    </li>
                    <li>
                        <a href="#" class="table-pagination-btn w-9">1</a>
                    </li>
                    <li>
                        <a href="#" class="table-pagination-btn w-9">2</a>
                    </li>
                    <!-- Active Page Button -->
                    <li>
                        <a href="#" class="table-pagination-btn-active">3</a>
                    </li>
                    <li>
                        <a href="#" class="table-pagination-btn w-9">...</a>
                    </li>
                    <li>
                        <a href="#" class="table-pagination-btn w-9">5</a>
                    </li>
                    <li>
                        <a href="#" class="table-pagination-btn rounded-e-base text-sm px-3">Next</a>
                    </li>
                </ul>
            </nav>
        </div>

    </div>
</div>
