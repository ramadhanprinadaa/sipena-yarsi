<div class="bg-white w-3xl h-[68vh] mx-auto p-6 rounded-xl shadow-lg flex flex-col">

    {{-- HEADER --}}
    <header class="flex items-center justify-between pb-4 mb-5 border-b border-gray-200">

        <div class="flex items-center gap-3">

            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                <i class="fa-solid fa-calendar-days text-indigo-500"></i>
            </div>

            <div>
                <h2 class="text-base font-semibold text-gray-800">
                    Detail Tanggal
                </h2>

                @if ($date)
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </p>
                @endif
            </div>

        </div>

        <button
            type="button"
            wire:click="close"
            class="w-8 h-8 rounded-md bg-red-400 hover:bg-red-500 text-white cursor-pointer transition-colors">

            <i class="fa-solid fa-xmark"></i>

        </button>

    </header>

    {{-- BODY --}}
    <div class="flex-1 overflow-y-auto">

        @if ($day && count($day['holidays']))

            <div class="space-y-3">

                @foreach ($day['holidays'] as $holiday)

                    <div class="border border-gray-100 rounded-xl p-4">

                        @if ($editingHolidayId === $holiday->id)

                            {{-- EDIT MODE --}}

                            <form
                                wire:submit.prevent="save"
                                class="space-y-3">

                                {{-- Nama --}}
                                <div>

                                    <label class="block text-xs text-gray-500 mb-1">
                                        Nama Hari Libur
                                    </label>

                                    <input
                                        type="text"
                                        wire:model.live="form.nama_hari_libur"
                                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2
                                        @error('form.nama_hari_libur') border-red-400 @enderror">

                                    @error('form.nama_hari_libur')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror

                                </div>

                                {{-- Jenis --}}
                                <div>

                                    <label class="block text-xs text-gray-500 mb-1">
                                        Jenis Hari Libur
                                    </label>

                                    <select
                                        wire:model.live="form.jenis_hari_libur"
                                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2
                                        @error('form.jenis_hari_libur') border-red-400 @enderror">

                                        <option value="">
                                            Pilih Jenis Hari Libur
                                        </option>

                                        @foreach ($jenisHariLibur as $jenis)

                                            <option value="{{ $jenis }}">
                                                {{ $jenis }}
                                            </option>

                                        @endforeach

                                    </select>

                                    @error('form.jenis_hari_libur')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror

                                </div>

                                {{-- Keterangan --}}
                                <div>

                                    <label class="block text-xs text-gray-500 mb-1">
                                        Keterangan
                                    </label>

                                    <textarea
                                        rows="3"
                                        wire:model.live="form.keterangan"
                                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2"></textarea>

                                </div>

                                {{-- ACTION --}}
                                <div class="flex justify-end gap-2 pt-2">

                                    <button
                                        type="button"
                                        wire:click="cancelEdit"
                                        class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer">

                                        Batal

                                    </button>

                                    <button
                                        type="submit"
                                        @disabled(! $this->isDirty)
                                        class="px-4 py-2 text-sm text-white rounded-lg
                                        {{ $this->isDirty
                                            ? 'bg-indigo-500 hover:bg-indigo-600 cursor-pointer'
                                            : 'bg-indigo-300 cursor-not-allowed' }}">

                                        Simpan

                                    </button>

                                </div>

                            </form>

                        @else

                            {{-- READ MODE --}}

                            @php
                                $colors = [
                                    'Hari Libur Nasional' => 'bg-red-50 text-red-700',
                                    'Hari Libur Cuti Bersama' => 'bg-amber-50 text-amber-700',
                                    'Hari Libur Institusi' => 'bg-blue-50 text-blue-700',
                                ];
                            @endphp

                            <div class="flex items-start justify-between gap-4">

                                <div>

                                    <div
                                        class="inline-flex items-center px-2 py-1 text-[11px] rounded-md mb-2
                                        {{ $colors[$holiday->jenis_hari_libur] ?? 'bg-gray-50 text-gray-700' }}">

                                        {{ $holiday->jenis_hari_libur }}

                                    </div>

                                    <h3 class="text-sm font-semibold text-gray-800">
                                        {{ $holiday->nama_hari_libur }}
                                    </h3>

                                    @if ($holiday->keterangan)

                                        <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                            {{ $holiday->keterangan }}
                                        </p>

                                    @endif

                                </div>

                                {{-- ACTION --}}
                                <div class="flex items-center gap-2">

                                    <button
                                        type="button"
                                        wire:click="startEdit({{ $holiday->id }})"
                                        class="w-8 h-8 rounded-md bg-indigo-50 hover:bg-indigo-100 text-indigo-600 cursor-pointer transition-colors">

                                        <i class="fa-solid fa-pen text-xs"></i>

                                    </button>

                                    <button
                                        type="button"
                                        wire:click="deleteHoliday({{ $holiday->id }})"
                                        wire:confirm="Hapus hari libur ini?"
                                        class="w-8 h-8 rounded-md bg-red-50 hover:bg-red-100 text-red-500 cursor-pointer transition-colors">

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

                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto">
                        <i class="fa-regular fa-calendar-xmark text-xl text-gray-400"></i>
                    </div>

                    <p class="mt-3 text-sm text-gray-400">
                        Tidak ada hari libur pada tanggal ini.
                    </p>

                </div>

            </div>

        @endif

    </div>

</div>