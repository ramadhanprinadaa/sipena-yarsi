<div class="flex flex-col h-full lg:h-[calc(100vh-180px)] space-y-4 px-2 py-3">
    <!-- Header -->
    <div class="flex-none flex gap-2 font-poppins">
        <h1 class="text-2xl font-semibold">Riwayat Impor File Presensi</h1>
    </div>

    <!-- Filter & Search -->
    <div class="flex-none flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Search -->
        <div class="relative w-70">
            <div class="absolute inset-y-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="text" class="input-search" wire:model.live.debounce.300ms="search"
                placeholder="Cari Nama File / Uploader ..." />
        </div>

        <!-- Date Picker -->
        <div class="relative w-full sm:w-auto">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
            </div>
            <input type="text" x-data x-ref="picker" x-init="const picker = new Datepicker($refs.picker, {
                format: 'dd/mm/yyyy',
                autohide: true,
                language: 'id'
            });
            $refs.picker.addEventListener('changeDate', () => {
                $wire.set('selectedDate', $refs.picker.value);
            });" placeholder="Pilih Tanggal Impor ..."
                class="w-full h-9 pl-9 pr-3 text-sm border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-3 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition" />
        </div>
    </div>

    <!-- Table -->
    <div class="table-container relative">
        <!-- Loading -->
        <div wire:loading>
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
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
                        <th scope="col" class="px-4 py-3 font-medium w-14 text-center">#</th>
                        <th scope="col" class="px-4 py-3 font-medium text-left">
                            Nama File
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-48 text-left">
                            Uploaded By
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-40 text-left">
                            Tanggal Upload
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-28 text-center">
                            Total Data
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium w-24 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 10; $i++)
                        <tr class="table-row">
                            <!-- No -->
                            <td class="px-4 py-3 text-center text-gray-500 text-sm">
                                {{ $i + 1 }}
                            </td>

                            <!-- Nama File -->
                            <td class="px-3 py-2">
                                <div class="truncate text-sm font-medium text-gray-700"
                                    title="template_upload_presensi.xlsx">
                                    template_upload_presensi.xlsx
                                </div>
                            </td>

                            <!-- Nama Uploader -->
                            <td class="px-3 py-2">
                                <div class="truncate text-sm text-gray-600" title="Rafly Eryan Azis">
                                    Rafly Eryan Azis
                                </div>
                            </td>

                            <!-- Tanggal Upload -->
                            <td class="px-3 py-2 text-sm text-gray-600">12/05/2026</td>

                            <!-- Total Data -->
                            <td class="px-3 py-2 text-center text-sm text-gray-600">500</td>

                            <!-- Aksi -->
                            <td class="px-3 py-2 text-center">
                                <button
                                    class="bg-indigo-500 hover:bg-indigo-600 text-white text-[11px] font-semibold px-2 py-1 rounded-md shadow-sm transition-all duration-200">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Footer & Pagination -->
        <div class="text-body bg-neutral-secondary-medium border-t border-default-medium rounded-md">

            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between px-4 py-2"
                aria-label="Table navigation">
                <span class="text-sm font-normal text-body block w-full md:inline md:w-auto">
                    Menampilkan
                    <span class="font-semibold text-heading">10</span> dari
                    <span class="font-semibold text-heading">100 file</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click=""
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click=""
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = 1; $i <= 5; $i++)
                        <li>
                            <button wire:click="" class="w-9 table-pagination-btn">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button wire:click=""
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click=""
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>
</div>
