<div class="flex flex-col h-full lg:h-[calc(100vh-180px)] space-y-4 px-2 py-3">
    <!-- Header -->
    <div class="flex-none flex gap-2 font-poppins">
        <h1 class="text-2xl font-semibold">Riwayat Impor File Presensi</h1>
    </div>

    <!-- Filter & Search -->
    <div class="flex-none flex flex-col sm:flex-row items-center justify-between gap-3">

        <!-- Date Picker Periode -->
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">

            <!-- Date Picker Periode Mulai -->
            <div x-data="{ picker: null }" x-init="picker = new Datepicker($refs.input, {
                format: 'dd/mm/yyyy',
                autohide: true,
                language: 'id'
            });

            $refs.input.addEventListener('changeDate', () => {
                $wire.set('selectedPeriodeMulai', $refs.input.value);
            });" class="relative w-full sm:w-auto">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                </div>
                <input type="text" x-ref="input" wire:model.live="selectedPeriodeMulai" placeholder="Dari Tanggal"
                    class="w-full h-10 pl-9 pr-3 text-bs border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-3 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition" />
                <button type="button" x-show="$wire.selectedPeriodeMulai"
                    @click="
                    $wire.set('selectedPeriodeMulai', null);
                    picker.setDate({ clear: true });
                    $refs.input.value = '';"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Date Picker Periode Selesai -->
            <div x-data="{ picker: null }" x-init="picker = new Datepicker($refs.input, {
                format: 'dd/mm/yyyy',
                autohide: true,
                language: 'id'
            });

            $refs.input.addEventListener('changeDate', () => {
                $wire.set('selectedPeriodeSelesai', $refs.input.value);
            });" class="relative w-full sm:w-auto">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
                </div>
                <input type="text" x-ref="input" placeholder="Sampai Tanggal"
                    class="w-full h-10 pl-9 pr-3 text-base border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-3 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition" />
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

        <!-- Search -->
        <div class="relative w-70">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="text" class="input-search placeholder-gray-400" wire:model.live.debounce.300ms="search"
                placeholder="Cari Nama File / Uploader ..." />
            <!-- Clear Button -->
            <button type="button" wire:click="$set('search', '')" x-show="$wire.search"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

    </div>

    <!-- Table -->
    <div class="table-container relative">
        <!-- Loading -->
        <div wire:loading
            wire:target="selectedPeriodeMulai, selectedPeriodeSelesai, search, previousPage, nextPage, gotoPage, refreshTable">
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper overflow-auto flex-1">
            @if ($this->filePresensi->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-file-arrow-up text-2xl text-indigo-500"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">
                        Tidak ada data
                    </h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm">
                        Silahkan upload file presensi.
                    </p>
                </div>
            @else
                <table class="table">
                    <thead class="table-header">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium w-[5%] text-center">#</th>
                            <th scope="col" class="px-3 py-3 font-medium w-[19%] text-left truncate">
                                Nama File
                            </th>
                            <th scope="col" class="px-3 py-3 font-medium w-[15%] text-left truncate">
                                Uploaded By
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[17%] text-center truncate">
                                Periode Mulai
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[17%] text-center truncate">
                                Periode Selesai
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[17%] text-center truncate">
                                Tanggal Upload
                            </th>
                            <th scope="col" class="px-4 py-3 font-medium w-[10%] text-center truncate">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="self-start">
                        @foreach ($this->filePresensi as $file)
                            <tr class="table-row">
                                <!-- No -->
                                <td class="px-3 py-2 text-center text-gray-500 text-sm">
                                    {{ $this->filePresensi->firstItem() + $loop->index }}
                                </td>

                                <!-- Nama File -->
                                <td class="px-3 py-2">
                                    <div class="truncate text-xs font-medium text-gray-700"
                                        title="template_upload_presensi.xlsx">
                                        {{ $file->file_name }}
                                    </div>
                                </td>

                                <!-- Nama Uploader -->
                                <td class="px-3 py-2">
                                    <div class="truncate text-sm text-gray-600">
                                        {{ $file->user?->pegawai?->nama ?? $file->user?->username }}
                                    </div>
                                </td>

                                <!-- Periode Mulai -->
                                <td class="px-3 py-2 text-xs text-gray-600 text-center">
                                    <span class="bg-emerald-50 text-emerald-600 px-2 py-1 rounded-full">
                                        {{ $file->periode_mulai?->format('d/m/Y') ?? '-' }}
                                    </span>
                                </td>

                                <!-- Periode Selesai -->
                                <td class="px-3 py-2 text-xs text-gray-600 text-center">
                                    <span class="bg-red-50 text-red-600 px-2 py-1 rounded-full">
                                        {{ $file->periode_selesai?->format('d/m/Y') ?? '-' }}
                                    </span>
                                </td>

                                <!-- Tanggal Upload -->
                                <td class="px-3 py-2 text-xs text-gray-600 text-center">
                                    <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded-full">
                                        {{ $file->created_at->format('d/m/Y') }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="px-3 py-2 text-center">
                                    <button @click="$dispatch('open-loading-detail-import')"
                                        wire:click="showDetailFile({{ $file->id }})"
                                        class="bg-indigo-500 hover:bg-indigo-600 text-white text-[9px] font-semibold px-2 py-1 rounded-md shadow-sm transition-all duration-200 cursor-pointer">
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
                        class="font-semibold text-heading">{{ $this->filePresensi->firstItem() }}-{{ $this->filePresensi->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-heading">{{ $this->filePresensi->total() }} file</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click="gotoPage(1)" @disabled($this->filePresensi->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click="previousPage" @disabled($this->filePresensi->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $this->filePresensi->currentPage() - 3); $i <= min($this->filePresensi->lastPage(), $this->filePresensi->currentPage() + 3); $i++)
                        <li>
                            <button wire:click="gotoPage({{ $i }})"
                            class="w-9 {{
                                $this->filePresensi->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}"
                                >
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button wire:click="nextPage" @disabled(!$this->filePresensi->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click="gotoPage({{ $this->filePresensi->lastPage() }})" @disabled($this->filePresensi->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>
</div>
