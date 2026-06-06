<!-- Calendar Container -->
<div class="bg-white border border-gray-100 shadow-sm rounded-xl flex flex-col overflow-hidden">

    <!-- Calendar Header -->
    <div class="flex flex-col gap-2 px-5 py-3 border-b border-gray-100 sm:flex-row sm:items-center sm:justify-between">

        <!-- Current Date -->
        <div>
            <p class="text-[12px] text-gray-400 mb-0.5">Hari ini</p>
            <h2 class="text-md font-medium text-gray-800">{{ $todayLabel }}</h2>
        </div>

        <!-- Navigation -->
        <div class="flex items-center gap-1">
            <button
                wire:click="previousMonth"
                class="flex items-center justify-center w-8 h-8 text-gray-400 rounded-full hover:bg-indigo-500 hover:text-white transition-colors cursor-pointer">
                <i class="fa-solid fa-chevron-left text-sm"></i>
            </button>
            <div class="min-w-[130px] text-center">
                <h3 class="text-md font-medium tracking-widest text-gray-700 uppercase">
                    {{ $monthLabel }}
                </h3>
            </div>
            <button
                wire:click="nextMonth"
                class="flex items-center justify-center w-8 h-8 text-gray-400 rounded-full hover:bg-indigo-500 hover:text-white transition-colors cursor-pointer">
                <i class="fa-solid fa-chevron-right text-sm"></i>
            </button>
        </div>

        <!-- Date Picker -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fa-solid fa-calendar-days text-gray-400 text-xs"></i>
            </div>
            <input
                type="text"
                x-data
                x-ref="picker"
                x-init="
                    const picker = new Datepicker($refs.picker, {
                        format: 'dd/mm/yyyy',
                        autohide: true,
                        language: 'id'
                    });
                    $refs.picker.addEventListener('changeDate', () => {
                        $wire.set('selectedDate', $refs.picker.value);
                    });
                "
                wire:model.live="selectedDate"
                placeholder="Pilih tanggal"
                class="h-9 pl-9 pr-3 text-sm border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-indigo-400 focus:border-transparent focus:outline-none transition"
            >
            <button type="button"
                x-show="$wire.selectedDate"
                @click="
                $wire.set('selectedDate', null);
                picker.setDate({ clear: true });
                $refs.input.value = '';"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Calendar Body -->
    <div class="flex-1 p-4 overflow-y-auto relative">

        <div
            wire:target="selectedDate, previousMonth, nextMonth, goToToday"
            wire:loading.flex
            class="absolute inset-0 z-10 items-center justify-center bg-white/70 backdrop-blur-sm">

            <div
                class="flex items-center gap-2 text-sm text-gray-500">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <span>Memuat kalender...</span>
            </div>
        </div>

        <div class="border border-gray-200 rounded-xl overflow-hidden">

            <!-- Days Header -->
            <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
                @foreach ($dayNames as $dayName)
                    <div class="py-2 text-sm font-medium text-center
                        {{ $dayName === 'Minggu' ? 'text-red-500' : 'text-gray-500' }}">
                        {{ $dayName }}
                    </div>
                @endforeach
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 divide-x divide-y divide-gray-200">
                @foreach ($calendar as $day)
                    <div
                        @click="$dispatch('open-loading-detail')"
                        wire:click="selectDay('{{ $day['date'] }}')"
                        class="min-h-[100px] p-2 flex flex-col gap-1 bg-white hover:bg-indigo-50/50 transition-colors cursor-pointer">

                        <!-- Day Number -->
                        <span class="inline-flex items-center justify-center w-7 h-7 text-xs rounded-full font-medium
                            {{ $day['is_today'] ? 'bg-indigo-500 text-white' : '' }}
                            {{ !$day['is_today'] && $day['is_current_month'] && !$day['is_weekend'] ? 'text-gray-700' : '' }}
                            {{ !$day['is_today'] && !$day['is_current_month'] ? 'text-gray-300' : '' }}
                            {{ !$day['is_today'] && $day['is_sunday'] && $day['is_current_month'] ? 'text-red-500' : '' }}">
                            {{ $day['day'] }}
                        </span>

                        <!-- Holiday Badge -->
                        @if ($day['is_holiday'])
                            <div class="flex flex-col gap-1">
                                @foreach ($day['holidays'] as $holiday)
                                    <div
                                        class="px-1 py-0.5 text-[0.7rem] font-medium rounded truncate leading-4
                                        {{ match($holiday->jenis_hari_libur) {
                                                'Hari Libur Nasional' => 'bg-red-50 text-red-700',
                                                'Hari Libur Cuti Bersama' => 'bg-amber-50 text-amber-700',
                                                'Hari Libur Institusi' => 'bg-blue-50 text-blue-700',
                                                default => 'bg-gray-50 text-gray-700',
                                            }
                                        }}"
                                    >
                                        {{ $holiday->nama_hari_libur }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>