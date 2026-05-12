<?php

namespace App\Livewire\Konfigurasi\HariLibur;

use App\Models\Holiday;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Livewire\Component;

class Index extends Component
{
    /**
     * Current Month
     */
    public int $month;

    /**
     * Current Year
     */
    public int $year;

    /**
     * Current Today Label
     */
    public string $todayLabel;

    /**
     * Current Month Label
     */
    public string $monthLabel;

    /**
     * Calendar Data
     */
    public array $calendar = [];

    /**
     * Day Names
     */
    public array $dayNames = [
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
        'Minggu',
    ];

    /**
     * Mount Component
     */
    public function mount(): void
    {
        $now = now();

        $this->month = $now->month;

        $this->year = $now->year;

        $this->todayLabel = $now->translatedFormat(
            'l, d F Y'
        );

        $this->generateCalendar();
    }

    /**
     * Generate Calendar
     */
    public function generateCalendar(): void
    {
        /**
         * Current Month Date
         */
        $currentMonth = Carbon::create(
            $this->year,
            $this->month,
            1
        );

        /**
         * Month Label
         */
        $this->monthLabel = $currentMonth
            ->translatedFormat('F Y');

        /**
         * Calendar Start
         *
         * Example:
         * 27 April 2026
         */
        $calendarStart = $currentMonth
            ->copy()
            ->startOfMonth()
            ->startOfWeek(Carbon::MONDAY);

        /**
         * Calendar End
         *
         * Example:
         * 07 Juni 2026
         */
        $calendarEnd = $currentMonth
            ->copy()
            ->endOfMonth()
            ->endOfWeek(Carbon::SUNDAY);

        /**
         * Holiday Data
         */
        $holidays = Holiday::query()
            ->whereBetween('holiday_date', [
                $calendarStart->toDateString(),
                $calendarEnd->toDateString(),
            ])
            ->get()
            ->keyBy(
                fn($holiday) => Carbon::parse(
                    $holiday->holiday_date
                )->toDateString()
            );

        /**
         * Calendar Result
         */
        $calendar = [];

        /**
         * Date Period
         */
        $period = CarbonPeriod::create(
            $calendarStart,
            $calendarEnd
        );

        foreach ($period as $date) {

            $formattedDate = $date->toDateString();

            $holiday = $holidays->get(
                $formattedDate
            );

            $calendar[] = [

                /**
                 * Full Date
                 */
                'date' => $formattedDate,

                /**
                 * Day Number
                 */
                'day' => $date->day,

                /**
                 * Full Label
                 */
                'full_date' => $date
                    ->translatedFormat(
                        'l, d F Y'
                    ),

                /**
                 * Current Month
                 */
                'is_current_month' =>
                $date->month === $this->month,

                /**
                 * Today
                 */
                'is_today' => $date->isToday(),

                /**
                 * Weekend
                 */
                'is_weekend' =>
                $date->isWeekend(),

                /**
                 * Holiday
                 */
                'is_holiday' => $holiday !== null,

                /**
                 * Holiday Name
                 */
                'holiday_name' => $holiday?->name,

                /**
                 * Holiday Type
                 */
                'holiday_type' => $holiday?->type,
            ];
        }

        /**
         * Ensure 42 Cells
         *
         * 6 rows × 7 columns
         */
        while (count($calendar) < 42) {

            $lastDate = Carbon::parse(
                last($calendar)['date']
            )->addDay();
            $holiday = $holidays->get(
                $lastDate->toDateString()
            );

            $calendar[] = [

                'date' => $lastDate
                    ->toDateString(),

                'day' => $lastDate->day,

                'full_date' => $lastDate
                    ->translatedFormat(
                        'l, d F Y'
                    ),

                'is_current_month' => false,

                'is_today' => $lastDate
                    ->isToday(),

                'is_weekend' => $lastDate
                    ->isWeekend(),

                'is_holiday' => $holiday !== null,

                'holiday_name' => null,

                'holiday_type' => null,
            ];
        }

        $this->calendar = $calendar;
    }

    /**
     * Previous Month
     */
    public function previousMonth(): void
    {
        $date = Carbon::create(
            $this->year,
            $this->month,
            1
        )->subMonth();

        $this->month = $date->month;

        $this->year = $date->year;

        $this->generateCalendar();
    }

    /**
     * Next Month
     */
    public function nextMonth(): void
    {
        $date = Carbon::create(
            $this->year,
            $this->month,
            1
        )->addMonth();

        $this->month = $date->month;

        $this->year = $date->year;

        $this->generateCalendar();
    }

    /**
     * Go To Today
     */
    public function goToToday(): void
    {
        $now = now();

        $this->month = $now->month;

        $this->year = $now->year;

        $this->generateCalendar();
    }

    /**
     * Change Calendar Date
     */
    public function updatedSelectedDate(
        string $value
    ): void {

        $date = Carbon::parse($value);

        $this->month = $date->month;

        $this->year = $date->year;

        $this->generateCalendar();
    }

    /**
     * Render Component
     */
    public function render()
    {
        return view(
            'livewire.konfigurasi.hari-libur.index'
        );
    }
}