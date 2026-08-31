<div x-data="{
        openEditModal: false,
        openLoadingDetail: false,
    }"
    @edit-sumber-daya-loaded.window="openLoadingDetail = false; openEditModal = true;"
    @open-edit-modal.window="openEditModal = true"
    @close-edit-modal.window="openEditModal = false"
    class="flex flex-col h-[calc(100vh-280px)]">

    <!-- Filter Search -->
    <div class="flex items-center justify-between gap-2">

        <!-- Filter Ekstensi File -->
        <div class="relative w-50" x-data="{ open: false }">
            <button x-on:click="open = !open" class="filter-dropdown" type="button">
                @php
                    $ekstensiLabel = 'Semua Ekstensi File';
                    foreach ($ekstensiOptions as $val => $label) {
                        if ($selectedEkstensiFile == $val) {
                            $ekstensiLabel = $label;
                        }
                    }
                @endphp
                <span class="truncate">{{ $ekstensiLabel }}</span>
                <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 9-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-cloak @click.outside="open = false" x-transition class="dropdown-menu">
                <ul class="p-2 text-sm text-body font-medium">
                    <li>
                        <button @click="$wire.set('selectedEkstensiFile', null); open = false" class="dropdown-item">
                            Semua Ekstensi File
                        </button>
                    </li>
                    @foreach ($ekstensiOptions as $value => $label)
                        <li><button @click="$wire.set('selectedEkstensiFile', '{{ $value }}'); open = false"
                                class="dropdown-item">{{ $label }}</button></li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Search -->
        <div class="relative w-80">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="text" class="input-search" wire:model.live.debounce.300ms="search"
                placeholder="Cari Judul File ...">
            <!-- Clear Button -->
            <button type="button" wire:click="$set('search', '')" x-show="$wire.search"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container relative mt-4">

        <!-- Loading -->
        <div wire:loading wire:target="search, selectedEkstensiFile">
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper">
            @if ($this->sumberDaya->isEmpty())
                @php
                    $user = auth()->user();
                @endphp
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-folder-open text-2xl text-gray-500"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">
                        {{ $this->emptyStateMessage }}
                    </h3>
                    @if ($user->hasRole('Admin') || $user->hasRole('SDM Yayasan'))
                        <p class="text-sm text-gray-500 mt-1 max-w-sm">
                            Silahkan Upload Sumber Daya.
                        </p>
                    @else
                        <p class="text-sm text-gray-500 mt-1 max-w-sm">
                            Silahkan Hubungi Administrator.
                        </p>
                    @endif
                </div>
            @else
                <table class="table w-full">
                    <thead class="table-header">
                        <tr class="text-xs uppercase font-semibold">
                            <th scope="col" class="px-4 py-3 text-center w-[5%]">
                                #
                            </th>
                            <th scope="col" class="px-4 py-3 w-[35%]">
                                <div class="flex items-center gap-2">
                                    <span class="truncate">Judul Dokumen</span>
                                    <div>
                                        <button wire:click="sortBy('judul')"
                                            class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                            <i class="fa-solid {{ $this->sortIcon('judul') }}"></i>
                                        </button>
                                    </div>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3 w-[15%]">Ekstensi</th>
                            <th scope="col" class="px-4 py-3 w-[15%] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->sumberDaya as $item)
                            <tr class="table-row hover:bg-gray-50 transition">

                                <!-- Nomor -->
                                <td class="px-4 py-3 text-center">
                                    {{ $this->sumberDaya->firstItem() + $loop->index }}
                                </td>

                                <!-- Judul Dokumen -->
                                <td class="px-4 py-3 font-medium text-gray-900 truncate">
                                    {{ $item->judul }}
                                </td>

                                <!-- Ekstensi File -->
                                <td class="px-4 py-3">
                                    @php
                                        $ext = strtolower($item->extension);
                                        $badge = 'bg-gray-100 text-gray-800 border-gray-300';
                                        $icon = 'fa-file';

                                        if (in_array($ext, ['pdf'])) {
                                            $badge = 'bg-red-100 text-red-800 border-red-300';
                                            $icon = 'fa-file-pdf';
                                        } elseif (in_array($ext, ['doc', 'docx'])) {
                                            $badge = 'bg-blue-100 text-blue-800 border-blue-300';
                                            $icon = 'fa-file-word';
                                        } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                            $badge = 'bg-green-100 text-green-800 border-green-300';
                                            $icon = 'fa-file-excel';
                                        } elseif (in_array($ext, ['ppt', 'pptx'])) {
                                            $badge = 'bg-orange-100 text-orange-800 border-orange-300';
                                            $icon = 'fa-file-powerpoint';
                                        } else {
                                            $badge = 'bg-gray-100 text-gray-800 border-gray-300';
                                            $icon = 'fa-file';
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center justify-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium border {{ $badge }}">
                                        <i class="fa-solid {{ $icon }}"></i> {{ strtoupper($ext) }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            wire:click="downloadFile({{ $item->id }})"
                                            class="py-1.5 px-2 text-white bg-indigo-600 hover:bg-indigo-100 hover:text-indigo-600 rounded-md transition-colors cursor-pointer"
                                            title="Download File">
                                            <i class="fa-solid fa-circle-arrow-down mr-1"></i>
                                            <span class="text-xs">
                                                Download
                                            </span>
                                        </button>

                                        @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan']))
                                            <button
                                                x-on:click="openLoadingDetail = true; $wire.editSumberDaya({{ $item->id }})"
                                                class="py-1.5 px-2 text-white bg-amber-500 hover:bg-amber-100 hover:text-amber-500 rounded-md transition-colors cursor-pointer"
                                                title="Edit Data">
                                                <i class="fa-solid fa-pen-to-square mr-1"></i>
                                                <span class="text-xs">
                                                    Edit
                                                </span>
                                            </button>
                                        @endif
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
                        class="font-semibold text-heading">{{ $this->sumberDaya->firstItem() }}-{{ $this->sumberDaya->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-heading">{{ $this->sumberDaya->total() }} Sumber Daya</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click="gotoPage(1)" @disabled($this->sumberDaya->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click="previousPage" @disabled($this->sumberDaya->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $this->sumberDaya->currentPage() - 3); $i <= min($this->sumberDaya->lastPage(), $this->sumberDaya->currentPage() + 3); $i++)
                        <li>
                            <button wire:click="gotoPage({{ $i }})"
                                class="w-9 {{ $this->sumberDaya->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button wire:click="nextPage" @disabled(!$this->sumberDaya->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click="gotoPage({{ $this->sumberDaya->lastPage() }})" @disabled($this->sumberDaya->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modal Edit Pegawai -->
    <template x-teleport="body">
        <div x-show="openEditModal || openLoadingDetail" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="openEditModal = false"
            @keydown.escape.window="openEditModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
            style="display: none;">

            <div x-show="openLoadingDetail" class="flex flex-col items-center gap-4">
                <div class="w-10 h-10 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                <div class="text-sm font-medium tracking-wide text-white">
                    Memuat Data...
                </div>
            </div>

            <div
                x-show="openEditModal && !openLoadingDetail"
                x-cloak
                @click.outside="openEditModal = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95">
                <livewire:resources.edit-sumber-daya />
            </div>
        </div>
    </template>
</div>
