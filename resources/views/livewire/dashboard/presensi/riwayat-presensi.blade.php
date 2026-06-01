<div
    x-data="{ showLoading: false, openExport: false }"
    x-on:open-export="showLoading = false; openExport = true;"
    class="flex flex-col h-full space-y-4 px-2 py-3">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-3">

        <!-- Judul Header -->
        <div class="flex-none font-poppins">
            <h1 class="text-2xl font-semibold text-slate-800 tracking-tight">
                Riwayat Presensi
            </h1>
            <p class="text-sm text-slate-600 mt-1 hidden md:block">
                Pantau detail jam masuk, jam pulang, dan status kehadiran harian Anda.
            </p>
        </div>

        <!-- Tombol Export Excel -->
        <div class="flex gap-2">
            <button
                x-on:click="showLoading = true; $wire.openExportPreview().finally(() => setTimeout(() => showLoading = false, 500))"
                class="flex items-center px-4 h-9 self-center justify-center cursor-pointer bg-teal-600/90 hover:bg-teal-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="flex flex-wrap items-center justify-between gap-3  p-4 rounded-xl border border-gray-100 shadow-sm">

        <div class="flex flex-wrap items-center gap-3 w-full">

            <!-- Status Kehadiran Dropdown -->
            <div class="relative w-full sm:w-56" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center justify-between w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 p-2.5 transition" type="button">
                    <span x-text="$wire.selectedStatusKehadiran ?? 'Semua Status Kehadiran'" class="truncate"></span>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                    <ul class="p-2 text-sm text-gray-700 font-medium space-y-1">
                        <li>
                            <button @click="$wire.set('selectedStatusKehadiran', null); open=false" class="w-full text-left px-3 py-2 rounded-md hover:bg-teal-50 hover:text-teal-700 transition">
                                Semua Status Kehadiran
                            </button>
                        </li>
                        {{-- Asumsi $statusKehadiran dilooping dari Livewire Component --}}
                        @if(isset($statusKehadiran))
                            @foreach ($statusKehadiran as $status)
                                <li wire:key="status-{{ $loop->index }}">
                                    <button @click="$wire.set('selectedStatusKehadiran', '{{ $status }}'); open=false" class="w-full text-left px-3 py-2 rounded-md hover:bg-teal-50 hover:text-teal-700 transition">
                                        {{ $status }}
                                    </button>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Filter Tanggal (Flowbite Datepicker) -->
            <div class="flex flex-wrap items-center gap-2 flex-1">
                <div class="relative w-full sm:w-48">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <i class="fa-regular fa-calendar text-gray-400"></i>
                    </div>
                    <input type="text" wire:model.live="selectedPeriodeMulai" datepicker datepicker-format="dd/mm/yyyy"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full ps-10 p-2.5"
                        placeholder="Periode Mulai">
                </div>
                <span class="text-gray-400 hidden sm:inline">-</span>
                <div class="relative w-full sm:w-48">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <i class="fa-regular fa-calendar text-gray-400"></i>
                    </div>
                    <input type="text" wire:model.live="selectedPeriodeSelesai" datepicker datepicker-format="dd/mm/yyyy"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full ps-10 p-2.5"
                        placeholder="Periode Selesai">
                </div>

                {{-- Clear Filter Button --}}
                @if(isset($selectedPeriodeMulai) || isset($selectedPeriodeSelesai) || isset($selectedStatusKehadiran))
                    <button type="button" wire:click="$set('selectedPeriodeMulai', null); $set('selectedPeriodeSelesai', null); $set('selectedStatusKehadiran', null);"
                        class="px-3 py-2 text-sm font-medium text-rose-600 bg-rose-50 rounded-lg hover:bg-rose-100 transition border border-rose-100">
                        <i class="fa-solid fa-rotate-right mr-1.5"></i> Reset
                    </button>
                @endif
            </div>

        </div>
    </div>

    <!-- Table Container -->
    <div class="relative bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden min-h-[400px]">

        <!-- Loading Overlay -->
        <div wire:loading wire:target="selectedStatusKehadiran, selectedPeriodeMulai, selectedPeriodeSelesai, gotoPage, previousPage, nextPage" class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-sm rounded-xl">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 border-4 border-teal-200 border-t-teal-600 rounded-full animate-spin mb-2"></div>
                <span class="text-sm font-medium text-teal-700">Memuat Data...</span>
            </div>
        </div>

        <!-- Main Content (Tabel) -->
        <div class="overflow-x-auto">
            @if(isset($riwayatPresensi) && $riwayatPresensi->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center min-h-[300px] text-center p-6">
                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4 border border-slate-100 shadow-sm">
                        <i class="fa-solid fa-calendar-minus text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-base font-semibold text-slate-700">
                        {{ $this->emptyStateMessage ?? 'Belum ada data presensi.' }}
                    </h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm whitespace-pre-line">
                        Silakan ubah parameter filter tanggal atau status kehadiran di atas.
                    </p>
                </div>
            @else
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[5%] text-center">#</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[20%] text-left">Tanggal Presensi</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[15%] text-center">Jam Masuk</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[15%] text-center">Jam Pulang</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[15%] text-center">Total Jam</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[18%] text-center">Status Kehadiran</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold w-[12%] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(isset($riwayatPresensi))
                            @foreach ($riwayatPresensi as $riwayat)
                                <tr class="bg-white hover:bg-slate-50 transition-colors">
                                    <td class="px-3 py-3 text-center text-gray-500 text-sm">
                                        {{ $riwayatPresensi->firstItem() + $loop->index }}
                                    </td>

                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $riwayat->tanggal?->translatedFormat('l, d F Y') ?? '-' }}
                                    </td>

                                    <td class="px-3 py-3 text-center text-gray-700">
                                        {{ $riwayat->jam_masuk?->format('H:i') ?? '-' }}
                                    </td>

                                    <td class="px-3 py-3 text-center text-gray-700">
                                        {{ $riwayat->jam_keluar?->format('H:i') ?? '-' }}
                                    </td>

                                    <td class="px-3 py-3 text-center font-medium text-gray-800">
                                        {{ $riwayat->total_jam_kerja ?? '-' }}
                                    </td>

                                    <td class="px-3 py-3 text-center">
                                        {{-- Asumsi badge_style disediakan oleh model/service --}}
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold tracking-wide border"
                                            style="{{ $riwayat->statusKehadiran?->badge_style ?? 'background-color: #f3f4f6; color: #374151; border-color: #e5e7eb;' }}">
                                            {{ $riwayat->statusKehadiran?->status ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3 text-center">
                                        <button @click="$dispatch('open-loading-detail-riwayat')"
                                            wire:click="showDetailRiwayat({{ $riwayat->id }})"
                                            class="bg-white hover:bg-teal-50 text-teal-600 border border-teal-200 hover:border-teal-300 text-[11px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition-all duration-200 cursor-pointer">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Footer & Pagination -->
        @if(isset($riwayatPresensi) && !$riwayatPresensi->isEmpty())
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/50">
                {{ $riwayatPresensi->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Confirmation for Export File (Re-used from Management, adjusted colors) -->
    <template x-teleport="body">
        <div x-show="showLoading || openExport"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="openExport = false"
            @keydown.escape.window="openExport = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md"
            style="display: none;">

            <div x-show="showLoading" class="flex flex-col items-center gap-4">
                <div class="w-10 h-10 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                <div class="text-sm font-medium tracking-wide text-white">
                    Memuat Data...
                </div>
            </div>

            <div x-show="openExport" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.stop>

                {{-- Asumsi preview data disediakan oleh Livewire --}}
                @php $preview = $this->exportPreviewData ?? ['isEmpty' => true, 'periode' => 'N/A', 'status' => 'N/A']; @endphp

                <x-modal.confirmation
                    title="Export Riwayat Presensi"
                    subTitle="Konfirmasi parameter filter sebelum mengunduh data."
                    icon="fa-solid fa-file-excel"
                    iconBg="bg-teal-500"
                    iconShadow="shadow-teal-200"
                    closeAction="openExport = false">

                    <div>
                        @if($preview['isEmpty'])
                            <div class="flex flex-col items-center justify-center min-h-[250px] p-6 text-center bg-rose-50/50 border border-rose-100 rounded-xl">
                                <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mb-4 shadow-sm shadow-rose-100">
                                    <i class="fa-solid fa-triangle-exclamation text-2xl text-rose-500"></i>
                                </div>
                                <h3 class="text-base font-bold text-rose-800">Tidak Ada Data Untuk Diexport</h3>
                                <p class="text-sm text-rose-600 mt-1.5 max-w-[280px] leading-relaxed mx-auto">
                                    Data presensi Anda kosong pada periode yang dipilih.
                                </p>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-3">
                                    {{-- Periode --}}
                                    <div class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-xl flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-teal-500/10 flex items-center justify-center text-teal-600 shrink-0">
                                            <i class="fa-regular fa-calendar-days text-base"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Periode Presensi</h4>
                                            <p class="text-sm font-bold text-slate-700 mt-0.5">{{ $preview['periode'] }}</p>
                                        </div>
                                    </div>

                                    {{-- Status Kehadiran --}}
                                    <div class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-xl flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-teal-500/10 flex items-center justify-center text-teal-600 shrink-0">
                                            <i class="fa-solid fa-check-double text-base"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Filter Status</h4>
                                            <p class="text-sm font-semibold text-slate-700 mt-0.5 truncate">{{ $preview['status'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <x-slot:footer>
                        <button type="button" @click="openExport = false"
                            class="px-5 py-2 rounded-md border border-slate-200 bg-rose-500/90 hover:bg-rose-600 text-white transition cursor-pointer font-medium">
                            Batal
                        </button>

                        <button type="button"
                            @if(!$preview['isEmpty'])
                                x-on:click="await $wire.exportData(); setTimeout(() => openExport = false, 800)"
                            @endif
                            @disabled($preview['isEmpty'])
                            wire:loading.attr="disabled"
                            wire:target="exportData"
                            wire:loading.class="opacity-70 !cursor-wait scale-95"
                            class="px-5 py-2 rounded-md transition-all duration-200 flex items-center justify-center font-medium
                                {{ $preview['isEmpty']
                                    ? 'bg-slate-200 text-slate-400 cursor-not-allowed'
                                    : 'bg-teal-600 hover:bg-teal-700 text-white cursor-pointer shadow-sm active:scale-95' }}">

                            <i wire:loading.remove wire:target="exportData" class="fa-solid fa-download mr-2"></i>

                            <svg wire:loading wire:target="exportData" class="w-4 h-4 animate-spin mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>

                            <span wire:loading.remove wire:target="exportData">Unduh File Excel</span>
                            <span wire:loading wire:target="exportData">Memproses...</span>
                        </button>
                    </x-slot:footer>

                </x-modal.confirmation>
            </div>
        </div>
    </template>
</div>