<div class="flex flex-col space-y-3 h-[calc(100vh-280px)] bg-white/20 backdrop-blur-md shadow-md rounded-xl p-4">
    
    <!-- Filter & Search -->
    <div class="flex items-center justify-between gap-2">
        <!-- Search -->
        <div class="relative w-87">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            </div>
            <input type="text" class="input-search text-sm"
                wire:model.live.debounce.300ms="search" placeholder="Cari Nama Unit atau Pimpinan ...">
        </div>

        <!-- Filter -->
        <div class="flex gap-2">

            <!-- Filter Unit SDM -->
            <div class="relative w-46" x-data="{ open: false, selected: 'Semua Unit SDM' }">
                <button
                    @click="open = !open"
                    wire:model.live="selectedUnitSdm"
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
                            <button @click="selected='Semua Unit SDM'; open=false" class="dropdown-item" wire:click="$set('selectedUnitSdm', '')">
                                Semua Unit SDM
                            </button>
                        </li>
                        <li>
                            <button @click="selected='SDM Yayasan'; open=false" class="dropdown-item" wire:click="$set('selectedUnitSdm', '1')">
                                SDM Yayasan
                            </button>
                        </li>
                        <li>
                            <button @click="selected='SDM Universitas'; open=false" class="dropdown-item" wire:click="$set('selectedUnitSdm', '2')">
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
                        <th scope="col" class="px-4 py-3 font-medium text-center w-18">
                            #
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-72">
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate">Nama Unit Kerja</span>
                                <div>
                                    <button wire:click="sortBy('unit_kerja.name')"
                                        class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                        <i class="fa-solid {{ $this->sortIcon('unit_kerja.name') }}"></i>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium">
                            Pimpinan Unit
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center">
                            Total Pegawai
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center">
                            Unit SDM
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-center w-32">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($unit_kerja as $unit)
                        <tr class="table-row">
                            <td class="px-4 py-2 text-center">
                                {{ $unit_kerja->firstItem() + $loop->index }}
                            </td>
                            <td class="px-4 py-2 font-medium text-heading truncate">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        @php
                                            $nama = explode(' ', $unit->name);
                                        @endphp
                                        <span class="text-indigo-600 text-xs font-semibold">
                                            {{ strtoupper(substr($nama[0],0,1) . ($nama[1][0] ?? '') . ($nama[2][0] ?? '')) }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-700 truncate">
                                        {{ $unit->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-2 truncate">
                                <div class="flex items-center gap-2">
                                    @if ( $unit->pimpinan_id != null)
                                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                            @php
                                                $nama = explode(' ', $unit->pimpinan->nama);
                                            @endphp
                                            <span class="text-amber-600 text-xs font-semibold">
                                                {{ strtoupper(substr($nama[0],0,1) . ($nama[1][0] ?? '')) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-700 truncate">
                                            {{ $unit->pimpinan->nama }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-sm">Belum ditentukan</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center gap-3">
                                <span class="text-sm font-medium text-gray-700">{{ $unit->pegawai->count() }}</span>
                                <span class="text-xs text-gray-400 ml-0.5">org</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if ($unit->unit_sdm_id == 1)
                                    <span class="px-2.5 py-1 text-xs font-medium bg-violet-100 text-violet-600 rounded-md">
                                        SDM Yayasan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-md">
                                        SDM Universitas
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">
                                <button
                                    wire:click="openDetail({{ $unit->id }})"
                                    class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition cursor-pointer">
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
                    <span class="font-semibold text-heading">{{ $unit_kerja->firstItem() }}-{{ $unit_kerja->lastItem() }}</span> dari
                    <span class="font-semibold text-heading">{{ $unit_kerja->total() }} unit kerja</span>
                </span>
                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button
                            wire:click="gotoPage(1)"
                            @disabled($unit_kerja->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                            >
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button
                            wire:click="previousPage"
                            @disabled($unit_kerja->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $unit_kerja->currentPage() - 3);
                        $i <= min($unit_kerja->lastPage(), $unit_kerja->currentPage() + 3);
                        $i++)
                        <li>
                            <button
                                wire:click="gotoPage({{ $i }})"
                                class="w-9 {{ $unit_kerja->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button
                            wire:click="nextPage"
                            @disabled(!$unit_kerja->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button
                        wire:click="gotoPage({{ $unit_kerja->lastPage() }})"
                        @disabled($unit_kerja->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                        >
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
     </div>

</div>
