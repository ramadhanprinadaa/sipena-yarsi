<?php

namespace App\Livewire\Config\Kalender;

use App\Models\HariLibur;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Livewire\Component;

class Index extends Component
{
    public int $month;
    public int $year;
    public string $todayLabel;
    public string $monthLabel;
    public string $selectedDate = '';

    public array $calendar = [];
    public array $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function mount()
    {
        $now = Carbon::now();
        $this->month = $now->month;
        $this->year = $now->year;
        $this->todayLabel = $now->translatedFormat('l, d F Y');

        $this->generateCalendar();
    }

    public function generateCalendar()
    {
        // Current month and label
        $currentMonth = Carbon::create($this->year, $this->month, 1);
        $this->monthLabel = $currentMonth->translatedFormat('F Y');

        // Calendar Start and End
        $calendarStart = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        // Get Holidays dates of the month
        $holidays = HariLibur::query()
            ->whereBetween('tanggal', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->get()
            ->keyBy(
                fn($holiday) => Carbon::parse(
                    $holiday->tanggal
                )->toDateString()
            );


        // Calendar Result
        $calendar = [];

        // Date Period
        $period = CarbonPeriod::create($calendarStart, $calendarEnd);

        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $holiday = $holidays->get($formattedDate);
            $calendar[] = [
                'date'              => $formattedDate,
                'day'               => $date->day,
                'full_date'         => $date->translatedFormat('l, d F Y'),
                'is_current_month'  => $date->month === $this->month,
                'is_today'          => $date->isToday(),
                'is_weekend'        => $date->isWeekend(),
                'is_sunday'         => $date->isSunday(),
                'is_holiday'        => $holiday !== null,
                'holiday_name'      => $holiday?->nama_hari_libur,
                'holiday_type'      => $holiday?->jenis_hari_libur,
            ];
        }

        // Get Layout Calendar
        while (count($calendar) < 35) {
            $lastDate = Carbon::parse(last($calendar)['date'])->addDay();
            $holiday = $holidays->get($lastDate->toDateString());
            $calendar[] = [
                'date'              => $lastDate->toDateString(),
                'day'               => $lastDate->day,
                'full_date'         => $lastDate->translatedFormat('l, d F Y'),
                'is_current_month'  => false,
                'is_today'          => $lastDate->isToday(),
                'is_weekend'        => $lastDate->isWeekend(),
                'is_sunday'         => $lastDate->isSunday(),
                'is_holiday'        => $holiday !== null,
                'holiday_name'      => null,
                'holiday_type'      => null,
            ];
        }
        $this->calendar = $calendar;
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->generateCalendar();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->generateCalendar();
    }

    public function goToToday()
    {
        $now = now();
        $this->month = $now->month;
        $this->year = $now->year;
        $this->generateCalendar();
    }

    public function updatedSelectedDate($value)
    {
        $normalized = $this->normalizeDate($value);
        if (!$normalized) {
            return;
        }
        $date = Carbon::parse($normalized);
        $this->month = $date->month;
        $this->year = $date->year;
        $this->generateCalendar();
    }

    private function normalizeDate($date)
    {
        return filled($date)
            ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d')
            : null;
    }

    public function render()
    {
        return view('livewire.config.kalender.index');
    }
}