<div class="flex flex-col space-y-4 h-[calc(100vh-280px)]">

    <div class="flex items-center justify-between gap-2">

        <div class="relative w-50" x-data="{ open: false }">
            <button @click="open = !open" class="filter-dropdown" type="button">
                @php
                    $ekstensiLabel = 'Semua Ekstensi';
                    foreach($ekstensiOptions as $val => $label) {
                        if($selectedEkstensiFile == $val) $ekstensiLabel = $label;
                    }
                @endphp
                <span class="truncate">{{ $ekstensiLabel }}</span>
                <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                </svg>
            </button>
            <div
                x-show="open" x-cloak
                @click.outside="open = false"
                x-transition class="dropdown-menu">
                <ul class="p-2 text-sm text-body font-medium">
                    <li>
                        <button
                            @click="$wire.set('selectedEkstensiFile', null); open = false"
                            class="dropdown-item">
                            Semua Ekstensi
                        </button>
                    </li>
                    @foreach($ekstensiOptions as $value => $label)
                        <li><button @click="$wire.set('selectedEkstensiFile', '{{ $value }}'); open = false" class="dropdown-item">{{ $label }}</button></li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="relative w-80">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="text" class="input-search block w-full ps-10 p-2.5" wire:model.live.debounce.300ms="search" placeholder="Cari Judul File ...">
            <button type="button" wire:click="$set('search', '')" x-show="$wire.search" x-cloak
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <div class="table-container relative flex-1">
        <div wire:loading wire:target="search, selectedEkstensiFile, sortBy, gotoPage">
            <div class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 flex items-center justify-center rounded-md">
                <x-ui.spinner />
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table w-full">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 w-16">No</th>
                        <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('judul')">
                            Judul <i class="fa-solid {{ $sortField === 'judul' ? ($sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} ml-1"></i>
                        </th>
                        <th class="px-4 py-3 text-center">Ekstensi</th>
                        <th class="px-4 py-3">Diupload Oleh</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sumberDayaList as $index => $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $sumberDayaList->firstItem() + $index }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->judul }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $ext = strtolower($item->extension);
                                    $badge = 'bg-gray-100 text-gray-800 border-gray-300';
                                    $icon = 'fa-file';

                                    if(in_array($ext, ['pdf'])) { $badge = 'bg-red-100 text-red-800 border-red-300'; $icon = 'fa-file-pdf'; }
                                    elseif(in_array($ext, ['doc', 'docx'])) { $badge = 'bg-blue-100 text-blue-800 border-blue-300'; $icon = 'fa-file-word'; }
                                    elseif(in_array($ext, ['xls', 'xlsx'])) { $badge = 'bg-green-100 text-green-800 border-green-300'; $icon = 'fa-file-excel'; }
                                    elseif(in_array($ext, ['ppt', 'pptx'])) { $badge = 'bg-orange-100 text-orange-800 border-orange-300'; $icon = 'fa-file-powerpoint'; }
                                @endphp
                                <span class="inline-flex items-center justify-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium border {{ $badge }}">
                                    <i class="fa-solid {{ $icon }}"></i> {{ strtoupper($ext) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $item->uploader->name ?? 'Sistem' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="downloadFile({{ $item->id }})" class="p-1.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-md transition-colors" title="Download File">
                                        <i class="fa-solid fa-download"></i>
                                    </button>

                                    @if (auth()->user()->hasRole(['Admin', 'SDM Yayasan']))
                                    <button @click="$dispatch('open-edit-modal'); $dispatch('edit-sumber-daya', { id: {{ $item->id }} })" class="p-1.5 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-md transition-colors" title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data sumber daya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $sumberDayaList->links() }}</div>
    </div>
</div>

<div x-data="{ openEditModal: false }" @open-edit-modal.window="openEditModal = true" @close-edit-modal.window="openEditModal = false">
    <template x-teleport="body">
        <div x-show="openEditModal" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md">

            <div x-show="openEditModal" @click.outside="openEditModal = false"
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