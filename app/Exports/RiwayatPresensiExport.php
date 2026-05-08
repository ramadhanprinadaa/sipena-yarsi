<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RiwayatPresensiExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return new Collection([
            [
                'tanggal' => '23 October 2026',
                'jam_masuk' => '09:00',
                'jam_keluar' => '18:00',
                'total_jam' => '9j 10m',
                'status' => 'Tepat Waktu',
                'status_lembur' => 'Disetujui',
                'jam_lembur' => '3j',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Total Jam',
            'Status',
            'Status Lembur',
            'Jam Lembur',
        ];
    }
}