<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PresensiExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Presensi::select(
            'tanggal',
            'jam_masuk',
            'jam_keluar',
            'total_jam',
            'status',
            'status_lembur',
            'jam_lembur'
        )->get();
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