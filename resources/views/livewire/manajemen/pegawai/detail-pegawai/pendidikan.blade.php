<div class="flex flex-col space-y-6 h-[calc(100vh-250px)]">
    <!-- Header -->
    <div
        class="flex items-center justify-between bg-white rounded-md shadow-md border border-indigo-100 border-t-4 border-t-indigo-500 p-5">

        <!-- Header Information -->
        <div>
            <h3 class="text-xl font-bold text-gray-800">Data Riwayat Pendidikan</h3>
            <p class="text-sm text-gray-500 mt-0.5">
                <i class="fa-solid fa-graduation-cap mr-1.5 text-indigo-500"></i>
                Informasi data riwayat pendidikan pegawai
            </p>
        </div>

        <!-- Filter -->
        <div class="flex flex-wrap items-center gap-3">

            <!-- Filter Jenjang Pendidikan -->
            <div class="relative w-55 bg-gray-50" x-data="{ open: false }">
                <button @click="open = !open" class="filter-dropdown" type="button">
                    <span x-text="$wire.selectedJenjangPendidikan ?? 'Jenjang Pendidikan'" class="truncate"></span>
                    <svg class="w-4 h-4 ms-1.5 -me-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                    class="dropdown-menu max-h-[calc(100vh-380px)] overflow-auto">
                    <ul class="p-2 text-sm text-body font-medium">
                        <li>
                            <button @click="$wire.set('selectedJenjangPendidikan', null); open=false"
                                class="dropdown-item">
                                Jenjang Pendidikan
                            </button>
                        </li>
                        @foreach ($jenjangPendidikan as $jenjang)
                            <li wire:key="status-{{ $loop->index }}">
                                <button
                                    @click="$wire.set('selectedJenjangPendidikan', '{{ $jenjang }}'); open=false"
                                    class="dropdown-item">
                                    {{ $jenjang }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container relative rounded-md shadow-md border border-slate-100 border-t-4 border-t-slate-500">
        <!-- Loading -->
        <div wire:loading wire:target="">
            <div
                class="absolute inset-0 backdrop-blur-xs bg-neutral-primary/20 z-10 gap-2 flex items-center justify-center rounded-md">
                <div role="status">
                    <x-ui.spinner />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="table-wrapper">
            @if ($this->pendidikan->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-graduation-cap text-2xl text-slate-600"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">
                        {{ $this->emptyStateMessage }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm">
                        Silahkan Hubungi Administrator.
                    </p>
                </div>
            @else
                <table class="table w-full">
                    <thead class="table-header text-xs bg-slate-50/80 text-gray-700">
                        <tr>
                            <!-- Header Index-->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[5%] uppercase">#</th>

                            <!-- Jenjang Pendidikan -->
                            <th scope="col" class="px-4 py-3 font-semibold w-[25%] uppercase">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="truncate">Jenjang Pendidikan</span>
                                    <div>
                                        <button wire:click="sortBy('urutan')"
                                            class="flex items-center justify-between w-full hover:text-indigo-500 cursor-pointer">
                                            <i class="fa-solid {{ $this->sortIcon('urutan') }}"></i>
                                        </button>
                                    </div>
                                </div>
                            </th>

                            <!-- Tahun Masuk -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[16%] uppercase">Tahun Masuk
                            </th>

                            <!-- Tahun Lulus -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[16%] uppercase">Tahun Lulus
                            </th>

                            <!-- File Ijazah -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[20%] uppercase">File Ijazah
                            </th>

                            <!-- Updated By -->
                            <th scope="col" class="px-4 py-3 font-semibold text-center w-[18%] uppercase">Diperbarui
                                Oleh
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->pendidikan as $p)
                            <tr class="table-row hover:bg-gray-100 transition">
                                <!-- No Index -->
                                <td class="px-4 py-3 text-center text-sm text-gray-500">
                                    {{ $this->pendidikan->firstItem() + $loop->index }}
                                </td>

                                <!-- Jenjang Pendidikan -->
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    @php
                                        $kodePendidikan = $p->jenjangPendidikan->kode;
                                        $badgeColor = match (true) {
                                            $kodePendidikan === 'SD' => 'bg-slate-100 text-slate-700',
                                            $kodePendidikan === 'SMP' => 'bg-zinc-100 text-zinc-700',
                                            in_array($kodePendidikan, ['SMA', 'SMK']) => 'bg-sky-100 text-sky-700',
                                            in_array($kodePendidikan, ['D1', 'D2']) => 'bg-cyan-100 text-cyan-700',
                                            in_array($kodePendidikan, ['D3', 'D4']) => 'bg-violet-100 text-violet-700',
                                            $kodePendidikan === 'S1' => 'bg-emerald-100 text-emerald-700',
                                            $kodePendidikan === 'S2' => 'bg-amber-100 text-amber-700',
                                            $kodePendidikan === 'S3' => 'bg-rose-100 text-rose-700',
                                            default => 'bg-gray-100 text-gray-700', // Warna Default
                                        };
                                    @endphp
                                    <div class="font-medium mb-1.5 self-center">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $badgeColor }} mr-2">
                                            {{ $kodePendidikan ?? '-' }}
                                        </span>
                                        <span>
                                            {{ $p->jenjangPendidikan->nama ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Tahun Masuk -->
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        {{ $p->tahun_masuk ?? '-' }}
                                    </span>
                                </td>

                                <!-- Tahun Lulus -->
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200">
                                        {{ $p->tahun_lulus ?? '-' }}
                                    </span>
                                </td>

                                <!-- File Ijazah -->
                                <td class="px-4 py-3 text-sm text-gray-800 text-center">
                                    @if ($p->file_ijazah)
                                        <span class="text-blue-600 hover:underline cursor-pointer">Lihat Berkas</span>
                                    @else
                                        <span class="text-gray-400 italic text-xs">Belum ada berkas</span>
                                    @endif
                                </td>

                                <!-- Diperbarui Oleh -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-semibold">
                                            {{ strtoupper(substr($p->editor?->pegawai?->nama ?? ($p->editor?->username ?? '?'), 0, 1)) }}
                                        </div>

                                        <div>
                                            <div class="text-sm font-medium text-gray-700">
                                                {{ $p->editor?->pegawai?->nama ?? ($p->editor?->username ?? '-') }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                Terakhir mengubah data
                                            </div>
                                        </div>
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
                        class="font-semibold text-heading">{{ $this->pendidikan->firstItem() }}-{{ $this->pendidikan->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-heading">{{ $this->pendidikan->total() }} riwayat pendidikan</span>
                </span>

                <ul class="flex -space-x-px text-sm border border-gray-300 rounded-lg">
                    <li>
                        <button wire:click="gotoPage(1)" @disabled($this->pendidikan->onFirstPage())
                            class="table-pagination-btn rounded-s-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fa-solid fa-angles-left text-xs"></i>
                        </button>
                    </li>
                    <li>
                        <button wire:click="previousPage" @disabled($this->pendidikan->onFirstPage())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                    </li>
                    @for ($i = max(1, $this->pendidikan->currentPage() - 3); $i <= min($this->pendidikan->lastPage(), $this->pendidikan->currentPage() + 3); $i++)
                        <li>
                            <button wire:click="gotoPage({{ $i }})"
                                class="w-9 {{ $this->pendidikan->currentPage() == $i ? 'table-pagination-btn-active' : 'table-pagination-btn' }}">
                                {{ $i }}
                            </button>
                        </li>
                    @endfor
                    <li>
                        <button wire:click="nextPage" @disabled(!$this->pendidikan->hasMorePages())
                            class="table-pagination-btn px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </li>
                    <button wire:click="gotoPage({{ $this->pendidikan->lastPage() }})" @disabled($this->pendidikan->onLastPage())
                        class="table-pagination-btn rounded-e-lg px-3 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </button>
                </ul>
            </nav>
        </div>
    </div>

</div>
