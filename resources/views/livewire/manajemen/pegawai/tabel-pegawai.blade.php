<div class="flex flex-col space-y-4 h-full">

    <!-- Filter & Search -->
    <div class="flex items-center justify-between">

        <!-- Filter -->
        <div class="flex gap-2">

            @if(auth()->user()->hasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                <!-- Filter Unit Kerja -->
                <div class="relative w-44" x-data="{ open: false, selected: 'Semua Unit Kerja' }">
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
            <div class="relative w-44" x-data="{ open: false, selected: 'Semua Gelar' }">
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
            <div class="relative w-44" x-data="{ open: false, selected: 'Semua Status' }">
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
            <div class="relative w-64" x-data="{ open: false, selected: 'Semua Rentang Masa Kerja' }">
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
        <div class="relative w-76">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            </div>
            <input type="text" class="input-search" placeholder="Cari Nama atau NIP ...">
        </div>
    </div>

    <!-- Table -->
    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-md border border-default">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-default-medium">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Nama Pegawai
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        NIK Pegawai
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Usia
                    </th>
                    @if (auth()->user()->HasRole(['Admin', 'SDM Yayasan', 'SDM Universitas']))
                        <th scope="col" class="px-6 py-3 font-medium">
                            Unit Kerja
                        </th>
                    @endif
                    <th scope="col" class="px-6 py-3 font-medium">
                        Tanggal Bergabung
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Tanggal Pensiun
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <th scope="row" class="px-6 py-2 font-medium text-heading whitespace-nowrap">
                        Apple MacBook Pro 17"
                    </th>
                    <td class="px-6 py-2">
                        Silver
                    </td>
                    <td class="px-6 py-2">
                        Laptop
                    </td>
                    <td class="px-6 py-2">
                        $2999
                    </td>
                    <td class="px-6 py-2 text-right">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <th scope="row" class="px-6 py-2 font-medium text-heading whitespace-nowrap">
                        Apple MacBook Pro 17"
                    </th>
                    <td class="px-6 py-2">
                        Silver
                    </td>
                    <td class="px-6 py-2">
                        Laptop
                    </td>
                    <td class="px-6 py-2">
                        $2999
                    </td>
                    <td class="px-6 py-2 text-right">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <th scope="row" class="px-6 py-2 font-medium text-heading whitespace-nowrap">
                        Apple MacBook Pro 17"
                    </th>
                    <td class="px-6 py-2">
                        Silver
                    </td>
                    <td class="px-6 py-2">
                        Laptop
                    </td>
                    <td class="px-6 py-2">
                        $2999
                    </td>
                    <td class="px-6 py-2 text-right">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <th scope="row" class="px-6 py-2 font-medium text-heading whitespace-nowrap">
                        Apple MacBook Pro 17"
                    </th>
                    <td class="px-6 py-2">
                        Silver
                    </td>
                    <td class="px-6 py-2">
                        Laptop
                    </td>
                    <td class="px-6 py-2">
                        $2999
                    </td>
                    <td class="px-6 py-2 text-right">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <th scope="row" class="px-6 py-2 font-medium text-heading whitespace-nowrap">
                        Apple MacBook Pro 17"
                    </th>
                    <td class="px-6 py-2">
                        Silver
                    </td>
                    <td class="px-6 py-2">
                        Laptop
                    </td>
                    <td class="px-6 py-2">
                        $2999
                    </td>
                    <td class="px-6 py-2 text-right">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <th scope="row" class="px-6 py-2 font-medium text-heading whitespace-nowrap">
                        Apple MacBook Pro 17"
                    </th>
                    <td class="px-6 py-2">
                        Silver
                    </td>
                    <td class="px-6 py-2">
                        Laptop
                    </td>
                    <td class="px-6 py-2">
                        $2999
                    </td>
                    <td class="px-6 py-2 text-right">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <th scope="row" class="px-6 py-2 font-medium text-heading whitespace-nowrap">
                        Apple MacBook Pro 17"
                    </th>
                    <td class="px-6 py-2">
                        Silver
                    </td>
                    <td class="px-6 py-2">
                        Laptop
                    </td>
                    <td class="px-6 py-2">
                        $2999
                    </td>
                    <td class="px-6 py-2 text-right">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
