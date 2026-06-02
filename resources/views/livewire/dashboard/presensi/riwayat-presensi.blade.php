<div x-data="{ showLoading: false, openExport: false }" x-on:open-export="showLoading = false; openExport = true;" class="space-y-6">


    <!-- Header & Action Bar -->
    <div
        class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Riwayat Presensi</h3>
            <p class="text-sm text-gray-500 mt-0.5">
                <i class="fa-regular fa-calendar-check mr-1.5 text-teal-500"></i>
                Periode:
                <span class="font-medium text-gray-700">
                    {{ $this->infoPeriodeAktif }}
                </span>
            </p>
        </div>

        {{-- Filter --}}
        <div class="flex flex-wrap items-center gap-2">

            {{-- Filter Status Kehadiran --}}
            <div class="relative" x-data="{ open: false }">
                {{-- Tombol Dropdown --}}
                <button @click="open = !open"
                    class="flex items-center justify-between w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-teal-500 focus:border-teal-500 focus:ring-1 p-2.5 transition cursor-pointer"
                    type="button">
                    <span x-text="$wire.selectedStatusKehadiran ?? 'Semua Status Kehadiran'" class="truncate"></span>
                    <svg class="w-4 h-4 ms-1.5 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                    </svg>
                </button>

                {{-- Isi Dropdown (Melayang) --}}
                <div x-show="open" @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-80 overflow-y-auto" style="display: none;">

                    <ul class="p-1.5 text-sm text-gray-700 font-medium">
                        <li>
                            <button @click="$wire.set('selectedStatusKehadiran', null); open=false"
                                class="w-full text-left px-3 py-2 rounded-md hover:bg-teal-50 hover:text-teal-700 transition-colors {{ !$selectedStatusKehadiran ? 'bg-teal-50 text-teal-700' : '' }}">
                                Semua Status Kehadiran
                            </button>
                        </li>

                        @foreach ($this->statusKehadiranList as $status)
                            <li wire:key="status-{{ $loop->index }}">
                                <button
                                    @click="$wire.set('selectedStatusKehadiran', '{{ $status }}'); open=false"
                                    class="w-full text-left px-3 py-2 mt-0.5 rounded-md hover:bg-teal-50 hover:text-teal-700 transition-colors {{ $selectedStatusKehadiran === $status ? 'bg-teal-50 text-teal-700' : '' }}">
                                    {{ $status }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Periode Mulai --}}
            <div x-data="{ picker: null }" x-init="picker = new Datepicker($refs.
                input, {
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    language: 'id'
                });
                $refs.input.addEventListener('changeDate', () => {
                    $wire.set('selectedPeriodeMulai', $refs.input.value);
                });"
            class="relative w-48">

                <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                    <i class="fa-regular fa-calendar text-gray-400"></i>
                </div>
                <input type="text" x-ref="input" wire:model.live="selectedPeriodeMulai"
                    class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-teal-500 focus:border-teal-500 block w-full ps-10 p-2.5 cursor-pointer"
                    placeholder="Pilih Periode Mulai">
                <button type="button" x-show="$wire.selectedPeriodeMulai"
                    @click="
                    $wire.set('selectedPeriodeMulai', null);
                    picker.setDate({ clear: true });
                    $refs.input.value = '';"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Periode Selesai --}}
            <div x-data="{ picker: null }" x-init="picker = new Datepicker($refs.
                input, {
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    language: 'id'
                });
                $refs.input.addEventListener('changeDate', () => {
                    $wire.set('selectedPeriodeSelesai', $refs.input.value);
                });"
            class="relative w-48">

                <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                    <i class="fa-regular fa-calendar text-gray-400"></i>
                </div>
                <input type="text" x-ref="input" wire:model.live="selectedPeriodeSelesai"
                    class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-teal-500 focus:border-teal-500 block w-full ps-10 p-2.5 cursor-pointer"
                    placeholder="Pilih Periode Selesai">
                <button type="button" x-show="$wire.selectedPeriodeSelesai"
                    @click="
                    $wire.set('selectedPeriodeSelesai', null);
                    picker.setDate({ clear: true });
                    $refs.input.value = '';"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container relative lg:h-[calc(100vh-280px)]">

        <!-- Loading -->
        <div wire:loading
            wire:target="selectedStatusKehadiran, selectedPeriodeMulai, selectedPeriodeSelesai, gotoPage, previousPage, nextPage"
            class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-sm rounded-xl">
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div class="w-8 h-8 border-4 border-teal-200 border-t-teal-600 rounded-full animate-spin mb-2"></div>
                <span class="text-sm font-medium text-teal-700">Memuat Data...</span>
            </div>
        </div>

        <!-- Main Content (Tabel) -->
        <div class="table-wrapper overflow-x-auto">
            @if ($this->riwayatData->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div
                        class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4 border border-slate-100 shadow-sm">
                        <i class="fa-solid fa-calendar-minus text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-base font-semibold text-slate-700 max-w-sm">
                        {{ $this->emptyStateMessage }}
                    </h3>
                </div>
            @else
                <table class="table w-full table-fixed">
                    <thead class="table-header">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[5%] text-center">#</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[20%] text-left">Tanggal Presensi</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[15%] text-center">Jam Masuk</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[15%] text-center">Jam Pulang</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[15%] text-center">Total Jam</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[18%] text-center">Status Kehadiran
                            </th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[12%] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($this->riwayatData as $riwayat)
                            <tr class="table-row">
                                <td class="px-3 py-2 text-center text-gray-500 text-sm">
                                    {{ $this->riwayatData->firstItem() + $loop->index }}
                                </td>

                                <td class="px-3 py-2 text-sm font-semibold text-gray-700 truncate">
                                    {{ $riwayat->tanggal?->translatedFormat('l, d F Y') ?? '-' }}
                                </td>

                                <td class="px-3 py-2 text-center text-sm text-gray-600">
                                    {{ $riwayat->jam_masuk?->format('H:i:s') ?? '-' }}
                                </td>

                                <td class="px-3 py-2 text-center text-gray-600">
                                    {{ $riwayat->jam_keluar?->format('H:i:s') ?? '-' }}</td>

                                <td class="px-3 py-2 text-center text-gray-800 font-bold truncate">
                                    {{ $riwayat->total_jam_kerja ?? '-' }}
                                </td>

                                <td class="px-3 py-2 text-center truncate">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded text-xs font-semibold"
                                        style="{{ $riwayat->statusKehadiran?->badge_style }}">
                                        {{ $riwayat->statusKehadiran?->status }}
                                    </span>
                                </td>

                                <td class="px-3 py-2 text-center">
                                    <button @click="$dispatch('open-loading-detail-riwayat')"
                                        wire:click="showDetailRiwayat({{ $riwayat->id }})"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition-all duration-200 cursor-pointer">
                                        Detail
                                    </button>
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
                        class="font-semibold text-heading">{{ $this->riwayatData->firstItem() }}-{{ $this->riwayatData->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-heading">{{ $this->riwayatData->total() }} data</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click="gotoPage(1)" @disabled($this->riwayatData->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click="previousPage" @disabled($this->riwayatData->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $this->riwayatData->currentPage() - 3); $i <= min($this->riwayatData->lastPage(), $this->riwayatData->currentPage() + 3); $i++)
                        <li>
                            <button wire:click="gotoPage({{ $i }})"
                                class="w-9 {{ $this->riwayatData->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button wire:click="nextPage" @disabled(!$this->riwayatData->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click="gotoPage({{ $this->riwayatData->lastPage() }})"
                        @disabled($this->riwayatData->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>

</div>
