<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapPresensiExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return new Collection([
            [
                'hadir' => 27,
                'tidak_hadir' => 5,
                'total_jam' => '9j 10m',
                'total_lembur' => 10,
                'jam_lembur' => '3j',
                'izin' => 1,
                'sakit' => 5,
                'cuti' => 11,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Hadir',
            'Tidak Hadir',
            'Total Jam',
            'Total Lembur',
            'Total Jam Lembur',
            'Izin',
            'Sakit',
            'Cuti',
        ];
    }
}