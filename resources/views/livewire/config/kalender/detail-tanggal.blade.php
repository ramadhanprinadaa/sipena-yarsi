<div class="bg-white w-3xl h-[78vh] mx-auto rounded-2xl shadow-xl flex flex-col overflow-hidden">

    <!-- Header -->
    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-indigo-100 backdrop-blur-sm flex items-center justify-center">
                <i class="fa-solid fa-calendar text-lg text-indigo-500"></i>
            </div>
            <div>
                <h2 class="text-md font-bold text-gray-800 leading-tight tracking-tight">
                    Detail Tanggal / Hari
                </h2>
                @if ($date)
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fa-regular fa-clock mr-1.5"></i>{{ Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Tombol Tutup --}}
            <button
                type="button"
                @click="$dispatch('close-detail-modal')"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </header>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto px-6 py-5">

        @if ($day && count($day['holidays']))
            <div class="space-y-4">
                @foreach ($day['holidays'] as $holiday)
                    <div class="border-l-4 border-indigo-500 bg-gradient-to-r from-indigo-50/50 to-transparent rounded-lg p-4 hover:shadow-md transition-all duration-200">
                        @if ($editingHolidayId === $holiday['id'])
                            {{-- EDIT MODE --}}
                            <form
                                wire:submit.prevent="save"
                                class="space-y-4">

                                {{-- Nama --}}
                                <div>

                                    <label class="block text-xs font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-tag text-indigo-600"></i>
                                        Nama Hari Libur
                                    </label>

                                    <input
                                        type="text"
                                        wire:model.live="form.nama_hari_libur"
                                        placeholder="Masukkan nama hari libur..."
                                        class="w-full text-sm border-2 border-gray-200 rounded-lg px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all duration-200
                                        @error('form.nama_hari_libur') border-red-400 focus:border-red-500 @enderror">

                                    @error('form.nama_hari_libur')
                                        <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                {{-- Jenis --}}
                                <div>

                                    <label class="block text-xs font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-list text-indigo-600"></i>
                                        Jenis Hari Libur
                                    </label>

                                    <select
                                        wire:model.live="form.jenis_hari_libur"
                                        class="w-full text-sm border-2 border-gray-200 rounded-lg px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all duration-200 appearance-none bg-white cursor-pointer
                                        @error('form.jenis_hari_libur') border-red-400 focus:border-red-500 @enderror"
                                        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%224f46e5%22%3E%3Cpath d=%22M7 10l5 5 5-5z%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.5em 1.5em; padding-right: 2.5rem;">

                                        <option value="">
                                            -- Pilih Jenis Hari Libur --
                                        </option>

                                        @foreach ($jenisHariLibur as $jenis)

                                            <option value="{{ $jenis }}">
                                                {{ $jenis }}
                                            </option>

                                        @endforeach

                                    </select>

                                    @error('form.jenis_hari_libur')
                                        <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                {{-- Keterangan --}}
                                <div>

                                    <label class="block text-xs font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-note-sticky text-indigo-600"></i>
                                        Keterangan
                                    </label>

                                    <textarea
                                        rows="3"
                                        wire:model.live="form.keterangan"
                                        placeholder="Tambahkan keterangan (opsional)..."
                                        class="w-full text-sm border-2 border-gray-200 rounded-lg px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all duration-200 resize-none"></textarea>

                                </div>

                                {{-- ACTION --}}
                                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">

                                    <button
                                        type="button"
                                        wire:click="cancelEdit"
                                        class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer transition-all duration-200 flex items-center gap-2">
                                        <i class="fa-solid fa-times text-xs"></i>
                                        Batal
                                    </button>

                                    <button
                                        type="submit"
                                        @disabled(! $this->isDirty)
                                        class="px-5 py-2 text-sm font-medium rounded-lg transition-all duration-200 flex items-center gap-2
                                        {{ $this->isDirty
                                            ? 'bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer shadow-md hover:shadow-lg'
                                            : 'bg-indigo-200 text-indigo-400 cursor-not-allowed' }}">
                                        <i class="fa-solid fa-check text-xs"></i>
                                        Simpan
                                    </button>

                                </div>

                            </form>

                        @else
                            {{-- READ MODE --}}
                            @php
                                $colors = [
                                    'Hari Libur Nasional' => [
                                        'badge' => 'bg-red-100 text-red-700 border-red-300',
                                        'icon' => 'fa-flag'
                                    ],
                                    'Hari Libur Cuti Bersama' => [
                                        'badge' => 'bg-amber-100 text-amber-700 border-amber-300',
                                        'icon' => 'fa-users'
                                    ],
                                    'Hari Libur Institusi' => [
                                        'badge' => 'bg-blue-100 text-blue-700 border-blue-300',
                                        'icon' => 'fa-building'
                                    ],
                                ];
                            @endphp

                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full border-2
                                        {{ $colors[$holiday['jenis_hari_libur']]['badge'] ?? 'bg-gray-100 text-gray-700 border-gray-300' }}">
                                            <i class="fa-solid {{ $colors[$holiday['jenis_hari_libur']]['icon'] ?? 'fa-calendar' }}"></i>
                                            {{ $holiday['jenis_hari_libur'] }}
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-2">
                                        {{ $holiday['nama_hari_libur'] }}
                                    </h3>
                                    @if ($holiday['keterangan'])
                                        <p class="text-xs text-gray-600 leading-relaxed bg-white/50 px-3 py-2 rounded-lg border border-gray-100">
                                            <i class="fa-solid fa-circle-info text-indigo-600 mr-2"></i>
                                            {{ $holiday['keterangan'] }}
                                        </p>
                                    @endif
                                </div>

                                {{-- ACTION --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button
                                        type="button"
                                        wire:click="startEdit({{ $holiday['id'] }})"
                                        class="w-9 h-9 rounded-lg bg-indigo-100 hover:bg-indigo-600 text-indigo-600 hover:text-white cursor-pointer transition-all duration-200 flex items-center justify-center hover:scale-110 shadow-sm">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="deleteHoliday({{ $holiday['id'] }})"
                                        wire:confirm="Hapus hari libur ini?"
                                        class="w-9 h-9 rounded-lg bg-red-100 hover:bg-red-600 text-red-600 hover:text-white cursor-pointer transition-all duration-200 flex items-center justify-center hover:scale-110 shadow-sm">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else

            {{-- EMPTY STATE --}}
            <div class="h-full flex items-center justify-center">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center mx-auto mb-4 ring-4 ring-indigo-50">
                        <i class="fa-regular fa-calendar-xmark text-3xl text-indigo-400"></i>
                    </div>
                    <p class="text-base font-semibold text-gray-500 mb-1">
                        Tidak ada hari libur
                    </p>
                    <p class="text-sm text-gray-400">
                        Pada tanggal ini belum ada data hari libur.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>